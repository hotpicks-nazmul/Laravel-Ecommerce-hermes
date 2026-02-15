<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ThemeService;

class ThemeController extends Controller
{
    protected ThemeService $themeService;

    public function __construct(ThemeService $themeService)
    {
        $this->themeService = $themeService;
    }

    public function index()
    {
        $themes = $this->themeService->getAvailableThemes();
        $activeTheme = $this->themeService->getActiveTheme();
        
        return view('admin.theme.index', compact('themes', 'activeTheme'));
    }

    public function activate(Request $request)
    {
        $request->validate([
            'theme' => 'required|string',
        ]);

        $this->themeService->activateTheme($request->theme);

        return back()->with('success', 'Theme activated successfully.');
    }

    public function settings()
    {
        $theme = $this->themeService->getActiveTheme();
        $settings = $this->themeService->getThemeSettings();
        
        return view('admin.theme.settings', compact('theme', 'settings'));
    }

    public function updateSettings(Request $request)
    {
        $this->themeService->updateThemeSettings($request->except('_token'));

        return back()->with('success', 'Theme settings updated successfully.');
    }

    public function reset()
    {
        $this->themeService->resetThemeSettings();

        return back()->with('success', 'Theme settings reset to default.');
    }
}
