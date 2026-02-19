@extends('admin.layouts.app')

@section('title', 'Blog Settings')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0"><i class="bi bi-gear me-2"></i> Blog Settings</h4>
                <a href="{{ route('admin.blogs.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Back to Posts
                </a>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.blog-settings.update') }}" method="POST">
        @csrf
        
        <div class="row">
            <!-- General Settings -->
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-sliders me-2"></i>General Settings</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Blog Title</label>
                            <input type="text" name="blog_title" class="form-control" 
                                   value="{{ $settings['blog_title']->value ?? 'Blog' }}">
                            <small class="text-muted">Title displayed on the blog page header</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Blog Subtitle</label>
                            <input type="text" name="blog_subtitle" class="form-control" 
                                   value="{{ $settings['blog_subtitle']->value ?? '' }}">
                            <small class="text-muted">Subtitle displayed below the title</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Posts Per Page</label>
                            <input type="number" name="blog_posts_per_page" class="form-control" 
                                   value="{{ $settings['blog_posts_per_page']->value ?? 9 }}" min="3" max="30">
                            <small class="text-muted">Number of posts to display per page</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Display Options -->
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-eye me-2"></i>Display Options</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="blog_show_author" id="showAuthor" 
                                   {{ ($settings['blog_show_author']->value ?? 1) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="showAuthor">Show Author</label>
                            <small class="text-muted d-block">Display author name and avatar on posts</small>
                        </div>
                        
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="blog_show_date" id="showDate" 
                                   {{ ($settings['blog_show_date']->value ?? 1) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="showDate">Show Publish Date</label>
                            <small class="text-muted d-block">Display publication date on posts</small>
                        </div>
                        
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="blog_show_category" id="showCategory" 
                                   {{ ($settings['blog_show_category']->value ?? 1) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="showCategory">Show Category</label>
                            <small class="text-muted d-block">Display category badge on posts</small>
                        </div>
                        
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="blog_show_tags" id="showTags" 
                                   {{ ($settings['blog_show_tags']->value ?? 1) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="showTags">Show Tags</label>
                            <small class="text-muted d-block">Display tags on single post page</small>
                        </div>
                        
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="blog_show_share_buttons" id="showShare" 
                                   {{ ($settings['blog_show_share_buttons']->value ?? 1) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="showShare">Show Share Buttons</label>
                            <small class="text-muted d-block">Display social share buttons on posts</small>
                        </div>
                        
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="blog_show_related_posts" id="showRelated" 
                                   {{ ($settings['blog_show_related_posts']->value ?? 1) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="showRelated">Show Related Posts</label>
                            <small class="text-muted d-block">Display related posts carousel on single post page</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Related Posts Count</label>
                            <input type="number" name="blog_related_posts_count" class="form-control" 
                                   value="{{ $settings['blog_related_posts_count']->value ?? 4 }}" min="2" max="10">
                            <small class="text-muted">Number of related posts to display</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar Settings -->
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-layout-sidebar me-2"></i>Sidebar Settings</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="blog_sidebar_show_search" id="showSearch" 
                                   {{ ($settings['blog_sidebar_show_search']->value ?? 1) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="showSearch">Show Search</label>
                            <small class="text-muted d-block">Display search box in sidebar</small>
                        </div>
                        
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="blog_sidebar_show_categories" id="showCategories" 
                                   {{ ($settings['blog_sidebar_show_categories']->value ?? 1) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="showCategories">Show Categories</label>
                            <small class="text-muted d-block">Display categories list in sidebar</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('admin.blogs.index') }}" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg me-1"></i> Save Settings
            </button>
        </div>
    </form>
</div>
@endsection
