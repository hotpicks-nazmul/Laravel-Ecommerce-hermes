---
name: admin-form-controls
description: Standard form control patterns including required fields, help text, validation errors, input groups, and form switches.
---

# Admin Form Controls

**Required Fields:**

<label for="name" class="form-label">Field Name <span class="text-danger">*</span></label>

**Help Text:**

<div class="form-text">Help text goes here</div>

**Validation Errors:**

<input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" required>
@error('name')
    <div class="invalid-feedback">{{ $message }}</div>
@enderror

**Input with Icon:**

<div class="input-group">
    <span class="input-group-text"><i class="bi bi-link-45deg"></i></span>
    <input type="text" class="form-control" id="slug" name="slug" placeholder="auto-generated">
</div>

**Form Switch with Icon:**

<div class="form-check form-switch mb-3">
    <input class="form-check-input" type="checkbox" id="is_active" form="itemForm">
    <label class="form-check-label" for="is_active">
        <i class="bi bi-check-circle text-success me-1"></i> Active
    </label>
    <div class="form-text">Help text</div>
</div>

**Header with Back Button:**

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Page Title</h4>
    <a href="{{ route('admin.items.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back to Items
    </a>
</div>