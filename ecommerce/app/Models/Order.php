<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_number',
        'order_type',
        'seller_id',
        'store_id',
        'pickup_point_id',
        'picked_up_at',
        'picked_up_by',
        'billing_first_name',
        'billing_last_name',
        'billing_email',
        'billing_phone',
        'billing_address',
        'billing_city',
        'billing_state',
        'billing_postcode',
        'billing_country',
        'shipping_first_name',
        'shipping_last_name',
        'shipping_email',
        'shipping_phone',
        'shipping_address',
        'shipping_city',
        'shipping_state',
        'shipping_postcode',
        'shipping_country',
        'subtotal',
        'shipping_cost',
        'shipping_method',
        'tax',
        'discount',
        'total',
        'payment_method',
        'payment_gateway',
        'payment_status',
        'transaction_id',
        'status',
        'tracking_number',
        'shipping_company',
        'notes',
        'coupon_code',
    ];

    protected $dates = [
        'picked_up_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'tax' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    /**
     * The "booting" method of the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = self::generateOrderNumber();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    /**
     * Get the pickup point for this order.
     */
    public function pickupPointLocation()
    {
        return $this->belongsTo(PickupPoint::class, 'pickup_point_id');
    }

    /**
     * Check if this is a pickup point order.
     */
    public function getIsPickupOrderAttribute()
    {
        return $this->order_type === 'pickup_point';
    }

    /**
     * Get pickup status badge class.
     */
    public function getPickupStatusBadgeClassAttribute()
    {
        if ($this->picked_up_at) {
            return 'bg-success';
        }
        return $this->status_badge_class;
    }

    /**
     * Get pickup status text.
     */
    public function getPickupStatusTextAttribute()
    {
        if ($this->picked_up_at) {
            return 'Picked Up';
        }
        return ucfirst($this->status);
    }

    /**
     * Get shipping method display name.
     */
    public function getShippingMethodNameAttribute()
    {
        return match($this->shipping_method) {
            'home_delivery' => 'Home Delivery',
            'local_pickup' => 'Local Pickup',
            default => 'Standard Delivery',
        };
    }

    /**
     * Get the billing full name.
     */
    public function getBillingFullNameAttribute()
    {
        return trim($this->billing_first_name . ' ' . $this->billing_last_name);
    }

    /**
     * Get the shipping full name.
     */
    public function getShippingFullNameAttribute()
    {
        if ($this->shipping_first_name || $this->shipping_last_name) {
            return trim($this->shipping_first_name . ' ' . $this->shipping_last_name);
        }
        return $this->billing_full_name;
    }

    /**
     * Get the status badge class for Bootstrap.
     */
    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'pending' => 'bg-warning',
            'processing' => 'bg-info',
            'confirmed' => 'bg-primary',
            'shipped' => 'bg-primary',
            'delivered' => 'bg-success',
            'cancelled' => 'bg-danger',
            'refunded' => 'bg-secondary',
            default => 'bg-secondary',
        };
    }

    /**
     * Get the status badge attribute (alias for compatibility).
     */
    public function getStatusBadgeAttribute()
    {
        return $this->status_badge_class;
    }

    /**
     * Get the payment status badge class for Bootstrap.
     */
    public function getPaymentStatusBadgeClassAttribute()
    {
        return match($this->payment_status) {
            'pending' => 'bg-warning',
            'paid' => 'bg-success',
            'failed' => 'bg-danger',
            'refunded' => 'bg-info',
            default => 'bg-secondary',
        };
    }

    /**
     * Get the payment status badge attribute (alias for compatibility).
     */
    public function getPaymentStatusBadgeAttribute()
    {
        return $this->payment_status_badge_class;
    }

    /**
     * Scope for inhouse orders
     */
    public function scopeInhouse($query)
    {
        return $query->where('order_type', 'inhouse');
    }

    /**
     * Scope for seller orders
     */
    public function scopeSeller($query)
    {
        return $query->where('order_type', 'seller');
    }

    /**
     * Scope for pickup point orders
     */
    public function scopePickupPoint($query)
    {
        return $query->where('order_type', 'pickup_point');
    }

    /**
     * Generate a unique order number.
     */
    public static function generateOrderNumber(): string
    {
        // Get order configuration settings
        $prefix = Setting::get('order_prefix', 'ORD');
        $suffix = Setting::get('order_suffix', '');
        $length = (int) Setting::get('order_number_length', 8);
        $format = Setting::get('order_number_format', 'random');
        
        if ($format === 'date') {
            // Date-based format: PREFIX-DATE-SEQUENCE
            $date = now()->format('Ymd');
            $lastOrder = self::withTrashed()
                ->whereDate('created_at', today())
                ->orderBy('id', 'desc')
                ->first();
            
            $sequence = $lastOrder ? (int) substr($lastOrder->order_number, -4) + 1 : 1;
            $sequence = str_pad($sequence, 4, '0', STR_PAD_LEFT);
            
            return "{$prefix}-{$date}-{$sequence}{$suffix}";
        } elseif ($format === 'sequential') {
            // Sequential format: PREFIX-SEQUENCE
            $lastOrder = self::withTrashed()
                ->orderBy('id', 'desc')
                ->first();
            
            $sequence = $lastOrder ? $lastOrder->id + 1 : 1;
            $sequence = str_pad($sequence, $length, '0', STR_PAD_LEFT);
            
            return "{$prefix}{$sequence}{$suffix}";
        } else {
            // Random format: PREFIX-RANDOM
            $random = '';
            $characters = '0123456789';
            $charactersLength = strlen($characters);
            
            for ($i = 0; $i < $length; $i++) {
                $random .= $characters[rand(0, $charactersLength - 1)];
            }
            
            return "{$prefix}{$random}{$suffix}";
        }
    }
}
