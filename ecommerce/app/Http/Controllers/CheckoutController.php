<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Cart;
use App\Models\Setting;
use App\Services\PaymentService;

class CheckoutController extends Controller
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Display checkout page.
     */
    public function index()
    {
        $cart = $this->getCart();
        
        if (!$cart || $cart->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $paymentGateways = \App\Models\PaymentGateway::where('is_active', true)->get();
        $user = auth()->user();

        return view('themes.general.checkout.index', compact('cart', 'paymentGateways', 'user'));
    }

    /**
     * Process checkout.
     */
    public function process(Request $request)
    {
        $request->validate([
            'billing_first_name' => 'required|string|max:255',
            'billing_last_name' => 'required|string|max:255',
            'billing_email' => 'required|email|max:255',
            'billing_phone' => 'required|string|max:20',
            'billing_address' => 'required|string|max:500',
            'billing_city' => 'required|string|max:100',
            'billing_state' => 'required|string|max:100',
            'billing_postcode' => 'required|string|max:20',
            'billing_country' => 'required|string|max:100',
            'payment_method' => 'required|string|in:cod,bkash,sslcommerz,nagad,rocket',
        ]);

        $cart = $this->getCart();
        
        if (!$cart || $cart->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Your cart is empty.'], 400);
        }

        // Calculate shipping
        $freeShippingEnabled = Setting::get('free_shipping_enabled', '0') === '1';
        $freeShippingMinAmount = (float) Setting::get('free_shipping_min_amount', 0);
        $defaultShippingCost = (float) Setting::get('default_shipping_cost', 0);
        $subtotal = $cart->getSubtotal();
        $shipping = ($freeShippingEnabled && $freeShippingMinAmount > 0 && $subtotal >= $freeShippingMinAmount) ? 0 : $defaultShippingCost;
        $tax = $cart->getTax();
        $total = $subtotal + $shipping + $tax;

        // Build shipping name
        $shippingName = trim($request->billing_first_name . ' ' . $request->billing_last_name);

        // Create order
        $order = Order::create([
            'user_id' => Auth::id(),
            'order_number' => 'ORD-' . strtoupper(uniqid()),
            'billing_first_name' => $request->billing_first_name,
            'billing_last_name' => $request->billing_last_name,
            'billing_email' => $request->billing_email,
            'billing_phone' => $request->billing_phone,
            'billing_address' => $request->billing_address,
            'billing_city' => $request->billing_city,
            'billing_state' => $request->billing_state,
            'billing_postcode' => $request->billing_postcode,
            'billing_country' => $request->billing_country,
            'shipping_name' => $shippingName,
            'shipping_email' => $request->billing_email,
            'shipping_phone' => $request->billing_phone,
            'shipping_address' => $request->billing_address,
            'shipping_city' => $request->billing_city,
            'shipping_state' => $request->billing_state,
            'shipping_postcode' => $request->billing_postcode,
            'subtotal' => $subtotal,
            'shipping_cost' => $shipping,
            'tax' => $tax,
            'total' => $total,
            'payment_method' => $request->payment_method,
            'payment_status' => 'pending',
            'status' => 'pending',
        ]);

        // Create order items
        $cartItems = $cart->items ?? [];
        foreach ($cartItems as $item) {
            $product = Product::find($item['product_id']);
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'product_name' => $item['name'] ?? ($product ? $product->name : 'Unknown'),
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'total' => $item['price'] * $item['quantity'],
            ]);

            // Decrease product stock
            if ($product) {
                $product->decrement('quantity', $item['quantity']);
            }
        }

        // Clear cart
        $cart->clear();

        // Process payment
        if ($request->payment_method !== 'cod') {
            $paymentUrl = $this->paymentService->initiatePayment($order, $request->payment_method);
            
            if ($paymentUrl) {
                return response()->json(['success' => true, 'redirect' => $paymentUrl]);
            }
            
            return response()->json(['success' => false, 'message' => 'Payment initialization failed. Please try again.'], 400);
        }

        return response()->json(['success' => true, 'redirect' => route('checkout.success', $order->id)]);
    }

    /**
     * Get or create cart instance.
     */
    private function getCart()
    {
        if (Auth::check()) {
            return Cart::firstOrCreate(['user_id' => Auth::id()]);
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

    /**
     * Display success page.
     */
    public function success($orderId)
    {
        $order = Order::findOrFail($orderId);
        return view('themes.general.checkout.success', compact('order'));
    }

    /**
     * Handle cancelled checkout.
     */
    public function cancel()
    {
        return view('themes.general.checkout.cancel');
    }
}
