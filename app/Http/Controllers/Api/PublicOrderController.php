<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Owner;
use App\Models\Table;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\MenuItem;
use App\Services\SecurityService;
use App\Services\BroadcastService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class PublicOrderController extends Controller
{
    public function index(Request $request)
    {
        $ownerId = $request->query('ownerId');
        $tableNumber = (int) $request->query('tableNumber');
        $sessionToken = $request->query('sessionToken');

        if (!$ownerId || !$tableNumber || !$sessionToken) {
            return response()->json([
                'success' => false,
                'error' => ['code' => 'MISSING_FIELDS', 'message' => 'ownerId, tableNumber and sessionToken are required']
            ], 400);
        }

        $owner = Owner::where('id', $ownerId)->orWhere('username', $ownerId)->first();
        $resolvedOwnerId = $owner ? $owner->id : $ownerId;

        $table = Table::where('owner_id', $resolvedOwnerId)
            ->where('table_number', $tableNumber)
            ->where('is_active', true)
            ->first();

        if (!$table) {
            return response()->json(['success' => false, 'error' => ['code' => 'NOT_FOUND', 'message' => 'Table not found or inactive']], 404);
        }

        if (!SecurityService::verifySessionToken($sessionToken, $table->updated_at)) {
            return response()->json(['success' => false, 'error' => ['code' => 'SESSION_EXPIRED', 'message' => 'Dining session has expired.']], 403);
        }

        $orders = Order::where('owner_id', $resolvedOwnerId)
            ->where('table_number', $tableNumber)
            ->where('created_at', '>=', $table->updated_at)
            ->with('items')
            ->orderBy('created_at', 'asc')
            ->get();

        $formatted = $orders->map(function ($order) {
            return [
                'id' => $order->id,
                'status' => $order->status,
                'totalAmount' => (float) $order->total_amount,
                'estimatedTime' => $order->estimated_time,
                'tableNumber' => $order->table_number,
                'createdAt' => $order->created_at->toISOString(),
                'cancellationReason' => $order->cancellation_reason,
                'items' => $order->items->map(function ($item) {
                    return [
                        'menuItemName' => $item->menu_item_name,
                        'quantity' => $item->quantity,
                        'price' => (float) $item->price,
                        'selectedVariant' => $item->selected_variant,
                        'selectedAddons' => $item->selected_addons,
                        'notes' => $item->notes,
                    ];
                }),
            ];
        });

        return response()->json(['success' => true, 'data' => $formatted]);
    }

    public function store(Request $request)
    {
        try {
            $ownerId = $request->input('ownerId');
            $tableNumber = (int) $request->input('tableNumber');
            $items = $request->input('items');
            $sessionToken = $request->input('sessionToken');
            $notes = $request->input('notes');

            if (!$ownerId || !$tableNumber || !is_array($items) || count($items) === 0) {
                return response()->json([
                    'success' => false,
                    'error' => ['code' => 'MISSING_FIELDS', 'message' => 'ownerId, tableNumber and items are required']
                ], 400);
            }

            // Support lookup by either owner UUID or username (e.g. spicehub)
            $owner = Owner::where('id', $ownerId)->orWhere('username', $ownerId)->first();
            if (!$owner) {
                return response()->json(['success' => false, 'error' => ['code' => 'NOT_FOUND', 'message' => 'Restaurant venue not found']], 404);
            }
            $resolvedOwnerId = $owner->id;

            $table = Table::where('owner_id', $resolvedOwnerId)
                ->where('table_number', $tableNumber)
                ->where('is_active', true)
                ->first();

            if (!$table) {
                return response()->json(['success' => false, 'error' => ['code' => 'NOT_FOUND', 'message' => 'Table not found or inactive']], 404);
            }

            if (!SecurityService::verifySessionToken($sessionToken, $table->updated_at)) {
                return response()->json([
                    'success' => false,
                    'error' => ['code' => 'SESSION_EXPIRED', 'message' => 'Access Denied: Session expired or invalid. Please scan the QR code on your table to place orders.']
                ], 403);
            }

            $menuItemIds = array_column($items, 'menuItemId');
            $menuItems = MenuItem::whereIn('id', $menuItemIds)
                ->where('owner_id', $resolvedOwnerId)
                ->where('is_available', true)
                ->get()
                ->keyBy('id');

            if ($menuItems->count() !== count(array_unique($menuItemIds))) {
                return response()->json([
                    'success' => false,
                    'error' => ['code' => 'INVALID_ITEMS', 'message' => 'One or more items are unavailable']
                ], 400);
            }

            $totalAmount = 0.0;
            $maxPrepTime = 0;
            $orderItemsData = [];

            foreach ($items as $itemData) {
                $menuItem = $menuItems->get($itemData['menuItemId']);
                if (!$menuItem) continue;

                $qty = max(1, (int) ($itemData['quantity'] ?? 1));
                $basePrice = (float) $menuItem->price;

                // Handle portion variants
                $selectedVariant = null;
                $variantExtra = 0.0;
                if (!empty($itemData['selectedVariant'])) {
                    if (is_array($itemData['selectedVariant'])) {
                        $selectedVariant = $itemData['selectedVariant']['name'] ?? null;
                        $variantExtra = (float) ($itemData['selectedVariant']['price'] ?? 0);
                    } else {
                        $selectedVariant = (string) $itemData['selectedVariant'];
                        if ($menuItem->modifiers && !empty($menuItem->modifiers['variants'])) {
                            foreach ($menuItem->modifiers['variants'] as $v) {
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
                if (!empty($itemData['selectedAddons']) && is_array($itemData['selectedAddons'])) {
                    foreach ($itemData['selectedAddons'] as $addon) {
                        if (is_array($addon)) {
                            $name = $addon['name'] ?? null;
                            $addonPrice = (float) ($addon['price'] ?? 0);
                        } else {
                            $name = (string) $addon;
                            $addonPrice = 0.0;
                            if ($menuItem->modifiers && !empty($menuItem->modifiers['addons'])) {
                                foreach ($menuItem->modifiers['addons'] as $ad) {
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

                $itemNotes = trim((string) ($itemData['notes'] ?? '')) ?: null;
                $unitPrice = $basePrice + $variantExtra + $addonsExtra;
                $totalAmount += $unitPrice * $qty;
                $maxPrepTime = max($maxPrepTime, $menuItem->preparation_time);

                $orderItemsData[] = [
                    'menu_item_id' => $menuItem->id,
                    'menu_item_name' => $menuItem->name,
                    'quantity' => $qty,
                    'price' => $unitPrice,
                    'selected_variant' => $selectedVariant,
                    'selected_addons' => count($selectedAddons) > 0 ? $selectedAddons : null,
                    'notes' => $itemNotes,
                ];
            }

            // Pre-check database schema column existence to prevent 500 errors if migrations are in-flight
            $hasVariantCol = Schema::hasColumn('order_items', 'selected_variant');
            $hasAddonsCol = Schema::hasColumn('order_items', 'selected_addons');
            $hasNotesCol = Schema::hasColumn('order_items', 'notes');

            $order = DB::transaction(function () use ($resolvedOwnerId, $tableNumber, $totalAmount, $maxPrepTime, $notes, $orderItemsData, $hasVariantCol, $hasAddonsCol, $hasNotesCol) {
                $createdOrder = Order::create([
                    'owner_id' => $resolvedOwnerId,
                    'table_number' => $tableNumber,
                    'total_amount' => $totalAmount,
                    'estimated_time' => $maxPrepTime,
                    'status' => 'pending',
                    'notes' => trim($notes ?? '') ?: null,
                ]);

                foreach ($orderItemsData as $itemData) {
                    $itemPayload = [
                        'order_id' => $createdOrder->id,
                        'menu_item_id' => $itemData['menu_item_id'],
                        'menu_item_name' => $itemData['menu_item_name'],
                        'quantity' => $itemData['quantity'],
                        'price' => $itemData['price'],
                    ];
                    if ($hasVariantCol) {
                        $itemPayload['selected_variant'] = $itemData['selected_variant'];
                    }
                    if ($hasAddonsCol) {
                        $itemPayload['selected_addons'] = $itemData['selected_addons'];
                    }
                    if ($hasNotesCol) {
                        $itemPayload['notes'] = $itemData['notes'];
                    }
                    OrderItem::create($itemPayload);
                }

                return $createdOrder->load('items');
            });

            $formatted = [
                'id' => $order->id,
                'ownerId' => $order->owner_id,
                'tableNumber' => $order->table_number,
                'totalAmount' => (float) $order->total_amount,
                'estimatedTime' => $order->estimated_time,
                'status' => $order->status,
                'notes' => $order->notes,
                'cancellationReason' => $order->cancellation_reason,
                'createdAt' => $order->created_at->toISOString(),
                'items' => $order->items->map(function ($i) {
                    return [
                        'id' => $i->id,
                        'menuItemId' => $i->menu_item_id,
                        'menuItemName' => $i->menu_item_name,
                        'quantity' => $i->quantity,
                        'price' => (float) $i->price,
                        'selectedVariant' => $i->selected_variant,
                        'selectedAddons' => $i->selected_addons,
                        'notes' => $i->notes,
                    ];
                }),
            ];

            // Realtime alert to restaurant kitchen & owner dashboard
            BroadcastService::emitToRestaurant($resolvedOwnerId, 'order:new', $formatted);

            return response()->json(['success' => true, 'data' => $formatted], 201);
        } catch (\Throwable $e) {
            Log::error('Public order placement failed: ' . $e->getMessage(), [
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
}
