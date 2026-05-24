# Orange Pills Turning Green in Staff Modal

**Date:** 2026-05-22

## Symptom

Page-action pills in the Staff Permissions Manage modal (Status Management, Invoice, Create Order, etc.) turn **green** instead of **orange** when clicked ON.

## Root Cause

The `staff-perm-pill` click handler only had two color cases:

```javascript
const isSection = sectionActions.some(function(a) {
    return perm.endsWith('.' + a);
});
this.style.background = isSection ? '#6f42c1' : '#198754';  // purple or green
```

Since page-action permissions (e.g., `orders.show-update-status`) don't match section action patterns, they fell into the `else` branch and got green (`#198754`) instead of orange (`#e86c00`).

The same logic existed in two other places: Select All and Role Template fill.

## Fix

1. Added `data-orange="1"` attribute to all page-action pills in the staff modal (3 places: direct items, group items, child items)
2. Updated the click handler to check for `data-orange`:
```javascript
const isOrange = this.dataset.orange === '1';
this.style.background = isOrange ? '#e86c00' : (isSection ? '#6f42c1' : '#198754');
```
3. Applied the same fix to the Select All and Role Template fill handlers

## Verified

Before fix: `orders.show-update-status` → green (`#198754`) ✗  
After fix: `orders.show-update-status` → orange (`#e86c00`) ✓

## Files Changed

- `resources/views/admin/permissions/dashboard.blade.php` — 3 `data-orange` attributes + 3 JS updates
