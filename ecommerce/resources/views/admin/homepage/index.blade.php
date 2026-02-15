@extends('admin.layouts.app')

@section('title', 'Home Page Settings')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Home Page Settings</h1>
                    <p class="text-muted mb-0">Customize your homepage layout and sections</p>
                </div>
                <a href="{{ route('home') }}" target="_blank" class="btn btn-outline-primary">
                    <i class="bi bi-eye me-1"></i> Preview Homepage
                </a>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.homepage.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="row">
            <!-- Site Branding Settings -->
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">
                            <i class="bi bi-palette2 me-2 text-primary"></i>
                            Site Branding
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Site Name</label>
                                <input type="text" name="site_name" 
                                       class="form-control" 
                                       value="{{ $homeSettings['site_name']->value ?? 'Halal Food' }}"
                                       placeholder="Halal Food">
                                <p class="text-muted small mt-1">This appears in the header logo area</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Site Tagline</label>
                                <input type="text" name="site_tagline" 
                                       class="form-control" 
                                       value="{{ $homeSettings['site_tagline']->value ?? 'Premium Quality Store' }}"
                                       placeholder="Premium Quality Store">
                                <p class="text-muted small mt-1">Tagline appears below the site name</p>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Logo Icon (Bootstrap Icon)</label>
                                <input type="text" name="site_logo_icon" 
                                       class="form-control" 
                                       value="{{ $homeSettings['site_logo_icon']->value ?? 'bi bi-shop' }}"
                                       placeholder="bi bi-shop">
                                <p class="text-muted small mt-1">Bootstrap icon class (e.g., bi bi-shop, bi bi-cart4)</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Custom Logo Image</label>
                                <input type="file" name="site_logo" 
                                       class="form-control" 
                                       accept="image/*">
                                @if(!empty($homeSettings['site_logo']->value))
                                <div class="mt-2">
                                    <img src="{{ $homeSettings['site_logo']->value }}" alt="Current Logo" class="img-thumbnail" style="max-height: 60px;">
                                    <p class="text-muted small mt-1">Current logo. Upload new to replace.</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Product Grid Settings -->
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">
                            <i class="bi bi-grid-3x3-gap me-2 text-primary"></i>
                            Product Grid Settings
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Product Columns (Large Screens)</label>
                            <p class="text-muted small mb-2">Select how many products to show per row on large screens</p>
                            <div class="d-flex gap-2 flex-wrap">
                                @php $columns = $homeSettings['homepage_product_columns']->value ?? 6; @endphp
                                @for($i = 2; $i <= 6; $i++)
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="homepage_product_columns" 
                                               id="col{{ $i }}" value="{{ $i }}" {{ $columns == $i ? 'checked' : '' }}>
                                        <label class="form-check-label" for="col{{ $i }}">
                                            <span class="badge bg-{{ $columns == $i ? 'primary' : 'secondary' }} fs-6 px-3 py-2">
                                                {{ $i }} Columns
                                            </span>
                                        </label>
                                    </div>
                                @endfor
                            </div>
                        </div>
                        
                        <hr class="my-4">
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">Featured Products Count</label>
                                <input type="number" name="homepage_featured_products_count" 
                                       class="form-control" 
                                       value="{{ $homeSettings['homepage_featured_products_count']->value ?? 8 }}"
                                       min="4" max="12">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">New Arrivals Count</label>
                                <input type="number" name="homepage_new_arrivals_count" 
                                       class="form-control" 
                                       value="{{ $homeSettings['homepage_new_arrivals_count']->value ?? 8 }}"
                                       min="4" max="12">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">Sale Products Count</label>
                                <input type="number" name="homepage_sale_products_count" 
                                       class="form-control" 
                                       value="{{ $homeSettings['homepage_sale_products_count']->value ?? 8 }}"
                                       min="4" max="12">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Section Visibility -->
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">
                            <i class="bi bi-eye-slash me-2 text-primary"></i>
                            Section Visibility
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="homepage_show_featured_section" 
                                           id="show_featured" value="1"
                                           {{ ($homeSettings['homepage_show_featured_section']->value ?? '1') == '1' ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold" for="show_featured">
                                        <i class="bi bi-star-fill text-warning me-1"></i> Featured Products
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="homepage_show_new_arrivals_section" 
                                           id="show_new_arrivals" value="1"
                                           {{ ($homeSettings['homepage_show_new_arrivals_section']->value ?? '1') == '1' ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold" for="show_new_arrivals">
                                        <i class="bi bi-box-seam text-info me-1"></i> New Arrivals
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="homepage_show_sale_section" 
                                           id="show_sale" value="1"
                                           {{ ($homeSettings['homepage_show_sale_section']->value ?? '1') == '1' ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold" for="show_sale">
                                        <i class="bi bi-fire text-danger me-1"></i> Hot Deals
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="homepage_show_banner_section" 
                                           id="show_banner" value="1"
                                           {{ ($homeSettings['homepage_show_banner_section']->value ?? '1') == '1' ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold" for="show_banner">
                                        <i class="bi bi-megaphone text-success me-1"></i> Banner Section
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="homepage_show_why_choose_us_section" 
                                           id="show_why_choose_us" value="1"
                                           {{ ($homeSettings['homepage_show_why_choose_us_section']->value ?? '1') == '1' ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold" for="show_why_choose_us">
                                        <i class="bi bi-patch-check text-primary me-1"></i> Why Choose Us
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="homepage_show_testimonials_section" 
                                           id="show_testimonials" value="1"
                                           {{ ($homeSettings['homepage_show_testimonials_section']->value ?? '1') == '1' ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold" for="show_testimonials">
                                        <i class="bi bi-chat-quote text-secondary me-1"></i> Testimonials
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="homepage_show_blog_section" 
                                           id="show_blog" value="1"
                                           {{ ($homeSettings['homepage_show_blog_section']->value ?? '1') == '1' ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold" for="show_blog">
                                        <i class="bi bi-newspaper text-dark me-1"></i> Blog Section
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Section Titles -->
        <div class="row">
            <div class="col-12 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">
                            <i class="bi bi-type me-2 text-primary"></i>
                            Section Titles & Subtitles
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Categories Section Title</label>
                                <input type="text" name="homepage_categories_title" 
                                       class="form-control" 
                                       value="{{ $homeSettings['homepage_categories_title']->value ?? 'Shop by Category' }}"
                                       placeholder="Shop by Category">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Categories Section Subtitle</label>
                                <input type="text" name="homepage_categories_subtitle" 
                                       class="form-control" 
                                       value="{{ $homeSettings['homepage_categories_subtitle']->value ?? 'Browse our wide range of halal products' }}"
                                       placeholder="Browse our wide range of halal products">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Featured Products Title</label>
                                <input type="text" name="homepage_featured_title" 
                                       class="form-control" 
                                       value="{{ $homeSettings['homepage_featured_title']->value ?? 'Featured Products' }}"
                                       placeholder="Featured Products">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Featured Products Subtitle</label>
                                <input type="text" name="homepage_featured_subtitle" 
                                       class="form-control" 
                                       value="{{ $homeSettings['homepage_featured_subtitle']->value ?? 'Handpicked premium quality products for you' }}"
                                       placeholder="Handpicked premium quality products for you">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">New Arrivals Title</label>
                                <input type="text" name="homepage_new_arrivals_title" 
                                       class="form-control" 
                                       value="{{ $homeSettings['homepage_new_arrivals_title']->value ?? 'New Arrivals' }}"
                                       placeholder="New Arrivals">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">New Arrivals Subtitle</label>
                                <input type="text" name="homepage_new_arrivals_subtitle" 
                                       class="form-control" 
                                       value="{{ $homeSettings['homepage_new_arrivals_subtitle']->value ?? 'Fresh products just arrived in our store' }}"
                                       placeholder="Fresh products just arrived in our store">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Hot Deals Title</label>
                                <input type="text" name="homepage_sale_title" 
                                       class="form-control" 
                                       value="{{ $homeSettings['homepage_sale_title']->value ?? 'Hot Deals' }}"
                                       placeholder="Hot Deals">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Hot Deals Subtitle</label>
                                <input type="text" name="homepage_sale_subtitle" 
                                       class="form-control" 
                                       value="{{ $homeSettings['homepage_sale_subtitle']->value ?? 'Limited time offers - Grab them before they are gone!' }}"
                                       placeholder="Limited time offers - Grab them before they're gone!">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-12">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="bi bi-check-lg me-2"></i> Save Settings
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('styles')
<style>
    .form-check-input:checked {
        background-color: #667eea;
        border-color: #667eea;
    }
    .form-check-input:checked + .form-check-label .badge {
        background-color: #667eea !important;
    }
    .card {
        border-radius: 12px;
    }
    .card-header {
        border-radius: 12px 12px 0 0 !important;
    }
</style>
@endpush
