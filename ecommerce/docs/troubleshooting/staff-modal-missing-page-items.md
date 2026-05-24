# Staff Manage Popup Missing Page-Action Items

**Date:** 2026-05-22

## Symptom

The Staff Permissions Manage modal (Tab 3 > Manage button) only shows:
- Module-level CRUD pills (green)
- Section pills (purple)
- Submenu visibility toggles (blue)

Missing: page-action pills (orange) like Status Management, Invoice, Create Order, Customer Info, Billing Address, etc. These existed in the Permission Settings tab (Tab 1) but not in the staff modal.

## Root Cause

The staff submenu section (`staff-submenu-content`) only rendered the submenu visibility toggle for each submenu. It never included the page items, groups, and child pages that the Permission Settings tab and Role Create/Edit pages had.

## Fix v1

Added the full tree structure to the staff modal's submenu section:

1. Added `$allStaffPageComponents = PermissionHelper::pageComponents()` to the staff module's PHP block
2. For each submenu, rendered:
   - Direct page items (orange pills with inline `#e86c00` style)
   - Group headers with group items
   - Child pages with their items
3. Used `$member->hasPermission($permName)` to check initial state

## Fix v2 — Remove Duplicate Section Groups

**Date:** Later on 2026-05-22

### Symptom

After Fix v1, the staff modal showed extra rows: "Table Columns" group with Customer, Pricing, Pricing Detail, Discount. These are section permissions already covered by the purple pills at the top — duplicate.

### Fix

Added a check to skip groups where ALL items are section-level permissions (i.e., their permission names end with a `sectionActions()` action):

```blade
@php
    $allSection = !empty($gItems) && collect($gItems)->every(function($perm, $label) use ($sectionActions) {
        return collect($sectionActions)->contains(fn($a) => str_ends_with($perm, '.' . $a));
    });
@endphp
@if(!$allSection)
    {{-- render group header and items --}}
@endif
```

This keeps "Table Actions" (View Details, Edit, Delete) and child pages visible while hiding "Table Columns" (Customer, Pricing, Pricing Detail, Discount). The `$sectionActions` variable is already available in the Blade scope (defined earlier in the staff module group loop).

## Files Changed

- `resources/views/admin/permissions/dashboard.blade.php` — Added page-items/groups/children rendering + `@if(!$allSection)` filter
