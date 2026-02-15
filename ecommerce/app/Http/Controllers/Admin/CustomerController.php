<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'customer');

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        $customers = $query->latest()->paginate(15);

        return view('admin.customers.index', compact('customers'));
    }

    public function show(User $customer)
    {
        $customer->load('orders', 'addresses');
        return view('admin.customers.show', compact('customer'));
    }

    public function update(Request $request, User $customer)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $customer->id,
            'phone' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        $customer->update($request->all());

        return back()->with('success', 'Customer updated successfully.');
    }

    public function destroy(User $customer)
    {
        $customer->delete();
        return back()->with('success', 'Customer deleted successfully.');
    }

    public function orders(User $customer)
    {
        $orders = $customer->orders()->latest()->paginate(10);
        return view('admin.customers.orders', compact('customer', 'orders'));
    }

    public function loginAs(User $customer)
    {
        auth()->login($customer);
        return redirect()->route('home');
    }
}
