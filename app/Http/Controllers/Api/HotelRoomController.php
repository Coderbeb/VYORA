<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\RoomCategory;
use App\Models\Owner;
use App\Services\SecurityService;
use App\Services\QRService;

class HotelRoomController extends Controller
{
    // Categories CRUD
    public function categories(Request $request)
    {
        $user = $request->attributes->get('auth_user');
        $ownerId = $user->id;
        $categories = RoomCategory::where('owner_id', $ownerId)
            ->withCount('rooms')
            ->orderBy('name')
            ->get();

        return response()->json(['success' => true, 'data' => $categories]);
    }

    public function storeCategory(Request $request)
    {
        $user = $request->attributes->get('auth_user');
        $ownerId = $user->id;
        $request->validate([
            'name' => 'required|string|max:100',
            'base_price' => 'nullable|numeric|min:0',
            'max_occupancy' => 'nullable|integer|min:1',
            'amenities' => 'nullable|array',
        ]);

        $cat = RoomCategory::create([
            'owner_id' => $ownerId,
            'name' => $request->name,
            'base_price' => $request->base_price ?? 0.00,
            'max_occupancy' => $request->max_occupancy ?? 2,
            'amenities' => $request->amenities ?? ['Wi-Fi', 'Air Conditioning', 'TV'],
        ]);

        return response()->json(['success' => true, 'data' => $cat], 201);
    }

    public function destroyCategory(Request $request, $id)
    {
        $user = $request->attributes->get('auth_user');
        $ownerId = $user->id;
        $cat = RoomCategory::where('owner_id', $ownerId)->where('id', $id)->first();
        if (!$cat) {
            return response()->json(['success' => false, 'error' => ['code' => 'NOT_FOUND', 'message' => 'Category not found']], 404);
        }

        $cat->delete();
        return response()->json(['success' => true, 'message' => 'Category deleted']);
    }

    // Rooms CRUD
    public function index(Request $request)
    {
        $user = $request->attributes->get('auth_user');
        $ownerId = $user->id;
        $host = $request->getSchemeAndHttpHost();

        $rooms = Room::where('owner_id', $ownerId)
            ->with(['category', 'activeBooking.guest'])
            ->orderBy('room_number')
            ->get();

        foreach ($rooms as $room) {
            $expectedUrl = QRService::buildRoomUrl($ownerId, $room->room_number, $host);
            if (!$room->qr_code_data || $room->qr_code_data !== $expectedUrl || !$room->qr_code_image_url) {
                $room->qr_code_data = $expectedUrl;
                $room->qr_code_image_url = QRService::generateSvgDataUri($expectedUrl);
                $room->save();
            }
        }

        return response()->json(['success' => true, 'data' => $rooms]);
    }

    public function store(Request $request)
    {
        $user = $request->attributes->get('auth_user');
        $ownerId = $user->id;
        $request->validate([
            'room_number' => 'required|string|max:50',
            'category_id' => 'nullable|uuid',
            'floor' => 'nullable|integer',
        ]);

        $owner = Owner::with('plan')->find($ownerId);
        $maxRooms = $owner->plan ? $owner->plan->max_rooms : 10;
        $currentCount = Room::where('owner_id', $ownerId)->count();

        if ($maxRooms > 0 && $currentCount >= $maxRooms) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'PLAN_LIMIT_REACHED',
                    'message' => "Your plan allows a maximum of {$maxRooms} rooms. Please upgrade to add more."
                ]
            ], 403);
        }

        $exists = Room::where('owner_id', $ownerId)->where('room_number', $request->room_number)->exists();
        if ($exists) {
            return response()->json([
                'success' => false,
                'error' => ['code' => 'DUPLICATE_ROOM', 'message' => "Room {$request->room_number} already exists."]
            ], 400);
        }

        $host = $request->getSchemeAndHttpHost();
        $qrData = QRService::buildRoomUrl($ownerId, $request->room_number, $host);
        $qrImage = QRService::generateSvgDataUri($qrData);

        $room = Room::create([
            'owner_id' => $ownerId,
            'category_id' => $request->category_id,
            'room_number' => $request->room_number,
            'floor' => $request->floor ?? 1,
            'status' => 'vacant_clean',
            'qr_code_data' => $qrData,
            'qr_code_image_url' => $qrImage,
        ]);

        return response()->json(['success' => true, 'data' => $room->load('category')], 201);
    }

    public function update(Request $request, $id)
    {
        $user = $request->attributes->get('auth_user');
        $ownerId = $user->id;
        $room = Room::where('owner_id', $ownerId)->where('id', $id)->first();
        if (!$room) {
            return response()->json(['success' => false, 'error' => ['code' => 'NOT_FOUND', 'message' => 'Room not found']], 404);
        }

        if ($request->has('status')) {
            $validStatuses = ['vacant_clean', 'vacant_dirty', 'occupied', 'maintenance'];
            if (!in_array($request->status, $validStatuses)) {
                return response()->json(['success' => false, 'error' => ['code' => 'INVALID_STATUS', 'message' => 'Invalid status']], 400);
            }
            $room->status = $request->status;
        }

        if ($request->has('category_id')) $room->category_id = $request->category_id;
        if ($request->has('floor')) $room->floor = $request->floor;

        $room->save();
        return response()->json(['success' => true, 'data' => $room->load('category')]);
    }

    public function destroy(Request $request, $id)
    {
        $user = $request->attributes->get('auth_user');
        $ownerId = $user->id;
        $room = Room::where('owner_id', $ownerId)->where('id', $id)->first();
        if (!$room) {
            return response()->json(['success' => false, 'error' => ['code' => 'NOT_FOUND', 'message' => 'Room not found']], 404);
        }

        if ($room->status === 'occupied') {
            return response()->json(['success' => false, 'error' => ['code' => 'ROOM_OCCUPIED', 'message' => 'Cannot delete an occupied room. Please check out the guest first.']], 400);
        }

        $room->delete();
        return response()->json(['success' => true, 'message' => 'Room deleted']);
    }
}
