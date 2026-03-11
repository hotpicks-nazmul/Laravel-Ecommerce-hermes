<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;
use Spatie\Sitemap\SitemapGenerator;

class SeoController extends Controller
{
    public function index()
    {
        $settings = Setting::whereIn('key', [
            'site_meta_title', 'site_meta_description', 'site_meta_keywords',
            'google_analytics_id', 'google_search_console_code', 'facebook_pixel_id',
            'og_title', 'og_description', 'og_image',
            'twitter_card_type',
        ])->pluck('value', 'key');

        $redirects = json_decode(Setting::get('seo_redirects', '[]'), true);
        
        return view('admin.seo.index', compact('settings', 'redirects'));
    }

    public function updateMeta(Request $request)
    {
        foreach ($request->except('_token') as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value, 'group' => 'seo']);
        }

        return back()->with('success', 'SEO settings updated successfully.');
    }

    public function generateSitemap()
    {
        try {
            SitemapGenerator::create(config('app.url'))
                ->writeToFile(storage_path('app/sitemap.xml'));

            return back()->with('success', 'Sitemap generated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to generate sitemap: ' . $e->getMessage());
        }
    }

    public function redirects()
    {
        $redirects = json_decode(Setting::get('seo_redirects', '[]'), true);
        return view('admin.seo.index', compact('redirects'));
    }

    public function storeRedirect(Request $request)
    {
        $request->validate([
            'from' => 'required|string',
            'to' => 'required|string',
            'type' => 'required|in:301,302',
        ]);

        $redirects = json_decode(Setting::get('seo_redirects', '[]'), true);
        $redirects[] = [
            'from' => $request->from,
            'to' => $request->to,
            'type' => $request->type,
        ];

        Setting::updateOrCreate(['key' => 'seo_redirects'], ['value' => json_encode($redirects), 'group' => 'seo']);

        return back()->with('success', 'Redirect added successfully.');
    }

    public function deleteRedirect($index)
    {
        $redirects = json_decode(Setting::get('seo_redirects', '[]'), true);
        
        if (isset($redirects[$index])) {
            unset($redirects[$index]);
            Setting::updateOrCreate(['key' => 'seo_redirects'], ['value' => json_encode(array_values($redirects)), 'group' => 'seo']);
        }

        return back()->with('success', 'Redirect deleted successfully.');
    }
}
