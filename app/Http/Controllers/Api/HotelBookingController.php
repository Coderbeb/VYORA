<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\Guest;
use App\Models\Booking;
use App\Services\BroadcastService;
use Carbon\Carbon;

class HotelBookingController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->attributes->get('auth_user');
        $ownerId = $user->id;
        $status = $request->query('status', 'active');

        $query = Booking::where('owner_id', $ownerId)
            ->with(['room.category', 'guest', 'roomOrders.items', 'serviceRequests']);

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        $bookings = $query->orderBy('created_at', 'desc')->get();

        return response()->json(['success' => true, 'data' => $bookings]);
    }

    public function checkIn(Request $request)
    {
        $user = $request->attributes->get('auth_user');
        $ownerId = $user->id;
        $request->validate([
            'room_id' => 'required|uuid',
            'full_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'id_type' => 'nullable|string|max:100',
            'id_number' => 'nullable|string|max:100',
            'room_rate' => 'required|numeric|min:0',
            'advance_deposit' => 'nullable|numeric|min:0',
            'expected_check_out' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $room = Room::where('owner_id', $ownerId)->where('id', $request->room_id)->first();
        if (!$room) {
            return response()->json(['success' => false, 'error' => ['code' => 'NOT_FOUND', 'message' => 'Room not found']], 404);
        }

        if ($room->status === 'occupied') {
            return response()->json(['success' => false, 'error' => ['code' => 'ROOM_ALREADY_OCCUPIED', 'message' => "Room {$room->room_number} is already occupied!"]], 400);
        }

        // Find or create Guest
        $guest = null;
        if ($request->phone) {
            $guest = Guest::where('owner_id', $ownerId)->where('phone', $request->phone)->first();
        }
        if (!$guest) {
            $guest = Guest::create([
                'owner_id' => $ownerId,
                'full_name' => $request->full_name,
                'phone' => $request->phone,
                'email' => $request->email,
                'id_type' => $request->id_type ?? 'Passport',
                'id_number' => $request->id_number,
            ]);
        } else {
            $guest->update([
                'full_name' => $request->full_name,
                'email' => $request->email ?? $guest->email,
                'id_type' => $request->id_type ?? $guest->id_type,
                'id_number' => $request->id_number ?? $guest->id_number,
            ]);
        }

        $booking = Booking::create([
            'owner_id' => $ownerId,
            'room_id' => $room->id,
            'guest_id' => $guest->id,
            'check_in' => Carbon::now(),
            'expected_check_out' => $request->expected_check_out ? Carbon::parse($request->expected_check_out) : Carbon::now()->addDay(),
            'room_rate' => $request->room_rate,
            'advance_deposit' => $request->advance_deposit ?? 0.00,
            'status' => 'active',
            'notes' => $request->notes,
        ]);

        // Mark room as occupied and touch updated_at to ensure fresh session
        $room->status = 'occupied';
        $room->touch();
        $room->save();

        BroadcastService::emitToRestaurant($ownerId, 'room:status', [
            'roomId' => $room->id,
            'roomNumber' => $room->room_number,
            'status' => 'occupied',
            'guest' => $guest->full_name,
        ]);

        return response()->json([
            'success' => true,
            'data' => $booking->load(['room.category', 'guest']),
            'message' => "Guest checked into Room {$room->room_number} successfully!"
        ], 201);
    }

    public function checkOut(Request $request)
    {
        $user = $request->attributes->get('auth_user');
        $ownerId = $user->id;
        $request->validate([
            'booking_id' => 'required|uuid',
        ]);

        $booking = Booking::where('owner_id', $ownerId)
            ->where('id', $request->booking_id)
            ->with(['room', 'guest', 'roomOrders.items'])
            ->first();

        if (!$booking) {
            return response()->json(['success' => false, 'error' => ['code' => 'NOT_FOUND', 'message' => 'Booking not found']], 404);
        }

        if ($booking->status === 'checked_out') {
            return response()->json(['success' => false, 'error' => ['code' => 'ALREADY_CHECKED_OUT', 'message' => 'Booking is already checked out']], 400);
        }

        $booking->check_out = Carbon::now();
        $booking->status = 'checked_out';
        $booking->save();

        // Mark all in-house room orders completed
        foreach ($booking->roomOrders as $order) {
            if ($order->status !== 'completed' && $order->status !== 'cancelled') {
                $order->status = 'completed';
                $order->completed_at = Carbon::now();
                $order->save();
            }
        }

        // Mark room as vacant_dirty (requires housekeeping turnover)
        if ($booking->room) {
            $booking->room->status = 'vacant_dirty';
            $booking->room->touch(); // invalidates guest QR session token
            $booking->room->save();

            BroadcastService::emitToRestaurant($ownerId, 'room:status', [
                'roomId' => $booking->room->id,
                'roomNumber' => $booking->room->room_number,
                'status' => 'vacant_dirty',
            ]);

            BroadcastService::emitToRestaurant($ownerId, 'table:reset', [
                'roomNumber' => $booking->room->room_number,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => "Room {$booking->room->room_number} successfully checked out and marked for housekeeping turnover."
        ]);
    }
}
