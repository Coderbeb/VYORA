<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Table;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\SecurityService;
use App\Services\BroadcastService;
use Carbon\Carbon;

class ServiceRequestController extends Controller
{
    public function store(Request $request)
    {
        $ownerId = $request->input('ownerId');
        $tableNumber = (int) $request->input('tableNumber');
        $type = $request->input('type');
        $sessionToken = $request->input('sessionToken');

        if (!$ownerId || !$tableNumber || !$type) {
            return response()->json([
                'success' => false,
                'error' => ['code' => 'MISSING_FIELDS', 'message' => 'ownerId, tableNumber and type are required']
            ], 400);
        }

        if (!in_array($type, ['waiter', 'water'])) {
            return response()->json([
                'success' => false,
                'error' => ['code' => 'INVALID_TYPE', 'message' => 'Type must be "waiter" or "water"']
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
                'error' => ['code' => 'SESSION_EXPIRED', 'message' => 'Session expired. Please scan the QR code again.']
            ], 403);
        }

        // Broadcast service alert to dashboard
        BroadcastService::emitToRestaurant($ownerId, 'service:request', [
            'tableNumber' => $tableNumber,
            'type' => $type,
            'timestamp' => Carbon::now()->toISOString(),
        ]);

        if ($type === 'water') {
            $waterItem = MenuItem::where('owner_id', $ownerId)
                ->where('is_available', true)
                ->whereRaw('LOWER(name) LIKE ?', ['%water%'])
                ->first();

            if (!$waterItem) {
                return response()->json([
                    'success' => false,
                    'error' => ['code' => 'NO_WATER_ITEM', 'message' => 'No water item found in menu. Please ask the owner to add one.']
                ], 404);
            }

            $itemPrice = (float) $waterItem->price;

            $order = Order::create([
                'owner_id' => $ownerId,
                'table_number' => $tableNumber,
                'total_amount' => $itemPrice,
                'estimated_time' => $waterItem->preparation_time,
                'status' => 'pending',
                'notes' => 'Quick water order',
            ]);

            OrderItem::create([
                'order_id' => $order->id,
                'menu_item_id' => $waterItem->id,
                'menu_item_name' => $waterItem->name,
                'quantity' => 1,
                'price' => $itemPrice,
            ]);

            $formatted = [
                'id' => $order->id,
                'ownerId' => $order->owner_id,
                'tableNumber' => $order->table_number,
                'totalAmount' => $itemPrice,
                'estimatedTime' => $order->estimated_time,
                'status' => $order->status,
                'notes' => $order->notes,
                'createdAt' => $order->created_at->toISOString(),
                'items' => [[
                    'menuItemId' => $waterItem->id,
                    'menuItemName' => $waterItem->name,
                    'quantity' => 1,
                    'price' => $itemPrice,
                ]],
            ];

            BroadcastService::emitToRestaurant($ownerId, 'order:new', $formatted);

            return response()->json([
                'success' => true,
                'data' => ['type' => $type, 'tableNumber' => $tableNumber, 'order' => $formatted],
                'message' => 'Water order placed!',
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => ['type' => $type, 'tableNumber' => $tableNumber],
            'message' => 'Waiter has been notified!',
        ]);
    }
}
