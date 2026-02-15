<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
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
        
        return view('admin.hero.index', compact('heroSettings'));
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
}