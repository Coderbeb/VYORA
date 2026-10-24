<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Table;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\MenuItem;
use App\Services\SecurityService;
use App\Services\BroadcastService;
use Illuminate\Support\Facades\DB;

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

        $table = Table::where('owner_id', $ownerId)
            ->where('table_number', $tableNumber)
            ->where('is_active', true)
            ->first();

        if (!$table) {
            return response()->json(['success' => false, 'error' => ['code' => 'NOT_FOUND', 'message' => 'Table not found or inactive']], 404);
        }

        if (!SecurityService::verifySessionToken($sessionToken, $table->updated_at)) {
            return response()->json(['success' => false, 'error' => ['code' => 'SESSION_EXPIRED', 'message' => 'Dining session has expired.']], 403);
        }

        $orders = Order::where('owner_id', $ownerId)
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
                    ];
                }),
            ];
        });

        return response()->json(['success' => true, 'data' => $formatted]);
    }

    public function store(Request $request)
    {
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

        $table = Table::where('owner_id', $ownerId)
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
            ->where('owner_id', $ownerId)
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
            $price = (float) $menuItem->price;
            $totalAmount += $price * $qty;
            $maxPrepTime = max($maxPrepTime, $menuItem->preparation_time);

            $orderItemsData[] = [
                'menu_item_id' => $menuItem->id,
                'menu_item_name' => $menuItem->name,
                'quantity' => $qty,
                'price' => $price,
            ];
        }

        $order = DB::transaction(function () use ($ownerId, $tableNumber, $totalAmount, $maxPrepTime, $notes, $orderItemsData) {
            $createdOrder = Order::create([
                'owner_id' => $ownerId,
                'table_number' => $tableNumber,
                'total_amount' => $totalAmount,
                'estimated_time' => $maxPrepTime,
                'status' => 'pending',
                'notes' => trim($notes ?? '') ?: null,
            ]);

            foreach ($orderItemsData as $itemData) {
                $itemData['order_id'] = $createdOrder->id;
                OrderItem::create($itemData);
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
                ];
            }),
        ];

        // Realtime alert to restaurant kitchen & owner dashboard
        BroadcastService::emitToRestaurant($ownerId, 'order:new', $formatted);

        return response()->json(['success' => true, 'data' => $formatted], 201);
    }
}
