<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display products list with advanced filtering.
     */
    public function index(Request $request)
    {
        $query = Product::with('category');

        // Search by name, SKU, Product Code, or description
        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('product_code', 'like', "%{$search}%")
                    ->orWhere('short_description', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->category) {
            $query->where('category_id', $request->category);
        }

        // Filter by status
        if ($request->status !== null && $request->status !== '') {
            $query->where('is_active', $request->status === 'active');
        }

        // Filter by stock status
        if ($request->stock_status) {
            switch ($request->stock_status) {
                case 'in_stock':
                    $query->where('quantity', '>', 10);
                    break;
                case 'low_stock':
                    $query->whereBetween('quantity', [1, 10]);
                    break;
                case 'out_of_stock':
                    $query->where('quantity', '<=', 0);
                    break;
            }
        }

        // Filter by featured status
        if ($request->featured !== null && $request->featured !== '') {
            $query->where('is_featured', $request->featured === 'yes');
        }

        // Filter by price range
        if ($request->price_min) {
            $query->where('price', '>=', $request->price_min);
        }
        if ($request->price_max) {
            $query->where('price', '<=', $request->price_max);
        }

        // Filter by date range
        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Sorting
        $sortField = $request->sort ?? 'created_at';
        $sortDirection = $request->direction ?? 'desc';
        
        $allowedSorts = ['name', 'price', 'quantity', 'created_at', 'sale_price'];
        if (in_array($sortField, $allowedSorts)) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->latest();
        }

        // Per page
        $perPage = $request->per_page ?? 25;

        $products = $query->paginate($perPage)->appends($request->query());
        $categories = Category::where('status', 'active')->get();

        // Statistics for cards
        $stats = [
            'total' => Product::count(),
            'active' => Product::where('is_active', true)->count(),
            'inactive' => Product::where('is_active', false)->count(),
            'featured' => Product::where('is_featured', true)->count(),
            'low_stock' => Product::whereBetween('quantity', [1, 10])->count(),
            'out_of_stock' => Product::where('quantity', '<=', 0)->count(),
        ];

        // Return JSON for AJAX requests
        if ($request->ajax || $request->ajax == '1' || $request->wantsJson()) {
            $html = view('admin.products.partials.product-rows', compact('products'))->render();
            
            $pagination = '';
            if ($products->hasPages()) {
                $pagination = '<div class="d-flex justify-content-center mt-3">' . $products->links()->toHtml() . '</div>';
            }
            
            return response()->json([
                'html' => $html,
                'pagination' => $pagination,
                'stats' => $stats,
                'total' => $products->total()
            ]);
        }

        return view('admin.products.index', compact('products', 'categories', 'stats'));
    }

    /**
     * Display In-House Products with advanced management features.
     * In-House products are products sold directly by the store (not by sellers).
     */
    public function inHouse(Request $request)
    {
        $query = Product::with('category')->inHouse();

        // Search functionality
        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('product_code', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%")
                    ->orWhere('short_description', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->category) {
            $query->where('category_id', $request->category);
        }

        // Filter by brand
        if ($request->brand) {
            $query->where('brand', $request->brand);
        }

        // Filter by status
        if ($request->status !== null && $request->status !== '') {
            $query->where('is_active', $request->status === 'active');
        }

        // Filter by stock status
        if ($request->stock_status) {
            switch ($request->stock_status) {
                case 'in_stock':
                    $query->whereColumn('quantity', '>', 'low_stock_threshold');
                    break;
                case 'low_stock':
                    $query->whereColumn('quantity', '<=', 'low_stock_threshold')
                          ->where('quantity', '>', 0);
                    break;
                case 'out_of_stock':
                    $query->where('quantity', '<=', 0);
                    break;
            }
        }

        // Filter by featured status
        if ($request->featured !== null && $request->featured !== '') {
            $query->where('is_featured', $request->featured === 'yes');
        }

        // Filter by price range
        if ($request->price_min) {
            $query->where('price', '>=', $request->price_min);
        }
        if ($request->price_max) {
            $query->where('price', '<=', $request->price_max);
        }

        // Filter by date range
        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Sorting
        $sortField = $request->sort ?? 'created_at';
        $sortDirection = $request->direction ?? 'desc';
        
        $allowedSorts = ['name', 'price', 'quantity', 'created_at', 'sale_price', 'purchase_price', 'low_stock_threshold'];
        if (in_array($sortField, $allowedSorts)) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->latest();
        }

        // Per page
        $perPage = $request->per_page ?? 25;

        $products = $query->paginate($perPage)->appends($request->query());
        $categories = Category::where('status', 'active')->get();
        
        // Get unique brands for filter
        $brands = Product::inHouse()
            ->whereNotNull('brand')
            ->distinct()
            ->pluck('brand')
            ->sort()
            ->values();

        // Statistics for In-House products
        $stats = [
            'total' => Product::inHouse()->count(),
            'active' => Product::inHouse()->where('is_active', true)->count(),
            'inactive' => Product::inHouse()->where('is_active', false)->count(),
            'featured' => Product::inHouse()->where('is_featured', true)->count(),
            'low_stock' => Product::inHouse()
                ->whereColumn('quantity', '<=', 'low_stock_threshold')
                ->where('quantity', '>', 0)
                ->count(),
            'out_of_stock' => Product::inHouse()->where('quantity', '<=', 0)->count(),
            'total_stock_value' => Product::inHouse()->sum(DB::raw('quantity * COALESCE(purchase_price, cost_price, 0)')),
            'total_retail_value' => Product::inHouse()->sum(DB::raw('quantity * COALESCE(sale_price, price, 0)')),
            'total_quantity' => Product::inHouse()->sum('quantity'),
        ];

        // Return JSON for AJAX requests
        if ($request->ajax || $request->ajax == '1' || $request->wantsJson()) {
            $html = view('admin.products.partials.in-house-product-rows', compact('products'))->render();
            
            $pagination = '';
            if ($products->hasPages()) {
                $pagination = '<div class="d-flex justify-content-center mt-3">' . $products->links()->toHtml() . '</div>';
            }
            
            return response()->json([
                'html' => $html,
                'pagination' => $pagination,
                'stats' => $stats,
                'total' => $products->total()
            ]);
        }

        return view('admin.products.in-house', compact('products', 'categories', 'brands', 'stats'));
    }

    /**
     * Show create form.
     */
    public function create(Request $request)
    {
        $categories = Category::getFlattenedTree();
        $preselectedCategory = $request->get('category_id');
        return view('admin.products.create', compact('categories', 'preselectedCategory'));
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
            'purchase_price' => 'nullable|numeric|min:0',
            'sku' => 'required|string|max:100|unique:products',
            'product_code' => 'nullable|string|max:100',
            'barcode' => 'nullable|string|max:100|unique:products,barcode',
            'brand' => 'nullable|string|max:100',
            'stock' => 'required|integer|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0',
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
        
        // Set as in-house product by default
        $data['product_source'] = 'in_house';
        $data['low_stock_threshold'] = $request->low_stock_threshold ?? 10;
        $data['stock_update_date'] = now();

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
        $categories = Category::getFlattenedTree();
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
            'purchase_price' => 'nullable|numeric|min:0',
            'sku' => 'required|string|max:100|unique:products,sku,' . $product->id,
            'product_code' => 'nullable|string|max:100',
            'barcode' => 'nullable|string|max:100|unique:products,barcode,' . $product->id,
            'brand' => 'nullable|string|max:100',
            'stock' => 'required|integer|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0',
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
        $data['low_stock_threshold'] = $request->low_stock_threshold ?? $product->low_stock_threshold ?? 10;
        
        // Track stock updates
        if ($request->stock != $product->quantity) {
            $data['stock_update_date'] = now();
        }

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
     * Bulk action with enhanced options.
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|string',
            'ids' => 'required|array',
            'ids.*' => 'exists:products,id',
        ]);

        $action = $request->action;
        $ids = $request->ids;
        $count = count($ids);

        switch ($action) {
            case 'delete':
                // Delete images for each product
                $products = Product::whereIn('id', $ids)->get();
                foreach ($products as $product) {
                    if ($product->featured_image) {
                        $oldPath = str_replace('/storage/', '', $product->featured_image);
                        if (Storage::disk('public')->exists($oldPath)) {
                            Storage::disk('public')->delete($oldPath);
                        }
                    }
                    $images = json_decode($product->images ?? '[]', true);
                    foreach ($images as $image) {
                        $oldPath = str_replace('/storage/', '', $image);
                        if (Storage::disk('public')->exists($oldPath)) {
                            Storage::disk('public')->delete($oldPath);
                        }
                    }
                }
                Product::whereIn('id', $ids)->delete();
                $message = "{$count} product(s) deleted successfully.";
                break;

            case 'activate':
                Product::whereIn('id', $ids)->update(['is_active' => true]);
                $message = "{$count} product(s) activated successfully.";
                break;

            case 'deactivate':
                Product::whereIn('id', $ids)->update(['is_active' => false]);
                $message = "{$count} product(s) deactivated successfully.";
                break;

            case 'feature':
                Product::whereIn('id', $ids)->update(['is_featured' => true]);
                $message = "{$count} product(s) marked as featured.";
                break;

            case 'unfeature':
                Product::whereIn('id', $ids)->update(['is_featured' => false]);
                $message = "{$count} product(s) removed from featured.";
                break;

            case 'duplicate':
                $products = Product::whereIn('id', $ids)->get();
                foreach ($products as $product) {
                    $newProduct = $product->replicate();
                    $newProduct->name = $product->name . ' (Copy)';
                    $newProduct->slug = Str::slug($newProduct->name) . '-' . Str::random(5);
                    $newProduct->sku = $product->sku . '-copy-' . Str::random(5);
                    $newProduct->is_active = false;
                    $newProduct->save();
                }
                $message = "{$count} product(s) duplicated successfully.";
                break;

            default:
                return back()->with('error', 'Invalid action selected.');
        }

        return back()->with('success', $message);
    }

    /**
     * Toggle product status (AJAX).
     */
    public function toggleStatus(Request $request, Product $product)
    {
        $product->update(['is_active' => !$product->is_active]);
        
        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully.',
            'is_active' => $product->is_active
        ]);
    }

    /**
     * Toggle featured status (AJAX).
     */
    public function toggleFeatured(Request $request, Product $product)
    {
        $product->update(['is_featured' => !$product->is_featured]);
        
        return response()->json([
            'success' => true,
            'message' => 'Featured status updated.',
            'is_featured' => $product->is_featured
        ]);
    }

    /**
     * Quick update (AJAX).
     */
    public function quickUpdate(Request $request, Product $product)
    {
        $request->validate([
            'field' => 'required|in:price,sale_price,quantity,name,category_id,product_code,short_description',
            'value' => 'required',
        ]);

        $field = $request->field;
        $value = $request->value;

        if ($field === 'price' || $field === 'sale_price') {
            $value = (float) $value;
        } elseif ($field === 'quantity') {
            $value = (int) $value;
        }

        $product->update([$field => $value]);

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully.',
            'product' => $product->fresh()
        ]);
    }

    /**
     * Duplicate product.
     */
    public function duplicate(Product $product)
    {
        $newProduct = $product->replicate();
        $newProduct->name = $product->name . ' (Copy)';
        $newProduct->slug = Str::slug($newProduct->name) . '-' . Str::random(5);
        $newProduct->sku = $product->sku . '-copy-' . Str::random(5);
        $newProduct->is_active = false;
        $newProduct->save();

        return back()->with('success', 'Product duplicated successfully.');
    }

    /**
     * Export products to CSV.
     */
    public function export(Request $request)
    {
        $query = Product::with('category');

        // Apply same filters as index
        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($request->category) {
            $query->where('category_id', $request->category);
        }

        if ($request->status !== null && $request->status !== '') {
            $query->where('is_active', $request->status === 'active');
        }

        $products = $query->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="products-' . date('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($products) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Name', 'SKU', 'Category', 'Price', 'Sale Price', 'Quantity', 'Status', 'Featured', 'Created At']);

            foreach ($products as $product) {
                fputcsv($file, [
                    $product->id,
                    $product->name,
                    $product->sku,
                    $product->category->name ?? 'N/A',
                    $product->price,
                    $product->sale_price ?? '',
                    $product->quantity,
                    $product->is_active ? 'Active' : 'Inactive',
                    $product->is_featured ? 'Yes' : 'No',
                    $product->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
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

    /**
     * Update stock quantity for in-house product.
     */
    public function updateStock(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => 'required|integer',
            'operation' => 'required|in:set,add,subtract',
        ]);

        $operation = $request->operation;
        $quantity = (int) $request->quantity;

        switch ($operation) {
            case 'set':
                $product->quantity = $quantity;
                break;
            case 'add':
                $product->quantity += $quantity;
                break;
            case 'subtract':
                $product->quantity = max(0, $product->quantity - $quantity);
                break;
        }

        $product->stock_update_date = now();
        $product->save();

        return response()->json([
            'success' => true,
            'message' => 'Stock updated successfully.',
            'quantity' => $product->quantity,
            'stock_status' => $product->isOutOfStock() ? 'out_of_stock' : ($product->isLowStock() ? 'low_stock' : 'in_stock'),
        ]);
    }

    /**
     * Bulk stock update for in-house products.
     */
    public function bulkStockUpdate(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:products,id',
            'operation' => 'required|in:set,add,subtract',
            'quantity' => 'required|integer|min:0',
        ]);

        $ids = $request->ids;
        $operation = $request->operation;
        $quantity = (int) $request->quantity;

        $products = Product::whereIn('id', $ids)->get();

        foreach ($products as $product) {
            switch ($operation) {
                case 'set':
                    $product->quantity = $quantity;
                    break;
                case 'add':
                    $product->quantity += $quantity;
                    break;
                case 'subtract':
                    $product->quantity = max(0, $product->quantity - $quantity);
                    break;
            }
            $product->stock_update_date = now();
            $product->save();
        }

        return response()->json([
            'success' => true,
            'message' => count($ids) . ' product(s) stock updated successfully.',
        ]);
    }

    /**
     * Update low stock threshold.
     */
    public function updateLowStockThreshold(Request $request, Product $product)
    {
        $request->validate([
            'threshold' => 'required|integer|min:0',
        ]);

        $product->low_stock_threshold = (int) $request->threshold;
        $product->save();

        return response()->json([
            'success' => true,
            'message' => 'Low stock threshold updated.',
            'threshold' => $product->low_stock_threshold,
        ]);
    }

    /**
     * Get low stock products for alerts.
     */
    public function lowStockAlerts(Request $request)
    {
        $products = Product::with('category')
            ->inHouse()
            ->where(function ($query) {
                $query->whereColumn('quantity', '<=', 'low_stock_threshold')
                      ->where('quantity', '>', 0);
            })
            ->orWhere('quantity', '<=', 0)
            ->orderBy('quantity', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'products' => $products,
            'count' => $products->count(),
        ]);
    }

    /**
     * Export in-house products to CSV.
     */
    public function exportInHouse(Request $request)
    {
        $query = Product::with('category')->inHouse();

        // Apply same filters as inHouse method
        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        if ($request->category) {
            $query->where('category_id', $request->category);
        }

        $products = $query->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="in-house-products-' . date('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($products) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Name', 'SKU', 'Barcode', 'Category', 'Brand', 'Purchase Price', 'Price', 'Sale Price', 'Quantity', 'Low Stock Threshold', 'Stock Value', 'Status', 'Featured', 'Created At']);

            foreach ($products as $product) {
                fputcsv($file, [
                    $product->id,
                    $product->name,
                    $product->sku,
                    $product->barcode ?? '',
                    $product->category->name ?? 'N/A',
                    $product->brand ?? '',
                    $product->purchase_price ?? $product->cost_price ?? '',
                    $product->price,
                    $product->sale_price ?? '',
                    $product->quantity,
                    $product->low_stock_threshold,
                    $product->stock_value,
                    $product->is_active ? 'Active' : 'Inactive',
                    $product->is_featured ? 'Yes' : 'No',
                    $product->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Bulk action for in-house products only.
     */
    public function inHouseBulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|string',
            'ids' => 'required|array',
            'ids.*' => 'exists:products,id',
        ]);

        $action = $request->action;
        $ids = $request->ids;
        
        // Ensure only in-house products are affected
        $products = Product::whereIn('id', $ids)->inHouse()->get();
        $count = $products->count();

        if ($count === 0) {
            return back()->with('error', 'No in-house products selected.');
        }

        $productIds = $products->pluck('id')->toArray();

        switch ($action) {
            case 'delete':
                foreach ($products as $product) {
                    if ($product->featured_image) {
                        $oldPath = str_replace('/storage/', '', $product->featured_image);
                        if (Storage::disk('public')->exists($oldPath)) {
                            Storage::disk('public')->delete($oldPath);
                        }
                    }
                    $images = json_decode($product->images ?? '[]', true);
                    foreach ($images as $image) {
                        $oldPath = str_replace('/storage/', '', $image);
                        if (Storage::disk('public')->exists($oldPath)) {
                            Storage::disk('public')->delete($oldPath);
                        }
                    }
                }
                Product::whereIn('id', $productIds)->delete();
                $message = "{$count} in-house product(s) deleted successfully.";
                break;

            case 'activate':
                Product::whereIn('id', $productIds)->update(['is_active' => true]);
                $message = "{$count} in-house product(s) activated successfully.";
                break;

            case 'deactivate':
                Product::whereIn('id', $productIds)->update(['is_active' => false]);
                $message = "{$count} in-house product(s) deactivated successfully.";
                break;

            case 'feature':
                Product::whereIn('id', $productIds)->update(['is_featured' => true]);
                $message = "{$count} in-house product(s) marked as featured.";
                break;

            case 'unfeature':
                Product::whereIn('id', $productIds)->update(['is_featured' => false]);
                $message = "{$count} in-house product(s) removed from featured.";
                break;

            case 'duplicate':
                foreach ($products as $product) {
                    $newProduct = $product->replicate();
                    $newProduct->name = $product->name . ' (Copy)';
                    $newProduct->slug = Str::slug($newProduct->name) . '-' . Str::random(5);
                    $newProduct->sku = $product->sku . '-copy-' . Str::random(5);
                    $newProduct->is_active = false;
                    $newProduct->save();
                }
                $message = "{$count} in-house product(s) duplicated successfully.";
                break;

            default:
                return back()->with('error', 'Invalid action selected.');
        }

        return back()->with('success', $message);
    }

    /**
     * Display bulk import page.
     */
    public function bulkImport()
    {
        $categories = Category::where('status', 'active')->get();
        return view('admin.products.bulk-import', compact('categories'));
    }

    /**
     * Process bulk import from CSV/Excel file.
     */
    public function processBulkImport(Request $request)
    {
        $request->validate([
            'import_file' => 'required|file|mimes:csv,txt,xlsx,xls|max:10240',
            'default_category' => 'nullable|exists:categories,id',
            'skip_duplicates' => 'nullable|boolean',
            'update_existing' => 'nullable|boolean',
        ]);

        $file = $request->file('import_file');
        $defaultCategory = $request->default_category;
        $skipDuplicates = $request->boolean('skip_duplicates');
        $updateExisting = $request->boolean('update_existing');

        $path = $file->getRealPath();
        $extension = $file->getClientOriginalExtension();

        $imported = 0;
        $updated = 0;
        $skipped = 0;
        $errors = [];

        try {
            if ($extension === 'csv' || $extension === 'txt') {
                $data = array_map('str_getcsv', file($path));
            } else {
                // For Excel files, use a simple approach
                $data = $this->parseExcelFile($path);
            }

            // Remove header row
            $headers = array_shift($data);

            // Normalize headers
            $headers = array_map(function ($header) {
                return strtolower(trim(str_replace([' ', '-'], '_', $header)));
            }, $headers);

            foreach ($data as $rowIndex => $row) {
                $rowNumber = $rowIndex + 2; // Account for header row

                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }

                // Map row data to headers
                $rowData = array_combine($headers, $row);

                // Validate required fields
                if (empty($rowData['name']) && empty($rowData['product_name'])) {
                    $errors[] = "Row {$rowNumber}: Product name is required.";
                    $skipped++;
                    continue;
                }

                $name = $rowData['name'] ?? $rowData['product_name'] ?? null;
                $sku = $rowData['sku'] ?? $rowData['product_sku'] ?? null;

                // Check for duplicates
                $existingProduct = null;
                if ($sku) {
                    $existingProduct = Product::where('sku', $sku)->first();
                }

                if ($existingProduct && $skipDuplicates) {
                    $skipped++;
                    continue;
                }

                // Prepare product data
                $productData = [
                    'name' => $name,
                    'slug' => Str::slug($name) . '-' . Str::random(5),
                    'sku' => $sku ?: 'SKU-' . Str::random(8),
                    'product_code' => $rowData['product_code'] ?? $rowData['code'] ?? null,
                    'barcode' => $rowData['barcode'] ?? null,
                    'brand' => $rowData['brand'] ?? null,
                    'short_description' => $rowData['short_description'] ?? $rowData['summary'] ?? null,
                    'long_description' => $rowData['long_description'] ?? $rowData['description'] ?? null,
                    'price' => floatval($rowData['price'] ?? $rowData['regular_price'] ?? 0),
                    'sale_price' => !empty($rowData['sale_price']) ? floatval($rowData['sale_price']) : null,
                    'cost_price' => floatval($rowData['cost_price'] ?? $rowData['cost'] ?? 0),
                    'purchase_price' => floatval($rowData['purchase_price'] ?? 0),
                    'quantity' => intval($rowData['quantity'] ?? $rowData['stock'] ?? $rowData['qty'] ?? 0),
                    'low_stock_threshold' => intval($rowData['low_stock_threshold'] ?? $rowData['min_stock'] ?? 10),
                    'weight' => floatval($rowData['weight'] ?? 0),
                    'is_active' => isset($rowData['status']) ? strtolower($rowData['status']) === 'active' : true,
                    'is_featured' => isset($rowData['featured']) ? in_array(strtolower($rowData['featured']), ['yes', '1', 'true']) : false,
                    'meta_title' => $rowData['meta_title'] ?? null,
                    'meta_description' => $rowData['meta_description'] ?? null,
                    'meta_keywords' => $rowData['meta_keywords'] ?? null,
                    'product_source' => 'in_house',
                ];

                // Handle category
                if (!empty($rowData['category'])) {
                    $category = Category::where('name', $rowData['category'])->first();
                    if ($category) {
                        $productData['category_id'] = $category->id;
                    } elseif ($defaultCategory) {
                        $productData['category_id'] = $defaultCategory;
                    }
                } elseif ($defaultCategory) {
                    $productData['category_id'] = $defaultCategory;
                }

                // Handle tags
                if (!empty($rowData['tags'])) {
                    $productData['tags'] = array_map('trim', explode(',', $rowData['tags']));
                }

                if ($existingProduct && $updateExisting) {
                    $existingProduct->update($productData);
                    $updated++;
                } else {
                    Product::create($productData);
                    $imported++;
                }
            }

            $message = "Import completed: {$imported} products imported";
            if ($updated > 0) {
                $message .= ", {$updated} updated";
            }
            if ($skipped > 0) {
                $message .= ", {$skipped} skipped";
            }

            return back()->with('success', $message)
                        ->with('import_errors', $errors);

        } catch (\Exception $e) {
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    /**
     * Parse Excel file (simple implementation).
     */
    private function parseExcelFile($path)
    {
        // Simple CSV fallback for Excel - in production, use PhpSpreadsheet
        $data = [];
        if (($handle = fopen($path, 'r')) !== false) {
            while (($row = fgetcsv($handle)) !== false) {
                $data[] = $row;
            }
            fclose($handle);
        }
        return $data;
    }

    /**
     * Display bulk export page.
     */
    public function bulkExport(Request $request)
    {
        $categories = Category::where('status', 'active')->get();
        $totalProducts = Product::count();
        
        return view('admin.products.bulk-export', compact('categories', 'totalProducts'));
    }

    /**
     * Export products to CSV with full options.
     */
    public function exportProducts(Request $request)
    {
        $query = Product::with('category');

        // Apply filters
        if ($request->category) {
            $query->where('category_id', $request->category);
        }

        if ($request->status !== null && $request->status !== '') {
            $query->where('is_active', $request->status === 'active');
        }

        if ($request->stock_status) {
            switch ($request->stock_status) {
                case 'in_stock':
                    $query->where('quantity', '>', 10);
                    break;
                case 'low_stock':
                    $query->whereBetween('quantity', [1, 10]);
                    break;
                case 'out_of_stock':
                    $query->where('quantity', '<=', 0);
                    break;
            }
        }

        if ($request->featured !== null && $request->featured !== '') {
            $query->where('is_featured', $request->featured === 'yes');
        }

        if ($request->price_min) {
            $query->where('price', '>=', $request->price_min);
        }
        if ($request->price_max) {
            $query->where('price', '<=', $request->price_max);
        }

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $products = $query->get();

        // Determine export format
        $format = $request->format ?? 'csv';
        $filename = 'products-export-' . date('Y-m-d-His');

        if ($format === 'json') {
            return response()->json($products)
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '.json"');
        }

        // CSV Export
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '.csv"',
        ];

        $callback = function () use ($products) {
            $file = fopen('php://output', 'w');
            
            // CSV Headers
            fputcsv($file, [
                'ID',
                'Name',
                'Slug',
                'SKU',
                'Product Code',
                'Barcode',
                'Category',
                'Brand',
                'Short Description',
                'Long Description',
                'Price',
                'Sale Price',
                'Cost Price',
                'Purchase Price',
                'Quantity',
                'Low Stock Threshold',
                'Weight',
                'Status',
                'Featured',
                'Product Source',
                'Meta Title',
                'Meta Description',
                'Meta Keywords',
                'Tags',
                'Created At',
                'Updated At',
            ]);

            foreach ($products as $product) {
                fputcsv($file, [
                    $product->id,
                    $product->name,
                    $product->slug,
                    $product->sku,
                    $product->product_code ?? '',
                    $product->barcode ?? '',
                    $product->category->name ?? '',
                    $product->brand ?? '',
                    $product->short_description ?? '',
                    $product->long_description ?? '',
                    $product->price,
                    $product->sale_price ?? '',
                    $product->cost_price ?? '',
                    $product->purchase_price ?? '',
                    $product->quantity,
                    $product->low_stock_threshold,
                    $product->weight ?? '',
                    $product->is_active ? 'Active' : 'Inactive',
                    $product->is_featured ? 'Yes' : 'No',
                    $product->product_source ?? 'in_house',
                    $product->meta_title ?? '',
                    $product->meta_description ?? '',
                    $product->meta_keywords ?? '',
                    is_array($product->tags) ? implode(',', $product->tags) : '',
                    $product->created_at->format('Y-m-d H:i:s'),
                    $product->updated_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Display bulk discount page.
     */
    public function bulkDiscount()
    {
        $categories = Category::where('status', 'active')->get();
        $totalProducts = Product::count();
        
        return view('admin.products.bulk-discount', compact('categories', 'totalProducts'));
    }

    /**
     * Apply bulk discount to products.
     */
    public function applyBulkDiscount(Request $request)
    {
        $request->validate([
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'apply_to' => 'required|in:all,category,selected',
            'category_id' => 'required_if:apply_to,category|nullable|exists:categories,id',
            'product_ids' => 'required_if:apply_to,selected|nullable|array',
            'product_ids.*' => 'exists:products,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'clear_existing_sale_price' => 'nullable|boolean',
        ]);

        $discountType = $request->discount_type;
        $discountValue = floatval($request->discount_value);
        $applyTo = $request->apply_to;
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $clearExisting = $request->boolean('clear_existing_sale_price');

        // Build query based on selection
        $query = Product::query();

        switch ($applyTo) {
            case 'category':
                $query->where('category_id', $request->category_id);
                break;
            case 'selected':
                $query->whereIn('id', $request->product_ids ?? []);
                break;
            case 'all':
            default:
                // No additional filter
                break;
        }

        // Only apply to active products
        if ($request->boolean('active_only', true)) {
            $query->where('is_active', true);
        }

        // Filter by price range
        if ($request->price_min) {
            $query->where('price', '>=', $request->price_min);
        }
        if ($request->price_max) {
            $query->where('price', '<=', $request->price_max);
        }

        $products = $query->get();
        $updatedCount = 0;
        $errors = [];

        foreach ($products as $product) {
            try {
                $originalPrice = $product->price;
                
                // Clear existing sale price if requested
                if ($clearExisting) {
                    $product->sale_price = null;
                }

                // Calculate new sale price
                if ($discountType === 'percentage') {
                    $discountAmount = $originalPrice * ($discountValue / 100);
                } else {
                    $discountAmount = $discountValue;
                }

                $newSalePrice = max(0, $originalPrice - $discountAmount);

                // Validate sale price is less than regular price
                if ($newSalePrice >= $originalPrice) {
                    $errors[] = "Product '{$product->name}': Sale price would be equal or greater than regular price. Skipped.";
                    continue;
                }

                $product->sale_price = round($newSalePrice, 2);
                $product->save();
                $updatedCount++;

            } catch (\Exception $e) {
                $errors[] = "Product '{$product->name}': " . $e->getMessage();
            }
        }

        $message = "Bulk discount applied to {$updatedCount} product(s).";
        
        if (!empty($errors)) {
            return back()->with('warning', $message)
                        ->with('discount_errors', $errors);
        }

        return back()->with('success', $message);
    }

    /**
     * Remove bulk discount from products.
     */
    public function removeBulkDiscount(Request $request)
    {
        $request->validate([
            'apply_to' => 'required|in:all,category,selected',
            'category_id' => 'required_if:apply_to,category|nullable|exists:categories,id',
            'product_ids' => 'required_if:apply_to,selected|nullable|array',
            'product_ids.*' => 'exists:products,id',
        ]);

        $query = Product::query()->whereNotNull('sale_price');

        switch ($request->apply_to) {
            case 'category':
                $query->where('category_id', $request->category_id);
                break;
            case 'selected':
                $query->whereIn('id', $request->product_ids ?? []);
                break;
        }

        $count = $query->update(['sale_price' => null]);

        return back()->with('success', "Removed sale price from {$count} product(s).");
    }

    /**
     * Get products for selection (AJAX).
     */
    public function getProductsForSelection(Request $request)
    {
        $query = Product::with('category')->active();

        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($request->category) {
            $query->where('category_id', $request->category);
        }

        $products = $query->select(['id', 'name', 'sku', 'price', 'sale_price', 'category_id', 'quantity'])
            ->limit(50)
            ->get();

        return response()->json([
            'products' => $products->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'price' => $product->price,
                    'sale_price' => $product->sale_price,
                    'category' => $product->category->name ?? 'N/A',
                    'quantity' => $product->quantity,
                ];
            }),
        ]);
    }
}
