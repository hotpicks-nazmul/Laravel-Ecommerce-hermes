@extends('admin.layouts.app')

@section('title', 'Create Page')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Create New Page</h4>
                <a href="{{ route('admin.pages.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Back to List
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form action="{{ route('admin.pages.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" 
                                   value="{{ old('title') }}" placeholder="Enter page title" required>
                            @error('title')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Content <span class="text-danger">*</span></label>
                            <textarea name="content" id="page-content" class="form-control @error('content') is-invalid @enderror" 
                                      rows="10" placeholder="Write your page content here..." required>{{ old('content') }}</textarea>
                            @error('content')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <hr>
                        
                        <h6 class="mb-3">SEO Settings</h6>
                        
                        <div class="mb-3">
                            <label class="form-label">Meta Title</label>
                            <input type="text" name="meta_title" class="form-control" value="{{ old('meta_title') }}" 
                                   placeholder="SEO title (defaults to page title)">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Meta Description</label>
                            <textarea name="meta_description" class="form-control" rows="2" 
                                      placeholder="Brief description for search engines">{{ old('meta_description') }}</textarea>
                        </div>
                        
                        <hr>
                        
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input type="checkbox" name="is_active" class="form-check-input" id="isActive" 
                                       value="1" checked>
                                <label class="form-check-label" for="isActive">Publish Page</label>
                            </div>
                            <small class="text-muted">If unchecked, the page will be saved as draft</small>
                        </div>
                        
                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('admin.pages.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i> Create Page
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-lightbulb me-2"></i>Tips</h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Use descriptive titles</li>
                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Add meta descriptions for SEO</li>
                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Use headings to organize content</li>
                        <li class="mb-0"><i class="bi bi-check-circle text-success me-2"></i>Preview before publishing</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote.min.css" rel="stylesheet">
<style>
    .note-editor {
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
    }
    .note-editor .note-toolbar {
        background: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
        border-radius: 0.375rem 0.375rem 0 0;
    }
    .note-editor .note-editable {
        min-height: 300px;
    }
    .note-editor.note-frame .note-editing-area .note-editable {
        padding: 15px;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize Summernote editor
    $('#page-content').summernote({
        height: 300,
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'italic', 'underline', 'strikethrough', 'clear']],
            ['fontname', ['fontname']],
            ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['height', ['height']],
            ['table', ['table']],
            ['insert', ['link', 'picture', 'video']],
            ['view', ['fullscreen', 'codeview']],
            ['help', ['help']]
        ],
        fontNames: ['Arial', 'Arial Black', 'Comic Sans MS', 'Courier New', 'Helvetica', 'Impact', 'Tahoma', 'Times New Roman', 'Verdana'],
        fontSizes: ['8', '9', '10', '11', '12', '14', '16', '18', '20', '24', '28', '32', '36', '48', '64'],
        styleTags: [
            'p', 
            { title: 'Heading 1', tag: 'h1', value: 'h1' },
            { title: 'Heading 2', tag: 'h2', value: 'h2' },
            { title: 'Heading 3', tag: 'h3', value: 'h3' },
            { title: 'Heading 4', tag: 'h4', value: 'h4' },
            { title: 'Heading 5', tag: 'h5', value: 'h5' },
            { title: 'Heading 6', tag: 'h6', value: 'h6' },
            'blockquote', 'pre'
        ],
        callbacks: {
            onImageUpload: function(files) {
                uploadImage(files[0]);
            }
        }
    });
    
    // Image upload handler
    function uploadImage(file) {
        var formData = new FormData();
        formData.append('image', file);
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
        
        $.ajax({
            url: '{{ route("admin.media.upload") }}',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                if (response.url) {
                    $('#page-content').summernote('insertImage', response.url);
                }
            },
            error: function(xhr) {
                alert('Image upload failed. Please try again.');
            }
        });
    }
});
</script>
@endpush
