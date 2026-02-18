@extends('admin.layouts.app')

@section('title', 'Create Slider')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 py-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="mb-1 fw-bold">
                            <i class="bi bi-plus-circle text-primary me-2"></i> Create New Slider
                        </h4>
                        <p class="text-muted mb-0 small">Add a new slide to your hero section slider</p>
                    </div>
                    <a href="{{ route('admin.sliders.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left me-1"></i> Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<form action="{{ route('admin.sliders.store') }}" method="POST" enctype="multipart/form-data" id="slider-form">
    @csrf
    
    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-semibold">Slider Content</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-medium">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" placeholder="Enter slider title" required>
                        <div class="form-text">This will be displayed as the main heading on the slide.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-medium">Subtitle</label>
                        <input type="text" name="subtitle" class="form-control" placeholder="Enter slider subtitle">
                        <div class="form-text">Optional subtitle displayed above the title.</div>
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Button Text</label>
                            <input type="text" name="button_text" class="form-control" placeholder="Shop Now">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Link URL</label>
                            <input type="url" name="link" class="form-control" placeholder="https://example.com">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-semibold">Slider Image</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-medium">Image <span class="text-danger">*</span></label>
                        <input type="file" name="image" class="form-control" accept="image/*" required>
                        <div class="form-text">Recommended size: 1920x600px. JPG or PNG format.</div>
                    </div>
                    
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" checked>
                        <label class="form-check-label" for="is_active">Active</label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Floating Save Button -->
<div class="floating-save-container">
    <a href="{{ route('admin.sliders.index') }}" class="btn btn-secondary floating-reset-btn">
        <i class="bi bi-x-lg me-1"></i> Cancel
    </a>
    <button type="submit" form="slider-form" class="btn btn-primary floating-save-btn">
        <i class="bi bi-check-lg me-1"></i> Create Slider
    </button>
</div>
@endsection
