<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AffiliateProduct;
use App\Models\AffiliateCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class AffiliateProductController extends Controller
{
    /**
     * Display list of affiliate products
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $products = AffiliateProduct::with('category')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('admin.affiliate.products.index', compact('products'));
    }

    /**
     * Show form to create new affiliate product
     * 
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $categories = AffiliateCategory::where('status', 'active')->get();
        
        return view('admin.affiliate.products.create', compact('categories'));
    }

    /**
     * Store new affiliate product
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:affiliate_categories,id',
            'slug' => 'nullable|string|max:255|unique:affiliate_products,slug',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'price' => 'required|numeric|min:0',
            'commission_rate' => 'required|numeric|min:0|max:100',
            'external_url' => 'required|url',
            'status' => 'required|in:active,inactive',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('affiliate-products', 'public');
            $validated['image'] = $imagePath;
        }

        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        AffiliateProduct::create($validated);

        return redirect()->route('admin.affiliate.products.index')
            ->with('success', 'Affiliate product created successfully.');
    }

    /**
     * Display affiliate product details
     * 
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $product = AffiliateProduct::with('category')->findOrFail($id);
        
        return view('admin.affiliate.products.show', compact('product'));
    }

    /**
     * Show form to edit affiliate product
     * 
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $product = AffiliateProduct::findOrFail($id);
        $categories = AffiliateCategory::where('status', 'active')->get();
        
        return view('admin.affiliate.products.edit', compact('product', 'categories'));
    }

    /**
     * Update affiliate product
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $product = AffiliateProduct::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:affiliate_categories,id',
            'slug' => 'nullable|string|max:255|unique:affiliate_products,slug,' . $id,
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'price' => 'required|numeric|min:0',
            'commission_rate' => 'required|numeric|min:0|max:100',
            'external_url' => 'required|url',
            'status' => 'required|in:active,inactive',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $imagePath = $request->file('image')->store('affiliate-products', 'public');
            $validated['image'] = $imagePath;
        }

        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $product->update($validated);

        return redirect()->route('admin.affiliate.products.index')
            ->with('success', 'Affiliate product updated successfully.');
    }

    /**
     * Delete affiliate product
     * 
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $product = AffiliateProduct::findOrFail($id);

        // Delete image
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->back()
            ->with('success', 'Affiliate product deleted successfully.');
    }
}
