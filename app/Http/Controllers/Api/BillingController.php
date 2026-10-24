<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Table;
use App\Models\Order;
use App\Services\BroadcastService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BillingController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->attributes->get('auth_user');

        $tables = Table::where('owner_id', $user->id)
            ->where('is_active', true)
            ->orderBy('table_number', 'asc')
            ->get();

        $orders = Order::where('owner_id', $user->id)
            ->where('status', '!=', 'cancelled')
            ->with('items')
            ->orderBy('created_at', 'asc')
            ->get();

        $result = $tables->map(function ($table) use ($orders) {
            $sessionOrders = $orders->filter(function ($order) use ($table) {
                return $order->table_number === $table->table_number && $order->created_at > $table->updated_at;
            })->values();

            if ($sessionOrders->isEmpty()) {
                return [
                    'tableId' => $table->id,
                    'tableNumber' => $table->table_number,
                    'status' => 'idle',
                    'session' => null,
                ];
            }

            $servedOrders = $sessionOrders->filter(fn($o) => $o->status === 'completed')->values();
            $unservedOrders = $sessionOrders->filter(fn($o) => $o->status !== 'completed')->values();

            $itemMap = [];
            $totalAmount = 0;
            $allCompleted = $sessionOrders->isNotEmpty() && $sessionOrders->every(fn($o) => $o->status === 'completed');

            // Strictly include ONLY served (completed) orders into the billable items and total
            foreach ($servedOrders as $order) {
                $totalAmount += (float) $order->total_amount;
                foreach ($order->items as $item) {
                    $key = $item->menu_item_name;
                    $price = (float) $item->price;
                    if (isset($itemMap[$key])) {
                        $itemMap[$key]['quantity'] += $item->quantity;
                    } else {
                        $itemMap[$key] = [
                            'menuItemName' => $key,
                            'quantity' => $item->quantity,
                            'price' => $price,
                        ];
                    }
                }
            }

            // Track unserved items so cashier is informed
            $unservedItems = [];
            foreach ($unservedOrders as $order) {
                foreach ($order->items as $item) {
                    $unservedItems[] = [
                        'menuItemName' => $item->menu_item_name,
                        'quantity' => $item->quantity,
                        'price' => (float) $item->price,
                        'status' => $order->status,
                    ];
                }
            }

            return [
                'tableId' => $table->id,
                'tableNumber' => $table->table_number,
                'status' => $allCompleted ? 'completed_unpaid' : ($servedOrders->isNotEmpty() ? 'active' : 'cooking'),
                'session' => [
                    'orders' => $sessionOrders->map(function ($o) {
                        return [
                            'id' => $o->id,
                            'status' => $o->status,
                            'isServed' => $o->status === 'completed',
                            'totalAmount' => (float) $o->total_amount,
                            'createdAt' => $o->created_at->toISOString(),
                        ];
                    }),
                    'items' => array_values($itemMap),
                    'totalAmount' => $totalAmount,
                    'servedOrdersCount' => $servedOrders->count(),
                    'unservedOrdersCount' => $unservedOrders->count(),
                    'unservedItems' => $unservedItems,
                    'createdAt' => $sessionOrders[0]->created_at->toISOString(),
                ]
            ];
        });

        return response()->json(['success' => true, 'data' => $result]);
    }

    public function settle(Request $request)
    {
        $user = $request->attributes->get('auth_user');
        $tableNumber = (int) $request->input('tableNumber');

        if (!$tableNumber) {
            return response()->json([
                'success' => false,
                'error' => ['code' => 'MISSING_FIELDS', 'message' => 'tableNumber is required']
            ], 400);
        }

        $table = Table::where('owner_id', $user->id)
            ->where('table_number', $tableNumber)
            ->first();

        if (!$table) {
            return response()->json(['success' => false, 'error' => ['code' => 'NOT_FOUND', 'message' => 'Table not found']], 404);
        }

        $activeOrders = Order::where('owner_id', $user->id)
            ->where('table_number', $tableNumber)
            ->where('created_at', '>', $table->updated_at)
            ->whereIn('status', ['pending', 'preparing', 'ready'])
            ->get();

        DB::transaction(function () use ($activeOrders, $table) {
            if ($activeOrders->isNotEmpty()) {
                Order::whereIn('id', $activeOrders->pluck('id'))
                    ->update([
                        'status' => 'completed',
                        'completed_at' => Carbon::now(),
                    ]);
            }
            $table->touch(); // updates updated_at, invalidating old sessions
        });

        // Realtime notifications
        foreach ($activeOrders as $order) {
            BroadcastService::emitToRestaurant($user->id, 'order:updated', [
                'id' => $order->id,
                'status' => 'completed'
            ]);
        }

        BroadcastService::emitToRestaurant($user->id, 'table:reset', ['tableNumber' => $tableNumber]);

        return response()->json([
            'success' => true,
            'data' => ['success' => true],
            'message' => 'Table bill settled and session reset successfully.'
        ]);
    }
}
