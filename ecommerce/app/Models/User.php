<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'avatar',
        'status',
        'provider',
        'provider_id',
        // Seller specific fields
        'shop_name',
        'shop_description',
        'shop_logo',
        'shop_banner',
        'business_registration_number',
        'tax_id',
        'bank_name',
        'bank_account_number',
        'bank_account_name',
        'bank_routing_code',
        'commission_rate',
        'wallet_balance',
        'pending_balance',
        'verification_status',
        'verified_at',
        'verification_notes',
        'contact_person_name',
        'contact_person_phone',
        'contact_person_email',
        'return_address',
        'seller_type',
        'company_name',
        'company_address',
        // Staff specific fields
        'designation',
        'permissions',
        'warehouse_id',
        'is_super_admin',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user has a specific role
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    /**
     * Check if user has any of the given roles
     */
    public function hasAnyRole(array $roles): bool
    {
        return in_array($this->role, $roles);
    }

    /**
     * Check if user is vendor
     */
    public function isVendor(): bool
    {
        return $this->role === 'vendor';
    }

    /**
     * Check if user is customer
     */
    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }

    /**
     * Get user's orders
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get user's cart
     */
    public function cart()
    {
        return $this->hasOne(Cart::class);
    }

    /**
     * Get user's wishlist
     */
    public function wishlist()
    {
        return $this->hasMany(Wishlist::class);
    }

    /**
     * Get user's reviews
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Get user's addresses
     */
    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    /**
     * Get user's default address
     */
    public function defaultAddress()
    {
        return $this->addresses()->where('is_default', true)->first();
    }

    /**
     * Get user's chats
     */
    public function chats()
    {
        return $this->hasMany(Chat::class);
    }

    /**
     * Get products created by this user
     */
    public function createdProducts()
    {
        return $this->hasMany(Product::class, 'created_by');
    }

    /**
     * Get products where this user is the seller
     */
    public function products()
    {
        return $this->hasMany(Product::class, 'seller_id');
    }

    /**
     * Get the customer group this user belongs to
     */
    public function customerGroup()
    {
        return $this->belongsTo(CustomerGroup::class, 'customer_group_id');
    }

    // ==================== Seller Methods ====================

    /**
     * Check if user is a seller (vendor)
     */
    public function isSeller(): bool
    {
        return $this->role === 'vendor';
    }

    /**
     * Get the seller's shop name
     */
    public function getShopNameAttribute(): string
    {
        return $this->shop_name ?? $this->name;
    }

    /**
     * Get shop logo URL
     */
    public function getShopLogoUrlAttribute(): ?string
    {
        return $this->shop_logo ? asset('uploads/shop_logos/' . $this->shop_logo) : null;
    }

    /**
     * Get shop banner URL
     */
    public function getShopBannerUrlAttribute(): ?string
    {
        return $this->shop_banner ? asset('uploads/shop_banners/' . $this->shop_banner) : null;
    }

    /**
     * Check if seller is verified
     */
    public function isVerified(): bool
    {
        return $this->verification_status === 'verified';
    }

    /**
     * Check if seller verification is pending
     */
    public function isVerificationPending(): bool
    {
        return $this->verification_status === 'pending';
    }

    /**
     * Check if seller verification is rejected
     */
    public function isVerificationRejected(): bool
    {
        return $this->verification_status === 'rejected';
    }

    /**
     * Get seller status (active/inactive)
     */
    public function isActiveSeller(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Get total products count
     */
    public function getProductsCountAttribute(): int
    {
        return $this->products()->count();
    }

    /**
     * Get total orders count
     */
    public function getOrdersCountAttribute(): int
    {
        return Order::whereHas('items', function ($query) {
            $query->whereHas('product', function ($q) {
                $q->where('seller_id', $this->id);
            });
        })->count();
    }

    /**
     * Get total sales amount
     */
    public function getTotalSalesAttribute(): float
    {
        return Order::whereHas('items', function ($query) {
            $query->whereHas('product', function ($q) {
                $q->where('seller_id', $this->id);
            });
        })->where('payment_status', 'paid')
          ->whereIn('status', ['delivered', 'shipped', 'confirmed'])
          ->sum('grand_total');
    }

    /**
     * Scope to get only sellers
     */
    public function scopeSellers($query)
    {
        return $query->where('role', 'vendor');
    }

    /**
     * Scope to get verified sellers
     */
    public function scopeVerifiedSellers($query)
    {
        return $query->where('role', 'vendor')->where('verification_status', 'verified');
    }

    /**
     * Scope to get only staff members
     */
    public function scopeStaff($query)
    {
        return $query->where('role', 'staff');
    }

    /**
     * Check if user is staff
     */
    public function isStaff(): bool
    {
        return $this->role === 'staff';
    }

    /**
     * Get the warehouse associated with this staff member
     */
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * Scope to get sellers by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('role', 'vendor')->where('status', $status);
    }

    /**
     * Scope to get sellers by verification status
     */
    public function scopeByVerificationStatus($query, $verificationStatus)
    {
        return $query->where('role', 'vendor')->where('verification_status', $verificationStatus);
    }
}
