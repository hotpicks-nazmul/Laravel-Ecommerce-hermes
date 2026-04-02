<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\ImageHelper;
use App\Models\Color;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ColorController extends Controller
{
    /**
     * Display a listing of colors.
     */
    public function index(Request $request)
    {
        $query = Color::withCount('products');

        // Search
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('code', 'like', "%{$request->search}%")
                    ->orWhere('slug', 'like', "%{$request->search}%");
            });
        }

        // Status filter
        if ($request->status === 'active') {
            $query->where('is_active', true);
        } elseif ($request->status === 'inactive') {
            $query->where('is_active', false);
        }

        // Sorting
        $sort = $request->sort ?? 'display_order';
        $direction = $request->direction ?? 'asc';
        $query->orderBy($sort, $direction);

        // Pagination
        $perPage = $request->per_page ?? 25;
        $colors = $query->paginate($perPage);

        // Statistics
        $stats = [
            'total' => Color::count(),
            'active' => Color::where('is_active', true)->count(),
            'inactive' => Color::where('is_active', false)->count(),
            'products' => \DB::table('product_colors')->count(),
        ];

        // AJAX response
        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.colors.partials.table-rows', compact('colors'))->render(),
                'pagination' => $colors->links()->toHtml(),
                'stats' => $stats,
                'search' => $request->search,
            ]);
        }

        return view('admin.colors.index', compact('colors', 'stats'));
    }

    /**
     * Show the form for creating a new color.
     */
    public function create()
    {
        return view('admin.colors.create');
    }

    /**
     * Store a newly created color.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:colors,slug',
            'code' => 'nullable|string|max:10|unique:colors,code',
            'hex_code' => 'required|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'description' => 'nullable|string',
            'display_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        // Handle image upload
        $imagePath = null;
        if ($request->hasFile('image')) {
            if (ImageHelper::isValidImage($request->file('image'))) {
                $imageResult = ImageHelper::processImage(
                    $request->file('image'),
                    'colors',
                    1920,
                    300,
                    85
                );
                $imagePath = ltrim($imageResult['path'], '/');
            }
        }

        $color = Color::create([
            'name' => $validated['name'],
            'slug' => $validated['slug'] ?? Str::slug($validated['name']),
            'code' => $validated['code'] ?? strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $validated['name']), 0, 3)),
            'hex_code' => $validated['hex_code'],
            'image' => $imagePath,
            'description' => $validated['description'] ?? null,
            'display_order' => $validated['display_order'] ?? 0,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.colors.index')
            ->with('success', 'Color created successfully.');
    }

    /**
     * Show the form for editing the specified color.
     */
    public function edit(Color $color)
    {
        return view('admin.colors.edit', compact('color'));
    }

    /**
     * Update the specified color.
     */
    public function update(Request $request, Color $color)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:colors,slug,' . $color->id,
            'code' => 'nullable|string|max:10|unique:colors,code,' . $color->id,
            'hex_code' => 'required|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'description' => 'nullable|string',
            'display_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($color->image) {
                ImageHelper::deleteImage($color->image);
            }
            if (ImageHelper::isValidImage($request->file('image'))) {
                $imageResult = ImageHelper::processImage(
                    $request->file('image'),
                    'colors',
                    1920,
                    300,
                    85
                );
                $validated['image'] = ltrim($imageResult['path'], '/');
            }
        }

        $color->update([
            'name' => $validated['name'],
            'slug' => $validated['slug'] ?? Str::slug($validated['name']),
            'code' => $validated['code'] ?? $color->code,
            'hex_code' => $validated['hex_code'],
            'image' => $validated['image'] ?? $color->image,
            'description' => $validated['description'] ?? null,
            'display_order' => $validated['display_order'] ?? 0,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.colors.index')
            ->with('success', 'Color updated successfully.');
    }

    /**
     * Remove the specified color.
     */
    public function destroy(Color $color)
    {
        // Delete image
        if ($color->image) {
            ImageHelper::deleteImage($color->image);
        }

        $color->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Color deleted successfully.',
            ]);
        }

        return redirect()->route('admin.colors.index')
            ->with('success', 'Color deleted successfully.');
    }

    /**
     * Toggle color status.
     */
    public function toggleStatus(Color $color)
    {
        $color->update([
            'is_active' => !$color->is_active,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Color status updated.',
            'is_active' => $color->is_active,
        ]);
    }

    /**
     * Bulk action for colors.
     */
    public function bulkAction(Request $request)
    {
        $action = $request->action;
        $ids = json_decode($request->ids, true);

        if (empty($ids)) {
            return response()->json([
                'success' => false,
                'message' => 'No colors selected.',
            ]);
        }

        switch ($action) {
            case 'activate':
                Color::whereIn('id', $ids)->update(['is_active' => true]);
                $message = 'Selected colors activated successfully.';
                break;
            case 'deactivate':
                Color::whereIn('id', $ids)->update(['is_active' => false]);
                $message = 'Selected colors deactivated successfully.';
                break;
            case 'delete':
                $colors = Color::whereIn('id', $ids)->get();
                foreach ($colors as $color) {
                    if ($color->image) {
                        ImageHelper::deleteImage($color->image);
                    }
                }
                Color::whereIn('id', $ids)->delete();
                $message = 'Selected colors deleted successfully.';
                break;
            default:
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid action.',
                ]);
        }

        return response()->json([
            'success' => true,
            'message' => $message,
        ]);
    }

    /**
     * Export colors.
     */
    public function export()
    {
        $colors = Color::withCount('products')->get();

        $filename = 'colors_' . date('Y-m-d_H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($colors) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Name', 'Code', 'Hex Code', 'Status', 'Products Count', 'Display Order', 'Created At']);

            foreach ($colors as $color) {
                fputcsv($file, [
                    $color->id,
                    $color->name,
                    $color->code,
                    $color->hex_code,
                    $color->is_active ? 'Active' : 'Inactive',
                    $color->products_count,
                    $color->display_order,
                    $color->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get colors for AJAX requests.
     */
    public function getColors()
    {
        $colors = Color::active()
            ->ordered()
            ->get(['id', 'name', 'code', 'hex_code', 'image']);

        return response()->json([
            'colors' => $colors,
        ]);
    }
}
