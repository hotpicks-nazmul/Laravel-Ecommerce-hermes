@extends('admin.layouts.app')

@section('title', 'SEO Settings')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">SEO Settings</h4>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-gear me-2"></i>Meta Settings</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.seo.meta.update') }}" method="POST" id="seo-form">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="meta_title" class="form-label">Site Meta Title <span class="text-danger">*</span></label>
                        <input type="text" id="meta_title" name="meta_title" class="form-control" value="{{ old('meta_title', $settings['site_meta_title'] ?? '') }}">
                        <div class="form-text">Default title for SEO (50-60 characters recommended)</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="meta_description" class="form-label">Site Meta Description</label>
                        <textarea id="meta_description" name="meta_description" class="form-control" rows="3">{{ old('meta_description', $settings['site_meta_description'] ?? '') }}</textarea>
                        <div class="form-text">Default description for SEO (150-160 characters recommended)</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="meta_keywords" class="form-label">Site Meta Keywords</label>
                        <input type="text" id="meta_keywords" name="meta_keywords" class="form-control" value="{{ old('meta_keywords', $settings['site_meta_keywords'] ?? '') }}">
                        <div class="form-text">Comma separated keywords</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="google_analytics_id" class="form-label">Google Analytics ID</label>
                        <input type="text" id="google_analytics_id" name="google_analytics_id" class="form-control" value="{{ old('google_analytics_id', $settings['google_analytics_id'] ?? '') }}" placeholder="G-XXXXXXXXXX">
                    </div>
                    
                    <div class="mb-3">
                        <label for="google_search_console" class="form-label">Google Search Console Code</label>
                        <input type="text" id="google_search_console" name="google_search_console" class="form-control" value="{{ old('google_search_console', $settings['google_search_console'] ?? '') }}">
                    </div>
                    
                    <div class="mb-3">
                        <label for="facebook_pixel_id" class="form-label">Facebook Pixel ID</label>
                        <input type="text" id="facebook_pixel_id" name="facebook_pixel_id" class="form-control" value="{{ old('facebook_pixel_id', $settings['facebook_pixel_id'] ?? '') }}">
                    </div>
                </form>
            </div>
        </div>
        
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-arrow-left-right me-2"></i>URL Redirects</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.seo.redirects.store') }}" method="POST" class="row g-2 mb-3">
                    @csrf
                    <div class="col-md-4">
                        <input type="text" name="from" class="form-control" placeholder="From URL (e.g., /old-page)" required>
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="to" class="form-control" placeholder="To URL (e.g., /new-page)" required>
                    </div>
                    <div class="col-md-2">
                        <select name="type" class="form-select">
                            <option value="301">301 (Permanent)</option>
                            <option value="302">302 (Temporary)</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-plus-lg me-1"></i> Add
                        </button>
                    </div>
                </form>
                
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>From</th>
                                <th>To</th>
                                <th>Type</th>
                                <th style="width: 80px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($redirects ?? [] as $index => $redirect)
                            <tr>
                                <td><code>{{ $redirect['from'] ?? '' }}</code></td>
                                <td><code>{{ $redirect['to'] ?? '' }}</code></td>
                                <td><span class="badge bg-{{ $redirect['type'] == 301 ? 'success' : 'warning' }}">{{ $redirect['type'] }}</span></td>
                                <td>
                                    <form action="{{ route('admin.seo.redirects.destroy', $index) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4">
                                    <i class="bi bi-arrow-left-right text-muted" style="font-size: 2rem;"></i>
                                    <p class="text-muted mb-0 mt-2">No redirects configured</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-diagram-3 me-2"></i>Sitemap</h6>
            </div>
            <div class="card-body">
                <p class="text-muted small">Generate a sitemap.xml file for search engines.</p>
                <form action="{{ route('admin.seo.sitemap.generate') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-arrow-clockwise me-1"></i> Generate Sitemap
                    </button>
                </form>
                <hr>
                <p class="mb-1 small"><strong>Sitemap URL:</strong></p>
                <code class="small">{{ url('sitemap.xml') }}</code>
            </div>
        </div>
        
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-share me-2"></i>Open Graph</h6>
            </div>
            <div class="card-body">
                <p class="text-muted small">Default Open Graph settings for social sharing.</p>
                <form action="{{ route('admin.seo.meta.update') }}" method="POST" id="og-form">
                    @csrf
                    <div class="mb-3">
                        <label for="og_title" class="form-label">OG Title</label>
                        <input type="text" id="og_title" name="og_title" class="form-control form-control-sm" value="{{ old('og_title', $settings['og_title'] ?? '') }}">
                    </div>
                    <div class="mb-3">
                        <label for="og_description" class="form-label">OG Description</label>
                        <textarea id="og_description" name="og_description" class="form-control form-control-sm" rows="2">{{ old('og_description', $settings['og_description'] ?? '') }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label for="og_image" class="form-label">OG Image URL</label>
                        <input type="text" id="og_image" name="og_image" class="form-control form-control-sm" value="{{ old('og_image', $settings['og_image'] ?? '') }}">
                    </div>
                </form>
            </div>
        </div>
        
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-twitter me-2"></i>Twitter Card</h6>
            </div>
            <div class="card-body">
                <p class="text-muted small">Default Twitter Card settings.</p>
                <form action="{{ route('admin.seo.meta.update') }}" method="POST" id="twitter-form">
                    @csrf
                    <div class="mb-2">
                        <label for="twitter_card_type" class="form-label">Card Type</label>
                        <select id="twitter_card_type" name="twitter_card_type" class="form-select form-select-sm">
                            <option value="summary" {{ ($settings['twitter_card_type'] ?? '') == 'summary' ? 'selected' : '' }}>Summary</option>
                            <option value="summary_large_image" {{ ($settings['twitter_card_type'] ?? '') == 'summary_large_image' ? 'selected' : '' }}>Summary with Large Image</option>
                        </select>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Floating Save Button -->
<div class="floating-save-container">
    <button type="submit" form="seo-form" class="btn btn-primary floating-save-btn">
        <i class="bi bi-check-lg me-1"></i> Save SEO Settings
    </button>
</div>
@endsection
