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

    public function addItem($product, $quantity = 1)
    {
        $items = $this->items ?? [];
        
        $found = false;
        foreach ($items as &$item) {
            if ($item['product_id'] == $product->id) {
                $item['quantity'] += $quantity;
                $found = true;
                break;
            }
        }

        if (!$found) {
            $items[] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => $product->final_price,
                'quantity' => $quantity,
                'image' => $product->image,
            ];
        }

        $this->items = $items;
        $this->save();
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
            return $item['price'] * $item['quantity'];
        }, $items));
    }

    public function getTotal()
    {
        return $this->getSubtotal();
    }
}
