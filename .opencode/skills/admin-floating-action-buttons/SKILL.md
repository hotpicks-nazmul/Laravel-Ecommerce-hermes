---
name: admin-floating-action-buttons
description: Standardized floating action buttons for admin create/edit pages using global floating-save-container style with cancel and submit buttons.
---

# Admin Floating Action Buttons

All create and edit pages must use the floating-save-container style for action buttons. This style is already defined in `ecommerce/resources/views/admin/layouts/app.blade.php`.

**HTML Structure:**

<div class="floating-save-container">
    <a href="{{ route('cancel.route') }}" class="btn btn-secondary floating-reset-btn">
        <i class="bi bi-x-lg me-1"></i> Cancel
    </a>
    <button type="submit" form="formId" class="btn btn-primary floating-save-btn">
        <i class="bi bi-check-lg me-1"></i> Action Name
    </button>
</div>

**CSS Classes:**
- `floating-save-container`: Container for floating buttons (fixed position at bottom-right)
- `floating-save-btn`: Primary action button (gradient purple background)
- `floating-reset-btn`: Secondary/Cancel button (gray background)

**Button Styling by Type:**
- Cancel: `btn btn-secondary floating-reset-btn` with `bi-x-lg` icon
- Create: `btn btn-primary floating-save-btn` with `bi-check-lg` icon
- Update: `btn btn-primary floating-save-btn` with `bi-check-lg` icon
- Delete: `btn btn-outline-danger floating-reset-btn` with `bi-trash` icon

**Example – Create Page:**

<div class="floating-save-container">
    <a href="{{ route('admin.items.index') }}" class="btn btn-secondary floating-reset-btn">
        <i class="bi bi-x-lg me-1"></i> Cancel
    </a>
    <button type="submit" form="itemForm" class="btn btn-primary floating-save-btn">
        <i class="bi bi-check-lg me-1"></i> Create Item
    </button>
</div>

**Example – Edit Page with Delete:**

<div class="floating-save-container">
    <a href="{{ route('admin.items.index') }}" class="btn btn-secondary floating-reset-btn">
        <i class="bi bi-x-lg me-1"></i> Cancel
    </a>
    <a href="{{ route('admin.items.destroy', $item->id) }}" 
       class="btn btn-outline-danger floating-reset-btn" 
       onclick="event.preventDefault(); if(confirm('Are you sure?')) { document.getElementById('deleteForm').submit(); }">
        <i class="bi bi-trash me-1"></i> Delete
    </a>
    <button type="submit" form="itemForm" class="btn btn-primary floating-save-btn">
        <i class="bi bi-check-lg me-1"></i> Update Item
    </button>
</div>

**Do NOT Use:**
Do not create custom floating actions with inline CSS. Always use the global floating-save-container classes defined in the admin layout.