<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class RoomCategory extends Model
{
    use HasUuids;

    protected $table = 'room_categories';

    protected $fillable = [
        'id',
        'owner_id',
        'name',
        'base_price',
        'max_occupancy',
        'amenities',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'max_occupancy' => 'integer',
        'amenities' => 'array',
    ];

    public function owner()
    {
        return $this->belongsTo(Owner::class, 'owner_id');
    }

    public function rooms()
    {
        return $this->hasMany(Room::class, 'category_id');
    }
}
