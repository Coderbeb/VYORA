<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Owner;
use App\Models\Order;
use App\Models\Table;
use App\Models\SubscriptionPlan;

class SuperAdminController extends Controller
{
    public function stats(Request $request)
    {
        $totalRestaurants = Owner::where('role', 'RESTAURANT_OWNER')->count();
        $totalOrders = Order::count();
        $platformRevenue = (float) Order::where('status', 'completed')->sum('total_amount');
        $activeTables = Table::where('is_active', true)->count();

        return response()->json([
            'success' => true,
            'data' => [
                'totalRestaurants' => $totalRestaurants,
                'totalOrders' => $totalOrders,
                'platformRevenue' => $platformRevenue,
                'activeTables' => $activeTables,
            ]
        ]);
    }

    public function restaurants(Request $request)
    {
        $restaurants = Owner::where('role', 'RESTAURANT_OWNER')
            ->with(['plan'])
            ->withCount(['tables', 'orders'])
            ->orderBy('created_at', 'desc')
            ->get();

        $formatted = $restaurants->map(function ($r) {
            return [
                'id' => $r->id,
                'username' => $r->username,
                'email' => $r->email,
                'restaurantName' => $r->restaurant_name,
                'ownerName' => $r->owner_name,
                'phone' => $r->phone,
                'role' => $r->role,
                'subscriptionStatus' => $r->subscription_status,
                'showOnLanding' => (bool) $r->show_on_landing,
                'cuisine' => $r->cuisine,
                'createdAt' => $r->created_at->toISOString(),
                'updatedAt' => $r->updated_at->toISOString(),
                'plan' => $r->plan,
                '_count' => [
                    'tables' => $r->tables_count,
                    'orders' => $r->orders_count,
                ]
            ];
        });

        return response()->json(['success' => true, 'data' => $formatted]);
    }

    public function updateRestaurant(Request $request)
    {
        $ownerId = $request->input('ownerId');
        $tier = $request->input('tier');
        $showOnLanding = $request->input('showOnLanding');
        $cuisine = $request->input('cuisine');

        $owner = Owner::find($ownerId);
        if (!$owner) {
            return response()->json(['success' => false, 'error' => ['message' => 'Restaurant not found']], 404);
        }

        if ($tier) {
            $plan = SubscriptionPlan::firstOrCreate(
                ['tier' => $tier],
                [
                    'price' => $tier === 'FREE' ? 0 : ($tier === 'PRO' ? 49.99 : 99.99),
                    'max_tables' => $tier === 'FREE' ? 5 : ($tier === 'PRO' ? 25 : 100),
                    'features' => []
                ]
            );
            $owner->plan_id = $plan->id;
        }

        if ($showOnLanding !== null) {
            $owner->show_on_landing = (bool) $showOnLanding;
        }

        if ($cuisine !== null) {
            $owner->cuisine = $cuisine;
        }

        $owner->save();

        return response()->json(['success' => true, 'data' => $owner]);
    }
}
