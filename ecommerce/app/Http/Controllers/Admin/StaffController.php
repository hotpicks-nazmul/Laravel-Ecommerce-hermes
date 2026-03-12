<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class StaffController extends Controller
{
    /**
     * Display a listing of staff members.
     */
    public function index(Request $request)
    {
        // Get statistics
        $stats = $this->getStats();

        // Build query for staff
        $query = User::staff()->with('warehouse');

        // Search
        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('designation', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Filter by warehouse
        if ($request->warehouse_id) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        // Sort
        $sortBy = $request->sort_by ?? 'created_at';
        $sortOrder = $request->sort_order ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);

        // Paginate
        $perPage = $request->per_page ?? 10;
        $staffs = $query->paginate($perPage);

        // Get warehouses for filter
        $warehouses = Warehouse::where('is_active', 1)->orderBy('name')->get();

        // If AJAX request, return JSON
        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.staffs.partials.table-rows', compact('staffs'))->render(),
                'stats' => $stats,
                'pagination' => $staffs->links()->toHtml(),
            ]);
        }

        return view('admin.staffs.index', compact('staffs', 'stats', 'warehouses'));
    }

    /**
     * Get statistics for staff
     */
    protected function getStats()
    {
        $staff = User::staff();

        return [
            'total' => (clone $staff)->count(),
            'active' => (clone $staff)->where('status', 'active')->count(),
            'inactive' => (clone $staff)->where('status', 'inactive')->count(),
            'banned' => (clone $staff)->where('status', 'banned')->count(),
        ];
    }

    /**
     * Show the form for creating a new staff member.
     */
    public function create()
    {
        $warehouses = Warehouse::where('is_active', 1)->orderBy('name')->get();
        
        return view('admin.staffs.create', compact('warehouses'));
    }

    /**
     * Store a newly created staff member.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'designation' => 'nullable|string|max:255',
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'is_super_admin' => 'boolean',
            'status' => 'required|in:active,inactive,banned',
        ]);

        $staff = new User();
        $staff->name = $request->name;
        $staff->email = $request->email;
        $staff->phone = $request->phone;
        $staff->password = Hash::make($request->password);
        $staff->role = 'staff';
        $staff->designation = $request->designation;
        $staff->warehouse_id = $request->warehouse_id;
        $staff->is_super_admin = $request->is_super_admin ?? false;
        $staff->status = $request->status;

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            $avatar = $request->file('avatar');
            $avatarName = time() . '_' . $avatar->getClientOriginalName();
            $avatar->move(public_path('uploads/staffs'), $avatarName);
            $staff->avatar = 'staffs/' . $avatarName;
        }

        $staff->save();

        return redirect()->route('admin.staffs.index')
            ->with('success', 'Staff member created successfully.');
    }

    /**
     * Show the form for editing the specified staff member.
     */
    public function edit($id)
    {
        $staff = User::staff()->findOrFail($id);
        $warehouses = Warehouse::where('is_active', 1)->orderBy('name')->get();

        return view('admin.staffs.edit', compact('staff', 'warehouses'));
    }

    /**
     * Update the specified staff member.
     */
    public function update(Request $request, $id)
    {
        $staff = User::staff()->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($staff->id),
            ],
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
            'designation' => 'nullable|string|max:255',
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'is_super_admin' => 'boolean',
            'status' => 'required|in:active,inactive,banned',
        ]);

        $staff->name = $request->name;
        $staff->email = $request->email;
        $staff->phone = $request->phone;
        $staff->designation = $request->designation;
        $staff->warehouse_id = $request->warehouse_id;
        $staff->is_super_admin = $request->is_super_admin ?? false;
        $staff->status = $request->status;

        // Update password if provided
        if ($request->password) {
            $staff->password = Hash::make($request->password);
        }

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar
            if ($staff->avatar && file_exists(public_path('uploads/' . $staff->avatar))) {
                unlink(public_path('uploads/' . $staff->avatar));
            }
            
            $avatar = $request->file('avatar');
            $avatarName = time() . '_' . $avatar->getClientOriginalName();
            $avatar->move(public_path('uploads/staffs'), $avatarName);
            $staff->avatar = 'staffs/' . $avatarName;
        }

        $staff->save();

        return redirect()->route('admin.staffs.index')
            ->with('success', 'Staff member updated successfully.');
    }

    /**
     * Remove the specified staff member.
     */
    public function destroy($id)
    {
        $staff = User::staff()->findOrFail($id);

        // Delete avatar
        if ($staff->avatar && file_exists(public_path('uploads/' . $staff->avatar))) {
            unlink(public_path('uploads/' . $staff->avatar));
        }

        $staff->delete();

        return redirect()->route('admin.staffs.index')
            ->with('success', 'Staff member deleted successfully.');
    }

    /**
     * Display warehouse staff page.
     */
    public function warehouse(Request $request)
    {
        // Get staff assigned to warehouses
        $query = User::staff()->whereNotNull('warehouse_id')->with('warehouse');

        // Search
        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filter by warehouse
        if ($request->warehouse_id) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        $sortBy = $request->sort_by ?? 'created_at';
        $sortOrder = $request->sort_order ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);

        $perPage = $request->per_page ?? 10;
        $staffs = $query->paginate($perPage);

        $warehouses = Warehouse::where('is_active', 1)->orderBy('name')->get();

        return view('admin.staffs.warehouse', compact('staffs', 'warehouses'));
    }

    /**
     * Display staff permissions page.
     */
    public function permissions(Request $request)
    {
        $staff = User::staff()->with('warehouse')->get();
        $warehouses = Warehouse::where('is_active', 1)->orderBy('name')->get();

        return view('admin.staffs.permissions', compact('staff', 'warehouses'));
    }

    /**
     * Update staff permissions.
     */
    public function updatePermissions(Request $request)
    {
        $request->validate([
            'staff_id' => 'required|exists:users,id',
            'permissions' => 'nullable|array',
        ]);

        $staff = User::staff()->findOrFail($request->staff_id);
        $staff->permissions = json_encode($request->permissions ?? []);
        $staff->save();

        return redirect()->route('admin.staffs.permissions')
            ->with('success', 'Permissions updated successfully.');
    }

    /**
     * Bulk actions on staff members.
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'staff_ids' => 'required|array',
            'staff_ids.*' => 'exists:users,id',
        ]);

        $staffIds = $request->staff_ids;
        $action = $request->action;

        switch ($action) {
            case 'activate':
                User::whereIn('id', $staffIds)->update(['status' => 'active']);
                $message = 'Selected staff members have been activated.';
                break;
            case 'deactivate':
                User::whereIn('id', $staffIds)->update(['status' => 'inactive']);
                $message = 'Selected staff members have been deactivated.';
                break;
            case 'delete':
                $staffs = User::whereIn('id', $staffIds)->get();
                foreach ($staffs as $staff) {
                    if ($staff->avatar && file_exists(public_path('uploads/' . $staff->avatar))) {
                        unlink(public_path('uploads/' . $staff->avatar));
                    }
                }
                User::whereIn('id', $staffIds)->delete();
                $message = 'Selected staff members have been deleted.';
                break;
        }

        return redirect()->route('admin.staffs.index')
            ->with('success', $message);
    }
}
