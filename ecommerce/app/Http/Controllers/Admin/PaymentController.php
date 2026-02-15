<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;

class PaymentController extends Controller
{
    public function index()
    {
        $settings = Setting::whereIn('key', [
            'bkash_enabled', 'bkash_merchant_number', 'bkash_username', 'bkash_password', 'bkash_app_key', 'bkash_app_secret', 'bkash_sandbox',
            'sslcommerz_enabled', 'sslcommerz_store_id', 'sslcommerz_store_password', 'sslcommerz_sandbox',
            'nagad_enabled', 'nagad_merchant_id', 'nagad_merchant_number', 'nagad_sandbox',
            'rocket_enabled', 'rocket_merchant_id', 'rocket_merchant_number', 'rocket_password', 'rocket_sandbox',
            'cod_enabled', 'cod_instructions',
        ])->pluck('value', 'key');

        return view('admin.payment.index', compact('settings'));
    }

    public function updateBkash(Request $request)
    {
        foreach (['bkash_enabled', 'bkash_merchant_number', 'bkash_username', 'bkash_password', 'bkash_app_key', 'bkash_app_secret', 'bkash_sandbox'] as $key) {
            Setting::updateOrCreate(['key' => $key], ['value' => $request->input($key, '')]);
        }

        return back()->with('success', 'bKash settings updated successfully.');
    }

    public function updateSslcommerz(Request $request)
    {
        foreach (['sslcommerz_enabled', 'sslcommerz_store_id', 'sslcommerz_store_password', 'sslcommerz_sandbox'] as $key) {
            Setting::updateOrCreate(['key' => $key], ['value' => $request->input($key, '')]);
        }

        return back()->with('success', 'SSLCommerz settings updated successfully.');
    }

    public function updateNagad(Request $request)
    {
        foreach (['nagad_enabled', 'nagad_merchant_id', 'nagad_merchant_number', 'nagad_sandbox'] as $key) {
            Setting::updateOrCreate(['key' => $key], ['value' => $request->input($key, '')]);
        }

        return back()->with('success', 'Nagad settings updated successfully.');
    }

    public function updateRocket(Request $request)
    {
        foreach (['rocket_enabled', 'rocket_merchant_id', 'rocket_merchant_number', 'rocket_password', 'rocket_sandbox'] as $key) {
            Setting::updateOrCreate(['key' => $key], ['value' => $request->input($key, '')]);
        }

        return back()->with('success', 'Rocket settings updated successfully.');
    }

    public function updateCod(Request $request)
    {
        foreach (['cod_enabled', 'cod_instructions'] as $key) {
            Setting::updateOrCreate(['key' => $key], ['value' => $request->input($key, '')]);
        }

        return back()->with('success', 'Cash on Delivery settings updated successfully.');
    }

    public function toggle($gateway)
    {
        $key = $gateway . '_enabled';
        $setting = Setting::where('key', $key)->first();
        
        if ($setting) {
            $setting->update(['value' => $setting->value === '1' ? '0' : '1']);
        } else {
            Setting::create(['key' => $key, 'value' => '1']);
        }

        return back()->with('success', 'Payment gateway status updated.');
    }
}
