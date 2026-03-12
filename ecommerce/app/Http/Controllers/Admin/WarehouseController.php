<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Warehouse;

class WarehouseController extends Controller
{
    /**
     * Display a listing of warehouses.
     */
    public function index(Request $request)
    {
        $query = Warehouse::query();

        // Search by name, code, city, or phone
        if ($request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->status === 'active') {
            $query->where('is_active', true);
        } elseif ($request->status === 'inactive') {
            $query->where('is_active', false);
        }

        // Filter by city
        if ($request->city) {
            $query->where('city', $request->city);
        }

        // Sorting
        $sort = $request->sort ?? 'sort_order';
        $direction = $request->direction ?? 'asc';
        $query->orderBy($sort, $direction);

        // Pagination
        $perPage = $request->per_page ?? 25;
        $warehouses = $query->paginate($perPage);

        // Get cities for filter
        $cities = Warehouse::distinct()->pluck('city')->sort()->values();

        // Get stats
        $stats = [
            'total' => Warehouse::count(),
            'active' => Warehouse::where('is_active', true)->count(),
            'inactive' => Warehouse::where('is_active', false)->count(),
        ];

        // AJAX response
        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.warehouses.partials.table-rows', compact('warehouses'))->render(),
                'pagination' => $warehouses->links()->toHtml(),
                'stats' => $stats
            ]);
        }

        return view('admin.warehouses.index', compact('warehouses', 'cities', 'stats'));
    }

    /**
     * Show the form for creating a new warehouse.
     */
    public function create()
    {
        return view('admin.warehouses.create');
    }

    /**
     * Store a newly created warehouse.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:warehouses,code',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'required|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'postcode' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'opening_hours' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
            'is_primary' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        // If this is set as primary, unset other primary warehouses
        if ($request->is_primary) {
            Warehouse::where('is_primary', true)->update(['is_primary' => false]);
        }

        Warehouse::create($request->all());

        return redirect()->route('admin.warehouses.index')
            ->with('success', 'Warehouse created successfully.');
    }

    /**
     * Display the specified warehouse.
     */
    public function show(Warehouse $warehouse)
    {
        return view('admin.warehouses.show', compact('warehouse'));
    }

    /**
     * Show the form for editing the warehouse.
     */
    public function edit(Warehouse $warehouse)
    {
        return view('admin.warehouses.edit', compact('warehouse'));
    }

    /**
     * Update the specified warehouse.
     */
    public function update(Request $request, Warehouse $warehouse)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:warehouses,code,' . $warehouse->id,
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'required|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'postcode' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'opening_hours' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
            'is_primary' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        // If this is set as primary, unset other primary warehouses
        if ($request->is_primary && !$warehouse->is_primary) {
            Warehouse::where('is_primary', true)->update(['is_primary' => false]);
        }

        $warehouse->update($request->all());

        return redirect()->route('admin.warehouses.index')
            ->with('success', 'Warehouse updated successfully.');
    }

    /**
     * Remove the specified warehouse.
     */
    public function destroy(Warehouse $warehouse)
    {
        $warehouse->delete();

        return redirect()->route('admin.warehouses.index')
            ->with('success', 'Warehouse deleted successfully.');
    }

    /**
     * Toggle the active status.
     */
    public function toggleStatus(Warehouse $warehouse)
    {
        $warehouse->update(['is_active' => !$warehouse->is_active]);

        return back()->with('success', 'Status updated successfully.');
    }

    /**
     * Get warehouses for AJAX requests.
     */
    public function getWarehouses(Request $request)
    {
        $query = Warehouse::active();

        if ($request->city) {
            $query->where('city', $request->city);
        }

        if ($request->search) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        $warehouses = $query->orderBy('sort_order')->get();

        return response()->json([
            'success' => true,
            'warehouses' => $warehouses
        ]);
    }
}
