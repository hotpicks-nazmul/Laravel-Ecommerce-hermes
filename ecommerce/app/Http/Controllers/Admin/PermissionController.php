<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\PermissionHelper;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{
    private function authorizeAccess(): void
    {
        $user = auth()->user();
        if (!$user || $user->role !== 'super_admin') {
            abort(403, 'Only super admins can manage permission configuration.');
        }
    }

    /**
     * Unified dashboard with 3 tabs: Permission Keys, Role Templates, Staff Permissions.
     */
    public function dashboard()
    {
        $this->authorizeAccess();
        // Tab 1: Permission Keys
        $permissions = Permission::where('guard_name', 'web')
            ->orderBy('name')
            ->get()
            ->groupBy(function ($perm) {
                return explode('.', $perm->name)[0];
            });

        $existingModules = array_keys($permissions->toArray());
        $existingActions = $permissions->flatten()->map(fn($p) => explode('.', $p->name)[1] ?? 'view')->unique()->sort()->values()->toArray();

        // All possible actions: merge common CRUD + existing + section actions
        $commonActions = ['view', 'create', 'edit', 'delete', 'export', 'import', 'manage', 'upload', 'install', 'uninstall'];
        $sectionActions = PermissionHelper::sectionActions();
        $allActions = array_unique(array_merge($commonActions, $existingActions, $sectionActions));
        sort($allActions);

        // Module actions define which actions each module supports
        $moduleActions = PermissionHelper::moduleActions();

        // Tab 2: Role Templates
        $roles = Role::where('guard_name', 'web')
            ->with('permissions')
            ->orderBy('name')
            ->get();

        // Tab 3: Staff Permissions
        $currentUser = auth()->user();

        if ($currentUser->role === 'super_admin' || $currentUser->role === 'admin') {
            $staff = User::adminPanel()->with(['warehouse', 'roles']);
        } else {
            $staff = User::staff()->with(['warehouse', 'roles']);
        }

        $staff = $staff->orderBy('created_at', 'desc')->paginate(25);

        $permissionModules = PermissionHelper::modules();
        $roleTemplates = Role::where('guard_name', 'web')->with('permissions')->orderBy('name')->get();

        return view('admin.permissions.dashboard', compact(
            'permissions', 'existingModules', 'allActions', 'moduleActions',
            'roles', 'staff', 'permissionModules', 'roleTemplates'
        ));
    }

    /**
     * List all permissions grouped by module prefix.
     */
    public function index()
    {
        $permissions = Permission::where('guard_name', 'web')
            ->orderBy('name')
            ->get()
            ->groupBy(function ($perm) {
                return explode('.', $perm->name)[0];
            });

        return view('admin.permissions.index', compact('permissions'));
    }

    /**
     * Store a new permission.
     */
    public function store(Request $request)
    {
        $this->authorizeAccess();

        $request->validate([
            'module' => 'required|string|max:50|regex:/^[a-z-]+$/',
            'action' => 'required|string|max:50|regex:/^[a-z-]+$/',
        ]);

        $name = $request->module . '.' . $request->action;

        $request->validate([
            'module' => [function ($attr, $val, $fail) use ($name) {
                if (Permission::where('name', $name)->where('guard_name', 'web')->exists()) {
                    $fail('This permission already exists.');
                }
            }],
        ]);

        Permission::create(['name' => $name, 'guard_name' => 'web']);

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->route('admin.permissions.index')
            ->with('success', "Permission '$name' created successfully.");
    }

    /**
     * Delete a permission.
     */
    public function destroy($id)
    {
        $this->authorizeAccess();

        $permission = Permission::findOrFail($id);
        $name = $permission->name;
        $permission->delete();

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->route('admin.permissions.index')
            ->with('success', "Permission '$name' deleted successfully.");
    }

    /**
     * Bulk delete permissions.
     */
    public function bulkDelete(Request $request)
    {
        $this->authorizeAccess();

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:permissions,id',
        ]);

        Permission::whereIn('id', $request->ids)->delete();

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return response()->json(['success' => true, 'message' => 'Selected permissions deleted.']);
    }

    /**
     * AJAX: Toggle a single permission key on/off.
     * If ON and key doesn't exist → create it.
     * If OFF and key exists → delete it.
     */
    public function toggleKey(Request $request)
    {
        $this->authorizeAccess();

        $request->validate([
            'module' => 'required_without:full_name|string|max:50|regex:/^[a-z-]+$/',
            'action' => 'required_without:full_name|string|max:50|regex:/^[a-z-]+$/',
            'full_name' => 'required_without:module|string|max:100|regex:/^[a-z0-9_.-]+$/',
        ]);

        if ($request->filled('full_name')) {
            $name = $request->full_name;
        } else {
            $name = $request->module . '.' . $request->action;
        }
        $existing = Permission::where('name', $name)->where('guard_name', 'web')->first();

        if ($existing) {
            $existing->delete();
            $state = false;
            $message = "Permission '$name' removed.";
        } else {
            Permission::create(['name' => $name, 'guard_name' => 'web']);
            $state = true;
            $message = "Permission '$name' created.";
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return response()->json([
            'success' => true,
            'state' => $state,
            'message' => $message,
        ]);
    }

    /**
     * Toggle a submenu's sidebar visibility.
     */
    public function toggleSubmenuVisibility(string $submenuKey)
    {
        $this->authorizeAccess();

        $visible = PermissionHelper::toggleSubmenuVisibility($submenuKey);

        return response()->json([
            'success' => true,
            'visible' => $visible,
            'message' => $visible
                ? "Submenu '{$submenuKey}' is now visible in the sidebar."
                : "Submenu '{$submenuKey}' is now hidden from the sidebar.",
        ]);
    }

    /**
     * Toggle a module's sidebar visibility.
     */
    public function toggleModuleVisibility(string $module)
    {
        $this->authorizeAccess();

        $visible = PermissionHelper::toggleModuleVisibility($module);

        return response()->json([
            'success' => true,
            'visible' => $visible,
            'message' => $visible
                ? "Module '{$module}' is now visible in the sidebar."
                : "Module '{$module}' is now hidden from the sidebar.",
        ]);
    }
}
