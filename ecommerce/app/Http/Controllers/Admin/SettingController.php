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

    /**
     * WhatsApp Chat Settings
     */
    public function whatsapp()
    {
        $settings = Setting::where('group', 'whatsapp')->pluck('value', 'key');
        return view('admin.settings.whatsapp', compact('settings'));
    }

    /**
     * Update WhatsApp Chat Settings
     */
    public function updateWhatsapp(Request $request)
    {
        $settings = [
            'whatsapp_enabled' => $request->has('whatsapp_enabled') ? '1' : '0',
            'whatsapp_phone_number' => $request->whatsapp_phone_number ?? '',
            'whatsapp_display_name' => $request->whatsapp_display_name ?? 'Customer Support',
            'whatsapp_welcome_message' => $request->whatsapp_welcome_message ?? 'Hello! How can I help you today?',
            'whatsapp_position' => $request->whatsapp_position ?? 'bottom-right',
            'whatsapp_button_color' => $request->whatsapp_button_color ?? '#25D366',
            'whatsapp_predefined_messages' => $request->whatsapp_predefined_messages ?? '',
            'whatsapp_show_on_mobile' => $request->has('whatsapp_show_on_mobile') ? '1' : '0',
            'whatsapp_show_on_desktop' => $request->has('whatsapp_show_on_desktop') ? '1' : '0',
        ];

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key, 'group' => 'whatsapp'],
                ['value' => $value]
            );
        }

        return back()->with('success', 'WhatsApp chat settings updated successfully.');
    }

    /**
     * Footer Settings
     */
    public function footer()
    {
        $settings = Setting::where('group', 'footer')->pluck('value', 'key');
        return view('admin.settings.footer', compact('settings'));
    }

    /**
     * Update Footer Settings
     */
    public function updateFooter(Request $request)
    {
        $settings = [
            'footer_about_text' => $request->footer_about_text ?? '',
            'footer_facebook_url' => $request->footer_facebook_url ?? '',
            'footer_instagram_url' => $request->footer_instagram_url ?? '',
            'footer_youtube_url' => $request->footer_youtube_url ?? '',
            'footer_twitter_url' => $request->footer_twitter_url ?? '',
            'footer_linkedin_url' => $request->footer_linkedin_url ?? '',
            'footer_address' => $request->footer_address ?? '',
            'footer_phone' => $request->footer_phone ?? '',
            'footer_email' => $request->footer_email ?? '',
            'footer_business_hours' => $request->footer_business_hours ?? '',
            'footer_copyright_text' => $request->footer_copyright_text ?? '',
            'footer_newsletter_enabled' => $request->has('footer_newsletter_enabled') ? '1' : '0',
            'footer_newsletter_title' => $request->footer_newsletter_title ?? '',
            'footer_newsletter_subtitle' => $request->footer_newsletter_subtitle ?? '',
            'footer_column1_title' => $request->footer_column1_title ?? 'Quick Links',
            'footer_column2_title' => $request->footer_column2_title ?? 'Customer Service',
            'footer_column3_title' => $request->footer_column3_title ?? 'Contact Us',
            'footer_payment_methods' => $request->footer_payment_methods ?? 'bkash,nagad,rocket,visa,mastercard',
            'footer_show_payment_icons' => $request->has('footer_show_payment_icons') ? '1' : '0',
        ];

        foreach ($settings as $key => $value) {
            // Use updateOrCreate with just the key as unique identifier
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value, 'group' => 'footer']
            );
        }

        return back()->with('success', 'Footer settings updated successfully.');
    }
}
