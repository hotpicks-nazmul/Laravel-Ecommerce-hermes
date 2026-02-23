@extends('admin.layouts.app')

@section('title', 'Create Affiliate Banner')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Create Affiliate Banner</h1>
        <a href="{{ route('admin.affiliate.banners.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back
        </a>
    </div>

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-circle me-2"></i>
        <ul class="mb-0">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Banner Details</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.affiliate.banners.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Banner Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" value="{{ old('name') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Affiliate</label>
                        <select class="form-select" name="affiliate_id">
                            <option value="">General Banner (No specific affiliate)</option>
                            @foreach($affiliates as $affiliate)
                            <option value="{{ $affiliate->id }}" {{ old('affiliate_id') == $affiliate->id ? 'selected' : '' }}>
                                {{ $affiliate->user->name ?? 'Unknown' }} ({{ $affiliate->affiliate_code }})
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Banner Image <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" name="image" accept="image/*" required onchange="previewImage(this)">
                        <small class="text-muted">Supported formats: JPG, PNG, GIF, WebP. Max size: 2MB</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Preview</label>
                        <div id="imagePreview" class="border rounded p-2 text-center" style="min-height: 100px;">
                            <span class="text-muted">No image selected</span>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Width (px)</label>
                        <input type="number" class="form-control" name="width" value="{{ old('width', 728) }}" min="1">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Height (px)</label>
                        <input type="number" class="form-control" name="height" value="{{ old('height', 90) }}" min="1">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Preset Sizes</label>
                        <select class="form-select" id="presetSizes" onchange="applyPreset()">
                            <option value="">Custom Size</option>
                            <option value="728,90">Leaderboard (728x90)</option>
                            <option value="468,60">Banner (468x60)</option>
                            <option value="300,250">Medium Rectangle (300x250)</option>
                            <option value="336,280">Large Rectangle (336x280)</option>
                            <option value="160,600">Wide Skyscraper (160x600)</option>
                            <option value="120,600">Skyscraper (120x600)</option>
                            <option value="320,50">Mobile Banner (320x50)</option>
                            <option value="320,100">Large Mobile Banner (320x100)</option>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Target URL</label>
                        <input type="url" class="form-control" name="target_url" value="{{ old('target_url') }}" placeholder="https://example.com">
                        <small class="text-muted">URL where users will be redirected when clicking the banner</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status">
                            <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" name="description" rows="3">{{ old('description') }}</textarea>
                </div>

                <div class="text-end">
                    <button type="reset" class="btn btn-secondary me-2" onclick="resetPreview()">
                        <i class="bi bi-x-circle me-2"></i>Reset
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-2"></i>Save Banner
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function previewImage(input) {
        var preview = document.getElementById('imagePreview');
        
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            
            reader.onload = function(e) {
                preview.innerHTML = '<img src="' + e.target.result + '" style="max-width: 100%; max-height: 200px;">';
            };
            
            reader.readAsDataURL(input.files[0]);
        }
    }
    
    function resetPreview() {
        document.getElementById('imagePreview').innerHTML = '<span class="text-muted">No image selected</span>';
    }
    
    function applyPreset() {
        var preset = document.getElementById('presetSizes').value;
        if (preset) {
            var sizes = preset.split(',');
            document.querySelector('input[name="width"]').value = sizes[0];
            document.querySelector('input[name="height"]').value = sizes[1];
        }
    }
</script>
@endpush
