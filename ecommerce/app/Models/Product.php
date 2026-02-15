<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'sku',
        'short_description',
        'long_description',
        'price',
        'sale_price',
        'cost_price',
        'quantity',
        'stock_status',
        'weight',
        'dimensions',
        'images',
        'featured_image',
        'gallery',
        'attributes',
        'variations',
        'tags',
        'is_featured',
        'is_active',
        'is_digital',
        'download_link',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'created_by',
    ];

    protected $casts = [
        'images' => 'array',
        'gallery' => 'array',
        'attributes' => 'array',
        'variations' => 'array',
        'tags' => 'array',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'is_digital' => 'boolean',
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'weight' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function getFinalPriceAttribute()
    {
        return $this->sale_price ?? $this->price;
    }

    public function getDiscountPercentageAttribute()
    {
        if ($this->sale_price && $this->price > 0) {
            return round((($this->price - $this->sale_price) / $this->price) * 100);
        }
        return 0;
    }

    public function getAverageRatingAttribute()
    {
        return $this->reviews()->avg('rating') ?? 0;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('quantity', '>', 0);
    }

    /**
     * Get the product's main image.
     */
    public function getImageAttribute()
    {
        return $this->featured_image ?? ($this->images[0] ?? null);
    }

    /**
     * Get reviews count.
     */
    public function getReviewsCountAttribute()
    {
        return $this->reviews()->count();
    }

    /**
     * Get stock quantity.
     */
    public function getStockAttribute()
    {
        return $this->quantity ?? 0;
    }

    /**
     * Get discount percent.
     */
    public function getDiscountPercentAttribute()
    {
        if ($this->sale_price && $this->price > 0) {
            return round((($this->price - $this->sale_price) / $this->price) * 100);
        }
        return 0;
    }

    /**
     * Get sale price or regular price.
     */
    public function getSalePriceAttribute($value)
    {
        return $value;
    }
}
