<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Order extends Model
{
    use HasUuids;

    protected $table = 'orders';

    protected $fillable = [
        'id',
        'owner_id',
        'table_number',
        'room_id',
        'room_number',
        'hotel_id',
        'hotel_name',
        'order_type', // dine_in, room_service
        'booking_id',
        'billing_type', // folio, direct_restaurant
        'fulfilled_by_id',
        'total_amount',
        'estimated_time',
        'status', // pending, preparing, ready, completed, cancelled
        'notes',
        'cancellation_reason',
        'completed_at',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'estimated_time' => 'integer',
        'table_number' => 'integer',
        'completed_at' => 'datetime',
    ];

    public function owner()
    {
        return $this->belongsTo(Owner::class, 'owner_id');
    }

    public function hotel()
    {
        return $this->belongsTo(Owner::class, 'hotel_id');
    }

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    public function fulfilledBy()
    {
        return $this->belongsTo(Owner::class, 'fulfilled_by_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }
}
