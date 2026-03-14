<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;

class DashboardController extends Controller
{
    /**
     * Display staff dashboard.
     */
    public function index()
    {
        $user = auth()->user();
        
        // Get staff's permissions (use getPermissionsArray for consistency with User model cast)
        $permissions = $user->getPermissionsArray();
        
        // Get stats based on permissions
        $stats = [];
        
        if (in_array('orders', $permissions) || $user->is_super_admin) {
            $stats['pending_orders'] = Order::where('delivery_status', 'pending')->count();
            $stats['total_orders'] = Order::count();
        }
        
        if (in_array('products', $permissions) || $user->is_super_admin) {
            $stats['total_products'] = Product::count();
            $stats['low_stock'] = Product::whereRaw('stock_quantity <= low_stock_quantity')->count();
        }
        
        return view('staff.dashboard', compact('stats', 'permissions'));
    }
}
