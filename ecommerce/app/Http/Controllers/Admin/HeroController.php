<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\Slider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HeroController extends Controller
{
    /**
     * Show the hero settings form.
     */
    public function index()
    {
        $heroSettings = Setting::where('group', 'hero')->get()->keyBy('key');
        $sliders = Slider::where('is_active', true)->orderBy('order')->get();
        
        return view('admin.hero.index', compact('heroSettings', 'sliders'));
    }

    /**
     * Update the hero settings.
     */
    public function update(Request $request)
    {
        $settings = $request->except('_token');
        
        foreach ($settings as $key => $value) {
            if ($request->hasFile($key)) {
                // Handle file upload
                $file = $request->file($key);
                $path = $file->store('hero', 'public');
                $value = Storage::url($path);
                
                // Delete old image if exists
                $oldSetting = Setting::where('key', $key)->first();
                if ($oldSetting && $oldSetting->value) {
                    $oldPath = str_replace('/storage/', '', $oldSetting->value);
                    if (Storage::disk('public')->exists($oldPath)) {
                        Storage::disk('public')->delete($oldPath);
                    }
                }
            }
            
            Setting::set($key, $value, 'hero');
        }
        
        return redirect()->route('admin.hero.index')
            ->with('success', 'Hero settings updated successfully!');
    }

    /**
     * Update hero type setting.
     */
    public function updateType(Request $request)
    {
        $request->validate([
            'hero_type' => 'required|in:standard,slider',
        ]);
        
        Setting::set('hero_type', $request->hero_type, 'hero');
        
        return redirect()->route('admin.hero.index')
            ->with('success', 'Hero type updated successfully!');
    }
}