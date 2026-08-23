<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HotelServiceRequest;
use App\Services\BroadcastService;
use Carbon\Carbon;

class HotelServiceController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->attributes->get('auth_user');
        $ownerId = $user->id;
        $status = $request->query('status');

        $query = HotelServiceRequest::where('owner_id', $ownerId)
            ->with(['room.category', 'booking.guest']);

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        $requests = $query->orderBy('created_at', 'desc')->get();

        return response()->json(['success' => true, 'data' => $requests]);
    }

    public function update(Request $request, $id)
    {
        $user = $request->attributes->get('auth_user');
        $ownerId = $user->id;
        $request->validate([
            'status' => 'required|in:pending,in_progress,completed',
        ]);

        $serviceReq = HotelServiceRequest::where('owner_id', $ownerId)->where('id', $id)->first();
        if (!$serviceReq) {
            return response()->json(['success' => false, 'error' => ['code' => 'NOT_FOUND', 'message' => 'Service request not found']], 404);
        }

        $serviceReq->status = $request->status;
        if ($request->status === 'completed') {
            $serviceReq->completed_at = Carbon::now();
        }
        $serviceReq->save();

        BroadcastService::emitToRestaurant($ownerId, 'service:updated', [
            'id' => $serviceReq->id,
            'roomNumber' => $serviceReq->room ? $serviceReq->room->room_number : null,
            'status' => $serviceReq->status,
        ]);

        return response()->json(['success' => true, 'data' => $serviceReq]);
    }
}
