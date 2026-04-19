---
name: create-edit-form-layout
description: Standard two-column layout for create/edit forms with main content area (col-lg-8) and sidebar cards (col-lg-4) using form attribute for cross-card fields.
---

# Create/Edit Form Layout

**Standard Two-Column Layout with Sidebar Cards:**

<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-icon me-2"></i>Card Title</h6>
            </div>
            <div class="card-body">
                <form id="itemForm" method="POST" action="...">
                    @csrf
                    <!-- Form fields -->
                </form>
            </div>
        </div>
        
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-icon me-2"></i>Card Title</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="field_name" class="form-label">Field Label</label>
                    <input type="text" id="field_name" name="field_name" form="itemForm" class="form-control">
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white">
                <h6 class="mb-0">Status</h6>
            </div>
            <div class="card-body">
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="is_active" form="itemForm">
                    <label class="form-check-label" for="is_active">
                        <i class="bi bi-check-circle text-success me-1"></i> Active
                    </label>
                </div>
            </div>
        </div>
    </div>
</div>

**Key Patterns:**
- `id="itemForm"`: Add to main form element
- `form="itemForm"`: Add to all input fields outside the main form
- `id="field_name"`: Add to all inputs for label connection
- `for="field_name"`: Add to labels to connect with inputs
- `<div class="form-text">`: Use for help text instead of `<small>`