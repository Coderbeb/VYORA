<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Table extends Model
{
    use HasUuids;

    protected $table = 'tables';

    protected $fillable = [
        'id',
        'owner_id',
        'table_number',
        'qr_code_data',
        'qr_code_image_url',
        'is_active',
    ];

    protected $casts = [
        'table_number' => 'integer',
        'is_active' => 'boolean',
    ];

    public function owner()
    {
        return $this->belongsTo(Owner::class, 'owner_id');
    }
}
