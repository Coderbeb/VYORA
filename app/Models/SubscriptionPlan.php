<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class SubscriptionPlan extends Model
{
    use HasUuids;

    protected $table = 'subscription_plans';

    protected $fillable = [
        'id',
        'tier', // FREE, PRO, PREMIUM
        'business_type', // RESTAURANT, HOTEL, COMBO
        'price',
        'max_tables',
        'max_rooms',
        'features',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'max_tables' => 'integer',
        'max_rooms' => 'integer',
        'features' => 'array',
    ];

    public function owners()
    {
        return $this->hasMany(Owner::class, 'plan_id');
    }
}
