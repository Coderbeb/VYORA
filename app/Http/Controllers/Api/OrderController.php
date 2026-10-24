<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Table;
use App\Models\MenuItem;
use App\Services\BroadcastService;
use Carbon\Carbon;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->attributes->get('auth_user');
        $status = $request->query('status');
        $tableNumber = $request->query('tableNumber');
        $limit = (int) ($request->query('limit') ?: 50);

        $query = Order::where(function($q) use ($user) {
                $q->where('owner_id', $user->id)
                  ->orWhere('hotel_id', $user->id);
            })
            ->with(['items.menuItem' => function ($q) {
                $q->select('id', 'name', 'image_url', 'is_veg');
            }, 'hotel'])
            ->orderBy('created_at', 'desc')
            ->take($limit);

        if ($status) {
            $query->where('status', $status);
        }

        if ($tableNumber) {
            $query->where('table_number', (int) $tableNumber);
        }

        $orders = $query->get();

        $formatted = $orders->map(function ($order) {
            $hotelTitle = $order->hotel_name ?: ($order->hotel ? $order->hotel->restaurant_name : null);
            return [
                'id' => $order->id,
                'ownerId' => $order->owner_id,
                'tableNumber' => $order->table_number,
                'orderType' => $order->order_type, // 'dine_in' or 'room_service'
                'roomNumber' => $order->room_number,
                'hotelId' => $order->hotel_id,
                'hotelName' => $hotelTitle,
                'billingType' => $order->billing_type,
                'totalAmount' => (float) $order->total_amount,
                'estimatedTime' => $order->estimated_time,
                'status' => $order->status,
                'notes' => $order->notes,
                'cancellationReason' => $order->cancellation_reason,
                'createdAt' => $order->created_at->toISOString(),
                'completedAt' => $order->completed_at ? $order->completed_at->toISOString() : null,
                'items' => $order->items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'orderId' => $item->order_id,
                        'menuItemId' => $item->menu_item_id,
                        'menuItemName' => $item->menu_item_name,
                        'quantity' => $item->quantity,
                        'price' => (float) $item->price,
                        'isVeg' => $item->menuItem ? (bool) $item->menuItem->is_veg : true,
                        'menuItem' => $item->menuItem ? [
                            'name' => $item->menuItem->name,
                            'imageUrl' => $item->menuItem->image_url,
                            'isVeg' => (bool) $item->menuItem->is_veg,
                        ] : null,
                    ];
                }),
            ];
        });

        return response()->json(['success' => true, 'data' => $formatted]);
    }

    public function show(Request $request, string $id)
    {
        $user = $request->attributes->get('auth_user');
        $order = Order::where('id', $id)
            ->where(function($q) use ($user) {
                $q->where('owner_id', $user->id)
                  ->orWhere('hotel_id', $user->id);
            })
            ->with(['items.menuItem' => function ($q) {
                $q->select('id', 'name', 'image_url', 'is_veg');
            }, 'hotel'])
            ->first();

        if (!$order) {
            return response()->json(['success' => false, 'error' => ['code' => 'NOT_FOUND', 'message' => 'Order not found']], 404);
        }

        $hotelTitle = $order->hotel_name ?: ($order->hotel ? $order->hotel->restaurant_name : null);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $order->id,
                'ownerId' => $order->owner_id,
                'tableNumber' => $order->table_number,
                'orderType' => $order->order_type,
                'roomNumber' => $order->room_number,
                'hotelId' => $order->hotel_id,
                'hotelName' => $hotelTitle,
                'billingType' => $order->billing_type,
                'totalAmount' => (float) $order->total_amount,
                'estimatedTime' => $order->estimated_time,
                'status' => $order->status,
                'notes' => $order->notes,
                'cancellationReason' => $order->cancellation_reason,
                'createdAt' => $order->created_at->toISOString(),
                'completedAt' => $order->completed_at ? $order->completed_at->toISOString() : null,
                'items' => $order->items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'orderId' => $item->order_id,
                        'menuItemId' => $item->menu_item_id,
                        'menuItemName' => $item->menu_item_name,
                        'quantity' => $item->quantity,
                        'price' => (float) $item->price,
                        'isVeg' => $item->menuItem ? (bool) $item->menuItem->is_veg : true,
                    ];
                }),
            ]
        ]);
    }

    public function update(Request $request, string $id)
    {
        $user = $request->attributes->get('auth_user');
        $request->validate([
            'status' => 'required|string|in:pending,preparing,ready,completed,cancelled',
            'cancellationReason' => 'nullable|string',
        ]);

        $order = Order::where('id', $id)
            ->where(function($q) use ($user) {
                $q->where('owner_id', $user->id)
                  ->orWhere('hotel_id', $user->id);
            })
            ->with('items')
            ->first();

        if (!$order) {
            return response()->json(['success' => false, 'error' => ['code' => 'NOT_FOUND', 'message' => 'Order not found']], 404);
        }

        $newStatus = $request->input('status');
        $order->status = $newStatus;

        if ($newStatus === 'completed') {
            $order->completed_at = Carbon::now();
        } elseif ($newStatus === 'cancelled') {
            $order->cancellation_reason = $request->input('cancellationReason') ?: 'Cancelled by staff';
        }

        $order->save();

        // Broadcast real-time update to Restaurant
        BroadcastService::emitToRestaurant($order->owner_id, 'order:updated', [
            'id' => $order->id,
            'tableNumber' => $order->table_number,
            'roomNumber' => $order->room_number,
            'orderType' => $order->order_type,
            'status' => $order->status,
        ]);

        // If from a partner hotel, also broadcast update to Hotel's dashboard!
        if ($order->hotel_id && $order->hotel_id !== $order->owner_id) {
            BroadcastService::emitToRestaurant($order->hotel_id, 'order:updated', [
                'id' => $order->id,
                'roomNumber' => $order->room_number,
                'orderType' => $order->order_type,
                'status' => $order->status,
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $order->id,
                'status' => $order->status,
                'completedAt' => $order->completed_at ? $order->completed_at->toISOString() : null,
                'cancellationReason' => $order->cancellation_reason,
            ]
        ]);
    }
}
