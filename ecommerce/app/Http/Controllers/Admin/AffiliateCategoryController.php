<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AffiliateCategory;
use App\Models\AffiliateProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class AffiliateCategoryController extends Controller
{
    /**
     * Display list of affiliate categories
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $categories = AffiliateCategory::withCount('products')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('admin.affiliate.categories.index', compact('categories'));
    }

    /**
     * Show form to create new affiliate category
     * 
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.affiliate.categories.create');
    }

    /**
     * Store new affiliate category
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:affiliate_categories,slug',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'commission_rate' => 'required|numeric|min:0|max:100',
            'status' => 'required|in:active,inactive',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('affiliate-categories', 'public');
            $validated['image'] = $imagePath;
        }

        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        AffiliateCategory::create($validated);

        return redirect()->route('admin.affiliate.categories.index')
            ->with('success', 'Affiliate category created successfully.');
    }

    /**
     * Show form to edit affiliate category
     * 
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $category = AffiliateCategory::findOrFail($id);
        
        return view('admin.affiliate.categories.edit', compact('category'));
    }

    /**
     * Update affiliate category
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $category = AffiliateCategory::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:affiliate_categories,slug,' . $id,
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'commission_rate' => 'required|numeric|min:0|max:100',
            'status' => 'required|in:active,inactive',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }
            $imagePath = $request->file('image')->store('affiliate-categories', 'public');
            $validated['image'] = $imagePath;
        }

        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $category->update($validated);

        return redirect()->route('admin.affiliate.categories.index')
            ->with('success', 'Affiliate category updated successfully.');
    }

    /**
     * Delete affiliate category
     * 
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $category = AffiliateCategory::findOrFail($id);
        
        // Check if category has products
        if ($category->products()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete category. It has associated products.');
        }

        // Delete image
        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }

        $category->delete();

        return redirect()->back()
            ->with('success', 'Affiliate category deleted successfully.');
    }
}
