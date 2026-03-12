<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\ActivityLog;

class OrderController extends Controller
{
    /**
     * Display user's orders.
     */
    public function index()
    {
        $orders = Order::where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('themes.general.orders.index', compact('orders'));
    }

    /**
     * Display a single order.
     */
    public function show(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        return view('themes.general.orders.show', compact('order'));
    }

    /**
     * Cancel an order.
     */
    public function cancel(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        if (!in_array($order->status, ['pending', 'processing'])) {
            return back()->with('error', 'This order cannot be cancelled.');
        }

        $oldStatus = $order->status;
        $order->update(['status' => 'cancelled']);

        // Restore product stock
        foreach ($order->items as $item) {
            $item->product->increment('stock', $item->quantity);
        }

        // Log order cancellation
        ActivityLog::customerLog(
            'Order cancelled',
            $order,
            auth()->user(),
            [
                'order_id' => $order->id,
                'order_code' => $order->code,
                'old_status' => $oldStatus,
                'new_status' => 'cancelled'
            ]
        );

        return back()->with('success', 'Order cancelled successfully.');
    }

    /**
     * Track order status.
     */
    public function track(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        return view('themes.general.orders.track', compact('order'));
    }
}
