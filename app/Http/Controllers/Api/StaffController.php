<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StaffMember;
use App\Services\SecurityService;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->attributes->get('auth_user');
        $staff = StaffMember::where('owner_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $staff
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->attributes->get('auth_user');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:100',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'password' => 'nullable|string|min:4',
            'pin' => 'nullable|string|max:10',
            'role' => 'required|in:FRONT_DESK,HOUSEKEEPING,CHEF,WAITER,CASHIER,MANAGER',
        ]);

        $existing = StaffMember::where('owner_id', $user->id)
            ->where('username', strtolower(trim($validated['username'])))
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'error' => ['message' => 'A staff member with this username already exists in your venue.']
            ], 422);
        }

        $passwordHash = !empty($validated['password'])
            ? SecurityService::hashPassword($validated['password'])
            : SecurityService::hashPassword($validated['pin'] ?? '1234');

        $staff = StaffMember::create([
            'owner_id' => $user->id,
            'name' => $validated['name'],
            'username' => strtolower(trim($validated['username'])),
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'password_hash' => $passwordHash,
            'pin' => $validated['pin'] ?? null,
            'role' => $validated['role'],
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'data' => $staff
        ], 201);
    }

    public function update(Request $request, string $id)
    {
        $user = $request->attributes->get('auth_user');
        $staff = StaffMember::where('owner_id', $user->id)->where('id', $id)->first();

        if (!$staff) {
            return response()->json(['success' => false, 'error' => ['message' => 'Staff member not found']], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'username' => 'sometimes|required|string|max:100',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'password' => 'nullable|string|min:4',
            'pin' => 'nullable|string|max:10',
            'role' => 'sometimes|required|in:FRONT_DESK,HOUSEKEEPING,CHEF,WAITER,CASHIER,MANAGER',
            'is_active' => 'sometimes|boolean',
        ]);

        if (isset($validated['name'])) $staff->name = $validated['name'];
        if (isset($validated['username'])) $staff->username = strtolower(trim($validated['username']));
        if (isset($validated['email'])) $staff->email = $validated['email'];
        if (isset($validated['phone'])) $staff->phone = $validated['phone'];
        if (isset($validated['role'])) $staff->role = $validated['role'];
        if (isset($validated['is_active'])) $staff->is_active = (bool) $validated['is_active'];
        if (isset($validated['pin'])) $staff->pin = $validated['pin'];

        if (!empty($validated['password'])) {
            $staff->password_hash = SecurityService::hashPassword($validated['password']);
        }

        $staff->save();

        return response()->json([
            'success' => true,
            'data' => $staff
        ]);
    }

    public function destroy(Request $request, string $id)
    {
        $user = $request->attributes->get('auth_user');
        $staff = StaffMember::where('owner_id', $user->id)->where('id', $id)->first();

        if (!$staff) {
            return response()->json(['success' => false, 'error' => ['message' => 'Staff member not found']], 404);
        }

        $staff->delete();

        return response()->json([
            'success' => true,
            'message' => 'Staff member removed successfully'
        ]);
    }
}
