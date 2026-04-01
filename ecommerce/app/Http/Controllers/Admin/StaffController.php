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

        // Build query - show all admin panel users for super_admin, only staff for others
        $currentUser = auth()->user();
        if ($currentUser->role === 'super_admin') {
            $query = User::adminPanel()->with('warehouse');
        } else {
            $query = User::staff()->with('warehouse');
        }

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
            $showingText = '';
            if ($staffs->hasPages()) {
                $showingText = "Showing {$staffs->firstItem()} - {$staffs->lastItem()} of {$staffs->total()} staffs";
            }
            return response()->json([
                'html' => view('admin.staffs.partials.table-rows', compact('staffs'))->render(),
                'stats' => $stats,
                'pagination' => $staffs->links()->toHtml(),
                'showing_text' => $showingText,
            ]);
        }

        return view('admin.staffs.index', compact('staffs', 'stats', 'warehouses'));
    }

    /**
     * Get statistics for staff
     */
    protected function getStats()
    {
        $currentUser = auth()->user();
        
        // Show all admin panel users for super_admin, only staff for others
        if ($currentUser->role === 'super_admin') {
            $users = User::adminPanel();
        } else {
            $users = User::staff();
        }

        return [
            'total' => (clone $users)->count(),
            'active' => (clone $users)->where('status', 'active')->count(),
            'inactive' => (clone $users)->where('status', 'inactive')->count(),
            'banned' => (clone $users)->where('status', 'banned')->count(),
        ];
    }

    /**
     * Show the form for creating a new staff member.
     */
    public function create()
    {
        $user = auth()->user();
        
        // Determine available roles based on current user's role
        $allowedRoles = [];
        if ($user->role === 'super_admin') {
            $allowedRoles = ['admin', 'staff'];
        } elseif ($user->role === 'admin') {
            $allowedRoles = ['staff'];
        } else {
            $allowedRoles = ['staff'];
        }
        
        $warehouses = Warehouse::where('is_active', 1)->orderBy('name')->get();
        
        return view('admin.staffs.create', compact('warehouses', 'allowedRoles'));
    }

    /**
     * Store a newly created staff member.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        
        // Staff cannot create staff members
        if ($user->role === 'staff') {
            abort(403, 'Unauthorized access. Staff members cannot create other staff.');
        }
        
        // Determine available roles based on current user's role
        $allowedRoles = [];
        if ($user->role === 'super_admin') {
            $allowedRoles = ['admin', 'staff'];
        } elseif ($user->role === 'admin') {
            $allowedRoles = ['staff'];
        } else {
            $allowedRoles = ['staff'];
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'designation' => 'nullable|string|max:255',
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'role' => 'required|in:' . implode(',', $allowedRoles),
            'is_super_admin' => 'boolean',
            'status' => 'required|in:active,inactive,banned',
        ]);

        $staff = new User();
        $staff->name = $request->name;
        $staff->email = $request->email;
        $staff->phone = $request->phone;
        $staff->password = Hash::make($request->password);
        $staff->role = $request->role;
        $staff->designation = $request->designation;
        $staff->warehouse_id = $request->warehouse_id;
        $staff->is_super_admin = $request->is_super_admin ?? false;
        $staff->status = $request->status;

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            $avatar = $request->file('avatar');
            $avatarName = time() . '_' . $avatar->getClientOriginalName();
            $avatar->move(public_path('uploads/staffs'), $avatarName);
            $staff->avatar = 'uploads/staffs/' . $avatarName;
        }

        $staff->save();

        $roleLabel = ucfirst($request->role);
        return redirect()->route('admin.staffs.index')
            ->with('success', $roleLabel . ' created successfully.');
    }

    /**
     * Show the form for editing the specified staff member.
     */
    public function edit($id)
    {
        $currentUser = auth()->user();
        
        // Use adminPanel scope for super_admin, staff scope for others
        if ($currentUser->role === 'super_admin') {
            $staff = User::adminPanel()->findOrFail($id);
            $allowedRoles = ['admin', 'staff'];
        } else {
            $staff = User::staff()->findOrFail($id);
            $allowedRoles = ['staff'];
        }
        
        $warehouses = Warehouse::where('is_active', 1)->orderBy('name')->get();

        return view('admin.staffs.edit', compact('staff', 'warehouses', 'allowedRoles'));
    }

    /**
     * Update the specified staff member.
     */
    public function update(Request $request, $id)
    {
        $currentUser = auth()->user();
        
        // Use adminPanel scope for super_admin, staff scope for others
        if ($currentUser->role === 'super_admin') {
            $staff = User::adminPanel()->findOrFail($id);
            $allowedRoles = ['admin', 'staff'];
        } else {
            $staff = User::staff()->findOrFail($id);
            $allowedRoles = ['staff'];
        }

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
            'role' => 'sometimes|in:' . implode(',', $allowedRoles),
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

        // Update role if provided and allowed
        if ($request->has('role') && in_array($request->role, $allowedRoles)) {
            $staff->role = $request->role;
        }

        // Update password if provided
        if ($request->password) {
            $staff->password = Hash::make($request->password);
        }

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar
            if ($staff->avatar && file_exists(public_path($staff->avatar))) {
                unlink(public_path($staff->avatar));
            }
            
            $avatar = $request->file('avatar');
            $avatarName = time() . '_' . $avatar->getClientOriginalName();
            $avatar->move(public_path('uploads/staffs'), $avatarName);
            $staff->avatar = 'uploads/staffs/' . $avatarName;
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
        $currentUser = auth()->user();
        
        // Use adminPanel scope for super_admin, staff scope for others
        if ($currentUser->role === 'super_admin') {
            $staff = User::adminPanel()->findOrFail($id);
        } else {
            $staff = User::staff()->findOrFail($id);
        }

        // Delete avatar
        if ($staff->avatar && file_exists(public_path($staff->avatar))) {
            unlink(public_path($staff->avatar));
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
        $currentUser = auth()->user();
        
        // Staff cannot access warehouse staff page
        if ($currentUser->role === 'staff') {
            abort(403, 'Unauthorized access. Staff members cannot access warehouse staff page.');
        }
        
        // Get staff assigned to warehouses - use adminPanel for super_admin/admin
        if ($currentUser->role === 'super_admin' || $currentUser->role === 'admin') {
            $query = User::adminPanel()->whereNotNull('warehouse_id')->with('warehouse');
        } else {
            $query = User::staff()->whereNotNull('warehouse_id')->with('warehouse');
        }

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

        // If AJAX request, return JSON
        if ($request->ajax()) {
            $showingText = '';
            if ($staffs->hasPages()) {
                $showingText = "Showing {$staffs->firstItem()} - {$staffs->lastItem()} of {$staffs->total()} staffs";
            }
            return response()->json([
                'html' => view('admin.staffs.partials.warehouse-table-rows', compact('staffs'))->render(),
                'pagination' => $staffs->links()->toHtml(),
                'showing_text' => $showingText,
            ]);
        }

        return view('admin.staffs.warehouse', compact('staffs', 'warehouses'));
    }

    /**
     * Display staff permissions page.
     */
    public function permissions(Request $request)
    {
        $currentUser = auth()->user();
        
        // Staff cannot access permissions page
        if ($currentUser->role === 'staff') {
            abort(403, 'Unauthorized access. Staff members cannot manage permissions.');
        }
        
        // Use adminPanel for super_admin/admin, staff scope for others
        if ($currentUser->role === 'super_admin' || $currentUser->role === 'admin') {
            $staff = User::adminPanel()->with('warehouse')->get();
        } else {
            $staff = User::staff()->with('warehouse')->get();
        }
        
        $warehouses = Warehouse::where('is_active', 1)->orderBy('name')->get();

        return view('admin.staffs.permissions', compact('staff', 'warehouses'));
    }

    /**
     * Update staff permissions.
     */
    public function updatePermissions(Request $request)
    {
        $currentUser = auth()->user();
        
        // Staff cannot update permissions
        if ($currentUser->role === 'staff') {
            if ($request->ajax()) {
                return response()->json(['message' => 'Unauthorized access.'], 403);
            }
            abort(403, 'Unauthorized access. Staff members cannot manage permissions.');
        }
        
        $request->validate([
            'staff_id' => 'required|exists:users,id',
            'permissions' => 'nullable|array',
        ]);

        // Use adminPanel for super_admin/admin
        if ($currentUser->role === 'super_admin' || $currentUser->role === 'admin') {
            $staff = User::adminPanel()->findOrFail($request->staff_id);
        } else {
            $staff = User::staff()->findOrFail($request->staff_id);
        }
        
        $staff->permissions = $request->permissions ?? [];
        $staff->save();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Permissions updated successfully.',
            ]);
        }

        return redirect()->route('admin.staffs.permissions')
            ->with('success', 'Permissions updated successfully.');
    }

    /**
     * Bulk actions on staff members.
     */
    public function bulkAction(Request $request)
    {
        $currentUser = auth()->user();
        
        // Staff cannot perform bulk actions
        if ($currentUser->role === 'staff') {
            abort(403, 'Unauthorized access. Staff members cannot perform bulk actions.');
        }
        
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'staff_ids' => 'required|array',
            'staff_ids.*' => 'exists:users,id',
        ]);

        $staffIds = is_string($request->staff_ids) ? json_decode($request->staff_ids, true) : $request->staff_ids;
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
                    if ($staff->avatar && file_exists(public_path($staff->avatar))) {
                        unlink(public_path($staff->avatar));
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
