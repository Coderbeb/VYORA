<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Owner;
use App\Models\Table;
use App\Models\Room;
use App\Models\StaffMember;
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

        $username = trim($request->input('username'));
        $password = (string) $request->input('password');

        // 1. Check Venue Owner / Super Admin
        $owner = Owner::where('username', $username)->first();
        if ($owner && SecurityService::comparePassword($password, $owner->password_hash)) {
            $token = SecurityService::generateJwtToken([
                'id' => $owner->id,
                'username' => $owner->username,
                'email' => $owner->email,
                'role' => $owner->role,
                'businessType' => $owner->business_type,
                'isStaff' => false,
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
                        'ownerName' => $owner->owner_name,
                        'role' => $owner->role,
                        'businessType' => $owner->business_type,
                        'isStaff' => false,
                    ]
                ]
            ]);
        }

        // 2. Check Staff Member (supports username matching + password or 4-digit PIN)
        $staff = StaffMember::where('username', strtolower($username))
            ->orWhere('username', $username)
            ->where('is_active', true)
            ->first();

        if ($staff) {
            $passwordMatches = SecurityService::comparePassword($password, $staff->password_hash)
                || (!empty($staff->pin) && $password === (string) $staff->pin);

            if ($passwordMatches) {
                $venue = Owner::find($staff->owner_id);
                if ($venue) {
                    $token = SecurityService::generateJwtToken([
                        'id' => $venue->id,
                        'venueId' => $venue->id,
                        'staffId' => $staff->id,
                        'staffName' => $staff->name,
                        'username' => $staff->username,
                        'email' => $staff->email ?? $venue->email,
                        'role' => $staff->role,
                        'businessType' => $venue->business_type,
                        'isStaff' => true,
                    ]);

                    return response()->json([
                        'success' => true,
                        'data' => [
                            'token' => $token,
                            'owner' => [
                                'id' => $venue->id,
                                'venueId' => $venue->id,
                                'staffId' => $staff->id,
                                'username' => $staff->username,
                                'email' => $staff->email ?? $venue->email,
                                'restaurantName' => $venue->restaurant_name,
                                'ownerName' => $staff->name,
                                'staffName' => $staff->name,
                                'role' => $staff->role,
                                'businessType' => $venue->business_type,
                                'isStaff' => true,
                            ]
                        ]
                    ]);
                }
            }
        }

        return response()->json([
            'success' => false,
            'error' => ['code' => 'INVALID_CREDENTIALS', 'message' => 'Invalid username or password']
        ], 401);
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
        if (!$user || !isset($user->id)) {
            return response()->json([
                'success' => false,
                'error' => ['code' => 'UNAUTHORIZED', 'message' => 'Unauthorized']
            ], 401);
        }

        try {
            $owner = Owner::with(['plan', 'partnerRestaurant'])->find($user->id);

            if (!$owner) {
                return response()->json(['success' => false, 'error' => ['message' => 'User not found']], 404);
            }

            $isStaff = !empty($user->isStaff);
            $staffMember = null;
            if ($isStaff && !empty($user->staffId)) {
                $staffMember = StaffMember::find($user->staffId);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $owner->id,
                    'staffId' => $staffMember ? $staffMember->id : null,
                    'username' => $staffMember ? $staffMember->username : $owner->username,
                    'email' => ($staffMember && $staffMember->email) ? $staffMember->email : $owner->email,
                    'restaurantName' => $owner->restaurant_name,
                    'ownerName' => $staffMember ? $staffMember->name : $owner->owner_name,
                    'staffName' => $staffMember ? $staffMember->name : null,
                    'phone' => ($staffMember && $staffMember->phone) ? $staffMember->phone : $owner->phone,
                    'role' => $staffMember ? $staffMember->role : $owner->role,
                    'businessType' => $owner->business_type,
                    'isStaff' => $isStaff,
                    'partnerRestaurant' => $owner->partnerRestaurant ? [
                        'id' => $owner->partnerRestaurant->id,
                        'name' => $owner->partnerRestaurant->restaurant_name,
                    ] : null,
                    'cuisine' => $owner->cuisine,
                    'plan' => $owner->plan,
                ]
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Profile endpoint exception: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => ['code' => 'SERVER_ERROR', 'message' => 'Unable to load profile at this time']
            ], 500);
        }
    }

    public function changePassword(Request $request)
    {
        $user = $request->attributes->get('auth_user');
        $request->validate([
            'currentPassword' => 'required|string',
            'newPassword' => 'required|string|min:4',
        ]);

        if (!empty($user->isStaff) && !empty($user->staffId)) {
            $staff = StaffMember::find($user->staffId);
            if (!$staff) {
                return response()->json(['success' => false, 'error' => ['message' => 'Staff member not found']], 404);
            }

            $match = SecurityService::comparePassword($request->input('currentPassword'), $staff->password_hash)
                || (!empty($staff->pin) && $request->input('currentPassword') === (string) $staff->pin);

            if (!$match) {
                return response()->json([
                    'success' => false,
                    'error' => ['code' => 'INVALID_PASSWORD', 'message' => 'Incorrect current password or PIN']
                ], 400);
            }

            $newPass = $request->input('newPassword');
            $staff->password_hash = SecurityService::hashPassword($newPass);
            if (strlen($newPass) <= 6 && ctype_digit($newPass)) {
                $staff->pin = $newPass;
            }
            $staff->save();

            return response()->json([
                'success' => true,
                'message' => 'Password updated successfully'
            ]);
        }

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
