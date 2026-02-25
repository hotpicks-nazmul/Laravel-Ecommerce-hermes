<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Color extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'code',
        'hex_code',
        'image',
        'description',
        'display_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($color) {
            if (empty($color->slug)) {
                $color->slug = Str::slug($color->name);
            }
            if (empty($color->code)) {
                $color->code = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $color->name), 0, 3));
            }
        });

        static::updating(function ($color) {
            if ($color->isDirty('name') && empty($color->slug)) {
                $color->slug = Str::slug($color->name);
            }
        });
    }

    /**
     * Get products that have this color.
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_colors')
            ->withPivot(['image', 'quantity', 'price_adjustment', 'sku'])
            ->withTimestamps();
    }

    /**
     * Scope for active colors.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for ordered display.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order')->orderBy('name');
    }

    /**
     * Get the count of products using this color.
     */
    public function getProductsCountAttribute()
    {
        return $this->products()->count();
    }

    /**
     * Get the display badge with color preview.
     */
    public function getColorBadgeAttribute()
    {
        return '<span style="background-color: ' . $this->hex_code . '; width: 20px; height: 20px; display: inline-block; border-radius: 50%; border: 1px solid #ddd; vertical-align: middle;"></span> ' . $this->name;
    }

    /**
     * Get contrast text color (black or white) based on hex code.
     */
    public function getContrastColorAttribute()
    {
        $hex = ltrim($this->hex_code, '#');
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        $luminance = ($r * 0.299 + $g * 0.587 + $b * 0.114) / 255;
        return $luminance > 0.5 ? '#000000' : '#FFFFFF';
    }
}
