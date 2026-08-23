<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Owner extends Model
{
    use HasUuids;

    protected $table = 'owners';

    protected $fillable = [
        'id',
        'username',
        'email',
        'password_hash',
        'restaurant_name',
        'owner_name',
        'phone',
        'role',
        'business_type', // RESTAURANT, HOTEL, BOTH
        'plan_id',
        'partner_restaurant_id',
        'subscription_status',
        'show_on_landing',
        'cuisine',
    ];

    protected $hidden = [
        'password_hash',
    ];

    protected $casts = [
        'show_on_landing' => 'boolean',
    ];

    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id');
    }

    public function partnerRestaurant()
    {
        return $this->belongsTo(Owner::class, 'partner_restaurant_id');
    }

    public function partnerRestaurants()
    {
        return $this->belongsToMany(
            Owner::class,
            'hotel_partner_restaurants',
            'hotel_id',
            'restaurant_id'
        )->withPivot('is_active')->withTimestamps();
    }

    public function partnerHotels()
    {
        return $this->belongsToMany(
            Owner::class,
            'hotel_partner_restaurants',
            'restaurant_id',
            'hotel_id'
        )->withPivot('is_active')->withTimestamps();
    }

    public function tables()
    {
        return $this->hasMany(Table::class, 'owner_id');
    }

    public function rooms()
    {
        return $this->hasMany(Room::class, 'owner_id');
    }

    public function roomCategories()
    {
        return $this->hasMany(RoomCategory::class, 'owner_id');
    }

    public function guests()
    {
        return $this->hasMany(Guest::class, 'owner_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'owner_id');
    }

    public function hotelServiceRequests()
    {
        return $this->hasMany(HotelServiceRequest::class, 'owner_id');
    }

    public function menuCategories()
    {
        return $this->hasMany(MenuCategory::class, 'owner_id');
    }

    public function menuItems()
    {
        return $this->hasMany(MenuItem::class, 'owner_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'owner_id');
    }

    public function hotelRoomOrders()
    {
        return $this->hasMany(Order::class, 'hotel_id');
    }
}
