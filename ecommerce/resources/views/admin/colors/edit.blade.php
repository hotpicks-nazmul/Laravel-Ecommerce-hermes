@extends('admin.layouts.app')

@section('title', 'Edit Color')

@push('styles')
<style>
    .content-area {
        padding-bottom: 100px !important;
    }
    .color-preview {
        width: 80px;
        height: 80px;
        border-radius: 12px;
        display: inline-block;
        vertical-align: middle;
        border: 2px solid #ddd;
        transition: all 0.3s ease;
    }
    .color-preview:hover {
        transform: scale(1.05);
    }
    .color-swatch {
        width: 16px;
        height: 16px;
        border-radius: 50%;
        display: inline-block;
        vertical-align: middle;
        border: 1px solid #ddd;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1"><i class="bi bi-pencil me-2"></i>Edit Color</h4>
            <p class="text-muted mb-0">Update color: {{ $color->name }}</p>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <form id="colorForm" method="POST" action="{{ route('admin.colors.update', $color->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <!-- Basic Info Card -->
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white">
                        <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Color Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                       value="{{ old('name', $color->name) }}" placeholder="e.g., Red, Blue, Navy Blue">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Slug</label>
                                <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror" 
                                       value="{{ old('slug', $color->slug) }}" placeholder="Auto-generated from name">
                                <small class="text-muted">Leave empty to auto-generate</small>
                                @error('slug')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Code <span class="text-muted">(Short code like RED, BLU)</span></label>
                                <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" 
                                       value="{{ old('code', $color->code) }}" placeholder="e.g., RED, BLU, NAV" maxlength="10">
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Display Order</label>
                                <input type="number" name="display_order" class="form-control" 
                                       value="{{ old('display_order', $color->display_order) }}" min="0">
                                <small class="text-muted">Lower numbers appear first</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Color Selection Card -->
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white">
                        <h6 class="mb-0"><i class="bi bi-palette me-2"></i>Color Selection</h6>
                    </div>
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <label class="form-label">Hex Color Code <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="color" id="colorPicker" value="{{ old('hex_code', $color->hex_code) }}" 
                                           class="form-control form-control-color" style="width: 60px;">
                                    <input type="text" name="hex_code" id="hexCode" class="form-control @error('hex_code') is-invalid @enderror" 
                                           value="{{ old('hex_code', $color->hex_code) }}" placeholder="#000000" maxlength="7">
                                    @error('hex_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small class="text-muted">Click the color box to pick a color or enter hex code</small>
                            </div>
                            <div class="col-md-6 text-center">
                                <label class="form-label">Preview</label>
                                <div>
                                    <span class="color-preview" id="colorPreview" style="background-color: {{ old('hex_code', $color->hex_code) }};"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Color Selection -->
                        <div class="mt-4">
                            <label class="form-label small text-muted">Quick Select</label>
                            <div class="d-flex flex-wrap gap-2">
                                <button type="button" class="btn btn-sm p-2" style="background-color: #FF0000;" onclick="selectColor('#FF0000')" title="Red"></button>
                                <button type="button" class="btn btn-sm p-2" style="background-color: #FF5722;" onclick="selectColor('#FF5722')" title="Deep Orange"></button>
                                <button type="button" class="btn btn-sm p-2" style="background-color: #FF9800;" onclick="selectColor('#FF9800')" title="Orange"></button>
                                <button type="button" class="btn btn-sm p-2" style="background-color: #FFC107;" onclick="selectColor('#FFC107')" title="Amber"></button>
                                <button type="button" class="btn btn-sm p-2" style="background-color: #FFEB3B;" onclick="selectColor('#FFEB3B')" title="Yellow"></button>
                                <button type="button" class="btn btn-sm p-2" style="background-color: #CDDC39;" onclick="selectColor('#CDDC39')" title="Lime"></button>
                                <button type="button" class="btn btn-sm p-2" style="background-color: #8BC34A;" onclick="selectColor('#8BC34A')" title="Light Green"></button>
                                <button type="button" class="btn btn-sm p-2" style="background-color: #4CAF50;" onclick="selectColor('#4CAF50')" title="Green"></button>
                                <button type="button" class="btn btn-sm p-2" style="background-color: #009688;" onclick="selectColor('#009688')" title="Teal"></button>
                                <button type="button" class="btn btn-sm p-2" style="background-color: #00BCD4;" onclick="selectColor('#00BCD4')" title="Cyan"></button>
                                <button type="button" class="btn btn-sm p-2" style="background-color: #03A9F4;" onclick="selectColor('#03A9F4')" title="Light Blue"></button>
                                <button type="button" class="btn btn-sm p-2" style="background-color: #2196F3;" onclick="selectColor('#2196F3')" title="Blue"></button>
                                <button type="button" class="btn btn-sm p-2" style="background-color: #3F51B5;" onclick="selectColor('#3F51B5')" title="Indigo"></button>
                                <button type="button" class="btn btn-sm p-2" style="background-color: #673AB7;" onclick="selectColor('#673AB7')" title="Deep Purple"></button>
                                <button type="button" class="btn btn-sm p-2" style="background-color: #9C27B0;" onclick="selectColor('#9C27B0')" title="Purple"></button>
                                <button type="button" class="btn btn-sm p-2" style="background-color: #E91E63;" onclick="selectColor('#E91E63')" title="Pink"></button>
                                <button type="button" class="btn btn-sm p-2" style="background-color: #000000;" onclick="selectColor('#000000')" title="Black"></button>
                                <button type="button" class="btn btn-sm p-2" style="background-color: #9E9E9E;" onclick="selectColor('#9E9E9E')" title="Grey"></button>
                                <button type="button" class="btn btn-sm p-2" style="background-color: #795548;" onclick="selectColor('#795548')" title="Brown"></button>
                                <button type="button" class="btn btn-sm p-2" style="background-color: #FFFFFF; border: 1px solid #ddd;" onclick="selectColor('#FFFFFF')" title="White"></button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Image Upload Card -->
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white">
                        <h6 class="mb-0"><i class="bi bi-image me-2"></i>Color Image <span class="text-muted">(Optional)</span></h6>
                    </div>
                    <div class="card-body">
                        @if($color->image)
                        <div class="mb-3">
                            <label class="form-label">Current Image</label>
                            <div>
                                <img src="{{ asset($color->image) }}" alt="{{ $color->name }}" class="img-thumbnail" style="max-width: 100px; max-height: 100px;">
                            </div>
                        </div>
                        @endif
                        <div class="mb-3">
                            <label class="form-label">{{ $color->image ? 'Replace Image' : 'Swatch Image' }}</label>
                            <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" 
                                   accept="image/jpeg,image/png,image/jpg,image/webp">
                            <small class="text-muted">Upload a texture or pattern image for this color (e.g., wood grain, fabric pattern)</small>
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Additional Info Card -->
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white">
                        <h6 class="mb-0"><i class="bi bi-card-text me-2"></i>Additional Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="Optional description for this color">{{ old('description', $color->description) }}</textarea>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" name="is_active" class="form-check-input" id="isActive" 
                                   {{ old('is_active', $color->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="isActive">Active</label>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="col-lg-4">
            <!-- Stats Card -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-bar-chart me-2"></i>Statistics</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Products using this color:</span>
                        <strong>{{ $color->products_count }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Created:</span>
                        <strong>{{ $color->created_at->format('M d, Y') }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Last Updated:</span>
                        <strong>{{ $color->updated_at->format('M d, Y') }}</strong>
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
                            Changing the hex code will update the color display
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-info-circle text-info me-2"></i>
                            Deactivating a color hides it from product options
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-info-circle text-info me-2"></i>
                            Products using this color won't be affected by deletion
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Floating Save Button -->
<div class="floating-save-container">
    <a href="{{ route('admin.colors.index') }}" class="btn btn-secondary floating-reset-btn">
        <i class="bi bi-x-lg me-1"></i> Cancel
    </a>
    <form id="deleteForm" method="POST" action="{{ route('admin.colors.destroy', $color->id) }}" style="display: inline;">
        @csrf
        @method('DELETE')
    </form>
    <button type="button" class="btn btn-outline-danger floating-reset-btn" onclick="if(confirm('Are you sure?')) document.getElementById('deleteForm').submit()">
        <i class="bi bi-trash me-1"></i> Delete
    </button>
    <button type="submit" form="colorForm" class="btn btn-primary floating-save-btn">
        <i class="bi bi-check-lg me-1"></i> Update Color
    </button>
</div>
@endsection

@push('scripts')
<script>
    const colorPicker = document.getElementById('colorPicker');
    const hexCode = document.getElementById('hexCode');
    const colorPreview = document.getElementById('colorPreview');

    // Sync color picker and hex input
    colorPicker.addEventListener('input', function() {
        hexCode.value = this.value.toUpperCase();
        colorPreview.style.backgroundColor = this.value;
    });

    hexCode.addEventListener('input', function() {
        if (/^#[0-9A-Fa-f]{6}$/.test(this.value)) {
            colorPicker.value = this.value;
            colorPreview.style.backgroundColor = this.value;
        }
    });

    // Quick color selection
    function selectColor(hex) {
        colorPicker.value = hex;
        hexCode.value = hex.toUpperCase();
        colorPreview.style.backgroundColor = hex;
    }
</script>
@endpush
