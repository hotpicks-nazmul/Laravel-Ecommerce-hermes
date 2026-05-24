# Page-Action Pills Not Toggling (Spinner Stuck)

**Date:** 2026-05-21

## Symptom

Clicking orange page-action pills (Status Management, Invoice, Customer Info, Create Order, Export) in the Permission Settings tab shows a spinner that never resolves. The pill stays stuck on the spinner and never restores the original label text.

## Root Cause

The page-action pills had **both** `perm-toggle` AND `page-action-pill` CSS classes:

```html
<span class="badge rounded-pill perm-toggle page-action-pill" ...>
```

Clicking triggered **two separate event handlers** that race against each other:

1. **`.perm-toggle` handler** (registered first) — tries to access `this.dataset.module` and `this.dataset.action`, which don't exist on page-action pills. Sends an invalid AJAX request with `{module: undefined, action: undefined}`. The request fails validation on the server.
2. **`.page-action-pill` handler** (registered second) — correctly uses `this.dataset.fullName` and sends `{full_name: "orders.show-update-status"}`. This request succeeds.

The second handler captures `originalHtml` AFTER the first handler already replaced the content with the spinner. When it restores `originalHtml`, it sets it back to the spinner HTML. Then the first handler's `.catch` eventually fires, but by that time the pill is already in a broken state.

Additionally, clicking a page-action-pill would also trigger the `module-header-row` collapse/expand handler because `.page-action-pill` wasn't in its exclusion list.

## Fix

1. Removed `perm-toggle` class from all 3 page-action-pill elements in the Blade template (direct items, group items, child items)
2. Added `.page-action-pill` to the module-header-row click exclusion list

## Files Changed

- `resources/views/admin/permissions/dashboard.blade.php` — 3 class removals + 1 exclusion list update
