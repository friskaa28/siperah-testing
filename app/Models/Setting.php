<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'settings';
    
    protected $fillable = [
        'key',
        'value',
        'description',
    ];

    /**
     * Get setting value by key, with optional default.
     */
    public static function get($key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Helper to check if a boolean feature is enabled (returns true if value is "1" or "true")
     */
    public static function isEnabled($key)
    {
        $val = self::get($key, '0');
        return $val === '1' || $val === 'true' || $val === 1 || $val === true;
    }
}
