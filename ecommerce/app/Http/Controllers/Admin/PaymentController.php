<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    /**
     * Display payment methods list.
     */
    public function index()
    {
        $gateways = PaymentGateway::orderBy('sort_order')->get();
        
        // Convert to array with proper structure for the view
        $gatewaysArray = [];
        foreach ($gateways as $gateway) {
            $gatewaysArray[$gateway->slug] = [
                'id' => $gateway->id,
                'name' => $gateway->name,
                'description' => $gateway->description,
                'logo' => $gateway->logo,
                'enabled' => $gateway->is_active,
                'sandbox' => $gateway->test_mode,
                'credentials' => $gateway->credentials ?? [],
            ];
        }

        return view('admin.payment.index', compact('gateways', 'gatewaysArray'));
    }

    /**
     * Store a new payment gateway.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:payment_gateways,slug',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:png,jpg,jpeg,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = $request->except('_token');
        $data['is_active'] = $request->has('is_active');
        $data['test_mode'] = $request->has('test_mode');
        $data['sort_order'] = $request->input('sort_order', 0);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('payment-logos', 'public');
            $data['logo'] = $path;
        }

        PaymentGateway::create($data);

        return back()->with('success', 'Payment gateway created successfully.');
    }

    /**
     * Update a payment gateway.
     */
    public function update(Request $request, $id)
    {
        $gateway = PaymentGateway::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:payment_gateways,slug,' . $id,
            'description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:png,jpg,jpeg,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = $request->except('_token', '_method');
        $data['is_active'] = $request->has('is_active');
        $data['test_mode'] = $request->has('test_mode');
        $data['sort_order'] = $request->input('sort_order', 0);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo
            if ($gateway->logo) {
                Storage::disk('public')->delete($gateway->logo);
            }
            $path = $request->file('logo')->store('payment-logos', 'public');
            $data['logo'] = $path;
        }

        $gateway->update($data);

        return back()->with('success', 'Payment gateway updated successfully.');
    }

    /**
     * Update credentials for a specific gateway.
     */
    public function updateCredentials(Request $request, $slug)
    {
        $gateway = PaymentGateway::where('slug', $slug)->firstOrFail();
        
        $credentials = $gateway->credentials ?? [];
        
        // Merge new credentials with existing ones
        $newCredentials = $request->except('_token');
        foreach ($newCredentials as $key => $value) {
            // Encrypt sensitive credential values
            if (!empty($value) && !in_array($key, ['additional_settings', 'instructions', 'public_key'])) {
                $credentials[$key] = Crypt::encryptString($value);
            } else {
                $credentials[$key] = $value;
            }
        }

        $gateway->update(['credentials' => $credentials]);

        return back()->with('success', ucfirst($gateway->name) . ' credentials updated successfully.');
    }

    /**
     * Toggle gateway status.
     */
    public function toggle($id)
    {
        $gateway = PaymentGateway::findOrFail($id);
        $gateway->update(['is_active' => !$gateway->is_active]);

        return back()->with('success', $gateway->name . ' status updated.');
    }

    /**
     * Delete a payment gateway.
     */
    public function destroy($id)
    {
        $gateway = PaymentGateway::findOrFail($id);

        // Delete logo if exists
        if ($gateway->logo) {
            Storage::disk('public')->delete($gateway->logo);
        }

        $gateway->delete();

        return back()->with('success', 'Payment gateway deleted successfully.');
    }

    /**
     * Set default payment gateway.
     */
    public function setDefault($id)
    {
        $gateway = PaymentGateway::findOrFail($id);
        
        // Remove default from all other gateways
        PaymentGateway::where('is_default', true)->update(['is_default' => false]);
        
        // Set this gateway as default
        $gateway->update(['is_default' => true]);

        return back()->with('success', $gateway->name . ' set as default payment method.');
    }

    /**
     * Update gateway order/sort.
     */
    public function updateOrder(Request $request)
    {
        $order = $request->input('order', []);
        
        foreach ($order as $index => $id) {
            PaymentGateway::where('id', $id)->update(['sort_order' => $index]);
        }

        return response()->json(['success' => true]);
    }
}
