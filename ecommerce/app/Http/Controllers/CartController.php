<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Cart;
use App\Models\Setting;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    /**
     * Display the cart.
     */
    public function index()
    {
        $cart = $this->getCart();
        return view('themes.general.cart.index', compact('cart'));
    }

    /**
     * Add item to cart.
     */
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'integer|min:1|max:10',
            'color_id' => 'nullable|exists:colors,id',
            'attributes' => 'nullable|array',
        ]);

        $product = Product::findOrFail($request->product_id);
        $quantity = $request->quantity ?? 1;

        // Check stock - consider color-specific stock if applicable
        $availableStock = $product->quantity;
        if ($request->color_id) {
            $colorPivot = $product->colors()->where('color_id', $request->color_id)->first();
            if ($colorPivot && $colorPivot->pivot->quantity !== null) {
                $availableStock = $colorPivot->pivot->quantity;
            }
        }

        if ($availableStock < $quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient stock available.'
            ], 400);
        }

        // Prepare variant data
        $variantData = [];
        
        // Add color info
        if ($request->color_id) {
            $color = \App\Models\Color::find($request->color_id);
            if ($color) {
                $variantData['color_id'] = $color->id;
                $variantData['color_name'] = $color->name;
                $variantData['color_hex'] = $color->hex_code;
                
                // Check for color-specific price adjustment
                $colorPivot = $product->colors()->where('color_id', $request->color_id)->first();
                if ($colorPivot && $colorPivot->pivot->price_adjustment) {
                    $variantData['price_adjustment'] = $colorPivot->pivot->price_adjustment;
                }
                
                // Check for color-specific image
                if ($colorPivot && $colorPivot->pivot->image) {
                    $variantData['image'] = $colorPivot->pivot->image;
                }
            }
        }
        
        // Add attribute info
        if ($request->attributes && is_array($request->attributes)) {
            $variantData['attributes'] = [];
            foreach ($request->attributes as $key => $valueId) {
                $attributeValue = \App\Models\AttributeValue::with('attribute')->find($valueId);
                if ($attributeValue) {
                    $variantData['attributes'][] = [
                        'attribute_id' => $attributeValue->attribute_id,
                        'attribute_name' => $attributeValue->attribute->name ?? '',
                        'value_id' => $attributeValue->id,
                        'value' => $attributeValue->value,
                    ];
                }
            }
        }

        $cart = $this->getCart();
        $cart->addItem($product, $quantity, $variantData);
        
        // Save session to ensure cart_id is persisted
        session()->save();

        // Build items array for immediate response
        $items = [];
        if ($cart && $cart->items) {
            foreach ($cart->items as $index => $item) {
                $productId = $item['product_id'] ?? null;
                $productItem = Product::find($productId);
                if ($productItem) {
                    $price = $item['price'] ?? ($productItem->sale_price ?? $productItem->price);
                    
                    // Apply price adjustment if exists
                    if (isset($item['price_adjustment'])) {
                        $price += $item['price_adjustment'];
                    }
                    
                    $imagePath = $item['image'] ?? $productItem->featured_image ?? $productItem->image;
                    
                    // Build proper image URL
                    $imageUrl = null;
                    if ($imagePath) {
                        if (str_starts_with($imagePath, 'http')) {
                            $imageUrl = $imagePath;
                        } elseif (str_starts_with($imagePath, '/storage/')) {
                            $imageUrl = $imagePath;
                        } elseif (str_starts_with($imagePath, '/uploads/')) {
                            $imageUrl = asset($imagePath);
                        } else {
                            $imageUrl = asset('storage/' . $imagePath);
                        }
                    }
                    
                    // Build variant description
                    $variantDesc = [];
                    if (isset($item['color_name'])) {
                        $variantDesc[] = 'Color: ' . $item['color_name'];
                    }
                    if (isset($item['attributes']) && is_array($item['attributes'])) {
                        foreach ($item['attributes'] as $attr) {
                            $variantDesc[] = $attr['attribute_name'] . ': ' . $attr['value'];
                        }
                    }
                    
                    $items[] = [
                        'id' => $index,
                        'product_id' => $productId,
                        'name' => $item['name'] ?? $productItem->name,
                        'price' => $price,
                        'image' => $imageUrl,
                        'quantity' => $item['quantity'] ?? 1,
                        'variant' => implode(', ', $variantDesc),
                        'variant_data' => $item['variant_data'] ?? null,
                    ];
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Product added to cart!',
            'cart_count' => $cart->getItemCount(),
            'cart_id' => $cart->id,
            'items' => $items,
        ]);
    }

    /**
     * Update cart item quantity.
     */
    public function update(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1|max:10',
        ]);

        $cart = $this->getCart();
        $cart->updateItem($request->product_id, $request->quantity);

        return response()->json([
            'success' => true,
            'message' => 'Cart updated!',
            'cart_total' => $cart->getTotal()
        ]);
    }

    /**
     * Remove item from cart.
     */
    public function remove(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $cart = $this->getCart();
        $cart->removeItem($request->product_id);

        return response()->json([
            'success' => true,
            'message' => 'Product removed from cart!',
            'cart_count' => $cart->getItemCount()
        ]);
    }

    /**
     * Clear the cart.
     */
    public function clear()
    {
        Session::forget('cart');
        
        return response()->json([
            'success' => true,
            'message' => 'Cart cleared!'
        ]);
    }

    /**
     * Get cart item count.
     */
    public function count()
    {
        $cart = $this->getCart();
        return response()->json(['count' => $cart->getItemCount()]);
    }

    /**
     * Get cart items for API.
     */
    public function items(Request $request)
    {
        $cart = $this->getCart();
        $items = [];
        $subtotal = 0;
        
        if ($cart && $cart->items) {
            foreach ($cart->items as $index => $item) {
                $productId = $item['product_id'] ?? null;
                $product = Product::find($productId);
                if ($product) {
                    $price = $item['price'] ?? ($product->sale_price ?? $product->price);
                    $imagePath = $item['image'] ?? $product->featured_image ?? $product->image;
                    
                    // Build proper image URL
                    $imageUrl = null;
                    if ($imagePath) {
                        if (str_starts_with($imagePath, 'http')) {
                            $imageUrl = $imagePath;
                        } elseif (str_starts_with($imagePath, '/storage/')) {
                            $imageUrl = $imagePath;
                        } elseif (str_starts_with($imagePath, '/uploads/')) {
                            $imageUrl = asset($imagePath);
                        } else {
                            $imageUrl = asset('storage/' . $imagePath);
                        }
                    }
                    
                    $items[] = [
                        'id' => $index,
                        'product_id' => $productId,
                        'name' => $item['name'] ?? $product->name,
                        'price' => $price,
                        'image' => $imageUrl,
                        'quantity' => $item['quantity'] ?? 1,
                        'subtotal' => $price * ($item['quantity'] ?? 1),
                    ];
                    $subtotal += $price * ($item['quantity'] ?? 1);
                }
            }
        }
        
        // Calculate delivery cost using admin shipping settings
        $freeShippingEnabled = Setting::get('free_shipping_enabled', '0') === '1';
        $freeShippingMinAmount = (float) Setting::get('free_shipping_min_amount', 0);
        $defaultShippingCost = (float) Setting::get('default_shipping_cost', 0);
        
        // Check if free shipping applies
        if ($freeShippingEnabled && $freeShippingMinAmount > 0 && $subtotal >= $freeShippingMinAmount) {
            $delivery = 0;
        } else {
            $delivery = $defaultShippingCost;
        }
        
        return response()->json([
            'items' => $items,
            'subtotal' => number_format($subtotal, 2),
            'delivery' => number_format($delivery, 2),
            'total' => number_format($subtotal + $delivery, 2),
            'cart_id' => $cart->id,
            'free_shipping_enabled' => $freeShippingEnabled,
            'free_shipping_min_amount' => $freeShippingMinAmount,
            'free_shipping_remaining' => $freeShippingEnabled && $freeShippingMinAmount > 0 
                ? max(0, $freeShippingMinAmount - $subtotal) 
                : 0,
        ]);
    }

    /**
     * Get or create cart instance.
     */
    private function getCart()
    {
        if (auth()->check()) {
            return Cart::firstOrCreate(['user_id' => auth()->id()]);
        }
        
        // For non-authenticated users, try to get cart from session
        $cartId = session()->get('cart_id');
        
        if ($cartId) {
            $cart = Cart::find($cartId);
            if ($cart) {
                return $cart;
            }
        }
        
        // Create new cart and store in session
        $cart = Cart::create([
            'items' => []
        ]);
        
        session()->put('cart_id', $cart->id);
        
        return $cart;
    }
}
