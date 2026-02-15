<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
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
        $cart = session('cart');
        
        if (!$cart || empty($cart['items'])) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        return view('themes.general.checkout.index', compact('cart'));
    }

    /**
     * Process checkout.
     */
    public function process(Request $request)
    {
        $request->validate([
            'shipping_name' => 'required|string|max:255',
            'shipping_email' => 'required|email|max:255',
            'shipping_phone' => 'required|string|max:20',
            'shipping_address' => 'required|string|max:500',
            'shipping_city' => 'required|string|max:100',
            'shipping_state' => 'required|string|max:100',
            'shipping_postcode' => 'required|string|max:20',
            'payment_method' => 'required|string|in:cod,bkash,sslcommerz,nagad,rocket',
        ]);

        $cart = session('cart');
        
        if (!$cart || empty($cart['items'])) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        // Create order
        $order = Order::create([
            'user_id' => auth()->id(),
            'order_number' => 'ORD-' . strtoupper(uniqid()),
            'shipping_name' => $request->shipping_name,
            'shipping_email' => $request->shipping_email,
            'shipping_phone' => $request->shipping_phone,
            'shipping_address' => $request->shipping_address,
            'shipping_city' => $request->shipping_city,
            'shipping_state' => $request->shipping_state,
            'shipping_postcode' => $request->shipping_postcode,
            'subtotal' => $cart['subtotal'],
            'shipping_cost' => $cart['shipping'] ?? 0,
            'tax' => $cart['tax'] ?? 0,
            'total' => $cart['total'],
            'payment_method' => $request->payment_method,
            'payment_status' => 'pending',
            'status' => 'pending',
        ]);

        // Create order items
        foreach ($cart['items'] as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'total' => $item['price'] * $item['quantity'],
            ]);

            // Decrease product stock
            Product::where('id', $item['product_id'])->decrement('stock', $item['quantity']);
        }

        // Process payment
        if ($request->payment_method !== 'cod') {
            $paymentUrl = $this->paymentService->initiatePayment($order, $request->payment_method);
            
            if ($paymentUrl) {
                return redirect($paymentUrl);
            }
            
            return back()->with('error', 'Payment initialization failed. Please try again.');
        }

        // Clear cart
        session()->forget('cart');

        return redirect()->route('checkout.success', $order->id);
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
