<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Room extends Model
{
    use HasUuids;

    protected $table = 'rooms';

    protected $fillable = [
        'id',
        'owner_id',
        'category_id',
        'room_number',
        'floor',
        'status', // vacant_clean, vacant_dirty, occupied, maintenance
        'qr_code_data',
        'qr_code_image_url',
    ];

    protected $casts = [
        'floor' => 'integer',
    ];

    public function owner()
    {
        return $this->belongsTo(Owner::class, 'owner_id');
    }

    public function category()
    {
        return $this->belongsTo(RoomCategory::class, 'category_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'room_id');
    }

    public function activeBooking()
    {
        return $this->hasOne(Booking::class, 'room_id')->where('status', 'active')->latestOfMany();
    }

    public function serviceRequests()
    {
        return $this->hasMany(HotelServiceRequest::class, 'room_id');
    }
}
