<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Booking extends Model
{
    use HasUuids;

    protected $table = 'bookings';

    protected $fillable = [
        'id',
        'owner_id',
        'room_id',
        'guest_id',
        'check_in',
        'expected_check_out',
        'check_out',
        'room_rate',
        'advance_deposit',
        'status', // active, checked_out, cancelled
        'notes',
    ];

    protected $casts = [
        'check_in' => 'datetime',
        'expected_check_out' => 'datetime',
        'check_out' => 'datetime',
        'room_rate' => 'decimal:2',
        'advance_deposit' => 'decimal:2',
    ];

    public function owner()
    {
        return $this->belongsTo(Owner::class, 'owner_id');
    }

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    public function guest()
    {
        return $this->belongsTo(Guest::class, 'guest_id');
    }

    public function serviceRequests()
    {
        return $this->hasMany(HotelServiceRequest::class, 'booking_id');
    }

    public function roomOrders()
    {
        return $this->hasMany(Order::class, 'booking_id');
    }
}
