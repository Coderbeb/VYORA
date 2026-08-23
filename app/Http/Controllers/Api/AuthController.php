<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Owner;
use App\Models\Table;
use App\Models\Room;
use App\Models\SubscriptionPlan;
use App\Services\SecurityService;
use App\Services\QRService;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $username = $request->input('username');
        $password = $request->input('password');

        $owner = Owner::where('username', $username)->first();
        if (!$owner || !SecurityService::comparePassword($password, $owner->password_hash)) {
            return response()->json([
                'success' => false,
                'error' => ['code' => 'INVALID_CREDENTIALS', 'message' => 'Invalid username or password']
            ], 401);
        }

        $token = SecurityService::generateJwtToken([
            'id' => $owner->id,
            'username' => $owner->username,
            'email' => $owner->email,
            'role' => $owner->role,
            'businessType' => $owner->business_type,
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'token' => $token,
                'owner' => [
                    'id' => $owner->id,
                    'username' => $owner->username,
                    'email' => $owner->email,
                    'restaurantName' => $owner->restaurant_name,
                    'role' => $owner->role,
                    'businessType' => $owner->business_type,
                ]
            ]
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'restaurantName' => 'required|string',
            'ownerName' => 'required|string',
            'email' => 'required|email',
            'username' => 'required|string|min:3',
            'password' => 'required|string|min:6',
            'businessType' => 'nullable|in:RESTAURANT,HOTEL,BOTH',
            'tableCount' => 'nullable|integer|min:0',
            'roomCount' => 'nullable|integer|min:0',
            'phone' => 'nullable|string',
        ]);

        $username = $request->input('username');
        $email = $request->input('email');
        $businessType = $request->input('businessType', 'RESTAURANT');

        $existing = Owner::where('username', $username)->orWhere('email', $email)->first();
        if ($existing) {
            $field = $existing->username === $username ? 'Username' : 'Email';
            return response()->json([
                'success' => false,
                'error' => ['code' => 'DUPLICATE', 'message' => "{$field} already exists"]
            ], 409);
        }

        $freePlan = SubscriptionPlan::firstOrCreate(
            ['tier' => 'FREE', 'business_type' => $businessType],
            ['price' => 0.00, 'max_tables' => 5, 'max_rooms' => 10, 'features' => ['Basic QR access', 'Up to 5 tables / 10 rooms']]
        );

        $tableCount = (int) $request->input('tableCount', $businessType === 'HOTEL' ? 0 : 5);
        $roomCount = (int) $request->input('roomCount', in_array($businessType, ['HOTEL', 'BOTH']) ? 5 : 0);

        $owner = Owner::create([
            'username' => $username,
            'email' => $email,
            'password_hash' => SecurityService::hashPassword($request->input('password')),
            'restaurant_name' => $request->input('restaurantName'),
            'owner_name' => $request->input('ownerName'),
            'phone' => $request->input('phone'),
            'business_type' => $businessType,
            'plan_id' => $freePlan->id,
            'role' => 'RESTAURANT_OWNER',
        ]);

        $host = $request->getSchemeAndHttpHost();

        // Seed Tables if Restaurant or Both
        if (in_array($businessType, ['RESTAURANT', 'BOTH']) && $tableCount > 0) {
            for ($i = 1; $i <= $tableCount; $i++) {
                $orderUrl = QRService::buildTableUrl($owner->id, $i, $host);
                $qrDataUrl = QRService::generateSvgDataUri($orderUrl);

                Table::create([
                    'owner_id' => $owner->id,
                    'table_number' => $i,
                    'qr_code_data' => $orderUrl,
                    'qr_code_image_url' => $qrDataUrl,
                    'is_active' => true,
                ]);
            }
        }

        // Seed Rooms if Hotel or Both
        if (in_array($businessType, ['HOTEL', 'BOTH']) && $roomCount > 0) {
            for ($r = 101; $r < (101 + $roomCount); $r++) {
                $roomUrl = QRService::buildRoomUrl($owner->id, (string) $r, $host);
                $qrImage = QRService::generateSvgDataUri($roomUrl);

                Room::create([
                    'owner_id' => $owner->id,
                    'room_number' => (string) $r,
                    'floor' => 1,
                    'status' => 'vacant_clean',
                    'qr_code_data' => $roomUrl,
                    'qr_code_image_url' => $qrImage,
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'restaurantName' => $owner->restaurant_name,
                'username' => $owner->username,
                'email' => $owner->email,
                'businessType' => $businessType,
                'message' => 'Account registered successfully',
            ]
        ], 201);
    }

    public function profile(Request $request)
    {
        $user = $request->attributes->get('auth_user');
        $owner = Owner::with(['plan', 'partnerRestaurant'])->find($user->id);

        if (!$owner) {
            return response()->json(['success' => false, 'error' => ['message' => 'User not found']], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $owner->id,
                'username' => $owner->username,
                'email' => $owner->email,
                'restaurantName' => $owner->restaurant_name,
                'ownerName' => $owner->owner_name,
                'phone' => $owner->phone,
                'role' => $owner->role,
                'businessType' => $owner->business_type,
                'partnerRestaurant' => $owner->partnerRestaurant ? [
                    'id' => $owner->partnerRestaurant->id,
                    'name' => $owner->partnerRestaurant->restaurant_name,
                ] : null,
                'cuisine' => $owner->cuisine,
                'plan' => $owner->plan,
            ]
        ]);
    }

    public function changePassword(Request $request)
    {
        $user = $request->attributes->get('auth_user');
        $request->validate([
            'currentPassword' => 'required|string',
            'newPassword' => 'required|string|min:6',
        ]);

        $owner = Owner::find($user->id);
        if (!$owner || !SecurityService::comparePassword($request->input('currentPassword'), $owner->password_hash)) {
            return response()->json([
                'success' => false,
                'error' => ['code' => 'INVALID_PASSWORD', 'message' => 'Incorrect current password']
            ], 400);
        }

        $owner->password_hash = SecurityService::hashPassword($request->input('newPassword'));
        $owner->save();

        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully'
        ]);
    }

    public function verify(Request $request)
    {
        $user = $request->attributes->get('auth_user');
        return response()->json([
            'success' => true,
            'data' => ['valid' => true, 'user' => $user]
        ]);
    }
}
