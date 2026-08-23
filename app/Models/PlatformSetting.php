<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PlatformSetting extends Model
{
    protected $table = 'platform_settings';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'key',
        'value',
        'group',
        'type',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();
        if (!$setting) {
            return $default;
        }

        if ($setting->type === 'json') {
            return json_decode($setting->value, true) ?? $default;
        }
        if ($setting->type === 'boolean') {
            return filter_var($setting->value, FILTER_VALIDATE_BOOLEAN);
        }
        if ($setting->type === 'number') {
            return is_numeric($setting->value) ? $setting->value + 0 : $default;
        }

        return $setting->value;
    }

    public static function set(string $key, mixed $value, string $group = 'general', string $type = 'string'): static
    {
        $val = is_array($value) || is_object($value) ? json_encode($value) : (string) $value;
        if (is_array($value)) {
            $type = 'json';
        }

        return static::updateOrCreate(
            ['key' => $key],
            ['value' => $val, 'group' => $group, 'type' => $type]
        );
    }
}
