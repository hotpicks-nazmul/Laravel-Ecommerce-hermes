<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    /**
     * Get statistics for inhouse orders
     */
    protected function getInhouseStats()
    {
        return [
            'total' => Order::inhouse()->count(),
            'pending' => Order::inhouse()->where('status', 'pending')->count(),
            'processing' => Order::inhouse()->where('status', 'processing')->count(),
            'confirmed' => Order::inhouse()->where('status', 'confirmed')->count(),
            'shipped' => Order::inhouse()->where('status', 'shipped')->count(),
            'delivered' => Order::inhouse()->where('status', 'delivered')->count(),
            'cancelled' => Order::inhouse()->where('status', 'cancelled')->count(),
            'refunded' => Order::inhouse()->where('status', 'refunded')->count(),
        ];
    }

    /**
     * Get statistics for all orders
     */
    protected function getStats()
    {
        return [
            'total' => Order::count(),
            'pending' => Order::where('status', 'pending')->count(),
            'processing' => Order::where('status', 'processing')->count(),
            'confirmed' => Order::where('status', 'confirmed')->count(),
            'shipped' => Order::where('status', 'shipped')->count(),
            'delivered' => Order::where('status', 'delivered')->count(),
            'cancelled' => Order::where('status', 'cancelled')->count(),
            'refunded' => Order::where('status', 'refunded')->count(),
        ];
    }

    /**
     * Display a listing of orders.
     */
    public function index(Request $request)
    {
        $query = Order::with('user');

        // Search by order number, customer name, email, phone
        if ($request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('billing_first_name', 'like', "%{$search}%")
                  ->orWhere('billing_last_name', 'like', "%{$search}%")
                  ->orWhere('billing_email', 'like', "%{$search}%")
                  ->orWhere('billing_phone', 'like', "%{$search}%")
                  ->orWhere('shipping_first_name', 'like', "%{$search}%")
                  ->orWhere('shipping_last_name', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Filter by payment status
        if ($request->payment_status) {
            $query->where('payment_status', $request->payment_status);
        }

        // Filter by date range
        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Sorting
        $sort = $request->sort ?? 'created_at';
        $direction = $request->direction ?? 'desc';
        $query->orderBy($sort, $direction);

        // Pagination
        $perPage = $request->per_page ?? 25;
        $orders = $query->paginate($perPage);

        // Get stats
        $stats = $this->getStats();

        // AJAX response
        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.orders.partials.order-rows', compact('orders'))->render(),
                'pagination' => $orders->links()->toHtml(),
                'stats' => $stats
            ]);
        }

        return view('admin.orders.index', compact('orders', 'stats'));
    }

    /**
     * Display inhouse orders listing.
     */
    public function inHouse(Request $request)
    {
        $query = Order::inhouse()->with('user');

        // Search by order number, customer name, email, phone
        if ($request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('billing_first_name', 'like', "%{$search}%")
                  ->orWhere('billing_last_name', 'like', "%{$search}%")
                  ->orWhere('billing_email', 'like', "%{$search}%")
                  ->orWhere('billing_phone', 'like', "%{$search}%")
                  ->orWhere('shipping_first_name', 'like', "%{$search}%")
                  ->orWhere('shipping_last_name', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Filter by payment status
        if ($request->payment_status) {
            $query->where('payment_status', $request->payment_status);
        }

        // Filter by date range
        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Sorting
        $sort = $request->sort ?? 'created_at';
        $direction = $request->direction ?? 'desc';
        $query->orderBy($sort, $direction);

        // Pagination
        $perPage = $request->per_page ?? 25;
        $orders = $query->paginate($perPage);

        // Get stats
        $stats = $this->getInhouseStats();

        // AJAX response
        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.orders.partials.inhouse-order-rows', compact('orders'))->render(),
                'pagination' => $orders->links()->toHtml(),
                'stats' => $stats
            ]);
        }

        return view('admin.orders.inhouse.index', compact('orders', 'stats'));
    }

    /**
     * Show the form for creating a new inhouse order.
     */
    public function create()
    {
        $products = Product::where('status', 'active')
            ->select('id', 'name', 'sku', 'price', 'featured_image', 'current_stock')
            ->orderBy('name')
            ->get();
        
        $customers = User::where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'phone']);

        return view('admin.orders.inhouse.create', compact('products', 'customers'));
    }

    /**
     * Store a newly created inhouse order.
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:users,id',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'billing_first_name' => 'required|string|max:255',
            'billing_last_name' => 'required|string|max:255',
            'billing_email' => 'required|email|max:255',
            'billing_phone' => 'required|string|max:20',
            'billing_address' => 'required|string|max:500',
            'billing_city' => 'required|string|max:100',
            'billing_state' => 'required|string|max:100',
            'billing_postcode' => 'required|string|max:20',
            'billing_country' => 'required|string|max:100',
            'payment_method' => 'required|string|max:50',
            'payment_status' => 'required|in:pending,paid,failed',
        ]);

        // Generate order number
        $orderNumber = 'ORD-' . strtoupper(Str::random(8));

        // Calculate totals
        $subtotal = 0;
        $orderItems = [];

        foreach ($request->products as $item) {
            $product = Product::find($item['product_id']);
            $quantity = $item['quantity'];
            $price = $product->price;
            $itemTotal = $price * $quantity;
            $subtotal += $itemTotal;

            $orderItems[] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'variation' => $item['variation'] ?? null,
                'quantity' => $quantity,
                'price' => $price,
                'total' => $itemTotal,
            ];

            // Update stock
            $product->decrement('current_stock', $quantity);
        }

        // Shipping cost (free for now, can be configured)
        $shippingCost = 0;

        // Create order
        $order = Order::create([
            'order_number' => $orderNumber,
            'user_id' => $request->customer_id,
            'order_type' => 'inhouse',
            'status' => 'pending',
            'payment_status' => $request->payment_status,
            'payment_method' => $request->payment_method,
            'subtotal' => $subtotal,
            'shipping_cost' => $shippingCost,
            'total' => $subtotal + $shippingCost,
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
        foreach ($orderItems as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'product_name' => $item['product_name'],
                'variation' => $item['variation'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'total' => $item['total'],
            ]);
        }

        return redirect()->route('admin.orders.in-house.show', $order->id)
            ->with('success', 'Order created successfully.');
    }

    /**
     * Display inhouse order details.
     */
    public function inHouseShow(Order $order)
    {
        $order->load('user', 'items.product');
        return view('admin.orders.inhouse.show', compact('order'));
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order)
    {
        $order->load('user', 'items.product');
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Update the specified order.
     */
    public function update(Request $request, Order $order)
    {
        $request->validate([
            'shipping_name' => 'sometimes|string|max:255',
            'shipping_phone' => 'sometimes|string|max:20',
            'shipping_address' => 'sometimes|string|max:500',
            'notes' => 'nullable|string|max:500',
        ]);

        $order->update($request->all());

        return back()->with('success', 'Order updated successfully.');
    }

    /**
     * Update order status.
     */
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
        ]);

        $order->update(['status' => $request->status]);

        return back()->with('success', 'Order status updated.');
    }

    /**
     * Update payment status.
     */
    public function updatePaymentStatus(Request $request, Order $order)
    {
        $request->validate([
            'payment_status' => 'required|in:pending,paid,failed,refunded',
        ]);

        $order->update(['payment_status' => $request->payment_status]);

        return back()->with('success', 'Payment status updated.');
    }

    /**
     * Generate invoice.
     */
    public function invoice(Order $order)
    {
        $order->load('user', 'items.product');
        return view('admin.orders.invoice', compact('order'));
    }

    /**
     * Ship order.
     */
    public function ship(Request $request, Order $order)
    {
        $request->validate([
            'tracking_number' => 'required|string|max:100',
            'shipping_company' => 'required|string|max:100',
        ]);

        $order->update([
            'tracking_number' => $request->tracking_number,
            'shipping_company' => $request->shipping_company,
            'status' => 'shipped',
        ]);

        return back()->with('success', 'Order shipped successfully.');
    }

    /**
     * Get product details for AJAX.
     */
    public function getProduct(Request $request)
    {
        $product = Product::find($request->product_id);
        
        if ($product) {
            return response()->json([
                'success' => true,
                'product' => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'price' => $product->price,
                    'stock' => $product->current_stock,
                    'image' => $product->featured_image ? '/storage/' . $product->featured_image : null,
                ]
            ]);
        }
        
        return response()->json(['success' => false, 'message' => 'Product not found']);
    }

    /**
     * Get customer details for AJAX.
     */
    public function getCustomer(Request $request)
    {
        $customer = User::find($request->customer_id);
        
        if ($customer) {
            return response()->json([
                'success' => true,
                'customer' => [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'email' => $customer->email,
                    'phone' => $customer->phone,
                    'address' => $customer->address,
                    'city' => $customer->city,
                    'state' => $customer->state,
                    'country' => $customer->country,
                    'postal_code' => $customer->postal_code,
                ]
            ]);
        }
        
        return response()->json(['success' => false, 'message' => 'Customer not found']);
    }

    /**
     * Placeholder for seller orders.
     */
    public function seller()
    {
        return view('admin.orders.seller.index');
    }

    /**
     * Placeholder for pickup point orders.
     */
    public function pickupPoint()
    {
        return view('admin.orders.pickup-point.index');
    }
}
