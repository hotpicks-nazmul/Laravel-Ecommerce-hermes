<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Cart;
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
        ]);

        $product = Product::findOrFail($request->product_id);
        $quantity = $request->quantity ?? 1;

        if ($product->stock < $quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient stock available.'
            ], 400);
        }

        $cart = $this->getCart();
        $cart->addItem($product, $quantity);
        
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
                    $items[] = [
                        'id' => $index,
                        'product_id' => $productId,
                        'name' => $item['name'] ?? $productItem->name,
                        'price' => $price,
                        'image' => $item['image'] ?? $productItem->image,
                        'quantity' => $item['quantity'] ?? 1,
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
                    $items[] = [
                        'id' => $index,
                        'product_id' => $productId,
                        'name' => $item['name'] ?? $product->name,
                        'price' => $price,
                        'image' => $item['image'] ?? $product->image,
                        'quantity' => $item['quantity'] ?? 1,
                        'subtotal' => $price * ($item['quantity'] ?? 1),
                    ];
                    $subtotal += $price * ($item['quantity'] ?? 1);
                }
            }
        }
        
        $delivery = $subtotal > 500 ? 0 : 60;
        
        return response()->json([
            'items' => $items,
            'subtotal' => number_format($subtotal, 2),
            'delivery' => number_format($delivery, 2),
            'total' => number_format($subtotal + $delivery, 2),
            'cart_id' => $cart->id,
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
