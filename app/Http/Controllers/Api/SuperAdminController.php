<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Owner;
use App\Models\Order;
use App\Models\Table;
use App\Models\Room;
use App\Models\SubscriptionPlan;
use App\Models\PlatformSetting;
use App\Services\SecurityService;
use Illuminate\Support\Str;

class SuperAdminController extends Controller
{
    public function stats(Request $request)
    {
        $totalTenants = Owner::where('role', 'RESTAURANT_OWNER')->count();
        $totalRestaurants = Owner::where('role', 'RESTAURANT_OWNER')->whereIn('business_type', ['RESTAURANT', 'BOTH'])->count();
        $totalHotels = Owner::where('role', 'RESTAURANT_OWNER')->whereIn('business_type', ['HOTEL', 'BOTH'])->count();
        $totalOrders = Order::count();
        $platformRevenue = (float) Order::where('status', 'completed')->sum('total_amount');
        $activeTables = Table::where('is_active', true)->count();
        $activeRooms = Room::count();

        return response()->json([
            'success' => true,
            'data' => [
                'totalTenants' => $totalTenants,
                'totalRestaurants' => $totalRestaurants,
                'totalHotels' => $totalHotels,
                'totalOrders' => $totalOrders,
                'platformRevenue' => $platformRevenue,
                'activeTables' => $activeTables,
                'activeRooms' => $activeRooms,
            ]
        ]);
    }

    public function restaurants(Request $request)
    {
        $type = $request->query('type');
        $search = $request->query('search');

        $query = Owner::where('role', 'RESTAURANT_OWNER')
            ->with(['plan'])
            ->withCount(['tables', 'orders', 'rooms']);

        if ($type && in_array($type, ['RESTAURANT', 'HOTEL', 'BOTH'])) {
            $query->where('business_type', $type);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('restaurant_name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('owner_name', 'like', "%{$search}%");
            });
        }

        $restaurants = $query->orderBy('created_at', 'desc')->get();

        $formatted = $restaurants->map(function ($r) {
            return [
                'id' => $r->id,
                'username' => $r->username,
                'email' => $r->email,
                'restaurantName' => $r->restaurant_name,
                'ownerName' => $r->owner_name,
                'phone' => $r->phone,
                'role' => $r->role,
                'businessType' => $r->business_type ?? 'RESTAURANT',
                'subscriptionStatus' => $r->subscription_status ?? 'active',
                'showOnLanding' => (bool) $r->show_on_landing,
                'cuisine' => $r->cuisine,
                'createdAt' => $r->created_at ? $r->created_at->toISOString() : null,
                'plan' => $r->plan,
                '_count' => [
                    'tables' => $r->tables_count ?? 0,
                    'orders' => $r->orders_count ?? 0,
                    'rooms' => $r->rooms_count ?? 0,
                ]
            ];
        });

