<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Review;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = Review::with('user', 'product');

        if ($request->status === 'pending') {
            $query->where('is_approved', false);
        } elseif ($request->status === 'approved') {
            $query->where('is_approved', true);
        }

        $reviews = $query->latest()->paginate(15);

        return view('admin.reviews.index', compact('reviews'));
    }

    public function update(Request $request, Review $review)
    {
        $request->validate([
            'rating' => 'sometimes|integer|min:1|max:5',
            'title' => 'sometimes|string|max:255',
            'comment' => 'sometimes|string|max:1000',
        ]);

        $review->update($request->all());

        return back()->with('success', 'Review updated successfully.');
    }

    public function destroy(Review $review)
    {
        $review->delete();
        return back()->with('success', 'Review deleted successfully.');
    }

    public function approve(Review $review)
    {
        $review->update(['is_approved' => true]);
        return back()->with('success', 'Review approved successfully.');
    }

    public function bulkAction(Request $request)
    {
        $action = $request->action;
        $ids = $request->ids;

        if ($action === 'delete') {
            Review::whereIn('id', $ids)->delete();
        } elseif ($action === 'approve') {
            Review::whereIn('id', $ids)->update(['is_approved' => true]);
        }

        return back()->with('success', 'Bulk action completed.');
    }
}
