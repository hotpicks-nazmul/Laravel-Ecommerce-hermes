<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentGateway extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'logo',
        'credentials',
        'is_active',
        'test_mode',
        'sort_order',
    ];

    protected $casts = [
        'credentials' => 'array',
        'is_active' => 'boolean',
        'test_mode' => 'boolean',
    ];

    /**
     * Get active gateways.
     */
    public static function getActive()
    {
        return static::where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Get gateway by slug.
     */
    public static function findBySlug(string $slug): ?self
    {
        return static::where('slug', $slug)->first();
    }

    /**
     * Get credential value.
     */
    public function getCredential(string $key, $default = null)
    {
        return $this->credentials[$key] ?? $default;
    }

    /**
     * Scope for active gateways.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for ordered gateways.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    /**
     * Check if gateway is in test mode.
     */
    public function isTestMode(): bool
    {
        return $this->test_mode;
    }

    /**
     * Get gateway configuration.
     */
    public function getConfig(): array
    {
        return [
            'name' => $this->name,
            'slug' => $this->slug,
            'test_mode' => $this->test_mode,
            'credentials' => $this->credentials,
        ];
    }
}
