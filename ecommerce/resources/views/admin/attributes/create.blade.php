@extends('admin.layouts.app')

@section('title', 'Create Attribute')

@section('content')
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1"><i class="bi bi-plus-circle me-2"></i>Create Attribute</h4>
            <p class="text-muted mb-0">Add a new product attribute with values</p>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <form id="attributeForm" method="POST" action="{{ route('admin.attributes.store') }}">
                @csrf
                
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
                                       value="{{ old('name') }}" placeholder="e.g., Size, Material, Weight Capacity">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Slug</label>
                                <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror" 
                                       value="{{ old('slug') }}" placeholder="Auto-generated from name">
                                <div class="form-text">Leave empty to auto-generate</div>
                                @error('slug')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="2" placeholder="Optional description for this attribute">{{ old('description') }}</textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Display Order</label>
                                <input type="number" name="display_order" class="form-control" 
                                       value="{{ old('display_order', 0) }}" min="0">
                                <div class="form-text">Lower numbers appear first</div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">&nbsp;</label>
                                <div class="form-check form-switch mt-2">
                                    <input type="checkbox" name="is_active" class="form-check-input" id="isActive" checked>
                                    <label class="form-check-label" for="isActive">
                                        <i class="bi bi-check-circle text-success me-1"></i> Active
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">&nbsp;</label>
                                <div class="form-check form-switch mt-2">
                                    <input type="checkbox" name="is_filterable" class="form-check-input" id="isFilterable" checked>
                                    <label class="form-check-label" for="isFilterable">
                                        <i class="bi bi-funnel text-info me-1"></i> Filterable
                                    </label>
                                </div>
                                <div class="form-text">Show in frontend filters</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Attribute Values Card -->
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h6 class="mb-0"><i class="bi bi-list-ul me-2"></i>Attribute Values</h6>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addValueRow()">
                            <i class="bi bi-plus-lg me-1"></i> Add Value
                        </button>
                    </div>
                    <div class="card-body">
                        <p class="text-muted small mb-3">Add values for this attribute (e.g., Small, Medium, Large for Size)</p>
                        
                        <div id="valuesContainer">
                            <!-- Value rows will be added here -->
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="col-lg-4">
            <!-- Tips Card -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-lightbulb me-2"></i>Tips</h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            <strong>Attributes</strong> are product characteristics like Size, Color, Material
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            <strong>Values</strong> are options for each attribute (e.g., S, M, L, XL for Size)
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            <strong>Filterable</strong> attributes appear in frontend product filters
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Use <strong>Display Order</strong> to control sorting
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Examples Card -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-box me-2"></i>Common Attributes</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong class="small">Size</strong>
                        <p class="text-muted small mb-0">S, M, L, XL, XXL</p>
                    </div>
                    <div class="mb-3">
                        <strong class="small">Material</strong>
                        <p class="text-muted small mb-0">Cotton, Polyester, Silk, Leather</p>
                    </div>
                    <div class="mb-3">
                        <strong class="small">Weight Capacity</strong>
                        <p class="text-muted small mb-0">50kg, 100kg, 150kg, 200kg</p>
                    </div>
                    <div class="mb-0">
                        <strong class="small">Warranty</strong>
                        <p class="text-muted small mb-0">6 Months, 1 Year, 2 Years</p>
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
    <button type="submit" form="attributeForm" class="btn btn-primary floating-save-btn">
        <i class="bi bi-check-lg me-1"></i> Create Attribute
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
    let valueIndex = 0;

    function addValueRow(value = '', colorCode = '', displayOrder = '') {
        const container = document.getElementById('valuesContainer');
        const row = document.createElement('div');
        row.className = 'value-item';
        row.id = `value-row-${valueIndex}`;
        row.innerHTML = `
            <div class="row align-items-center">
                <div class="col-md-5 mb-2 mb-md-0">
                    <input type="text" name="values[${valueIndex}][value]" class="form-control form-control-sm" 
                           value="${value}" placeholder="Value (e.g., Large, Cotton)">
                </div>
                <div class="col-md-3 mb-2 mb-md-0">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text p-0">
                            <input type="color" name="values[${valueIndex}][color_code_picker]" 
                                   value="${colorCode || '#000000'}" 
                                   class="border-0" style="width: 30px; height: 30px; cursor: pointer;"
                                   onchange="this.nextElementSibling.value = this.value">
                        </span>
                        <input type="text" name="values[${valueIndex}][color_code]" class="form-control" 
                               value="${colorCode}" placeholder="#000000" maxlength="7"
                               onchange="this.previousElementSibling.querySelector('input[type=color]').value = this.value">
                    </div>
                    <div class="form-text small">Optional color</div>
                </div>
                <div class="col-md-2 mb-2 mb-md-0">
                    <input type="number" name="values[${valueIndex}][display_order]" class="form-control form-control-sm" 
                           value="${displayOrder || valueIndex}" min="0" placeholder="Order">
                </div>
                <div class="col-md-2 text-end">
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeValueRow(${valueIndex})">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        `;
        container.appendChild(row);
        valueIndex++;
    }

    function removeValueRow(index) {
        const row = document.getElementById(`value-row-${index}`);
        if (row) {
            row.remove();
        }
    }

    // Auto-generate slug from name
    document.querySelector('input[name="name"]').addEventListener('input', function() {
        const slugInput = document.querySelector('input[name="slug"]');
        if (!slugInput.value) {
            slugInput.value = this.value.toLowerCase()
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/(^-|-$)/g, '');
        }
    });

    // Add initial value row
    addValueRow();
</script>
@endpush
