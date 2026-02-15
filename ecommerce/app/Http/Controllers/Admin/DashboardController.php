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
        
        $salesByMonth = Order::where('payment_status', 'paid')
            ->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(total) as total')
            )
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->pluck('total', 'month');

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
            'pendingOrders',
            'processingOrders',
            'completedOrders',
            'activeProducts',
            'outOfStockProducts',
            'lowStockProducts',
            'categoryDistribution'
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
