<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AffiliateBanner;
use App\Models\Affiliate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AffiliateBannerController extends Controller
{
    /**
     * Display list of affiliate banners
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $banners = AffiliateBanner::with('affiliate.user')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('admin.affiliate.banners.index', compact('banners'));
    }

    /**
     * Show form to create new affiliate banner
     * 
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $affiliates = Affiliate::with('user')
            ->where('status', 'approved')
            ->get();
        
        return view('admin.affiliate.banners.create', compact('affiliates'));
    }

    /**
     * Store new affiliate banner
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'affiliate_id' => 'nullable|exists:affiliates,id',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'target_url' => 'nullable|url',
            'size' => 'nullable|string',
            'width' => 'nullable|integer',
            'height' => 'nullable|integer',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('affiliate-banners', 'public');
            $validated['image'] = $imagePath;
        }

        AffiliateBanner::create($validated);

        return redirect()->route('admin.affiliate.banners.index')
            ->with('success', 'Affiliate banner created successfully.');
    }

    /**
     * Show form to edit affiliate banner
     * 
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $banner = AffiliateBanner::findOrFail($id);
        $affiliates = Affiliate::with('user')
            ->where('status', 'approved')
            ->get();
        
        return view('admin.affiliate.banners.edit', compact('banner', 'affiliates'));
    }

    /**
     * Update affiliate banner
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $banner = AffiliateBanner::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'affiliate_id' => 'nullable|exists:affiliates,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'target_url' => 'nullable|url',
            'size' => 'nullable|string',
            'width' => 'nullable|integer',
            'height' => 'nullable|integer',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($banner->image) {
                Storage::disk('public')->delete($banner->image);
            }
            $imagePath = $request->file('image')->store('affiliate-banners', 'public');
            $validated['image'] = $imagePath;
        }

        $banner->update($validated);

        return redirect()->route('admin.affiliate.banners.index')
            ->with('success', 'Affiliate banner updated successfully.');
    }

    /**
     * Delete affiliate banner
     * 
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $banner = AffiliateBanner::findOrFail($id);

        // Delete image
        if ($banner->image) {
            Storage::disk('public')->delete($banner->image);
        }

        $banner->delete();

        return redirect()->back()
            ->with('success', 'Affiliate banner deleted successfully.');
    }
}
