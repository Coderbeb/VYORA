<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\MenuItem;
use App\Models\Table;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function stats(Request $request)
    {
        $user = $request->attributes->get('auth_user');
        $todayStart = Carbon::today('Asia/Kolkata');

        $totalOrders = Order::where('owner_id', $user->id)->count();
        $todayOrders = Order::where('owner_id', $user->id)
            ->where('created_at', '>=', $todayStart)
            ->count();

        $todayRevenue = (float) Order::where('owner_id', $user->id)
            ->where('status', '!=', 'cancelled')
            ->where('created_at', '>=', $todayStart)
            ->sum('total_amount');

        $pendingOrders = Order::where('owner_id', $user->id)
            ->whereIn('status', ['pending', 'preparing', 'ready'])
            ->count();

        $menuItems = MenuItem::where('owner_id', $user->id)->count();
        $tables = Table::where('owner_id', $user->id)->count();

        return response()->json([
            'success' => true,
            'data' => [
                'totalOrders' => $totalOrders,
                'todayOrders' => $todayOrders,
                'todayRevenue' => $todayRevenue,
                'pendingOrders' => $pendingOrders,
                'menuItems' => $menuItems,
                'tables' => $tables,
            ]
        ]);
    }
}
