<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function getValue(string $key, ?string $default = null): ?string
    {
        $setting = static::where('key', $key)->first();

        return $setting ? $setting->value : $default;
    }

    public static function setValue(string $key, ?string $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    public static function getMany(array $keys): array
    {
        $settings = static::whereIn('key', $keys)->pluck('value', 'key')->toArray();
        $result = [];
        foreach ($keys as $key) {
            $result[$key] = $settings[$key] ?? null;
        }

        return $result;
    }
}
