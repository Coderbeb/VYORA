<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Owner;
use App\Models\Room;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\HotelServiceRequest;
use App\Services\SecurityService;
use App\Services\BroadcastService;
use Carbon\Carbon;

class PublicRoomPortalController extends Controller
{
    public function info(Request $request)
    {
        $hotelId = $request->query('hotelId');
        $roomNumber = $request->query('roomNumber');
        $code = $request->query('code');
        $sessionToken = $request->query('sessionToken');
        $selectedRestaurantId = $request->query('restaurantId');

        if (!$hotelId || !$roomNumber) {
            return response()->json(['success' => false, 'error' => ['code' => 'MISSING_PARAMS', 'message' => 'hotelId and roomNumber are required']], 400);
        }

        $hotel = Owner::with(['partnerRestaurants', 'partnerRestaurant'])->find($hotelId);

        if (!$hotel) {
            return response()->json(['success' => false, 'error' => ['code' => 'HOTEL_NOT_FOUND', 'message' => 'Hotel not found']], 404);
        }

        $room = Room::where('owner_id', $hotelId)
            ->where('room_number', $roomNumber)
            ->with(['category', 'activeBooking.guest'])
            ->first();

        if (!$room) {
            return response()->json(['success' => false, 'error' => ['code' => 'ROOM_NOT_FOUND', 'message' => 'Room not found']], 404);
        }

        // Validate cryptographic access
        $isValid = false;
        $isExpiredSession = false;

        if ($sessionToken) {
            if (SecurityService::verifySessionToken($sessionToken, $room->updated_at)) {
                $isValid = true;
            } else {
                $isExpiredSession = true;
            }
        }

        if (!$isValid && !$isExpiredSession && $code) {
            $validSig = SecurityService::getRoomSignature($hotelId, $roomNumber);
            if (hash_equals($validSig, (string) $code)) {
                $isValid = true;
            }
        }

        if (!$isValid) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => $isExpiredSession ? 'SESSION_EXPIRED' : 'INVALID_SIGNATURE',
                    'message' => $isExpiredSession ? 'Room stay has concluded. Please scan the QR code in your room to begin a new session.' : 'Invalid or expired room access. Please scan the QR code in your room.'
                ]
            ], 403);
        }

        $newSessionToken = SecurityService::generateSessionToken($room->id, $room->updated_at);

        // Determine Available Partner Restaurants
        $partners = $hotel->partnerRestaurants;
        if ($partners->isEmpty() && $hotel->partner_restaurant_id && $hotel->partnerRestaurant) {
            $partners = collect([$hotel->partnerRestaurant]);
        }

        // Determine which menu to load
        $foodProvider = 'in_house';
        $foodProviderName = $hotel->restaurant_name;
        $menuOwnerId = $hotelId;

        if ($selectedRestaurantId) {
            $targetPartner = $partners->firstWhere('id', $selectedRestaurantId);
            if ($targetPartner) {
                $foodProvider = 'partner';
                $foodProviderName = $targetPartner->restaurant_name;
                $menuOwnerId = $targetPartner->id;
            }
        } elseif ($partners->isNotEmpty()) {
            $firstPartner = $partners->first();
            $foodProvider = 'partner';
            $foodProviderName = $firstPartner->restaurant_name;
            $menuOwnerId = $firstPartner->id;
        }

        $categories = MenuCategory::where('owner_id', $menuOwnerId)->orderBy('sort_order')->get();
        $items = MenuItem::where('owner_id', $menuOwnerId)->where('is_available', true)->with('category')->get();

        return response()->json([
            'success' => true,
            'data' => [
                'hotel' => [
                    'id' => $hotel->id,
                    'name' => $hotel->restaurant_name,
                    'phone' => $hotel->phone,
                ],
                'room' => [
                    'id' => $room->id,
                    'number' => $room->room_number,
                    'category' => $room->category ? $room->category->name : 'Room',
                    'status' => $room->status,
                    'guestName' => $room->activeBooking && $room->activeBooking->guest ? explode(' ', $room->activeBooking->guest->full_name)[0] : null,
                ],
                'food' => [
                    'provider' => $foodProvider,
                    'providerId' => $menuOwnerId,
                    'providerName' => $foodProviderName,
                    'billingNote' => $foodProvider === 'in_house' ? 'Charges will be added to your Room Bill (Folio)' : "Direct order fulfilled by {$foodProviderName}",
                    'partnerOptions' => $partners->map(fn($p) => [
                        'id' => $p->id,
                        'name' => $p->restaurant_name,
                        'cuisine' => $p->cuisine,
                    ]),
                    'categories' => $categories,
                    'items' => $items,
                ],
                'sessionToken' => $newSessionToken,
            ]
        ]);
    }

    public function placeOrder(Request $request)
    {
        try {
            $request->validate([
            'hotelId' => 'required|uuid',
            'roomNumber' => 'required|string',
            'restaurantId' => 'nullable|uuid',
            'items' => 'required|array|min:1',
            'items.*.menuItemId' => 'required|uuid',
            'items.*.quantity' => 'required|integer|min:1',
            'sessionToken' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $hotelId = $request->hotelId;
        $roomNumber = $request->roomNumber;
        $sessionToken = $request->sessionToken;

        $hotel = Owner::with(['partnerRestaurants', 'partnerRestaurant'])->find($hotelId);
        if (!$hotel) {
            return response()->json(['success' => false, 'error' => ['code' => 'HOTEL_NOT_FOUND', 'message' => 'Hotel not found']], 404);
        }

        $room = Room::where('owner_id', $hotelId)->where('room_number', $roomNumber)->with('activeBooking')->first();
        if (!$room) {
            return response()->json(['success' => false, 'error' => ['code' => 'ROOM_NOT_FOUND', 'message' => 'Room not found']], 404);
        }

        if (!SecurityService::verifySessionToken($sessionToken, $room->updated_at)) {
            return response()->json(['success' => false, 'error' => ['code' => 'SESSION_EXPIRED', 'message' => 'Session expired. Please scan QR code again.']], 403);
        }

        // Determine destination restaurant
        $orderOwnerId = $hotelId;
        $isPartner = false;

        if ($request->restaurantId && $request->restaurantId !== $hotelId) {
            $orderOwnerId = $request->restaurantId;
            $isPartner = true;
        } elseif ($hotel->partnerRestaurants->isNotEmpty()) {
            $orderOwnerId = $hotel->partnerRestaurants->first()->id;
            $isPartner = true;
        } elseif ($hotel->partner_restaurant_id) {
            $orderOwnerId = $hotel->partner_restaurant_id;
            $isPartner = true;
        }

        $billingType = $isPartner ? 'direct_restaurant' : 'folio';

        // Validate items and compute total
        $itemIds = collect($request->items)->pluck('menuItemId');
        $dbItems = MenuItem::where('owner_id', $orderOwnerId)->whereIn('id', $itemIds)->where('is_available', true)->get()->keyBy('id');

        $totalAmount = 0.00;
        $maxPrep = 15;
        $orderItemsData = [];

        foreach ($request->items as $i) {
            $dbItem = $dbItems->get($i['menuItemId']);
            if (!$dbItem) {
                return response()->json(['success' => false, 'error' => ['code' => 'ITEM_UNAVAILABLE', 'message' => 'One or more items are unavailable']], 400);
            }
            $qty = max(1, (int) ($i['quantity'] ?? 1));
            $basePrice = (float) $dbItem->price;

            // Handle variants
            $selectedVariant = null;
            $variantExtra = 0.0;
            if (!empty($i['selectedVariant'])) {
                if (is_array($i['selectedVariant'])) {
                    $selectedVariant = $i['selectedVariant']['name'] ?? null;
                    $variantExtra = (float) ($i['selectedVariant']['price'] ?? 0);
                } else {
                    $selectedVariant = (string) $i['selectedVariant'];
                    if ($dbItem->modifiers && !empty($dbItem->modifiers['variants'])) {
                        foreach ($dbItem->modifiers['variants'] as $v) {
                            if (($v['name'] ?? '') === $selectedVariant) {
                                $variantExtra = (float) ($v['price'] ?? 0);
                                break;
                            }
                        }
                    }
                }
            }

            // Handle add-ons
            $selectedAddons = [];
            $addonsExtra = 0.0;
            if (!empty($i['selectedAddons']) && is_array($i['selectedAddons'])) {
                foreach ($i['selectedAddons'] as $addon) {
                    if (is_array($addon)) {
                        $name = $addon['name'] ?? null;
                        $addonPrice = (float) ($addon['price'] ?? 0);
                    } else {
                        $name = (string) $addon;
                        $addonPrice = 0.0;
                        if ($dbItem->modifiers && !empty($dbItem->modifiers['addons'])) {
                            foreach ($dbItem->modifiers['addons'] as $ad) {
                                if (($ad['name'] ?? '') === $name) {
                                    $addonPrice = (float) ($ad['price'] ?? 0);
                                    break;
                                }
                            }
                        }
                    }
                    if ($name) {
                        $selectedAddons[] = [
                            'name' => $name,
                            'price' => $addonPrice,
                        ];
                        $addonsExtra += $addonPrice;
                    }
                }
            }

            $itemNotes = trim((string) ($i['notes'] ?? '')) ?: null;
            $unitPrice = $basePrice + $variantExtra + $addonsExtra;
            $lineTotal = $unitPrice * $qty;
            $totalAmount += $lineTotal;

            if ($dbItem->preparation_time > $maxPrep) {
                $maxPrep = $dbItem->preparation_time;
            }

            $orderItemsData[] = [
                'item' => $dbItem,
                'quantity' => $qty,
                'price' => $unitPrice,
                'selected_variant' => $selectedVariant,
                'selected_addons' => count($selectedAddons) > 0 ? $selectedAddons : null,
                'notes' => $itemNotes,
            ];
        }

        $order = Order::create([
            'owner_id' => $orderOwnerId,
            'table_number' => 0,
            'room_id' => $room->id,
            'room_number' => $room->room_number,
            'hotel_id' => $hotel->id,
            'hotel_name' => $hotel->restaurant_name,
            'order_type' => 'room_service',
            'booking_id' => $room->activeBooking ? $room->activeBooking->id : null,
            'billing_type' => $billingType,
            'fulfilled_by_id' => $isPartner ? $orderOwnerId : null,
            'total_amount' => $totalAmount,
            'estimated_time' => $maxPrep,
            'status' => 'pending',
            'notes' => $request->notes,
        ]);

        $hasVariantCol = \Illuminate\Support\Facades\Schema::hasColumn('order_items', 'selected_variant');
        $hasAddonsCol = \Illuminate\Support\Facades\Schema::hasColumn('order_items', 'selected_addons');
        $hasNotesCol = \Illuminate\Support\Facades\Schema::hasColumn('order_items', 'notes');

        foreach ($orderItemsData as $oid) {
            $itemPayload = [
                'order_id' => $order->id,
                'menu_item_id' => $oid['item']->id,
                'menu_item_name' => $oid['item']->name,
                'quantity' => $oid['quantity'],
                'price' => $oid['price'],
            ];
            if ($hasVariantCol) {
                $itemPayload['selected_variant'] = $oid['selected_variant'];
            }
            if ($hasAddonsCol) {
                $itemPayload['selected_addons'] = $oid['selected_addons'];
            }
            if ($hasNotesCol) {
                $itemPayload['notes'] = $oid['notes'];
            }
            OrderItem::create($itemPayload);
        }

        $formatted = [
            'id' => $order->id,
            'ownerId' => $orderOwnerId,
            'roomNumber' => $room->room_number,
            'hotelId' => $hotel->id,
            'hotelName' => $hotel->restaurant_name,
            'orderType' => 'room_service',
            'billingType' => $billingType,
            'totalAmount' => $totalAmount,
            'estimatedTime' => $maxPrep,
            'status' => 'pending',
            'notes' => $order->notes,
            'createdAt' => $order->created_at->toISOString(),
            'items' => collect($orderItemsData)->map(fn($o) => [
                'menuItemId' => $o['item']->id,
                'menuItemName' => $o['item']->name,
                'quantity' => $o['quantity'],
                'price' => $o['price'],
                'selectedVariant' => $o['selected_variant'],
                'selectedAddons' => $o['selected_addons'],
                'notes' => $o['notes'],
            ]),
        ];

        // Notify Restaurant Kitchen via Pusher (with clear hotel name & room number)
        BroadcastService::emitToRestaurant($orderOwnerId, 'order:new', $formatted);

        // Also notify Hotel dashboard if fulfilled by partner
        if ($isPartner) {
            BroadcastService::emitToRestaurant($hotelId, 'order:new', $formatted);
        }

        return response()->json([
            'success' => true,
            'data' => $formatted,
            'message' => 'Room service order submitted to kitchen!'
        ], 201);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('In-room order placement failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'ORDER_CREATION_FAILED',
                    'message' => 'Failed to place order: ' . $e->getMessage()
                ]
            ], 500);
        }
    }

    public function requestService(Request $request)
    {
        $request->validate([
            'hotelId' => 'required|uuid',
            'roomNumber' => 'required|string',
            'serviceName' => 'required|string|max:255',
            'category' => 'required|in:housekeeping,concierge,maintenance',
            'sessionToken' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $hotelId = $request->hotelId;
        $roomNumber = $request->roomNumber;
        $sessionToken = $request->sessionToken;

        $room = Room::where('owner_id', $hotelId)->where('room_number', $roomNumber)->with('activeBooking')->first();
        if (!$room) {
            return response()->json(['success' => false, 'error' => ['code' => 'ROOM_NOT_FOUND', 'message' => 'Room not found']], 404);
        }

        if (!SecurityService::verifySessionToken($sessionToken, $room->updated_at)) {
            return response()->json(['success' => false, 'error' => ['code' => 'SESSION_EXPIRED', 'message' => 'Session expired. Please scan QR code again.']], 403);
        }

        $serviceReq = HotelServiceRequest::create([
            'owner_id' => $hotelId,
            'room_id' => $room->id,
            'booking_id' => $room->activeBooking ? $room->activeBooking->id : null,
            'service_name' => $request->serviceName,
            'category' => $request->category,
            'status' => 'pending',
            'notes' => $request->notes,
        ]);

        // Broadcast to Hotel Front Desk & Housekeeping
        BroadcastService::emitToRestaurant($hotelId, 'service:request', [
            'id' => $serviceReq->id,
            'roomNumber' => $room->room_number,
            'serviceName' => $serviceReq->service_name,
            'category' => $serviceReq->category,
            'timestamp' => Carbon::now()->toISOString(),
        ]);

        return response()->json([
            'success' => true,
            'data' => $serviceReq,
            'message' => 'Service request received! Our team is on the way.'
        ], 201);
    }

    public function liveOrders(Request $request)
    {
        $hotelId = $request->query('hotelId');
        $roomNumber = $request->query('roomNumber');
        $sessionToken = $request->query('sessionToken');

        $room = Room::where('owner_id', $hotelId)->where('room_number', $roomNumber)->first();
        if (!$room || !SecurityService::verifySessionToken($sessionToken, $room->updated_at)) {
            return response()->json(['success' => false, 'error' => ['code' => 'FORBIDDEN', 'message' => 'Invalid session']], 403);
        }

        $orders = Order::where(function($q) use ($hotelId, $roomNumber) {
                $q->where('hotel_id', $hotelId)
                  ->orWhere('owner_id', $hotelId);
            })
            ->where('room_number', $roomNumber)
            ->where('created_at', '>=', $room->updated_at)
            ->with('items')
            ->orderBy('created_at', 'desc')
            ->get();

        $services = HotelServiceRequest::where('owner_id', $hotelId)
            ->where('room_id', $room->id)
            ->where('created_at', '>=', $room->updated_at)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'orders' => $orders,
                'services' => $services,
            ]
        ]);
    }
}
