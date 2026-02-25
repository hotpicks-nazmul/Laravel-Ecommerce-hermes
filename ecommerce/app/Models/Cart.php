<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'items',
    ];

    protected $casts = [
        'items' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function addItem($product, $quantity = 1, $variantData = [])
    {
        $items = $this->items ?? [];
        
        // Create a unique key for this product variant
        $variantKey = $this->generateVariantKey($product->id, $variantData);
        
        $found = false;
        foreach ($items as &$item) {
            // Check if same product with same variant exists
            $itemVariantKey = $this->generateVariantKey($item['product_id'], $item['variant_data'] ?? []);
            if ($itemVariantKey === $variantKey) {
                $item['quantity'] += $quantity;
                $found = true;
                break;
            }
        }

        if (!$found) {
            $newItem = [
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => $product->final_price,
                'quantity' => $quantity,
                'image' => $product->featured_image ?? $product->image,
                'variant_data' => $variantData,
            ];
            
            // Add color info to item root for easy access
            if (isset($variantData['color_id'])) {
                $newItem['color_id'] = $variantData['color_id'];
                $newItem['color_name'] = $variantData['color_name'] ?? '';
                $newItem['color_hex'] = $variantData['color_hex'] ?? '';
            }
            
            // Add price adjustment if exists
            if (isset($variantData['price_adjustment'])) {
                $newItem['price_adjustment'] = $variantData['price_adjustment'];
            }
            
            // Add color-specific image if exists
            if (isset($variantData['image'])) {
                $newItem['image'] = $variantData['image'];
            }
            
            // Add attributes info
            if (isset($variantData['attributes'])) {
                $newItem['attributes'] = $variantData['attributes'];
            }
            
            $items[] = $newItem;
        }

        $this->items = $items;
        $this->save();
    }
    
    /**
     * Generate a unique key for a product variant.
     */
    protected function generateVariantKey($productId, $variantData)
    {
        $key = 'product_' . $productId;
        
        if (isset($variantData['color_id'])) {
            $key .= '_color_' . $variantData['color_id'];
        }
        
        if (isset($variantData['attributes']) && is_array($variantData['attributes'])) {
            $attrIds = array_column($variantData['attributes'], 'value_id');
            sort($attrIds);
            $key .= '_attrs_' . implode('_', $attrIds);
        }
        
        return $key;
    }

    public function updateItem($productId, $quantity)
    {
        $items = $this->items ?? [];
        
        foreach ($items as &$item) {
            if ($item['product_id'] == $productId) {
                $item['quantity'] = $quantity;
                break;
            }
        }

        $this->items = $items;
        $this->save();
    }

    public function removeItem($productId)
    {
        $items = $this->items ?? [];
        
        $items = array_filter($items, function ($item) use ($productId) {
            return $item['product_id'] != $productId;
        });

        $this->items = array_values($items);
        $this->save();
    }

    public function getItemCount()
    {
        $items = $this->items ?? [];
        return array_sum(array_column($items, 'quantity'));
    }

    public function getSubtotal()
    {
        $items = $this->items ?? [];
        return array_sum(array_map(function ($item) {
            $price = $item['price'];
            // Add price adjustment if exists
            if (isset($item['price_adjustment'])) {
                $price += $item['price_adjustment'];
            }
            return $price * $item['quantity'];
        }, $items));
    }

    public function getTotal()
    {
        return $this->getSubtotal();
    }
}
