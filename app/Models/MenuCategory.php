<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class MenuCategory extends Model
{
    use HasUuids;

    protected $table = 'menu_categories';

    protected $fillable = [
        'id',
        'owner_id',
        'name',
        'description',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function owner()
    {
        return $this->belongsTo(Owner::class, 'owner_id');
    }

    public function items()
    {
        return $this->hasMany(MenuItem::class, 'category_id')->orderBy('created_at', 'desc');
    }
}
