@extends('admin.layouts.app')

@section('title', 'Edit Slider')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 py-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="mb-1 fw-bold">
                            <i class="bi bi-pencil text-primary me-2"></i> Edit Slider
                        </h4>
                        <p class="text-muted mb-0 small">Modify slider details</p>
                    </div>
                    <a href="{{ route('admin.sliders.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left me-1"></i> Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<form action="{{ route('admin.sliders.update', $slider) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    
    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-semibold">Slider Content</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-medium">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" value="{{ $slider->title }}" required>
                        <div class="form-text">This will be displayed as the main heading on the slide.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-medium">Subtitle</label>
                        <input type="text" name="subtitle" class="form-control" value="{{ $slider->subtitle }}">
                        <div class="form-text">Optional subtitle displayed above the title.</div>
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Button Text</label>
                            <input type="text" name="button_text" class="form-control" value="{{ $slider->button_text }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Link URL</label>
                            <input type="url" name="link" class="form-control" value="{{ $slider->link }}">
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
                        <label class="form-label fw-medium">Current Image</label>
                        <img src="{{ Storage::url($slider->image) }}" alt="{{ $slider->title }}" class="img-fluid rounded mb-3" style="max-height: 150px;">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-medium">New Image</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                        <div class="form-text">Leave empty to keep current image.</div>
                    </div>
                    
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ $slider->is_active ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">Active</label>
                    </div>
                </div>
            </div>
            
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-check-lg me-1"></i> Update Slider
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
