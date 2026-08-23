<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class HotelServiceRequest extends Model
{
    use HasUuids;

    protected $table = 'hotel_service_requests';

    protected $fillable = [
        'id',
        'owner_id',
        'room_id',
        'booking_id',
        'service_name',
        'category', // housekeeping, concierge, maintenance
        'status', // pending, in_progress, completed
        'notes',
        'completed_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    public function owner()
    {
        return $this->belongsTo(Owner::class, 'owner_id');
    }

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }
}
