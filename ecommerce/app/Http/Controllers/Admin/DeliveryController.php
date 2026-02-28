<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryPartner;
use App\Models\Carrier;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DeliveryController extends Controller
{
    /**
     * Display the Delivery Dashboard.
     */
    public function index(Request $request)
    {
        // Date range filter
        $dateRange = $request->get('date_range', 'today');
        $startDate = $this->getStartDate($dateRange);
        $endDate = Carbon::now()->endOfDay();

        // Get order statistics
        $stats = $this->getDeliveryStats($startDate, $endDate);
        
        // Get recent orders pending shipment
        $pendingShipments = Order::whereIn('status', ['pending', 'confirmed'])
            ->whereIn('payment_status', ['paid'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get orders in transit
        $inTransit = Order::where('status', 'shipped')
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        // Get recently delivered
        $recentlyDelivered = Order::where('status', 'delivered')
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        // Get failed/returned deliveries
        $failedDeliveries = Order::whereIn('status', ['cancelled', 'refunded'])
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        // Delivery performance metrics
        $performance = $this->getDeliveryPerformance($startDate, $endDate);

        return view('admin.delivery.dashboard', compact(
            'stats',
            'pendingShipments',
            'inTransit',
            'recentlyDelivered',
            'failedDeliveries',
            'performance',
            'dateRange'
        ));
    }

    /**
     * Get delivery statistics based on date range.
     */
    private function getDeliveryStats($startDate, $endDate)
    {
        // Total orders in period
        $totalOrders = Order::whereBetween('created_at', [$startDate, $endDate])->count();

        // Pending shipments
        $pendingShipments = Order::whereIn('status', ['pending', 'confirmed'])
            ->whereIn('payment_status', ['paid'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        // Shipped/In Transit
        $inTransit = Order::where('status', 'shipped')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        // Delivered
        $delivered = Order::where('status', 'delivered')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        // Cancelled/Refunded
        $failed = Order::whereIn('status', ['cancelled', 'refunded'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        // Total shipping revenue
        $shippingRevenue = Order::where('status', 'delivered')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('shipping_cost');

        // Average delivery time (in days)
        $avgDeliveryTime = $this->getAverageDeliveryTime($startDate, $endDate);

        // Success rate
        $successRate = $totalOrders > 0 ? round(($delivered / $totalOrders) * 100, 1) : 0;

        return [
            'total_orders' => $totalOrders,
            'pending_shipments' => $pendingShipments,
            'in_transit' => $inTransit,
            'delivered' => $delivered,
            'failed' => $failed,
            'shipping_revenue' => $shippingRevenue,
            'avg_delivery_time' => $avgDeliveryTime,
            'success_rate' => $successRate,
        ];
    }

    /**
     * Calculate average delivery time in days.
     */
    private function getAverageDeliveryTime($startDate, $endDate)
    {
        $deliveredOrders = Order::where('status', 'delivered')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        if ($deliveredOrders->isEmpty()) {
            return 0;
        }

        $totalDays = 0;
        foreach ($deliveredOrders as $order) {
            $created = Carbon::parse($order->created_at);
            $updated = Carbon::parse($order->updated_at);
            $totalDays += $created->diffInDays($updated);
        }

        return round($totalDays / $deliveredOrders->count(), 1);
    }

    /**
     * Get delivery performance metrics.
     */
    private function getDeliveryPerformance($startDate, $endDate)
    {
        // Daily delivery data for chart
        $dailyDeliveries = Order::where('status', 'delivered')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Status breakdown
        $statusBreakdown = Order::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Payment status breakdown
        $paymentBreakdown = Order::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('payment_status, COUNT(*) as count')
            ->groupBy('payment_status')
            ->pluck('count', 'payment_status')
            ->toArray();

        return [
            'daily_deliveries' => $dailyDeliveries,
            'status_breakdown' => $statusBreakdown,
            'payment_breakdown' => $paymentBreakdown,
        ];
    }

    /**
     * Get start date based on date range filter.
     */
    private function getStartDate($dateRange)
    {
        return match($dateRange) {
            'today' => Carbon::today(),
            'yesterday' => Carbon::yesterday(),
            'this_week' => Carbon::now()->startOfWeek(),
            'this_month' => Carbon::now()->startOfMonth(),
            'last_7_days' => Carbon::now()->subDays(7),
            'last_30_days' => Carbon::now()->subDays(30),
            'custom' => Carbon::now()->startOfMonth(), // Default for custom
            default => Carbon::today(),
        };
    }

    /**
     * Quick ship order action.
     */
    public function quickShip(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'tracking_number' => 'nullable|string|max:100',
        ]);

        $order = Order::findOrFail($request->order_id);
        
        if (!in_array($order->payment_status, ['paid'])) {
            return response()->json([
                'success' => false,
                'message' => 'Order must be paid before shipping.'
            ], 400);
        }

        $order->status = 'shipped';
        $order->tracking_number = $request->tracking_number ?? $order->tracking_number;
        $order->save();

        return response()->json([
            'success' => true,
            'message' => 'Order marked as shipped.'
        ]);
    }

    /**
     * Mark order as delivered.
     */
    public function markDelivered(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
        ]);

        $order = Order::findOrFail($request->order_id);
        $order->status = 'delivered';
        $order->save();

        return response()->json([
            'success' => true,
            'message' => 'Order marked as delivered.'
        ]);
    }

    /**
     * Get orders for delivery table (AJAX).
     */
    public function getOrders(Request $request)
    {
        $status = $request->get('status', 'all');
        $search = $request->get('search', '');
        
        $query = Order::query();

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('shipping_first_name', 'like', "%{$search}%")
                  ->orWhere('shipping_last_name', 'like', "%{$search}%")
                  ->orWhere('shipping_phone', 'like', "%{$search}%")
                  ->orWhere('tracking_number', 'like', "%{$search}%");
            });
        }

        $orders = $query->orderBy('created_at', 'desc')
            ->paginate(25);

        return response()->json([
            'html' => view('admin.delivery.partials.order-table', compact('orders'))->render(),
            'pagination' => $orders->links()->toHtml(),
        ]);
    }

    // Placeholder methods for other delivery routes
    
    /**
     * Display delivery partners list.
     */
    public function partners(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status');
        $serviceType = $request->get('service_type');
        $sort = $request->get('sort', 'sort_order');
        $direction = $request->get('direction', 'asc');
        $perPage = $request->get('per_page', 25);
        
        // Statistics
        $stats = [
            'total' => DeliveryPartner::count(),
            'active' => DeliveryPartner::where('is_active', true)->count(),
            'inactive' => DeliveryPartner::where('is_active', false)->count(),
            'featured' => DeliveryPartner::where('is_featured', true)->count(),
        ];
        
        // Build query
        $query = DeliveryPartner::query();
        
        // Search
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('contact_person', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }
        
        // Status filter
        if ($status !== null && $status !== '') {
            $query->where('is_active', $status === 'active');
        }
        
        // Service type filter
        if ($serviceType !== null && $serviceType !== '') {
            $query->where('service_type', $serviceType);
        }
        
        // Sorting
        $validSorts = ['name', 'sort_order', 'created_at', 'service_type'];
        if (in_array($sort, $validSorts)) {
            $query->orderBy($sort, $direction);
        } else {
            $query->orderBy('sort_order')->orderBy('name');
        }
        
        $partners = $query->paginate($perPage)->appends($request->query());
        
        // AJAX response
        if ($request->ajax() || $request->wantsJson()) {
            $html = view('admin.delivery.partners.partials.table-rows', compact('partners'))->render();
            
            return response()->json([
                'html' => $html,
                'stats' => $stats,
                'pagination' => $partners->links()->toHtml(),
                'total' => $partners->total()
            ]);
        }
        
        return view('admin.delivery.partners.index', compact('partners', 'stats'));
    }

    /**
     * Show create form for delivery partner.
     */
    public function createPartner()
    {
        return view('admin.delivery.partners.create');
    }

    /**
     * Store new delivery partner.
     */
    public function storePartner(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:delivery_partners,slug',
            'logo' => 'nullable|image|max:5120',
            'description' => 'nullable|string',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'website' => 'nullable|url|max:255',
            'service_type' => 'nullable|in:express,standard,overnight,international,all',
            'coverage_area' => 'nullable|string|max:255',
            'base_rate' => 'nullable|numeric|min:0',
            'cod_charge' => 'nullable|numeric|min:0',
            'free_shipping_threshold' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);
        
        $data = $request->only([
            'name', 'slug', 'description', 'contact_person', 'phone', 'email',
            'address', 'website', 'service_type', 'coverage_area',
            'base_rate', 'cod_charge', 'free_shipping_threshold',
            'is_active', 'is_featured', 'sort_order'
        ]);
        
        // Generate slug if not provided
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        
        // Handle checkbox values
        $data['is_active'] = $request->has('is_active');
        $data['is_featured'] = $request->has('is_featured');
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['base_rate'] = $data['base_rate'] ?? 0;
        $data['cod_charge'] = $data['cod_charge'] ?? 0;
        $data['free_shipping_threshold'] = $data['free_shipping_threshold'] ?? 0;
        
        // Upload logo
        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('delivery-partners', 'public');
        }
        
        DeliveryPartner::create($data);
        
        return redirect()->route('admin.delivery.partners')
            ->with('success', 'Delivery partner created successfully.');
    }

    /**
     * Show edit form for delivery partner.
     */
    public function editPartner(DeliveryPartner $partner)
    {
        return view('admin.delivery.partners.edit', compact('partner'));
    }

    /**
     * Update delivery partner.
     */
    public function updatePartner(Request $request, DeliveryPartner $partner)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:delivery_partners,slug,' . $partner->id,
            'logo' => 'nullable|image|max:5120',
            'description' => 'nullable|string',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'website' => 'nullable|url|max:255',
            'service_type' => 'nullable|in:express,standard,overnight,international,all',
            'coverage_area' => 'nullable|string|max:255',
            'base_rate' => 'nullable|numeric|min:0',
            'cod_charge' => 'nullable|numeric|min:0',
            'free_shipping_threshold' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);
        
        $data = $request->only([
            'name', 'slug', 'description', 'contact_person', 'phone', 'email',
            'address', 'website', 'service_type', 'coverage_area',
            'base_rate', 'cod_charge', 'free_shipping_threshold',
            'is_active', 'is_featured', 'sort_order'
        ]);
        
        // Generate slug if not provided
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        
        // Handle checkbox values
        $data['is_active'] = $request->has('is_active');
        $data['is_featured'] = $request->has('is_featured');
        
        // Upload new logo
        if ($request->hasFile('logo')) {
            // Delete old logo
            if ($partner->logo) {
                Storage::disk('public')->delete($partner->logo);
            }
            $data['logo'] = $request->file('logo')->store('delivery-partners', 'public');
        }
        
        // Remove logo if requested
        if ($request->has('remove_logo') && $partner->logo) {
            Storage::disk('public')->delete($partner->logo);
            $data['logo'] = null;
        }
        
        $partner->update($data);
        
        return redirect()->route('admin.delivery.partners')
            ->with('success', 'Delivery partner updated successfully.');
    }

    /**
     * Delete delivery partner.
     */
    public function destroyPartner(DeliveryPartner $partner)
    {
        // Delete logo
        if ($partner->logo) {
            Storage::disk('public')->delete($partner->logo);
        }
        
        $partner->delete();
        
        return redirect()->route('admin.delivery.partners')
            ->with('success', 'Delivery partner deleted successfully.');
    }

    /**
     * Toggle delivery partner status.
     */
    public function togglePartnerStatus(DeliveryPartner $partner)
    {
        $partner->update([
            'is_active' => !$partner->is_active
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Partner status updated.',
            'is_active' => $partner->is_active
        ]);
    }

    /**
     * Toggle delivery partner featured status.
     */
    public function togglePartnerFeatured(DeliveryPartner $partner)
    {
        $partner->update([
            'is_featured' => !$partner->is_featured
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Partner featured status updated.',
            'is_featured' => $partner->is_featured
        ]);
    }

    /**
     * Bulk actions for delivery partners.
     */
    public function bulkPartnerAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,feature,unfeature,delete',
            'ids' => 'required|array',
            'ids.*' => 'exists:delivery_partners,id',
        ]);
        
        $ids = $request->ids;
        $action = $request->action;
        
        switch ($action) {
            case 'activate':
                DeliveryPartner::whereIn('id', $ids)->update(['is_active' => true]);
                $message = count($ids) . ' partner(s) activated successfully.';
                break;
                
            case 'deactivate':
                DeliveryPartner::whereIn('id', $ids)->update(['is_active' => false]);
                $message = count($ids) . ' partner(s) deactivated successfully.';
                break;
                
            case 'feature':
                DeliveryPartner::whereIn('id', $ids)->update(['is_featured' => true]);
                $message = count($ids) . ' partner(s) marked as featured.';
                break;
                
            case 'unfeature':
                DeliveryPartner::whereIn('id', $ids)->update(['is_featured' => false]);
                $message = count($ids) . ' partner(s) removed from featured.';
                break;
                
            case 'delete':
                // Delete logos
                $partners = DeliveryPartner::whereIn('id', $ids)->whereNotNull('logo')->get();
                foreach ($partners as $partner) {
                    Storage::disk('public')->delete($partner->logo);
                }
                
                DeliveryPartner::whereIn('id', $ids)->delete();
                $message = count($ids) . ' partner(s) deleted successfully.';
                break;
        }
        
        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }

    // Placeholder methods for other delivery routes
    public function carriers(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status');
        $carrierType = $request->get('carrier_type');
        $serviceType = $request->get('service_type');
        $sort = $request->get('sort', 'sort_order');
        $direction = $request->get('direction', 'asc');
        $perPage = $request->get('per_page', 25);
        
        // Statistics
        $stats = [
            'total' => Carrier::count(),
            'active' => Carrier::where('is_active', true)->count(),
            'inactive' => Carrier::where('is_active', false)->count(),
            'featured' => Carrier::where('is_featured', true)->count(),
            'api_configured' => Carrier::where('is_api_configured', true)->count(),
        ];
        
        // Build query
        $query = Carrier::query();
        
        // Search
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('contact_person', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }
        
        // Status filter
        if ($status !== null && $status !== '') {
            $query->where('is_active', $status === 'active');
        }
        
        // Carrier type filter
        if ($carrierType !== null && $carrierType !== '') {
            $query->where('carrier_type', $carrierType);
        }
        
        // Service type filter
        if ($serviceType !== null && $serviceType !== '') {
            $query->where('service_type', $serviceType);
        }
        
        // Sorting
        $validSorts = ['name', 'sort_order', 'created_at', 'carrier_type', 'service_type', 'base_rate'];
        if (in_array($sort, $validSorts)) {
            $query->orderBy($sort, $direction);
        } else {
            $query->orderBy('sort_order')->orderBy('name');
        }
        
        $carriers = $query->paginate($perPage)->appends($request->query());
        
        // AJAX response
        if ($request->ajax() || $request->wantsJson()) {
            $html = view('admin.delivery.carriers.partials.table-rows', compact('carriers'))->render();
            
            return response()->json([
                'html' => $html,
                'stats' => $stats,
                'pagination' => $carriers->links()->toHtml(),
                'total' => $carriers->total()
            ]);
        }
        
        return view('admin.delivery.carriers.index', compact('carriers', 'stats'));
    }

    /**
     * Show create form for carrier.
     */
    public function createCarrier()
    {
        return view('admin.delivery.carriers.create');
    }

    /**
     * Store new carrier.
     */
    public function storeCarrier(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:carriers,slug',
            'logo' => 'nullable|image|max:5120',
            'description' => 'nullable|string',
            'carrier_type' => 'nullable|in:international,regional,local,express,freight,all',
            'service_type' => 'nullable|in:express,standard,economy,overnight,international,freight,all',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'website' => 'nullable|url|max:255',
            'api_key' => 'nullable|string',
            'api_secret' => 'nullable|string',
            'api_token' => 'nullable|string',
            'account_number' => 'nullable|string|max:100',
            'api_mode' => 'nullable|in:sandbox,production',
            'tracking_url_pattern' => 'nullable|url|max:500',
            'tracking_prefix' => 'nullable|string|max:50',
            'base_rate' => 'nullable|numeric|min:0',
            'per_kg_rate' => 'nullable|numeric|min:0',
            'fuel_surcharge_percent' => 'nullable|numeric|min:0|max:100',
            'cod_charge' => 'nullable|numeric|min:0',
            'free_shipping_threshold' => 'nullable|numeric|min:0',
            'coverage_countries' => 'nullable|string',
            'excluded_countries' => 'nullable|string',
            'estimated_delivery_days' => 'nullable|string|max:100',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'supports_tracking' => 'boolean',
            'supports_cod' => 'boolean',
            'supports_insurance' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);
        
        $data = $request->only([
            'name', 'slug', 'description', 'carrier_type', 'service_type',
            'contact_person', 'phone', 'email', 'address', 'website',
            'api_key', 'api_secret', 'api_token', 'account_number', 'api_mode',
            'tracking_url_pattern', 'tracking_prefix', 'base_rate', 'per_kg_rate',
            'fuel_surcharge_percent', 'cod_charge', 'free_shipping_threshold',
            'coverage_countries', 'excluded_countries', 'estimated_delivery_days',
            'is_active', 'is_featured', 'supports_tracking', 'supports_cod',
            'supports_insurance', 'sort_order'
        ]);
        
        // Generate slug if not provided
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        
        // Handle checkbox values
        $data['is_active'] = $request->has('is_active');
        $data['is_featured'] = $request->has('is_featured');
        $data['supports_tracking'] = $request->has('supports_tracking');
        $data['supports_cod'] = $request->has('supports_cod');
        $data['supports_insurance'] = $request->has('supports_insurance');
        
        // Check if API is configured
        $data['is_api_configured'] = !empty($data['api_key']) || !empty($data['api_token']);
        
        // Set defaults
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['base_rate'] = $data['base_rate'] ?? 0;
        $data['per_kg_rate'] = $data['per_kg_rate'] ?? 0;
        $data['fuel_surcharge_percent'] = $data['fuel_surcharge_percent'] ?? 0;
        $data['cod_charge'] = $data['cod_charge'] ?? 0;
        $data['free_shipping_threshold'] = $data['free_shipping_threshold'] ?? 0;
        
        // Upload logo
        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('carriers', 'public');
        }
        
        Carrier::create($data);
        
        return redirect()->route('admin.delivery.carriers.index')
            ->with('success', 'Carrier created successfully.');
    }

    /**
     * Show edit form for carrier.
     */
    public function editCarrier(Carrier $carrier)
    {
        return view('admin.delivery.carriers.edit', compact('carrier'));
    }

    /**
     * Update carrier.
     */
    public function updateCarrier(Request $request, Carrier $carrier)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:carriers,slug,' . $carrier->id,
            'logo' => 'nullable|image|max:5120',
            'description' => 'nullable|string',
            'carrier_type' => 'nullable|in:international,regional,local,express,freight,all',
            'service_type' => 'nullable|in:express,standard,economy,overnight,international,freight,all',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'website' => 'nullable|url|max:255',
            'api_key' => 'nullable|string',
            'api_secret' => 'nullable|string',
            'api_token' => 'nullable|string',
            'account_number' => 'nullable|string|max:100',
            'api_mode' => 'nullable|in:sandbox,production',
            'tracking_url_pattern' => 'nullable|url|max:500',
            'tracking_prefix' => 'nullable|string|max:50',
            'base_rate' => 'nullable|numeric|min:0',
            'per_kg_rate' => 'nullable|numeric|min:0',
            'fuel_surcharge_percent' => 'nullable|numeric|min:0|max:100',
            'cod_charge' => 'nullable|numeric|min:0',
            'free_shipping_threshold' => 'nullable|numeric|min:0',
            'coverage_countries' => 'nullable|string',
            'excluded_countries' => 'nullable|string',
            'estimated_delivery_days' => 'nullable|string|max:100',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'supports_tracking' => 'boolean',
            'supports_cod' => 'boolean',
            'supports_insurance' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);
        
        $data = $request->only([
            'name', 'slug', 'description', 'carrier_type', 'service_type',
            'contact_person', 'phone', 'email', 'address', 'website',
            'api_key', 'api_secret', 'api_token', 'account_number', 'api_mode',
            'tracking_url_pattern', 'tracking_prefix', 'base_rate', 'per_kg_rate',
            'fuel_surcharge_percent', 'cod_charge', 'free_shipping_threshold',
            'coverage_countries', 'excluded_countries', 'estimated_delivery_days',
            'is_active', 'is_featured', 'supports_tracking', 'supports_cod',
            'supports_insurance', 'sort_order'
        ]);
        
        // Generate slug if not provided
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        
        // Handle checkbox values
        $data['is_active'] = $request->has('is_active');
        $data['is_featured'] = $request->has('is_featured');
        $data['supports_tracking'] = $request->has('supports_tracking');
        $data['supports_cod'] = $request->has('supports_cod');
        $data['supports_insurance'] = $request->has('supports_insurance');
        
        // Check if API is configured
        $data['is_api_configured'] = !empty($data['api_key']) || !empty($data['api_token']);
        
        // Upload new logo
        if ($request->hasFile('logo')) {
            // Delete old logo
            if ($carrier->logo) {
                Storage::disk('public')->delete($carrier->logo);
            }
            $data['logo'] = $request->file('logo')->store('carriers', 'public');
        }
        
        // Remove logo if requested
        if ($request->has('remove_logo') && $carrier->logo) {
            Storage::disk('public')->delete($carrier->logo);
            $data['logo'] = null;
        }
        
        $carrier->update($data);
        
        return redirect()->route('admin.delivery.carriers.index')
            ->with('success', 'Carrier updated successfully.');
    }

    /**
     * Delete carrier.
     */
    public function destroyCarrier(Carrier $carrier)
    {
        // Delete logo
        if ($carrier->logo) {
            Storage::disk('public')->delete($carrier->logo);
        }
        
        $carrier->delete();
        
        return redirect()->route('admin.delivery.carriers.index')
            ->with('success', 'Carrier deleted successfully.');
    }

    /**
     * Toggle carrier status.
     */
    public function toggleCarrierStatus(Carrier $carrier)
    {
        $carrier->update([
            'is_active' => !$carrier->is_active
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Carrier status updated.',
            'is_active' => $carrier->is_active
        ]);
    }

    /**
     * Toggle carrier featured status.
     */
    public function toggleCarrierFeatured(Carrier $carrier)
    {
        $carrier->update([
            'is_featured' => !$carrier->is_featured
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Carrier featured status updated.',
            'is_featured' => $carrier->is_featured
        ]);
    }

    /**
     * Bulk actions for carriers.
     */
    public function bulkCarrierAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,feature,unfeature,delete',
            'ids' => 'required|array',
            'ids.*' => 'exists:carriers,id',
        ]);
        
        $ids = $request->ids;
        $action = $request->action;
        
        switch ($action) {
            case 'activate':
                Carrier::whereIn('id', $ids)->update(['is_active' => true]);
                $message = count($ids) . ' carrier(s) activated successfully.';
                break;
                
            case 'deactivate':
                Carrier::whereIn('id', $ids)->update(['is_active' => false]);
                $message = count($ids) . ' carrier(s) deactivated successfully.';
                break;
                
            case 'feature':
                Carrier::whereIn('id', $ids)->update(['is_featured' => true]);
                $message = count($ids) . ' carrier(s) marked as featured.';
                break;
                
            case 'unfeature':
                Carrier::whereIn('id', $ids)->update(['is_featured' => false]);
                $message = count($ids) . ' carrier(s) removed from featured.';
                break;
                
            case 'delete':
                // Delete logos
                $carriers = Carrier::whereIn('id', $ids)->whereNotNull('logo')->get();
                foreach ($carriers as $carrier) {
                    Storage::disk('public')->delete($carrier->logo);
                }
                
                Carrier::whereIn('id', $ids)->delete();
                $message = count($ids) . ' carrier(s) deleted successfully.';
                break;
        }
        
        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }

    public function tracking(Request $request)
    {
        // Get statistics for all shipments
        $stats = $this->getShipmentStats();
        
        // Build query for shipments (orders with tracking info or shipped orders)
        $query = Order::query()
            ->where(function($q) {
                $q->whereNotNull('tracking_number')
                  ->orWhereIn('status', ['shipped', 'processing', 'confirmed', 'pending']);
            })
            ->with(['user']);
        
        // Search by order number or tracking number
        if ($request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('tracking_number', 'like', "%{$search}%")
                  ->orWhere('shipping_phone', 'like', "%{$search}%")
                  ->orWhere('billing_phone', 'like', "%{$search}%");
            });
        }
        
        // Filter by status
        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        
        // Filter by payment status
        if ($request->payment_status && $request->payment_status !== 'all') {
            $query->where('payment_status', $request->payment_status);
        }
        
        // Filter by carrier
        if ($request->carrier) {
            $query->where('shipping_company', $request->carrier);
        }
        
        // Filter by date range
        if ($request->date_range) {
            $dateRange = $request->date_range;
            $startDate = match($dateRange) {
                'today' => Carbon::today(),
                'yesterday' => Carbon::yesterday(),
                'last_7_days' => Carbon::now()->subDays(7),
                'last_30_days' => Carbon::now()->subDays(30),
                'this_month' => Carbon::now()->startOfMonth(),
                'last_month' => Carbon::now()->subMonth()->startOfMonth(),
                default => Carbon::now()->subDays(30),
            };
            $query->whereDate('created_at', '>=', $startDate);
        }
        
        // Sorting
        $sort = $request->sort ?? 'created_at';
        $direction = $request->direction ?? 'desc';
        $query->orderBy($sort, $direction);
        
        // Pagination
        $perPage = $request->per_page ?? 25;
        $shipments = $query->paginate($perPage);
        
        // Get carriers for filter
        $carriers = Carrier::where('is_active', 1)->orderBy('name')->get();
        
        // If searching for a specific order, show tracking details
        $trackingOrder = null;
        if ($request->search) {
            $trackingOrder = Order::where('order_number', 'like', "%{$request->search}%")
                ->orWhere('tracking_number', 'like', "%{$request->search}%")
                ->first();
        }
        
        // AJAX response
        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.delivery.partials.tracking-table-rows', compact('shipments'))->render(),
                'pagination' => $shipments->links()->toHtml(),
                'stats' => $stats
            ]);
        }
        
        return view('admin.delivery.tracking', compact('shipments', 'stats', 'carriers', 'trackingOrder'));
    }
    
    /**
     * Get shipment statistics.
     */
    private function getShipmentStats()
    {
        return [
            'total' => Order::whereNotNull('tracking_number')->orWhereIn('status', ['shipped', 'processing'])->count(),
            'in_transit' => Order::where('status', 'shipped')->count(),
            'delivered' => Order::where('status', 'delivered')->count(),
            'pending' => Order::whereIn('status', ['pending', 'confirmed'])->count(),
            'returned' => Order::where('status', 'cancelled')->count(),
        ];
    }
    
    /**
     * Update shipment tracking status.
     */
    public function updateTrackingStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,confirmed,shipped,delivered,cancelled',
            'tracking_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);
        
        $order->update([
            'status' => $request->status,
            'tracking_number' => $request->tracking_number ?? $order->tracking_number,
            'notes' => $request->notes ?? $order->notes,
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Shipment status updated successfully.',
            'order' => $order->fresh()
        ]);
    }
    
    /**
     * Generate tracking number for an order.
     */
    public function generateTrackingNumber(Request $request, Order $order)
    {
        $trackingNumber = 'TRK' . strtoupper(uniqid()) . rand(1000, 9999);
        
        $order->update([
            'tracking_number' => $trackingNumber,
            'status' => 'shipped',
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Tracking number generated successfully.',
            'tracking_number' => $trackingNumber
        ]);
    }
    
    /**
     * Bulk tracking action.
     */
    public function bulkTrackingAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:shipped,delivered,cancelled,pending',
            'ids' => 'required|array',
            'ids.*' => 'exists:orders,id',
        ]);
        
        $count = Order::whereIn('id', $request->ids)->update([
            'status' => $request->action
        ]);
        
        return redirect()->route('admin.delivery.tracking')
            ->with('success', "{$count} shipments updated successfully.");
    }
    
    public function zones()
    {
        return view('admin.delivery.zones');
    }

    public function courierIntegration()
    {
        return view('admin.delivery.courier-integration');
    }

    public function deliveryBoys()
    {
        return view('admin.delivery.delivery-boys');
    }

    public function schedules()
    {
        return view('admin.delivery.schedules');
    }

    public function reports()
    {
        return view('admin.delivery.reports');
    }
}
