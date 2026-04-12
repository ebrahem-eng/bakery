<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    /**
     * Get a setting value by key, with language support.
     */
    public static function get(string $key, $default = null): ?string
    {
        $locale = app()->getLocale();
        
        // Try current locale e.g. hero_title_ar
        $setting = static::where('key', $key . '_' . $locale)->first();
        if ($setting) return $setting->value;
        
        // Fallback to English e.g. hero_title_en
        if ($locale !== 'en') {
            $setting = static::where('key', $key . '_en')->first();
            if ($setting) return $setting->value;
        }

        // Final fallback to exact key
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Set a setting value by key (create or update).
     */
    public static function set(string $key, ?string $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
    }
}
