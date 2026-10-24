<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class MenuItem extends Model
{
    use HasUuids;

    protected $table = 'menu_items';

    protected $fillable = [
        'id',
        'owner_id',
        'category_id',
        'name',
        'description',
        'price',
        'image_url',
        'preparation_time',
        'is_veg',
        'is_available',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'preparation_time' => 'integer',
        'is_veg' => 'boolean',
        'is_available' => 'boolean',
    ];

    public function owner()
    {
        return $this->belongsTo(Owner::class, 'owner_id');
    }

    public function category()
    {
        return $this->belongsTo(MenuCategory::class, 'category_id');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'menu_item_id');
    }
}
