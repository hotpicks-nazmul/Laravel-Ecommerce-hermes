@extends('admin.layouts.app')

@section('title', 'Edit Blog Post')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Edit Blog Post</h4>
                <div class="d-flex gap-2">
                    @if($blog->status === 'published')
                    <a href="{{ route('blogs.show', $blog->slug) }}" target="_blank" class="btn btn-outline-primary">
                        <i class="bi bi-eye me-1"></i> View Post
                    </a>
                    @endif
                    <a href="{{ route('admin.blogs.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form action="{{ route('admin.blogs.update', $blog->slug) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" 
                                   value="{{ old('title', $blog->title) }}" placeholder="Enter blog post title" required>
                            @error('title')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Slug</label>
                            <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror" 
                                   value="{{ old('slug', $blog->slug) }}" placeholder="url-friendly-slug">
                            @error('slug')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Excerpt</label>
                            <textarea name="excerpt" class="form-control" rows="2" placeholder="Brief summary of the post">{{ old('excerpt', $blog->excerpt) }}</textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Content <span class="text-danger">*</span></label>
                            <textarea name="content" id="blog-content" class="form-control @error('content') is-invalid @enderror" 
                                      rows="10" placeholder="Write your blog content here..." required>{{ old('content', $blog->content) }}</textarea>
                            @error('content')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Featured Image</label>
                                <input type="file" name="featured_image" class="form-control" accept="image/*">
                                @if($blog->featured_image)
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $blog->featured_image) }}" alt="Current image" 
                                         class="img-thumbnail" style="max-height: 100px;">
                                    <small class="text-muted d-block mt-1">Current image. Upload new to replace.</small>
                                </div>
                                @endif
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Category</label>
                                <select name="category_id" class="form-select">
                                    <option value="">-- Select Category --</option>
                                    @foreach($categories ?? [] as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id', $blog->category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tags</label>
                            <input type="text" name="tags" class="form-control" 
                                   value="{{ old('tags', is_array($blog->tags) ? implode(', ', $blog->tags) : $blog->tags) }}" 
                                   placeholder="Enter tags separated by commas">
                            <small class="text-muted">Example: halal, recipes, tips</small>
                        </div>
                        
                        <hr>
                        
                        <h6 class="mb-3">SEO Settings</h6>
                        
                        <div class="mb-3">
                            <label class="form-label">Meta Title</label>
                            <input type="text" name="meta_title" class="form-control" 
                                   value="{{ old('meta_title', $blog->meta_title) }}" 
                                   placeholder="SEO title (defaults to post title)">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Meta Description</label>
                            <textarea name="meta_description" class="form-control" rows="2" 
                                      placeholder="Brief description for search engines">{{ old('meta_description', $blog->meta_description) }}</textarea>
                        </div>
                        
                        <hr>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Status</label>
                                <select name="status" class="form-select">
                                    <option value="draft" {{ old('status', $blog->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="published" {{ old('status', $blog->status) == 'published' ? 'selected' : '' }}>Published</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Publish Date</label>
                                <input type="datetime-local" name="published_at" class="form-control" 
                                       value="{{ old('published_at', $blog->published_at ? $blog->published_at->format('Y-m-d\TH:i') : '') }}">
                                <small class="text-muted">Leave empty to publish immediately when status is published</small>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('admin.blogs.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i> Update Post
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Post Info</h6>
                </div>
                <div class="card-body">
                    <p class="mb-2"><strong>Author:</strong> {{ $blog->author->name ?? 'N/A' }}</p>
                    <p class="mb-2"><strong>Created:</strong> {{ $blog->created_at->format('M d, Y H:i') }}</p>
                    <p class="mb-0"><strong>Last Updated:</strong> {{ $blog->updated_at->format('M d, Y H:i') }}</p>
                </div>
            </div>
            
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-lightbulb me-2"></i>Tips</h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Use a catchy title</li>
                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Add a featured image</li>
                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Write engaging content</li>
                        <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Use relevant tags</li>
                        <li class="mb-0"><i class="bi bi-check-circle text-success me-2"></i>Optimize for SEO</li>
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
    $('#blog-content').summernote({
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
                    $('#blog-content').summernote('insertImage', response.url);
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
