<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    /**
     * Display all categories.
     */
    public function index()
    {
        $categories = Category::where('status', 'active')
            ->whereNull('parent_id')
            ->with('children')
            ->get();

        return view('themes.general.categories.index', compact('categories'));
    }

    /**
     * Display a single category.
     */
    public function show($slug)
    {
        $category = Category::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $products = $category->products()
            ->where('is_active', true)
            ->paginate(12);

        return view('themes.general.categories.show', compact('category', 'products'));
    }
}
