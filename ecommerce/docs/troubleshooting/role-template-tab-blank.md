# Role Templates Tab Blank

**Date:** 2026-05-21

## Symptom

Clicking "Role Templates" tab in admin > Staff > Permission Settings shows blank/empty content. Badge count shows correctly (e.g., "9"). URL does not change.

## Root Cause

Missing `</div>` closing tag for `<div class="module-header-row">` in the Permission Settings tab (Tab 1) of `dashboard.blade.php` (line 79). The div was opened but never closed before the `@if(!empty($submenus))` block on line 114.

This caused the browser's HTML parser to mis-nest the entire page DOM — the Role Templates (Tab 2) and Staff Permissions (Tab 3) tab-panes ended up nested inside a `.module-group` card from Tab 1. The `#permissionsTabsContent` container had `height: 0` because it only contained one child (`#keys`).

## Diagnosis

```javascript
// Browser console check:
document.getElementById('permissionsTabsContent').children.length  // Returns 1 instead of 3
document.getElementById('roles').parentElement.className           // '.module-group card...' instead of '#permissionsTabsContent'
```

## Fix

Added `</div>` to close the `module-header-row` between the submenu count badge and the `@if(!empty($submenus))` directive.

## Files Changed

- `resources/views/admin/permissions/dashboard.blade.php` — Added missing `</div>` at line 113
