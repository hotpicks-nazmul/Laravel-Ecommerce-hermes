<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AttributeController extends Controller
{
    /**
     * Display a listing of attributes.
     */
    public function index(Request $request)
    {
        $query = Attribute::withCount(['values', 'values as active_values_count' => function ($q) {
            $q->where('is_active', true);
        }]);

        // Search
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('slug', 'like', "%{$request->search}%");
            });
        }

        // Status filter
        if ($request->status === 'active') {
            $query->where('is_active', true);
        } elseif ($request->status === 'inactive') {
            $query->where('is_active', false);
        }

        // Filterable filter
        if ($request->filterable === 'yes') {
            $query->where('is_filterable', true);
        } elseif ($request->filterable === 'no') {
            $query->where('is_filterable', false);
        }

        // Sorting
        $sort = $request->sort ?? 'display_order';
        $direction = $request->direction ?? 'asc';
        $query->orderBy($sort, $direction);

        // Pagination
        $perPage = $request->per_page ?? 25;
        $attributes = $query->paginate($perPage);

        // Statistics
        $stats = [
            'total' => Attribute::count(),
            'active' => Attribute::where('is_active', true)->count(),
            'inactive' => Attribute::where('is_active', false)->count(),
            'filterable' => Attribute::where('is_filterable', true)->count(),
        ];

        // AJAX response
        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.attributes.partials.table-rows', compact('attributes'))->render(),
                'pagination' => $attributes->links()->toHtml(),
                'stats' => $stats,
            ]);
        }

        return view('admin.attributes.index', compact('attributes', 'stats'));
    }

    /**
     * Show the form for creating a new attribute.
     */
    public function create()
    {
        return view('admin.attributes.create');
    }

    /**
     * Store a newly created attribute.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:attributes,slug',
            'description' => 'nullable|string',
            'display_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'is_filterable' => 'boolean',
            'values' => 'nullable|array',
            'values.*.value' => 'required_with:values|string|max:255',
            'values.*.color_code' => 'nullable|string|max:7',
            'values.*.display_order' => 'nullable|integer|min:0',
        ]);

        // Create attribute
        $attribute = Attribute::create([
            'name' => $validated['name'],
            'slug' => $validated['slug'] ?? Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
            'display_order' => $validated['display_order'] ?? 0,
            'is_active' => $request->has('is_active'),
            'is_filterable' => $request->has('is_filterable'),
        ]);

        // Create attribute values
        if (!empty($validated['values'])) {
            foreach ($validated['values'] as $index => $valueData) {
                if (!empty($valueData['value'])) {
                    AttributeValue::create([
                        'attribute_id' => $attribute->id,
                        'value' => $valueData['value'],
                        'slug' => Str::slug($valueData['value']),
                        'color_code' => $valueData['color_code'] ?? null,
                        'display_order' => $valueData['display_order'] ?? $index,
                        'is_active' => true,
                    ]);
                }
            }
        }

        return redirect()->route('admin.attributes.index')
            ->with('success', 'Attribute created successfully.');
    }

    /**
     * Show the form for editing the specified attribute.
     */
    public function edit(Attribute $attribute)
    {
        $attribute->load('values');
        return view('admin.attributes.edit', compact('attribute'));
    }

    /**
     * Update the specified attribute.
     */
    public function update(Request $request, Attribute $attribute)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:attributes,slug,' . $attribute->id,
            'description' => 'nullable|string',
            'display_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'is_filterable' => 'boolean',
        ]);

        $attribute->update([
            'name' => $validated['name'],
            'slug' => $validated['slug'] ?? Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
            'display_order' => $validated['display_order'] ?? 0,
            'is_active' => $request->has('is_active'),
            'is_filterable' => $request->has('is_filterable'),
        ]);

        return redirect()->route('admin.attributes.index')
            ->with('success', 'Attribute updated successfully.');
    }

    /**
     * Remove the specified attribute.
     */
    public function destroy(Attribute $attribute)
    {
        $attribute->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Attribute deleted successfully.',
            ]);
        }

        return redirect()->route('admin.attributes.index')
            ->with('success', 'Attribute deleted successfully.');
    }

    /**
     * Toggle attribute status.
     */
    public function toggleStatus(Attribute $attribute)
    {
        $attribute->update([
            'is_active' => !$attribute->is_active,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Attribute status updated.',
            'is_active' => $attribute->is_active,
        ]);
    }

    /**
     * Toggle attribute filterable status.
     */
    public function toggleFilterable(Attribute $attribute)
    {
        $attribute->update([
            'is_filterable' => !$attribute->is_filterable,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Attribute filterable status updated.',
            'is_filterable' => $attribute->is_filterable,
        ]);
    }

    /**
     * Bulk action for attributes.
     */
    public function bulkAction(Request $request)
    {
        $action = $request->action;
        $ids = json_decode($request->ids, true);

        if (empty($ids)) {
            return response()->json([
                'success' => false,
                'message' => 'No attributes selected.',
            ]);
        }

        switch ($action) {
            case 'activate':
                Attribute::whereIn('id', $ids)->update(['is_active' => true]);
                $message = 'Selected attributes activated successfully.';
                break;
            case 'deactivate':
                Attribute::whereIn('id', $ids)->update(['is_active' => false]);
                $message = 'Selected attributes deactivated successfully.';
                break;
            case 'delete':
                Attribute::whereIn('id', $ids)->delete();
                $message = 'Selected attributes deleted successfully.';
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
     * Export attributes.
     */
    public function export()
    {
        $attributes = Attribute::with('values')->get();

        $filename = 'attributes_' . date('Y-m-d_H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($attributes) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Name', 'Slug', 'Description', 'Status', 'Filterable', 'Values Count', 'Created At']);

            foreach ($attributes as $attribute) {
                fputcsv($file, [
                    $attribute->id,
                    $attribute->name,
                    $attribute->slug,
                    $attribute->description,
                    $attribute->is_active ? 'Active' : 'Inactive',
                    $attribute->is_filterable ? 'Yes' : 'No',
                    $attribute->values_count,
                    $attribute->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // ==================== Attribute Value Methods ====================

    /**
     * Store a new attribute value.
     */
    public function storeValue(Request $request, Attribute $attribute)
    {
        $validated = $request->validate([
            'value' => 'required|string|max:255',
            'color_code' => 'nullable|string|max:7',
            'display_order' => 'nullable|integer|min:0',
        ]);

        $value = AttributeValue::create([
            'attribute_id' => $attribute->id,
            'value' => $validated['value'],
            'slug' => Str::slug($validated['value']),
            'color_code' => $validated['color_code'] ?? null,
            'display_order' => $validated['display_order'] ?? 0,
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Attribute value added successfully.',
            'value' => $value,
        ]);
    }

    /**
     * Update an attribute value.
     */
    public function updateValue(Request $request, Attribute $attribute, AttributeValue $value)
    {
        $validated = $request->validate([
            'value' => 'required|string|max:255',
            'color_code' => 'nullable|string|max:7',
            'display_order' => 'nullable|integer|min:0',
        ]);

        $value->update([
            'value' => $validated['value'],
            'slug' => Str::slug($validated['value']),
            'color_code' => $validated['color_code'] ?? null,
            'display_order' => $validated['display_order'] ?? $value->display_order,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Attribute value updated successfully.',
            'value' => $value,
        ]);
    }

    /**
     * Delete an attribute value.
     */
    public function destroyValue(Attribute $attribute, AttributeValue $value)
    {
        $value->delete();

        return response()->json([
            'success' => true,
            'message' => 'Attribute value deleted successfully.',
        ]);
    }

    /**
     * Toggle attribute value status.
     */
    public function toggleValueStatus(Attribute $attribute, AttributeValue $value)
    {
        $value->update([
            'is_active' => !$value->is_active,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Attribute value status updated.',
            'is_active' => $value->is_active,
        ]);
    }

    /**
     * Get attribute values for AJAX requests.
     */
    public function getValues(Attribute $attribute)
    {
        return response()->json([
            'values' => $attribute->values()->orderBy('display_order')->get(),
        ]);
    }
}
