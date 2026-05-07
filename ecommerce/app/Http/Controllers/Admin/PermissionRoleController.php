<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionRoleController extends Controller
{
    private function authorizeAccess(): void
    {
        $user = auth()->user();
        if (!$user || $user->role !== 'super_admin') {
            abort(403, 'Only super admins can manage role templates.');
        }
    }

    /**
     * List all role templates.
     */
    public function index()
    {
        $this->authorizeAccess();
        $roles = Role::where('guard_name', 'web')
            ->with('permissions')
            ->orderBy('name')
            ->get();

        return view('admin.permissions.roles', compact('roles'));
    }

    /**
     * Show create form.
     */
    public function create()
    {
        $this->authorizeAccess();

        $permissions = Permission::where('guard_name', 'web')
            ->orderBy('name')
            ->get()
            ->groupBy(function ($perm) {
                return explode('.', $perm->name)[0];
            });

        return view('admin.permissions.role-create', compact('permissions'));
    }

    /**
     * Store a new role.
     */
    public function store(Request $request)
    {
        $this->authorizeAccess();

        $request->validate([
            'name' => 'required|string|max:50|regex:/^[a-z-]+$/|unique:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string',
        ]);

        $role = Role::create([
            'name' => $request->name,
            'guard_name' => 'web',
        ]);

        if ($request->permissions) {
            $permIds = Permission::whereIn('name', $request->permissions)
                ->where('guard_name', 'web')
                ->pluck('id')
                ->toArray();
            $role->syncPermissions($permIds);
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->route('admin.roles.index')
            ->with('success', "Role '{$request->name}' created successfully.");
    }

    /**
     * Show edit form.
     */
    public function edit($id)
    {
        $this->authorizeAccess();

        $role = Role::with('permissions')->findOrFail($id);

        $permissions = Permission::where('guard_name', 'web')
            ->orderBy('name')
            ->get()
            ->groupBy(function ($perm) {
                return explode('.', $perm->name)[0];
            });

        return view('admin.permissions.role-edit', compact('role', 'permissions'));
    }

    /**
     * Update role.
     */
    public function update(Request $request, $id)
    {
        $this->authorizeAccess();

        $role = Role::findOrFail($id);

        $request->validate([
            'name' => ['required', 'string', 'max:50', 'regex:/^[a-z-]+$/', Rule::unique('roles')->ignore($role->id)],
            'permissions' => 'nullable|array',
            'permissions.*' => 'string',
        ]);

        $role->name = $request->name;
        $role->save();

        if ($request->permissions) {
            $permIds = Permission::whereIn('name', $request->permissions)
                ->where('guard_name', 'web')
                ->pluck('id')
                ->toArray();
            $role->syncPermissions($permIds);
        } else {
            $role->syncPermissions([]);
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->route('admin.roles.index')
            ->with('success', "Role '{$request->name}' updated successfully.");
    }

    /**
     * Delete a role.
     */
    public function destroy($id)
    {
        $this->authorizeAccess();

        $role = Role::findOrFail($id);
        $name = $role->name;
        $role->delete();

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->route('admin.roles.index')
            ->with('success', "Role '$name' deleted successfully.");
    }
}
