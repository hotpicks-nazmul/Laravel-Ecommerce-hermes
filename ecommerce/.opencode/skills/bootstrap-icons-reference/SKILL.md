---
name: bootstrap-icons-reference
description: Standard Bootstrap Icons mapping for common admin panel actions including add, edit, delete, save, cancel, back, view, settings, and category.
---

# Bootstrap Icons Reference

Use Bootstrap Icons (`bi bi-*`) consistently across the admin panel:

**Common Action Icons:**
- Add/Create: `bi-plus-lg` or `bi-plus-circle`
- Edit: `bi-pencil`
- Delete: `bi-trash`
- Save/Confirm: `bi-check-lg`
- Cancel: `bi-x-lg`
- Back: `bi-arrow-left`
- View: `bi-eye`
- Settings: `bi-gear`
- Category: `bi-folder`
- Digital: `bi-file-earmark-binary`

**Usage Example:**

<a href="{{ route('admin.items.create') }}" class="btn btn-primary">
    <i class="bi bi-plus-lg me-1"></i> Add New Item
</a>

Always include the icon with the `me-1` class for proper spacing between the icon and text.