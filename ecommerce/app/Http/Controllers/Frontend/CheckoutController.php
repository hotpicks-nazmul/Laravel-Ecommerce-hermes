<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Coupon;
use App\Models\PaymentGateway;
use App\Models\Setting;
use App\Services\ThemeService;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    protected $theme;
    protected $paymentService;

    public function __construct(ThemeService $theme, PaymentService $paymentService)
    {
        $this->theme = $theme;
        $this->paymentService = $paymentService;
    }

    /**
     * Display checkout page.
     */
    public function index()
    {
        $cart = $this->getCart();

        if ($cart->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $paymentGateways = PaymentGateway::getActive();
        $user = Auth::user();
        $addresses = $user ? $user->addresses : collect();

        return $this->theme->view('checkout.index', compact('cart', 'paymentGateways', 'user', 'addresses'));
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
            'shipping_method' => 'nullable|string|in:home_delivery,local_pickup',
            'payment_method' => 'required|string',
            'terms' => 'required|accepted',
        ]);

        $cart = $this->getCart();

        if ($cart->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        // Get order configuration settings
        $minOrderAmount = (float) Setting::get('min_order_amount', 0);
        $maxOrderAmount = (float) Setting::get('max_order_amount', 0);
        $subtotal = $cart->subtotal;

        // Validate minimum order amount
        if ($minOrderAmount > 0 && $subtotal < $minOrderAmount) {
            return back()->with('error', 'Minimum order amount is ' . config('app.currency_symbol', '৳') . number_format($minOrderAmount, 2))->withInput();
        }

        // Validate maximum order amount
        if ($maxOrderAmount > 0 && $subtotal > $maxOrderAmount) {
            return back()->with('error', 'Maximum order amount is ' . config('app.currency_symbol', '৳') . number_format($maxOrderAmount, 2))->withInput();
        }

        DB::beginTransaction();
        try {
            // Calculate totals
            $subtotal = $cart->subtotal;
            $tax = $subtotal * (Setting::get('tax_rate', 0) / 100);
            $shippingMethod = $request->input('shipping_method', 'home_delivery');
            $shippingCost = $this->calculateShipping($subtotal, $shippingMethod, $request->billing_city, $request->billing_state);
            $discount = 0;

            // Apply coupon
            $coupon = null;
            if ($request->coupon_code) {
                $coupon = Coupon::findValidByCode($request->coupon_code);
                if ($coupon && $coupon->isApplicable($subtotal)) {
                    $discount = $coupon->calculateDiscount($subtotal);
                }
            }

            $total = $subtotal + $tax + $shippingCost - $discount;

            // Create order
            $order = Order::create([
                'user_id' => Auth::id(),
                'order_type' => 'inhouse',
                'status' => 'pending',
                'payment_status' => 'pending',
                'payment_method' => $request->payment_method,
                'shipping_method' => $shippingMethod,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'shipping_cost' => $shippingCost,
                'discount' => $discount,
                'coupon_code' => $coupon ? $coupon->code : null,
                'total' => $total,
                'billing_first_name' => $request->billing_first_name,
                'billing_last_name' => $request->billing_last_name,
                'billing_email' => $request->billing_email,
                'billing_phone' => $request->billing_phone,
                'billing_address' => $request->billing_address,
                'billing_city' => $request->billing_city,
                'billing_state' => $request->billing_state,
                'billing_postcode' => $request->billing_postcode,
                'billing_country' => $request->billing_country,
                'shipping_first_name' => $request->shipping_first_name ?? $request->billing_first_name,
                'shipping_last_name' => $request->shipping_last_name ?? $request->billing_last_name,
                'shipping_email' => $request->shipping_email ?? $request->billing_email,
                'shipping_phone' => $request->shipping_phone ?? $request->billing_phone,
                'shipping_address' => $request->shipping_address ?? $request->billing_address,
                'shipping_city' => $request->shipping_city ?? $request->billing_city,
                'shipping_state' => $request->shipping_state ?? $request->billing_state,
                'shipping_postcode' => $request->shipping_postcode ?? $request->billing_postcode,
                'shipping_country' => $request->shipping_country ?? $request->billing_country,
                'notes' => $request->notes,
            ]);

            // Create order items
            foreach ($cart->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name,
                    'variation' => $item->variation,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'total' => $item->total,
                ]);

                // Update product stock
                $item->product->decrement('quantity', $item->quantity);
            }

            // Increment coupon usage
            if ($coupon) {
                $coupon->incrementUsage();
            }

            // Clear cart
            $cart->clear();

            // Process payment
            $gateway = PaymentGateway::findBySlug($request->payment_method);
            if ($gateway) {
                $this->paymentService->setGateway($gateway);
                $result = $this->paymentService->initialize($order);

                if ($result['success']) {
                    $order->update(['payment_gateway' => $gateway->slug]);
                    
                    DB::commit();

                    if (isset($result['redirect_url'])) {
                        return redirect($result['redirect_url']);
                    }

                    return redirect()->route('checkout.success', $order->id);
                }

                DB::rollBack();
                return back()->with('error', $result['message'] ?? 'Payment initialization failed.');
            }

            // COD or no payment gateway
            DB::commit();
            return redirect()->route('checkout.success', $order->id);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'An error occurred. Please try again.');
        }
    }

    /**
     * Display success page.
     */
    public function success($orderId)
    {
        $order = Order::with('items.product')->findOrFail($orderId);

        return $this->theme->view('checkout.success', compact('order'));
    }

    /**
     * Cancel order.
     */
    public function cancel($orderId)
    {
        $order = Order::findOrFail($orderId);

        if ($order->canBeCancelled()) {
            $order->update(['status' => 'cancelled']);
            return redirect()->route('home')->with('success', 'Order cancelled successfully.');
        }

        return redirect()->route('home')->with('error', 'Order cannot be cancelled.');
    }

    /**
     * Calculate shipping cost.
     */
    protected function calculateShipping($subtotal, $shippingMethod = null, $city = null, $state = null)
    {
        // Get shipping settings from admin panel
        $freeShippingEnabled = Setting::get('free_shipping_enabled', '0') === '1';
        $freeShippingMinAmount = (float) Setting::get('free_shipping_min_amount', 0);
        $defaultShippingCost = (float) Setting::get('default_shipping_cost', 0);
        $shippingCalculationType = Setting::get('shipping_calculation_type', 'flat');
        $localPickupEnabled = Setting::get('local_pickup_enabled', '0') === '1';
        $localPickupCost = (float) Setting::get('local_pickup_cost', 0);

        // Check for free shipping
        if ($freeShippingEnabled && $freeShippingMinAmount > 0 && $subtotal >= $freeShippingMinAmount) {
            return 0;
        }

        // Handle local pickup
        if ($shippingMethod === 'local_pickup') {
            return $localPickupEnabled ? $localPickupCost : 0;
        }

        // Handle delivery zone-based shipping
        if ($shippingCalculationType === 'location' && !empty($city)) {
            // Try to find matching delivery zone
            $zone = \App\Models\DeliveryZone::active()
                ->where(function($query) use ($city, $state) {
                    $query->where('city', 'like', "%{$city}%")
                        ->orWhere('state', 'like', "%{$state}%")
                        ->orWhereNull('city');
                })
                ->first();

            if ($zone) {
                // Check if free shipping threshold is met for this zone
                if ($zone->free_shipping_enabled && $zone->free_shipping_threshold > 0 && $subtotal >= $zone->free_shipping_threshold) {
                    return 0;
                }

                // Return zone shipping cost
                return (float) $zone->shipping_cost;
            }
        }

        // Default to flat rate
        return $defaultShippingCost;
    }

    /**
     * Get available shipping options for the cart.
     */
    public function getShippingOptions(Request $request)
    {
        $city = $request->input('city');
        $state = $request->input('state');
        $subtotal = (float) $request->input('subtotal', 0);

        // Get settings
        $freeShippingEnabled = Setting::get('free_shipping_enabled', '0') === '1';
        $freeShippingMinAmount = (float) Setting::get('free_shipping_min_amount', 0);
        $defaultShippingCost = (float) Setting::get('default_shipping_cost', 0);
        $shippingCalculationType = Setting::get('shipping_calculation_type', 'flat');
        $localPickupEnabled = Setting::get('local_pickup_enabled', '0') === '1';
        $localPickupCost = (float) Setting::get('local_pickup_cost', 0);

        $options = [];

        // Add home delivery option
        $homeDeliveryCost = $defaultShippingCost;
        $homeDeliveryLabel = 'Home Delivery';

        // If location-based, try to find matching zone
        if ($shippingCalculationType === 'location' && !empty($city)) {
            $zone = \App\Models\DeliveryZone::active()
                ->where(function($query) use ($city, $state) {
                    $query->where('city', 'like', "%{$city}%")
                        ->orWhere('state', 'like', "%{$state}%")
                        ->orWhereNull('city');
                })
                ->first();

            if ($zone) {
                if ($zone->free_shipping_enabled && $zone->free_shipping_threshold > 0 && $subtotal >= $zone->free_shipping_threshold) {
                    $homeDeliveryCost = 0;
                    $homeDeliveryLabel = 'Home Delivery (Free)';
                } else {
                    $homeDeliveryCost = (float) $zone->shipping_cost;
                    $homeDeliveryLabel = 'Home Delivery - ' . $zone->name;
                }
            }
        }

        // Check global free shipping
        if ($freeShippingEnabled && $freeShippingMinAmount > 0 && $subtotal >= $freeShippingMinAmount) {
            $homeDeliveryCost = 0;
            $homeDeliveryLabel = 'Free Shipping';
        }

        $options[] = [
            'id' => 'home_delivery',
            'name' => $homeDeliveryLabel,
            'cost' => $homeDeliveryCost,
            'estimated_days' => '3-5 business days',
        ];

        // Add local pickup option if enabled
        if ($localPickupEnabled) {
            $options[] = [
                'id' => 'local_pickup',
                'name' => 'Local Pickup',
                'cost' => $localPickupCost,
                'estimated_days' => 'Same day',
            ];
        }

        return response()->json([
            'success' => true,
            'options' => $options,
        ]);
    }

    /**
     * Get cart.
     */
    protected function getCart()
    {
        if (Auth::check()) {
            return Cart::findOrCreateForUser(Auth::id());
        }

        $sessionId = session()->get('cart_session_id');
        return Cart::findOrCreateForUser(null, $sessionId);
    }
}
