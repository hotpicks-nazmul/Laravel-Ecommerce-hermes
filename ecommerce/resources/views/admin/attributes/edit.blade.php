@extends('admin.layouts.app')

@section('title', 'Edit Attribute')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1"><i class="bi bi-pencil me-2"></i>Edit Attribute</h4>
            <p class="text-muted mb-0">Update attribute: {{ $attribute->name }}</p>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <form id="attributeForm" method="POST" action="{{ route('admin.attributes.update', $attribute->id) }}">
                @csrf
                @method('PUT')
                
                <!-- Basic Info Card -->
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white">
                        <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Basic Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                       value="{{ old('name', $attribute->name) }}" placeholder="e.g., Size, Material, Weight Capacity">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Slug</label>
                                <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror" 
                                       value="{{ old('slug', $attribute->slug) }}" placeholder="Auto-generated from name">
                                <div class="form-text">Leave empty to auto-generate</div>
                                @error('slug')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="2" placeholder="Optional description for this attribute">{{ old('description', $attribute->description) }}</textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Display Order</label>
                                <input type="number" name="display_order" class="form-control" 
                                       value="{{ old('display_order', $attribute->display_order) }}" min="0">
                                <div class="form-text">Lower numbers appear first</div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">&nbsp;</label>
                                <div class="form-check form-switch mt-2">
                                    <input type="checkbox" name="is_active" class="form-check-input" id="isActive" 
                                           {{ old('is_active', $attribute->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="isActive">
                                        <i class="bi bi-check-circle text-success me-1"></i> Active
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">&nbsp;</label>
                                <div class="form-check form-switch mt-2">
                                    <input type="checkbox" name="is_filterable" class="form-check-input" id="isFilterable" 
                                           {{ old('is_filterable', $attribute->is_filterable) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="isFilterable">
                                        <i class="bi bi-funnel text-info me-1"></i> Filterable
                                    </label>
                                </div>
                                <div class="form-text">Show in frontend filters</div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Attribute Values Card -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="bi bi-list-ul me-2"></i>Attribute Values</h6>
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="addValueRow()">
                        <i class="bi bi-plus-lg me-1"></i> Add Value
                    </button>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">Manage values for this attribute</p>
                    
                    <div id="valuesContainer">
                        @forelse($attribute->values as $value)
                        <div class="value-item" data-id="{{ $value->id }}">
                            <div class="row align-items-center">
                                <div class="col-md-4 mb-2 mb-md-0">
                                    <input type="text" class="form-control form-control-sm value-input" 
                                           value="{{ $value->value }}" placeholder="Value"
                                           data-id="{{ $value->id }}">
                                </div>
                                <div class="col-md-3 mb-2 mb-md-0">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text p-0">
                                            <input type="color" value="{{ $value->color_code ?? '#000000' }}" 
                                                   class="border-0 color-picker" style="width: 30px; height: 30px; cursor: pointer;"
                                                   data-id="{{ $value->id }}">
                                        </span>
                                        <input type="text" class="form-control color-code-input" 
                                               value="{{ $value->color_code }}" placeholder="#000000" maxlength="7"
                                               data-id="{{ $value->id }}">
                                    </div>
                                </div>
                                <div class="col-md-2 mb-2 mb-md-0">
                                    <input type="number" class="form-control form-control-sm order-input" 
                                           value="{{ $value->display_order }}" min="0" placeholder="Order"
                                           data-id="{{ $value->id }}">
                                </div>
                                <div class="col-md-2">
                                    <span class="badge {{ $value->is_active ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $value->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                                <div class="col-md-1 text-end">
                                    <button type="button" class="btn btn-sm btn-outline-primary me-1" onclick="saveValue({{ $value->id }})" title="Save">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteValue({{ $value->id }})" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        @empty
                        <p class="text-muted text-center py-3">No values added yet</p>
                        @endforelse
                    </div>

                    <!-- Add New Value Form -->
                    <div class="mt-4 pt-3 border-top">
                        <h6 class="mb-3">Add New Value</h6>
                        <div class="row align-items-end">
                            <div class="col-md-4 mb-2 mb-md-0">
                                <label class="form-label small">Value</label>
                                <input type="text" id="newValue" class="form-control form-control-sm" placeholder="e.g., Large">
                            </div>
                            <div class="col-md-3 mb-2 mb-md-0">
                                <label class="form-label small">Color Code</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text p-0">
                                        <input type="color" id="newColorPicker" value="#000000" 
                                               class="border-0" style="width: 30px; height: 30px; cursor: pointer;">
                                    </span>
                                    <input type="text" id="newColorCode" class="form-control" placeholder="#000000" maxlength="7">
                                </div>
                            </div>
                            <div class="col-md-2 mb-2 mb-md-0">
                                <label class="form-label small">Order</label>
                                <input type="number" id="newOrder" class="form-control form-control-sm" value="0" min="0">
                            </div>
                            <div class="col-md-3">
                                <button type="button" class="btn btn-sm btn-primary w-100" onclick="addNewValue()">
                                    <i class="bi bi-plus-lg me-1"></i> Add
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Stats Card -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-bar-chart me-2"></i>Statistics</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Total Values:</span>
                        <strong>{{ $attribute->values_count }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Active Values:</span>
                        <strong>{{ $attribute->active_values_count }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Created:</span>
                        <strong>{{ $attribute->created_at->format('M d, Y') }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Last Updated:</span>
                        <strong>{{ $attribute->updated_at->format('M d, Y') }}</strong>
                    </div>
                </div>
            </div>

            <!-- Tips Card -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-lightbulb me-2"></i>Tips</h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="bi bi-info-circle text-info me-2"></i>
                            Click <strong>Save</strong> button to update individual values
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-info-circle text-info me-2"></i>
                            Color code is optional - use for color-type attributes
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-info-circle text-info me-2"></i>
                            Display order affects sorting in frontend
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Floating Save Button -->
<div class="floating-save-container">
    <a href="{{ route('admin.attributes.index') }}" class="btn btn-secondary floating-reset-btn">
        <i class="bi bi-x-lg me-1"></i> Cancel
    </a>
    <form id="deleteForm" method="POST" action="{{ route('admin.attributes.destroy', $attribute->id) }}" style="display: inline;">
        @csrf
        @method('DELETE')
    </form>
    <button type="button" class="btn btn-outline-danger floating-reset-btn" onclick="if(confirm('Are you sure?')) document.getElementById('deleteForm').submit()">
        <i class="bi bi-trash me-1"></i> Delete
    </button>
    <button type="submit" form="attributeForm" class="btn btn-primary floating-save-btn">
        <i class="bi bi-check-lg me-1"></i> Update Attribute
    </button>
</div>
@endsection

@push('styles')
<style>
    .content-area {
        padding-bottom: 100px !important;
    }
    .value-item {
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 10px;
    }
    .value-item:hover {
        background: #f1f3f5;
    }
    .color-preview {
        width: 30px;
        height: 30px;
        border-radius: 4px;
        display: inline-block;
        vertical-align: middle;
        border: 1px solid #ddd;
    }
</style>
@endpush

@push('scripts')
<script>
    // Sync color picker and text input
    document.getElementById('newColorPicker').addEventListener('input', function() {
        document.getElementById('newColorCode').value = this.value;
    });
    document.getElementById('newColorCode').addEventListener('input', function() {
        if (/^#[0-9A-Fa-f]{6}$/.test(this.value)) {
            document.getElementById('newColorPicker').value = this.value;
        }
    });

    // Sync existing color pickers
    document.querySelectorAll('.color-picker').forEach(picker => {
        picker.addEventListener('input', function() {
            const id = this.dataset.id;
            document.querySelector(`.color-code-input[data-id="${id}"]`).value = this.value;
        });
    });
    document.querySelectorAll('.color-code-input').forEach(input => {
        input.addEventListener('input', function() {
            if (/^#[0-9A-Fa-f]{6}$/.test(this.value)) {
                const id = this.dataset.id;
                document.querySelector(`.color-picker[data-id="${id}"]`).value = this.value;
            }
        });
    });

    // Add new value
    function addNewValue() {
        const value = document.getElementById('newValue').value.trim();
        const colorCode = document.getElementById('newColorCode').value.trim();
        const displayOrder = document.getElementById('newOrder').value;

        if (!value) {
            alert('Please enter a value');
            return;
        }

        fetch(`{{ route('admin.attributes.values.store', $attribute->id) }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                value: value,
                color_code: colorCode || null,
                display_order: displayOrder
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Error adding value');
            }
        });
    }

    // Save value
    function saveValue(id) {
        const value = document.querySelector(`.value-input[data-id="${id}"]`).value.trim();
        const colorCode = document.querySelector(`.color-code-input[data-id="${id}"]`).value.trim();
        const displayOrder = document.querySelector(`.order-input[data-id="${id}"]`).value;

        if (!value) {
            alert('Please enter a value');
            return;
        }

        fetch(`{{ route('admin.attributes.values.update', [$attribute->id, 'VALUE_ID']) }}`.replace('VALUE_ID', id), {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                value: value,
                color_code: colorCode || null,
                display_order: displayOrder
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Error updating value');
            }
        });
    }

    // Delete value
    function deleteValue(id) {
        if (!confirm('Are you sure you want to delete this value?')) return;

        fetch(`{{ route('admin.attributes.values.destroy', [$attribute->id, 'VALUE_ID']) }}`.replace('VALUE_ID', id), {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Error deleting value');
            }
        });
    }
</script>
@endpush
