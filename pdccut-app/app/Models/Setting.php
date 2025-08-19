<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'description',
    ];

    /**
     * Get a setting value by key.
     */
    public static function getValue(string $key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Set a setting value.
     */
    public static function setValue(string $key, $value, string $type = 'string', string $group = 'general', string $description = null)
    {
        return static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'type' => $type,
                'group' => $group,
                'description' => $description,
            ]
        );
    }

    /**
     * Get all settings for a specific group.
     */
    public static function getGroup(string $group)
    {
        return static::where('group', $group)->get();
    }

    /**
     * Get logo setting.
     */
    public static function getLogo()
    {
        return static::getValue('logo', '/images/default-logo.png');
    }

    /**
     * Get icon setting.
     */
    public static function getIcon()
    {
        return static::getValue('icon', '/images/default-icon.png');
    }
}