        return response()->json(['success' => true, 'data' => $formatted]);
    }

    public function createTenant(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|unique:owners,username',
            'email' => 'required|email|unique:owners,email',
            'password' => 'required|string|min:6',
            'restaurant_name' => 'required|string|max:255',
            'owner_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'business_type' => 'required|in:RESTAURANT,HOTEL,BOTH',
            'plan_id' => 'nullable|uuid|exists:subscription_plans,id',
        ]);

        $owner = Owner::create([
            'username' => strtolower(trim($validated['username'])),
            'email' => strtolower(trim($validated['email'])),
            'password_hash' => SecurityService::hashPassword($validated['password']),
            'restaurant_name' => $validated['restaurant_name'],
            'owner_name' => $validated['owner_name'] ?? $validated['restaurant_name'],
            'phone' => $validated['phone'] ?? null,
            'business_type' => $validated['business_type'],
            'role' => 'RESTAURANT_OWNER',
            'plan_id' => $validated['plan_id'] ?? null,
            'subscription_status' => 'active',
            'show_on_landing' => true,
        ]);

        return response()->json(['success' => true, 'data' => $owner], 201);
    }

    public function updateRestaurant(Request $request)
    {
        $ownerId = $request->input('ownerId');
        $owner = Owner::find($ownerId);
        if (!$owner) {
            return response()->json(['success' => false, 'error' => ['message' => 'Property/Tenant not found']], 404);
        }

        if ($request->has('tier')) {
            $plan = SubscriptionPlan::where('tier', $request->input('tier'))->first();
            if ($plan) {
                $owner->plan_id = $plan->id;
            }
        }

        if ($request->has('plan_id')) {
            $owner->plan_id = $request->input('plan_id');
        }

        if ($request->has('business_type')) {
            $owner->business_type = $request->input('business_type');
        }

        if ($request->has('subscription_status')) {
            $owner->subscription_status = $request->input('subscription_status');
        }

        if ($request->has('showOnLanding')) {
            $owner->show_on_landing = (bool) $request->input('showOnLanding');
        }

        if ($request->has('restaurantName')) {
            $owner->restaurant_name = $request->input('restaurantName');
        }

        if ($request->has('cuisine')) {
            $owner->cuisine = $request->input('cuisine');
        }

        if ($request->has('phone')) {
            $owner->phone = $request->input('phone');
        }

        if ($request->filled('password')) {
            $owner->password_hash = SecurityService::hashPassword($request->input('password'));
        }

        $owner->save();

        return response()->json(['success' => true, 'data' => $owner]);
    }

    public function deleteRestaurant(string $id)
    {
        $owner = Owner::find($id);
        if (!$owner) {
            return response()->json(['success' => false, 'error' => ['message' => 'Property/Tenant not found']], 404);
        }

        if ($owner->role === 'SUPER_ADMIN') {
            return response()->json(['success' => false, 'error' => ['message' => 'Super Admin cannot be deleted']], 400);
        }

        $owner->delete();

        return response()->json(['success' => true, 'message' => 'Tenant successfully removed']);
    }

    // Platform Customizations & SaaS Settings
    public function getSettings(Request $request)
    {
        $defaults = [
            'platform_name' => 'Vyora',
            'tagline' => 'Unified OS for Stays & Dining',
            'support_email' => 'support@vyora.com',
            'support_phone' => '+91 98765 43210',
            'currency_symbol' => '₹',
            'currency_code' => 'INR',
            'primary_color' => '#034d37',
            'accent_color' => '#c5a880',
            'allow_self_registration' => true,
            'default_trial_days' => 14,
            'commission_percent' => 0.0,
            'pusher_app_id' => env('PUSHER_APP_ID', '2188435'),
            'pusher_app_key' => env('PUSHER_APP_KEY', '339d3e48e2eb0e402d7c'),
            'pusher_app_secret' => env('PUSHER_APP_SECRET', '45ec6635b845ee29cf72'),
            'pusher_app_cluster' => env('PUSHER_APP_CLUSTER', 'ap2'),
            'payment_gateway_mode' => 'manual', // stripe, razorpay, manual
            'stripe_public_key' => '',
            'stripe_secret_key' => '',
            'razorpay_key_id' => '',
            'razorpay_key_secret' => '',
            'terms_url' => '/terms',
            'privacy_url' => '/privacy',
        ];

        $settings = [];
        foreach ($defaults as $key => $defaultVal) {
            $settings[$key] = PlatformSetting::get($key, $defaultVal);
        }

        return response()->json(['success' => true, 'data' => $settings]);
    }

    public function updateSettings(Request $request)
    {
        $inputs = $request->all();

        foreach ($inputs as $key => $value) {
            $type = is_bool($value) ? 'boolean' : (is_numeric($value) ? 'number' : (is_array($value) ? 'json' : 'string'));
            PlatformSetting::set($key, $value, 'general', $type);
        }

        return response()->json(['success' => true, 'message' => 'Platform customizations saved successfully']);
    }

    // Subscription Plans CRUD
    public function getPlans(Request $request)
    {
        $plans = SubscriptionPlan::orderBy('price', 'asc')->get();
        return response()->json(['success' => true, 'data' => $plans]);
    }

    public function savePlan(Request $request)
    {
        $id = $request->input('id');
        $tier = strtoupper(trim($request->input('tier')));
        $businessType = $request->input('business_type', 'RESTAURANT');
        $price = (float) $request->input('price', 0);
        $maxTables = (int) $request->input('max_tables', 5);
        $maxRooms = (int) $request->input('max_rooms', 0);
        $features = $request->input('features', []);

        if (is_string($features)) {
            $features = array_filter(array_map('trim', explode("\n", $features)));
        }

        if ($id) {
            $plan = SubscriptionPlan::find($id);
            if (!$plan) {
                return response()->json(['success' => false, 'error' => ['message' => 'Plan not found']], 404);
            }
            $plan->update([
                'tier' => $tier,
                'business_type' => $businessType,
                'price' => $price,
                'max_tables' => $maxTables,
                'max_rooms' => $maxRooms,
                'features' => $features,
            ]);
        } else {
            $plan = SubscriptionPlan::create([
                'tier' => $tier,
                'business_type' => $businessType,
                'price' => $price,
                'max_tables' => $maxTables,
                'max_rooms' => $maxRooms,
                'features' => $features,
            ]);
        }

        return response()->json(['success' => true, 'data' => $plan]);
    }

    public function deletePlan(string $id)
    {
        $plan = SubscriptionPlan::find($id);
        if (!$plan) {
            return response()->json(['success' => false, 'error' => ['message' => 'Plan not found']], 404);
        }

        $plan->delete();
        return response()->json(['success' => true, 'message' => 'Plan deleted']);
    }
}
