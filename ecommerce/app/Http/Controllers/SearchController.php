<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class SearchController extends Controller
{
    /**
     * Search products.
     */
    public function index(Request $request)
    {
        $query = $request->get('q', '');

        $products = collect();
        $categories = collect();

        if (strlen($query) >= 2) {
            $products = Product::where('is_active', true)
                ->where(function ($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                      ->orWhere('description', 'like', "%{$query}%")
                      ->orWhere('sku', 'like', "%{$query}%");
                })
                ->paginate(12);

            $categories = Category::where('status', 'active')
                ->where('name', 'like', "%{$query}%")
                ->get();
        }

        return view('themes.general.search.index', compact('products', 'categories', 'query'));
    }

    /**
     * Get search suggestions.
     */
    public function suggestions(Request $request)
    {
        $query = $request->get('q', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        // Search products with relevance scoring
        $products = Product::where('is_active', true)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('short_description', 'like', "%{$query}%")
                  ->orWhere('sku', 'like', "%{$query}%");
            })
            ->with('category:id,name,slug')
            ->get(['id', 'name', 'slug', 'price', 'sale_price', 'featured_image', 'category_id'])
            ->map(function ($product) use ($query) {
                // Calculate relevance score
                $product->relevance = 0;
                $lowerName = strtolower($product->name);
                $lowerQuery = strtolower($query);
                
                // Highest priority: name starts with query
                if (str_starts_with($lowerName, $lowerQuery)) {
                    $product->relevance = 100;
                }
                // High priority: name contains query at word boundary
                elseif (preg_match('/\b' . preg_quote($lowerQuery, '/') . '/i', $product->name)) {
                    $product->relevance = 80;
                }
                // Medium priority: name contains query anywhere
                elseif (str_contains($lowerName, $lowerQuery)) {
                    $product->relevance = 60;
                }
                // Lower priority: found in description or SKU
                else {
                    $product->relevance = 40;
                }
                
                return $product;
            })
            ->sortByDesc('relevance')
            ->take(8)
            ->values();

        $categories = Category::where('status', 'active')
            ->where('name', 'like', "%{$query}%")
            ->take(3)
            ->get(['id', 'name', 'slug', 'image']);

        return response()->json([
            'products' => $products,
            'categories' => $categories,
            'query' => $query,
        ]);
    }
}
