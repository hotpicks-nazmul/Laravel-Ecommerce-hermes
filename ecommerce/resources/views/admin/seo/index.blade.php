@extends('admin.layouts.app')

@section('title', 'SEO Settings')

@section('content')
<div class="mb-4">
    <h4 class="mb-0">SEO Settings</h4>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">Meta Settings</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.seo.meta.update') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label">Site Meta Title</label>
                        <input type="text" name="meta_title" class="form-control" value="{{ old('meta_title', $settings['meta_title'] ?? '') }}">
                        <small class="text-muted">Default title for SEO (50-60 characters recommended)</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Site Meta Description</label>
                        <textarea name="meta_description" class="form-control" rows="3">{{ old('meta_description', $settings['meta_description'] ?? '') }}</textarea>
                        <small class="text-muted">Default description for SEO (150-160 characters recommended)</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Site Meta Keywords</label>
                        <input type="text" name="meta_keywords" class="form-control" value="{{ old('meta_keywords', $settings['meta_keywords'] ?? '') }}">
                        <small class="text-muted">Comma separated keywords</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Google Analytics ID</label>
                        <input type="text" name="google_analytics_id" class="form-control" value="{{ old('google_analytics_id', $settings['google_analytics_id'] ?? '') }}" placeholder="G-XXXXXXXXXX">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Google Search Console Code</label>
                        <input type="text" name="google_search_console" class="form-control" value="{{ old('google_search_console', $settings['google_search_console'] ?? '') }}">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Facebook Pixel ID</label>
                        <input type="text" name="facebook_pixel_id" class="form-control" value="{{ old('facebook_pixel_id', $settings['facebook_pixel_id'] ?? '') }}">
                    </div>
                    
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i> Save SEO Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">URL Redirects</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.seo.redirects.store') }}" method="POST" class="row g-2 mb-3">
                    @csrf
                    <div class="col-md-5">
                        <input type="text" name="from_url" class="form-control" placeholder="From URL (e.g., /old-page)">
                    </div>
                    <div class="col-md-5">
                        <input type="text" name="to_url" class="form-control" placeholder="To URL (e.g., /new-page)">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Add</button>
                    </div>
                </form>
                
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>From</th>
                                <th>To</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($redirects ?? [] as $redirect)
                            <tr>
                                <td><code>{{ $redirect->from_url }}</code></td>
                                <td><code>{{ $redirect->to_url }}</code></td>
                                <td>
                                    <form action="{{ route('admin.seo.redirects.destroy', $redirect->id) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted">No redirects configured</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Sitemap</h5>
            </div>
            <div class="card-body">
                <p class="text-muted">Generate a sitemap.xml file for search engines.</p>
                <form action="{{ route('admin.seo.sitemap.generate') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-arrow-clockwise me-1"></i> Generate Sitemap
                    </button>
                </form>
                <hr>
                <p class="mb-1"><strong>Sitemap URL:</strong></p>
                <code>{{ url('sitemap.xml') }}</code>
            </div>
        </div>
        
        <div class="card mt-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">Open Graph</h5>
            </div>
            <div class="card-body">
                <p class="text-muted">Default Open Graph settings for social sharing.</p>
                <div class="mb-3">
                    <label class="form-label">OG Image URL</label>
                    <input type="text" name="og_image" class="form-control" value="{{ $settings['og_image'] ?? '' }}">
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
