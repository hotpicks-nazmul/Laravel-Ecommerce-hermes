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

        $products = Product::where('is_active', true)
            ->where('name', 'like', "%{$query}%")
            ->take(5)
            ->get(['id', 'name', 'slug', 'price']);

        $categories = Category::where('status', 'active')
            ->where('name', 'like', "%{$query}%")
            ->take(3)
            ->get(['id', 'name', 'slug']);

        return response()->json([
            'products' => $products,
            'categories' => $categories,
        ]);
    }
}
