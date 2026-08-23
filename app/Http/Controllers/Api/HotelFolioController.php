<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use Carbon\Carbon;

class HotelFolioController extends Controller
{
    public function show(Request $request, $bookingId)
    {
        $user = $request->attributes->get('auth_user');
        $ownerId = $user->id;
        $booking = Booking::where('owner_id', $ownerId)
            ->where('id', $bookingId)
            ->with(['room.category', 'guest', 'roomOrders.items', 'serviceRequests'])
            ->first();

        if (!$booking) {
            return response()->json(['success' => false, 'error' => ['code' => 'NOT_FOUND', 'message' => 'Booking folio not found']], 404);
        }

        $checkIn = Carbon::parse($booking->check_in);
        $checkOut = $booking->check_out ? Carbon::parse($booking->check_out) : Carbon::now();

        // Calculate nights stayed (minimum 1 night)
        $diffHours = $checkIn->diffInHours($checkOut);
        $nights = max(1, (int) ceil($diffHours / 24));
        $roomTotal = $nights * (float) $booking->room_rate;

        // In-house dining orders billed to room folio
        $foodCharges = 0.00;
        $folioFoodOrders = [];
        foreach ($booking->roomOrders as $order) {
            if ($order->billing_type === 'folio' && $order->status !== 'cancelled') {
                $foodCharges += (float) $order->total_amount;
                $folioFoodOrders[] = [
                    'orderId' => $order->id,
                    'createdAt' => $order->created_at->toISOString(),
                    'total' => (float) $order->total_amount,
                    'items' => $order->items->map(fn($i) => [
                        'name' => $i->menu_item_name,
                        'quantity' => $i->quantity,
                        'price' => (float) $i->price,
                        'lineTotal' => (float) $i->price * $i->quantity,
                    ]),
                ];
            }
        }

        $subtotal = $roomTotal + $foodCharges;
        $advance = (float) $booking->advance_deposit;
        $balanceDue = max(0, $subtotal - $advance);

        $folioData = [
            'bookingId' => $booking->id,
            'status' => $booking->status,
            'guest' => [
                'name' => $booking->guest ? $booking->guest->full_name : 'Guest',
                'phone' => $booking->guest ? $booking->guest->phone : null,
                'email' => $booking->guest ? $booking->guest->email : null,
                'idType' => $booking->guest ? $booking->guest->id_type : 'ID',
                'idNumber' => $booking->guest ? $booking->guest->id_number : null,
            ],
            'room' => [
                'number' => $booking->room ? $booking->room->room_number : '-',
                'category' => $booking->room && $booking->room->category ? $booking->room->category->name : 'Standard Room',
                'floor' => $booking->room ? $booking->room->floor : 1,
            ],
            'stay' => [
                'checkIn' => $booking->check_in->toISOString(),
                'checkOut' => $booking->check_out ? $booking->check_out->toISOString() : null,
                'nights' => $nights,
                'nightlyRate' => (float) $booking->room_rate,
                'roomTotal' => $roomTotal,
            ],
            'foodOrders' => $folioFoodOrders,
            'foodTotal' => $foodCharges,
            'totals' => [
                'subtotal' => $subtotal,
                'advanceDeposit' => $advance,
                'balanceDue' => $balanceDue,
            ],
        ];

        return response()->json(['success' => true, 'data' => $folioData]);
    }
}
