<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display products list.
     */
    public function index(Request $request)
    {
        $query = Product::with('category');

        if ($request->search) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        if ($request->category) {
            $query->where('category_id', $request->category);
        }

        $products = $query->latest()->paginate(15);
        $categories = Category::all();

        return view('admin.products.index', compact('products', 'categories'));
    }

    /**
     * Show create form.
     */
    public function create()
    {
        $categories = Category::where('status', 'active')->get();
        return view('admin.products.create', compact('categories'));
    }

    /**
     * Store new product.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'sku' => 'required|string|max:100|unique:products',
            'stock' => 'required|integer|min:0',
            'description' => 'required|string',
            'short_description' => 'nullable|string|max:500',
            'image' => 'nullable|image|max:5120',
            'images.*' => 'nullable|image|max:5120',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ]);

        $data = $request->except(['description', 'stock']);
        $data['slug'] = Str::slug($request->name);
        $data['long_description'] = $request->description;
        $data['quantity'] = $request->stock;

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $data['featured_image'] = Storage::url($path);
        }

        if ($request->hasFile('images')) {
            $images = [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('products', 'public');
                $images[] = Storage::url($path);
            }
            $data['images'] = json_encode($images);
        }

        Product::create($data);

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
    }

    /**
     * Show product details.
     */
    public function show(Product $product)
    {
        return view('admin.products.show', compact('product'));
    }

    /**
     * Show edit form.
     */
    public function edit(Product $product)
    {
        $categories = Category::where('status', 'active')->get();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    /**
     * Update product.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'sku' => 'required|string|max:100|unique:products,sku,' . $product->id,
            'stock' => 'required|integer|min:0',
            'description' => 'required|string',
            'short_description' => 'nullable|string|max:500',
            'image' => 'nullable|image|max:5120',
            'images.*' => 'nullable|image|max:5120',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ]);

        $data = $request->except(['description', 'stock']);
        $data['slug'] = Str::slug($request->name);
        $data['long_description'] = $request->description;
        $data['quantity'] = $request->stock;

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($product->featured_image) {
                $oldPath = str_replace('/storage/', '', $product->featured_image);
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }
            $path = $request->file('image')->store('products', 'public');
            $data['featured_image'] = Storage::url($path);
        }

        if ($request->hasFile('images')) {
            $images = json_decode($product->images ?? '[]', true);
            foreach ($request->file('images') as $image) {
                $path = $image->store('products', 'public');
                $images[] = Storage::url($path);
            }
            $data['images'] = json_encode($images);
        }

        $product->update($data);

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
    }

    /**
     * Delete product.
     */
    public function destroy(Product $product)
    {
        // Delete main image
        if ($product->featured_image) {
            $oldPath = str_replace('/storage/', '', $product->featured_image);
            if (Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }
        }
        
        // Delete gallery images
        $images = json_decode($product->images ?? '[]', true);
        foreach ($images as $image) {
            $oldPath = str_replace('/storage/', '', $image);
            if (Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }
        }
        
        $product->delete();
        return back()->with('success', 'Product deleted successfully.');
    }

    /**
     * Bulk action.
     */
    public function bulkAction(Request $request)
    {
        $action = $request->action;
        $ids = $request->ids;

        if ($action === 'delete') {
            Product::whereIn('id', $ids)->delete();
        } elseif ($action === 'activate') {
            Product::whereIn('id', $ids)->update(['is_active' => true]);
        } elseif ($action === 'deactivate') {
            Product::whereIn('id', $ids)->update(['is_active' => false]);
        }

        return back()->with('success', 'Bulk action completed.');
    }

    /**
     * Duplicate product.
     */
    public function duplicate(Product $product)
    {
        $newProduct = $product->replicate();
        $newProduct->name = $product->name . ' (Copy)';
        $newProduct->slug = Str::slug($newProduct->name);
        $newProduct->sku = $product->sku . '-copy';
        $newProduct->save();

        return back()->with('success', 'Product duplicated successfully.');
    }

    /**
     * Upload additional images.
     */
    public function uploadImages(Request $request, Product $product)
    {
        $request->validate([
            'images.*' => 'required|image|max:5120',
        ]);

        $images = json_decode($product->images ?? '[]', true);

        foreach ($request->file('images') as $image) {
            $path = $image->store('products', 'public');
            $images[] = Storage::url($path);
        }

        $product->update(['images' => json_encode($images)]);

        return back()->with('success', 'Images uploaded successfully.');
    }

    /**
     * Delete product image.
     */
    public function deleteImage(Request $request, Product $product, $index)
    {
        $images = json_decode($product->images ?? '[]', true);
        
        if (isset($images[$index])) {
            // Delete file from storage
            $oldPath = str_replace('/storage/', '', $images[$index]);
            if (Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }
            
            unset($images[$index]);
            $product->update(['images' => json_encode(array_values($images))]);
        }

        return back()->with('success', 'Image deleted successfully.');
    }
}
