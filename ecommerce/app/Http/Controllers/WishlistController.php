<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Wishlist;

class WishlistController extends Controller
{
    /**
     * Display user's wishlist.
     */
    public function index()
    {
        $wishlist = auth()->user()->wishlist()->with('product')->get();
        return view('themes.general.wishlist.index', compact('wishlist'));
    }

    /**
     * Add item to wishlist.
     */
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $exists = Wishlist::where('user_id', auth()->id())
            ->where('product_id', $request->product_id)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Product already in wishlist.'
            ]);
        }

        Wishlist::create([
            'user_id' => auth()->id(),
            'product_id' => $request->product_id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Product added to wishlist!'
        ]);
    }

    /**
     * Remove item from wishlist.
     */
    public function remove(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        Wishlist::where('user_id', auth()->id())
            ->where('product_id', $request->product_id)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product removed from wishlist!'
        ]);
    }

    /**
     * Toggle item in wishlist.
     */
    public function toggle(Request $request)
    {
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Please login to add to wishlist.',
                'login_required' => true
            ], 401);
        }

        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $exists = Wishlist::where('user_id', auth()->id())
            ->where('product_id', $request->product_id)
            ->exists();

        if ($exists) {
            Wishlist::where('user_id', auth()->id())
                ->where('product_id', $request->product_id)
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'Product removed from wishlist!',
                'added' => false
            ]);
        }

        Wishlist::create([
            'user_id' => auth()->id(),
            'product_id' => $request->product_id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Product added to wishlist!',
            'added' => true
        ]);
    }
}
