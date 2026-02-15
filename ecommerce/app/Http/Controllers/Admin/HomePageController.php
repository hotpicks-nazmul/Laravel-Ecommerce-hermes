<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HomePageController extends Controller
{
    /**
     * Show the home page settings form.
     */
    public function index()
    {
        $homeSettings = Setting::where('group', 'homepage')->get()->keyBy('key');
        
        return view('admin.homepage.index', compact('homeSettings'));
    }

    /**
     * Update the home page settings.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'homepage_product_columns' => 'required|integer|min:2|max:6',
            'homepage_featured_products_count' => 'required|integer|min:4|max:12',
            'homepage_new_arrivals_count' => 'required|integer|min:4|max:12',
            'homepage_sale_products_count' => 'required|integer|min:4|max:12',
            'homepage_show_featured_section' => 'nullable|in:0,1',
            'homepage_show_new_arrivals_section' => 'nullable|in:0,1',
            'homepage_show_sale_section' => 'nullable|in:0,1',
            'homepage_show_testimonials_section' => 'nullable|in:0,1',
            'homepage_show_blog_section' => 'nullable|in:0,1',
            'homepage_show_banner_section' => 'nullable|in:0,1',
            'homepage_show_why_choose_us_section' => 'nullable|in:0,1',
            'homepage_categories_title' => 'nullable|string|max:255',
            'homepage_categories_subtitle' => 'nullable|string|max:255',
            'homepage_featured_title' => 'nullable|string|max:255',
            'homepage_featured_subtitle' => 'nullable|string|max:255',
            'homepage_new_arrivals_title' => 'nullable|string|max:255',
            'homepage_new_arrivals_subtitle' => 'nullable|string|max:255',
            'homepage_sale_title' => 'nullable|string|max:255',
            'homepage_sale_subtitle' => 'nullable|string|max:255',
            'site_name' => 'nullable|string|max:255',
            'site_tagline' => 'nullable|string|max:255',
            'site_logo' => 'nullable|image|mimes:jpeg,png,jpg,svg,webp|max:2048',
            'site_logo_icon' => 'nullable|string|max:255',
        ]);
        
        $settings = $request->except(['_token', '_method']);
        
        // Handle logo file upload
        if ($request->hasFile('site_logo')) {
            $file = $request->file('site_logo');
            $path = $file->store('logo', 'public');
            $settings['site_logo'] = Storage::url($path);
            
            // Delete old logo if exists
            $oldSetting = Setting::where('key', 'site_logo')->first();
            if ($oldSetting && $oldSetting->value) {
                $oldPath = str_replace('/storage/', '', $oldSetting->value);
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }
        }
        
        foreach ($settings as $key => $value) {
            Setting::set($key, $value ?? '', 'homepage');
        }
        
        return redirect()->route('admin.homepage.index')
            ->with('success', 'Home page settings updated successfully!');
    }
}
