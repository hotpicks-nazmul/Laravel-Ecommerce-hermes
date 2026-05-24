# Staff Permissions Not Persisting After Save

**Date:** 2026-05-22

## Symptom

Toggle Status Management / Invoice ON in the Staff Permissions Manage modal, click Save Changes, page reloads — permissions are back to OFF.

This only affected page-action permissions (orange pills). Regular module-level CRUD permissions saved fine.

## Root Cause

The `StaffController@updatePermissions` method did:

```php
$permIds = Permission::whereIn('name', $permNames)
    ->where('guard_name', 'web')
    ->pluck('id')
    ->toArray();
$staff->permissions()->sync($permIds);
```

`sync()` only assigns **existing** permission records. If a permission name like `orders.show-update-status` didn't already exist in the `permissions` table (because it was never toggled in the Permission Settings tab), it was **silently skipped** — no record found, no assignment made, no error raised. After page reload, the permission showed as OFF.

Also missing: permission cache flush after save.

## Fix

Added auto-creation of missing permissions before syncing:

```php
// Auto-create any permissions that don't exist yet
$existingPerms = Permission::whereIn('name', $permNames)
    ->where('guard_name', 'web')
    ->pluck('name')
    ->toArray();
$missingPerms = array_diff($permNames, $existingPerms);
foreach ($missingPerms as $missingName) {
    Permission::create([
        'name' => $missingName,
        'guard_name' => 'web',
    ]);
}
```

Also added cache flush:

```php
app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
```

## Verified

- Status Management ON → Save → reload → stays ON (orange) ✓
- Invoice ON → Save → reload → stays ON (orange) ✓
- Works without pre-creating in Permission Settings tab ✓

## Files Changed

- `app/Http/Controllers/Admin/StaffController.php` — Auto-create missing permissions + cache flush
