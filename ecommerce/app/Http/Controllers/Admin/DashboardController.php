<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display admin dashboard.
     */
    public function index()
    {
        $totalSales = Order::where('payment_status', 'paid')->sum('total');
        $totalOrders = Order::count();
        $totalProducts = Product::count();
        $totalCustomers = User::where('role', 'customer')->count();
        
        $todaySales = Order::where('payment_status', 'paid')
            ->whereDate('created_at', today())
            ->sum('total');
        
        $todayOrders = Order::whereDate('created_at', today())->count();
        
        $recentOrders = Order::latest()->take(5)->get();
        
        // Order status counts
        $pendingOrders = Order::where('status', 'pending')->count();
        $processingOrders = Order::where('status', 'processing')->count();
        $completedOrders = Order::where('status', 'completed')->count();
        $cancelledOrders = Order::where('status', 'cancelled')->count();
        
        // Product status counts
        $activeProducts = Product::where('is_active', true)->count();
        $outOfStockProducts = Product::where('quantity', 0)->count();
        $lowStockProducts = Product::where('quantity', '>', 0)->where('quantity', '<=', 10)->count();
        
        // Top selling products
        $topProducts = DB::table('order_items')
            ->select('product_name', DB::raw('SUM(quantity) as total_sold'))
            ->groupBy('product_name')
            ->orderByDesc('total_sold')
            ->take(5)
            ->get();
        
        // Category distribution
        $categoryDistribution = Category::withCount('products')
            ->orderByDesc('products_count')
            ->take(5)
            ->get();
        
        // Sales by month for current year (for line/bar chart)
        $salesByMonth = Order::where('payment_status', 'paid')
            ->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(total) as total')
            )
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->pluck('total', 'month');

        // Prepare monthly sales data for chart (all 12 months)
        $monthlySalesData = [];
        $monthlyOrderCounts = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthlySalesData[] = round($salesByMonth->get($i, 0), 2);
            $monthlyOrderCounts[] = Order::whereYear('created_at', date('Y'))
                ->whereMonth('created_at', $i)
                ->count();
        }

        // Last 7 days sales data (for area chart)
        $last7DaysSales = [];
        $last7DaysLabels = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $last7DaysLabels[] = $date->format('D');
            $last7DaysSales[] = round(Order::where('payment_status', 'paid')
                ->whereDate('created_at', $date->format('Y-m-d'))
                ->sum('total'), 2);
        }

        // Last 30 days sales data
        $last30DaysSales = [];
        $last30DaysLabels = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $last30DaysLabels[] = $date->format('d M');
            $last30DaysSales[] = round(Order::where('payment_status', 'paid')
                ->whereDate('created_at', $date->format('Y-m-d'))
                ->sum('total'), 2);
        }

        // Growth calculations
        $lastMonthSales = Order::where('payment_status', 'paid')
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->sum('total');
        
        $currentMonthSales = Order::where('payment_status', 'paid')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total');

        $salesGrowth = $lastMonthSales > 0 
            ? round((($currentMonthSales - $lastMonthSales) / $lastMonthSales) * 100, 1) 
            : ($currentMonthSales > 0 ? 100 : 0);

        // Customer growth
        $lastMonthCustomers = User::where('role', 'customer')
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->count();
        
        $currentMonthCustomers = User::where('role', 'customer')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $customerGrowth = $lastMonthCustomers > 0 
            ? round((($currentMonthCustomers - $lastMonthCustomers) / $lastMonthCustomers) * 100, 1) 
            : ($currentMonthCustomers > 0 ? 100 : 0);

        // Order growth
        $lastMonthOrders = Order::whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->count();
        
        $currentMonthOrders = Order::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $orderGrowth = $lastMonthOrders > 0 
            ? round((($currentMonthOrders - $lastMonthOrders) / $lastMonthOrders) * 100, 1) 
            : ($currentMonthOrders > 0 ? 100 : 0);

        // Sales by category for pie/doughnut chart
        $salesByCategory = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select('categories.name', DB::raw('SUM(order_items.quantity * order_items.price) as total_sales'))
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total_sales')
            ->take(6)
            ->get();

        // Payment method distribution
        $paymentMethods = Order::where('payment_status', 'paid')
            ->select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(total) as total'))
            ->groupBy('payment_method')
            ->get();

        return view('admin.dashboard', compact(
            'totalSales',
            'totalOrders',
            'totalProducts',
            'totalCustomers',
            'todaySales',
            'todayOrders',
            'recentOrders',
            'topProducts',
            'salesByMonth',
            'monthlySalesData',
            'monthlyOrderCounts',
            'last7DaysSales',
            'last7DaysLabels',
            'last30DaysSales',
            'last30DaysLabels',
            'pendingOrders',
            'processingOrders',
            'completedOrders',
            'cancelledOrders',
            'activeProducts',
            'outOfStockProducts',
            'lowStockProducts',
            'categoryDistribution',
            'salesGrowth',
            'customerGrowth',
            'orderGrowth',
            'salesByCategory',
            'paymentMethods'
        ));
    }

    /**
     * Display analytics page.
     */
    public function analytics()
    {
        return view('admin.analytics');
    }

    /**
     * Get sales chart data.
     */
    public function salesChart(Request $request)
    {
        $period = $request->get('period', 'week');

        $data = match ($period) {
            'week' => Order::where('created_at', '>=', now()->subWeek())
                ->select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('SUM(total) as total')
                )
                ->groupBy('date')
                ->get(),
            'month' => Order::where('created_at', '>=', now()->subMonth())
                ->select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('SUM(total) as total')
                )
                ->groupBy('date')
                ->get(),
            'year' => Order::where('created_at', '>=', now()->subYear())
                ->select(
                    DB::raw('MONTH(created_at) as month'),
                    DB::raw('SUM(total) as total')
                )
                ->groupBy('month')
                ->get(),
            default => collect(),
        };

        return response()->json($data);
    }

    /**
     * Display admin profile.
     */
    public function profile()
    {
        return view('admin.profile');
    }

    /**
     * Update admin profile.
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
        ]);

        $user->update($request->only(['name', 'email']));

        return back()->with('success', 'Profile updated successfully.');
    }

    /**
     * Update admin password.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = auth()->user();

        if (!\Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->update(['password' => \Hash::make($request->password)]);

        return back()->with('success', 'Password updated successfully.');
    }
}
