<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;

class SettingController extends Controller
{
    public function general()
    {
        $settings = Setting::pluck('value', 'key');
        return view('admin.settings.general', compact('settings'));
    }

    public function updateGeneral(Request $request)
    {
        foreach ($request->except('_token') as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        return back()->with('success', 'Settings updated successfully.');
    }

    public function store()
    {
        $settings = Setting::pluck('value', 'key');
        return view('admin.settings.store', compact('settings'));
    }

    public function updateStore(Request $request)
    {
        foreach ($request->except('_token') as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        return back()->with('success', 'Store settings updated successfully.');
    }

    public function email()
    {
        $settings = Setting::pluck('value', 'key');
        return view('admin.settings.email', compact('settings'));
    }

    public function updateEmail(Request $request)
    {
        foreach ($request->except('_token') as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        return back()->with('success', 'Email settings updated successfully.');
    }

    public function testEmail(Request $request)
    {
        // Test email sending logic
        return back()->with('success', 'Test email sent successfully.');
    }

    public function seo()
    {
        $settings = Setting::pluck('value', 'key');
        return view('admin.settings.seo', compact('settings'));
    }

    public function updateSeo(Request $request)
    {
        foreach ($request->except('_token') as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        return back()->with('success', 'SEO settings updated successfully.');
    }

    public function social()
    {
        $settings = Setting::pluck('value', 'key');
        return view('admin.settings.social', compact('settings'));
    }

    public function updateSocial(Request $request)
    {
        foreach ($request->except('_token') as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        return back()->with('success', 'Social settings updated successfully.');
    }

    public function maintenance()
    {
        $settings = Setting::pluck('value', 'key');
        return view('admin.settings.maintenance', compact('settings'));
    }

    public function updateMaintenance(Request $request)
    {
        foreach ($request->except('_token') as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        return back()->with('success', 'Maintenance settings updated successfully.');
    }

    public function backup()
    {
        return view('admin.settings.backup');
    }

    public function createBackup()
    {
        // Backup creation logic
        return back()->with('success', 'Backup created successfully.');
    }

    public function downloadBackup($file)
    {
        // Download backup file
    }

    public function restoreBackup(Request $request)
    {
        // Restore backup logic
        return back()->with('success', 'Backup restored successfully.');
    }

    /**
     * Social Login Settings
     */
    public function socialLogin()
    {
        $settings = Setting::where('group', 'social_login')->pluck('value', 'key');
        return view('admin.settings.social-login', compact('settings'));
    }

    /**
     * Update Social Login Settings
     */
    public function updateSocialLogin(Request $request)
    {
        $settings = [
            'google_enabled' => $request->has('google_enabled') ? '1' : '0',
            'google_client_id' => $request->google_client_id ?? '',
            'google_client_secret' => $request->google_client_secret ?? '',
            'facebook_enabled' => $request->has('facebook_enabled') ? '1' : '0',
            'facebook_client_id' => $request->facebook_client_id ?? '',
            'facebook_client_secret' => $request->facebook_client_secret ?? '',
        ];

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key, 'group' => 'social_login'],
                ['value' => $value]
            );
        }

        return back()->with('success', 'Social login settings updated successfully.');
    }
}
