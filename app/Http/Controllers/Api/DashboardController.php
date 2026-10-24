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

        $userMatch = function($q) use ($user) {
            $q->where('owner_id', $user->id)
              ->orWhere('hotel_id', $user->id);
        };

        $totalOrders = Order::where($userMatch)->count();
        $todayOrders = Order::where($userMatch)
            ->where('created_at', '>=', $todayStart)
            ->count();

        $todayRevenue = (float) Order::where($userMatch)
            ->where('status', '!=', 'cancelled')
            ->where('created_at', '>=', $todayStart)
            ->sum('total_amount');

        $pendingOrders = Order::where($userMatch)
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
