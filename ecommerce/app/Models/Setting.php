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
        'group',
        'type',
    ];

    public $timestamps = false;

    public static function get($key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    public static function set($key, $value, $group = null)
    {
        return static::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'group' => $group]
        );
    }

    /**
     * Get all settings by group
     */
    public static function getByGroup($group, $default = [])
    {
        $settings = static::where('group', $group)->pluck('value', 'key');
        return $settings->count() > 0 ? $settings->toArray() : $default;
    }

    /**
     * Get all file system settings
     */
    public static function getFileSystemSettings()
    {
        return static::getByGroup('file_system', [
            'max_upload_size' => 5120,
            'allowed_file_types' => 'jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,zip,mp4,mp3',
            'max_image_width' => 2000,
            'max_image_height' => 2000,
            'image_quality' => 85,
            'thumbnail_enabled' => '1',
            'thumbnail_width' => 150,
            'thumbnail_height' => 150,
            'watermark_enabled' => '0',
            'watermark_position' => 'bottom-right',
            'cache_driver' => 'file',
            'cache_ttl' => 3600,
            'enable_query_cache' => '0',
            'query_cache_ttl' => 300,
            'storage_disk' => 'public',
            'enable_cloud_storage' => '0',
            'cloud_driver' => 's3',
            'lazy_load_images' => '1',
            'optimize_images' => '1',
            'enable_static_cache' => '1',
        ]);
    }
}
