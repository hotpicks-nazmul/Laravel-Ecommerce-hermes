<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function sales(Request $request)
    {
        $startDate = $request->start_date ?? now()->subMonth()->format('Y-m-d');
        $endDate = $request->end_date ?? now()->format('Y-m-d');

        $orders = Order::whereBetween('created_at', [$startDate, $endDate . ' 23:59:59'])
            ->where('payment_status', 'paid')
            ->get();

        $totalSales = $orders->sum('total');
        $totalOrders = $orders->count();
        $averageOrder = $totalOrders > 0 ? $totalSales / $totalOrders : 0;

        $salesByDate = $orders->groupBy(function ($order) {
            return $order->created_at->format('Y-m-d');
        })->map(function ($group) {
            return $group->sum('total');
        });

        return view('admin.reports.sales', compact('orders', 'totalSales', 'totalOrders', 'averageOrder', 'salesByDate', 'startDate', 'endDate'));
    }

    public function products(Request $request)
    {
        $topProducts = Product::withSum('orderItems as total_sold', 'quantity')
            ->orderByDesc('total_sold')
            ->take(20)
            ->get();

        $lowStockProducts = Product::where('stock', '<', 10)
            ->orderBy('stock')
            ->get();

        return view('admin.reports.products', compact('topProducts', 'lowStockProducts'));
    }

    public function customers(Request $request)
    {
        $topCustomers = User::withCount('orders')
            ->withSum('orders as total_spent', 'total')
            ->where('role', 'customer')
            ->orderByDesc('total_spent')
            ->take(20)
            ->get();

        $newCustomers = User::where('role', 'customer')
            ->where('created_at', '>=', now()->subMonth())
            ->count();

        return view('admin.reports.customers', compact('topCustomers', 'newCustomers'));
    }

    public function inventory(Request $request)
    {
        $products = Product::select('id', 'name', 'sku', 'stock', 'price')
            ->orderBy('stock')
            ->paginate(50);

        $totalValue = Product::sum(DB::raw('stock * price'));

        return view('admin.reports.inventory', compact('products', 'totalValue'));
    }

    public function export($type)
    {
        // Export logic would go here
        return back()->with('success', 'Report exported successfully.');
    }
}
