<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('user');

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->payment_status) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->search) {
            $query->where('order_number', 'like', "%{$request->search}%");
        }

        $orders = $query->latest()->paginate(15);

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load('user', 'items.product');
        return view('admin.orders.show', compact('order'));
    }

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

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
        ]);

        $order->update(['status' => $request->status]);

        return back()->with('success', 'Order status updated.');
    }

    public function updatePaymentStatus(Request $request, Order $order)
    {
        $request->validate([
            'payment_status' => 'required|in:pending,paid,failed,refunded',
        ]);

        $order->update(['payment_status' => $request->payment_status]);

        return back()->with('success', 'Payment status updated.');
    }

    public function invoice(Order $order)
    {
        $order->load('user', 'items.product');
        return view('admin.orders.invoice', compact('order'));
    }

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
}
