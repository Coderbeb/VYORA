<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Guest extends Model
{
    use HasUuids;

    protected $table = 'guests';

    protected $fillable = [
        'id',
        'owner_id',
        'full_name',
        'phone',
        'email',
        'id_type',
        'id_number',
    ];

    public function owner()
    {
        return $this->belongsTo(Owner::class, 'owner_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'guest_id');
    }
}
