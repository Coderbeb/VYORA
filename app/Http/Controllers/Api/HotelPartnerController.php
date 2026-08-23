<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Owner;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class HotelPartnerController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->attributes->get('auth_user');
        $ownerId = $user->id;
        $owner = Owner::with(['partnerRestaurants', 'partnerHotels'])->find($ownerId);

        // List all other dining restaurants on QRestro (excluding self)
        $availableRestaurants = Owner::where('id', '!=', $ownerId)
            ->whereIn('business_type', ['RESTAURANT', 'BOTH'])
            ->where('role', '!=', 'SUPER_ADMIN')
            ->select('id', 'restaurant_name', 'owner_name', 'cuisine', 'phone', 'email')
            ->withCount('menuItems')
            ->get();

        $linkedPartnerIds = $owner ? $owner->partnerRestaurants->pluck('id')->toArray() : [];
        if ($owner && $owner->partner_restaurant_id && !in_array($owner->partner_restaurant_id, $linkedPartnerIds)) {
            $linkedPartnerIds[] = $owner->partner_restaurant_id;
        }

        return response()->json([
            'success' => true,
            'data' => [
                'linkedPartners' => $owner ? $owner->partnerRestaurants->map(fn($r) => [
                    'id' => $r->id,
                    'name' => $r->restaurant_name,
                    'cuisine' => $r->cuisine,
                    'phone' => $r->phone,
                ]) : [],
                'partnerHotels' => $owner ? $owner->partnerHotels->map(fn($h) => [
                    'id' => $h->id,
                    'name' => $h->restaurant_name,
                    'phone' => $h->phone,
                ]) : [],
                'availableRestaurants' => $availableRestaurants->map(function ($r) use ($linkedPartnerIds) {
                    return [
                        'id' => $r->id,
                        'name' => $r->restaurant_name,
                        'ownerName' => $r->owner_name,
                        'cuisine' => $r->cuisine,
                        'phone' => $r->phone,
                        'itemCount' => $r->menu_items_count,
                        'isLinked' => in_array($r->id, $linkedPartnerIds),
                    ];
                }),
            ]
        ]);
    }

    public function togglePartner(Request $request)
    {
        $user = $request->attributes->get('auth_user');
        $hotelId = $user->id;
        $request->validate([
            'restaurantId' => 'required|uuid',
            'link' => 'required|boolean',
        ]);

        $restaurantId = $request->restaurantId;
        $shouldLink = (bool) $request->link;

        $hotel = Owner::find($hotelId);
        $restaurant = Owner::find($restaurantId);

        if (!$hotel || !$restaurant) {
            return response()->json(['success' => false, 'error' => ['code' => 'NOT_FOUND', 'message' => 'Hotel or Restaurant not found']], 404);
        }

        if ($shouldLink) {
            $exists = DB::table('hotel_partner_restaurants')
                ->where('hotel_id', $hotelId)
                ->where('restaurant_id', $restaurantId)
                ->exists();

            if (!$exists) {
                DB::table('hotel_partner_restaurants')->insert([
                    'id' => (string) Str::uuid(),
                    'hotel_id' => $hotelId,
                    'restaurant_id' => $restaurantId,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            $hotel->partner_restaurant_id = $restaurantId;
            $hotel->save();

            return response()->json([
                'success' => true,
                'message' => "Partnered with {$restaurant->restaurant_name}! In-room guests can now order their dishes."
            ]);
        } else {
            DB::table('hotel_partner_restaurants')
                ->where('hotel_id', $hotelId)
                ->where('restaurant_id', $restaurantId)
                ->delete();

            if ($hotel->partner_restaurant_id === $restaurantId) {
                $hotel->partner_restaurant_id = null;
                $hotel->save();
            }

            return response()->json([
                'success' => true,
                'message' => "Unlinked partnership with {$restaurant->restaurant_name}."
            ]);
        }
    }
}
