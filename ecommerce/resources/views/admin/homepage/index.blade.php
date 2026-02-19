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

    <!-- Section Order - Drag and Drop -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-arrows-move me-2 text-primary"></i>
                        Section Order (Drag & Drop)
                    </h5>
                    <span class="badge bg-info">Drag sections to reorder</span>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">Drag and drop sections below to change their order on the homepage. The order from top to bottom will be reflected on the frontend.</p>
                    
                    <div id="section-order-container" class="section-order-list">
                        @foreach($sectionOrder as $index => $sectionKey)
                            @if(isset($availableSections[$sectionKey]))
                            <div class="section-item" data-section="{{ $sectionKey }}" draggable="true">
                                <div class="section-card">
                                    <div class="drag-handle">
                                        <i class="bi bi-grip-vertical"></i>
                                    </div>
                                    <div class="section-icon">
                                        <i class="bi {{ $availableSections[$sectionKey]['icon'] }}"></i>
                                    </div>
                                    <div class="section-info">
                                        <h6 class="section-title">{{ $availableSections[$sectionKey]['label'] }}</h6>
                                        <small class="section-desc">{{ $availableSections[$sectionKey]['description'] }}</small>
                                    </div>
                                    <div class="section-position">
                                        <span class="position-badge">{{ $index + 1 }}</span>
                                    </div>
                                </div>
                            </div>
                            @endif
                        @endforeach
                    </div>
                    
                    <div class="mt-3">
                        <div id="order-save-status" class="alert alert-success d-none">
                            <i class="bi bi-check-circle me-2"></i>
                            <span>Section order saved successfully!</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.homepage.update') }}" method="POST" enctype="multipart/form-data" id="homepage-form">
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
                        
                        <hr class="my-4">
                        
                        <h6 class="mb-3">Section Column Settings</h6>
                        <p class="text-muted small mb-3">Set columns for each product section independently</p>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">Featured Columns</label>
                                @php $featuredCols = $homeSettings['homepage_featured_columns']->value ?? 6; @endphp
                                <select name="homepage_featured_columns" class="form-select">
                                    @for($i = 2; $i <= 6; $i++)
                                        <option value="{{ $i }}" {{ $featuredCols == $i ? 'selected' : '' }}>{{ $i }} Columns</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">New Arrivals Columns</label>
                                @php $newArrivalsCols = $homeSettings['homepage_new_arrivals_columns']->value ?? 6; @endphp
                                <select name="homepage_new_arrivals_columns" class="form-select">
                                    @for($i = 2; $i <= 6; $i++)
                                        <option value="{{ $i }}" {{ $newArrivalsCols == $i ? 'selected' : '' }}>{{ $i }} Columns</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">Sale Columns</label>
                                @php $saleCols = $homeSettings['homepage_sale_columns']->value ?? 6; @endphp
                                <select name="homepage_sale_columns" class="form-select">
                                    @for($i = 2; $i <= 6; $i++)
                                        <option value="{{ $i }}" {{ $saleCols == $i ? 'selected' : '' }}>{{ $i }} Columns</option>
                                    @endfor
                                </select>
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
        
        <!-- Why Choose Us Section -->
        <div class="row">
            <div class="col-12 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">
                            <i class="bi bi-patch-check me-2 text-primary"></i>
                            Why Choose Us Section
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Section Title</label>
                                <input type="text" name="why_choose_us_title" 
                                       class="form-control" 
                                       value="{{ $homeSettings['why_choose_us_title']->value ?? 'Why Choose Us?' }}"
                                       placeholder="Why Choose Us?">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Section Subtitle</label>
                                <input type="text" name="why_choose_us_subtitle" 
                                       class="form-control" 
                                       value="{{ $homeSettings['why_choose_us_subtitle']->value ?? 'We are committed to providing the best halal products' }}"
                                       placeholder="We are committed to providing the best halal products">
                            </div>
                        </div>
                        
                        <hr class="my-4">
                        <h6 class="mb-3">Feature Cards</h6>
                        
                        @for($i = 1; $i <= 4; $i++)
                        <div class="row mb-3 align-items-end">
                            <div class="col-12 mb-2">
                                <span class="badge bg-primary">Feature {{ $i }}</span>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Icon (Bootstrap)</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="{{ $homeSettings['why_choose_us_icon_' . $i]->value ?? 'bi-patch-check-fill' }}"></i></span>
                                    <input type="text" name="why_choose_us_icon_{{ $i }}" 
                                           class="form-control icon-input" 
                                           data-preview="icon-preview-{{ $i }}"
                                           value="{{ $homeSettings['why_choose_us_icon_' . $i]->value ?? 'bi-patch-check-fill' }}"
                                           placeholder="bi-patch-check-fill">
                                </div>
                                <p class="text-muted small mt-1">e.g., bi-truck, bi-shield-check</p>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Title</label>
                                <input type="text" name="why_choose_us_title_{{ $i }}" 
                                       class="form-control" 
                                       value="{{ $homeSettings['why_choose_us_title_' . $i]->value ?? '' }}"
                                       placeholder="Feature Title">
                            </div>
                            <div class="col-md-5 mb-3">
                                <label class="form-label">Description</label>
                                <input type="text" name="why_choose_us_desc_{{ $i }}" 
                                       class="form-control" 
                                       value="{{ $homeSettings['why_choose_us_desc_' . $i]->value ?? '' }}"
                                       placeholder="Feature description">
                            </div>
                        </div>
                        @endfor
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
        
        <!-- Banner Section Settings -->
        <div class="row">
            <div class="col-12 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">
                            <i class="bi bi-megaphone me-2 text-success"></i>
                            Banner Section Settings
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @php
                                $bannerColors = [
                                    1 => ['color' => 'primary', 'label' => 'Green', 'gradient' => 'from-halal-green to-green-600'],
                                    2 => ['color' => 'warning', 'label' => 'Gold', 'gradient' => 'from-halal-gold to-yellow-500'],
                                    3 => ['color' => 'info', 'label' => 'Blue', 'gradient' => 'from-blue-500 to-blue-600'],
                                    4 => ['color' => 'danger', 'label' => 'Red', 'gradient' => 'from-red-500 to-red-600'],
                                ];
                            @endphp
                            @for($i = 1; $i <= 4; $i++)
                            <div class="col-md-6 col-lg-3 mb-4">
                                <div class="border rounded p-3 h-100">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="mb-0 text-{{ $bannerColors[$i]['color'] }}">
                                            <i class="bi bi-{{ $i }}-circle me-2"></i>Banner {{ $i }} ({{ $bannerColors[$i]['label'] }})
                                        </h6>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="banner{{ $i }}_visible"
                                                   id="banner{{ $i }}_visible" value="1"
                                                   {{ ($homeSettings['banner' . $i . '_visible']->value ?? '1') == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label small" for="banner{{ $i }}_visible">Show</label>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Badge Text</label>
                                        <input type="text" name="banner{{ $i }}_badge" 
                                               class="form-control" 
                                               value="{{ $homeSettings['banner' . $i . '_badge']->value ?? '' }}"
                                               placeholder="{{ $i == 1 ? 'Special Offer' : ($i == 2 ? 'Flash Sale' : ($i == 3 ? 'New Arrival' : 'Hot Deal')) }}">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Title</label>
                                        <input type="text" name="banner{{ $i }}_title" 
                                               class="form-control" 
                                               value="{{ $homeSettings['banner' . $i . '_title']->value ?? '' }}"
                                               placeholder="{{ $i == 1 ? 'Weekend Special!' : ($i == 2 ? 'Flash Sale!' : ($i == 3 ? 'Fresh Products' : 'Best Sellers')) }}">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Description</label>
                                        <textarea name="banner{{ $i }}_description" class="form-control" rows="2" placeholder="Banner description">{{ $homeSettings['banner' . $i . '_description']->value ?? '' }}</textarea>
                                    </div>
                                    <div class="row">
                                        <div class="col-6 mb-3">
                                            <label class="form-label">Button Text</label>
                                            <input type="text" name="banner{{ $i }}_button_text" 
                                                   class="form-control" 
                                                   value="{{ $homeSettings['banner' . $i . '_button_text']->value ?? '' }}"
                                                   placeholder="Shop Now">
                                        </div>
                                        <div class="col-6 mb-3">
                                            <label class="form-label">Link</label>
                                            <input type="text" name="banner{{ $i }}_link" 
                                                   class="form-control" 
                                                   value="{{ $homeSettings['banner' . $i . '_link']->value ?? '' }}"
                                                   placeholder="category-slug">
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Icon (Bootstrap)</label>
                                        <input type="text" name="banner{{ $i }}_icon" 
                                               class="form-control" 
                                               value="{{ $homeSettings['banner' . $i . '_icon']->value ?? '' }}"
                                               placeholder="bi-star-fill">
                                    </div>
                                </div>
                            </div>
                            @endfor
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Testimonials Section Settings -->
        <div class="row">
            <div class="col-12 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">
                            <i class="bi bi-chat-quote me-2 text-secondary"></i>
                            Testimonials Section Settings
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Section Title</label>
                                <input type="text" name="testimonials_title" 
                                       class="form-control" 
                                       value="{{ $homeSettings['testimonials_title']->value ?? 'What Our Customers Say' }}"
                                       placeholder="What Our Customers Say">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Section Subtitle</label>
                                <input type="text" name="testimonials_subtitle" 
                                       class="form-control" 
                                       value="{{ $homeSettings['testimonials_subtitle']->value ?? 'Trusted by thousands of customers across Bangladesh' }}"
                                       placeholder="Trusted by thousands of customers across Bangladesh">
                            </div>
                        </div>
                        
                        <hr class="my-4">
                        
                        <div class="row">
                            @for($i = 1; $i <= 3; $i++)
                            <div class="col-md-4 mb-3">
                                <div class="border rounded p-3 h-100">
                                    <h6 class="mb-3"><span class="badge bg-secondary">Testimonial {{ $i }}</span></h6>
                                    <div class="mb-3">
                                        <label class="form-label">Customer Name</label>
                                        <input type="text" name="testimonial{{ $i }}_name" 
                                               class="form-control" 
                                               value="{{ $homeSettings['testimonial' . $i . '_name']->value ?? '' }}"
                                               placeholder="John Doe">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Location</label>
                                        <input type="text" name="testimonial{{ $i }}_location" 
                                               class="form-control" 
                                               value="{{ $homeSettings['testimonial' . $i . '_location']->value ?? '' }}"
                                               placeholder="Dhaka">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Review Text</label>
                                        <textarea name="testimonial{{ $i }}_text" class="form-control" rows="3" placeholder="Customer review...">{{ $homeSettings['testimonial' . $i . '_text']->value ?? '' }}</textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Rating (1-5)</label>
                                        <select name="testimonial{{ $i }}_rating" class="form-select">
                                            @for($r = 1; $r <= 5; $r++)
                                                <option value="{{ $r }}" {{ ($homeSettings['testimonial' . $i . '_rating']->value ?? 5) == $r ? 'selected' : '' }}>{{ $r }} Star{{ $r > 1 ? 's' : '' }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                            </div>
                            @endfor
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Floating Save Button -->
<div class="floating-save-container">
    <button type="submit" form="homepage-form" class="btn btn-primary floating-save-btn">
        <i class="bi bi-check-lg me-1"></i> Save Settings
    </button>
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
    
    /* Vertical Drag and Drop Styles */
    .section-order-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
        max-width: 600px;
    }
    
    .section-item {
        background: #fff;
        border-radius: 12px;
        transition: all 0.2s ease;
        user-select: none;
    }
    
    .section-item .section-card {
        display: flex;
        align-items: center;
        padding: 16px 20px;
        background: #fff;
        border: 2px solid #e9ecef;
        border-radius: 12px;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: grab;
    }
    
    .section-item:hover .section-card {
        border-color: #667eea;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
        transform: translateY(-2px);
    }
    
    .section-item.dragging {
        opacity: 0.5;
    }
    
    .section-item.dragging .section-card {
        transform: scale(1.02);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.25);
    }
    
    .section-item.drag-over {
        position: relative;
    }
    
    .section-item.drag-over::before {
        content: '';
        position: absolute;
        top: -6px;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #667eea, #764ba2);
        border-radius: 2px;
        animation: pulse 1s infinite;
    }
    
    .section-item.drag-over::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        border: 2px dashed #667eea;
        border-radius: 12px;
        background: rgba(102, 126, 234, 0.05);
        pointer-events: none;
    }
    
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }
    
    .drag-handle {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        margin-right: 16px;
        color: #adb5bd;
        transition: color 0.2s ease;
    }
    
    .section-item:hover .drag-handle {
        color: #667eea;
    }
    
    .drag-handle i {
        font-size: 1.25rem;
    }
    
    .section-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 48px;
        height: 48px;
        margin-right: 16px;
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1));
        border-radius: 12px;
        transition: all 0.2s ease;
    }
    
    .section-icon i {
        font-size: 1.25rem;
        color: #667eea;
    }
    
    .section-item:hover .section-icon {
        background: linear-gradient(135deg, #667eea, #764ba2);
    }
    
    .section-item:hover .section-icon i {
        color: #fff;
    }
    
    .section-info {
        flex: 1;
    }
    
    .section-title {
        margin: 0;
        font-size: 1rem;
        font-weight: 600;
        color: #343a40;
        transition: color 0.2s ease;
    }
    
    .section-item:hover .section-title {
        color: #667eea;
    }
    
    .section-desc {
        color: #6c757d;
        font-size: 0.85rem;
    }
    
    .section-position {
        display: flex;
        align-items: center;
        justify-content: center;
        margin-left: 16px;
    }
    
    .position-badge {
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 32px;
        height: 32px;
        padding: 0 10px;
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: #fff;
        font-size: 0.875rem;
        font-weight: 600;
        border-radius: 8px;
        transition: all 0.2s ease;
    }
    
    .section-item:hover .position-badge {
        transform: scale(1.1);
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('section-order-container');
    const saveStatus = document.getElementById('order-save-status');
    let draggedItem = null;
    let placeholder = null;
    
    // Create placeholder element
    function createPlaceholder() {
        const div = document.createElement('div');
        div.className = 'section-item placeholder';
        div.innerHTML = '<div class="section-card" style="border: 2px dashed #667eea; background: rgba(102, 126, 234, 0.05); min-height: 80px;"></div>';
        return div;
    }
    
    // Add drag events to each section item
    const sections = container.querySelectorAll('.section-item');
    
    sections.forEach(item => {
        item.addEventListener('dragstart', function(e) {
            draggedItem = this;
            placeholder = createPlaceholder();
            
            setTimeout(() => {
                this.classList.add('dragging');
                this.style.opacity = '0.5';
            }, 0);
            
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/plain', '');
        });
        
        item.addEventListener('dragend', function(e) {
            this.classList.remove('dragging');
            this.style.opacity = '1';
            
            // Remove placeholder if exists
            if (placeholder && placeholder.parentNode) {
                placeholder.parentNode.removeChild(placeholder);
            }
            
            // Remove all drag-over classes
            document.querySelectorAll('.section-item').forEach(item => {
                item.classList.remove('drag-over');
            });
            
            updatePositions();
            saveOrder();
        });
    });
    
    // Container events
    container.addEventListener('dragover', function(e) {
        e.preventDefault();
        e.dataTransfer.dropEffect = 'move';
        
        const afterElement = getDragAfterElement(container, e.clientY);
        
        if (afterElement == null) {
            if (draggedItem !== container.lastElementChild) {
                container.appendChild(draggedItem);
            }
        } else {
            if (draggedItem !== afterElement) {
                container.insertBefore(draggedItem, afterElement);
            }
        }
    });
    
    container.addEventListener('dragenter', function(e) {
        e.preventDefault();
        const item = e.target.closest('.section-item');
        if (item && item !== draggedItem) {
            item.classList.add('drag-over');
        }
    });
    
    container.addEventListener('dragleave', function(e) {
        const item = e.target.closest('.section-item');
        if (item && !item.contains(e.relatedTarget)) {
            item.classList.remove('drag-over');
        }
    });
    
    container.addEventListener('drop', function(e) {
        e.preventDefault();
        document.querySelectorAll('.section-item').forEach(item => {
            item.classList.remove('drag-over');
        });
    });
    
    function getDragAfterElement(container, y) {
        const draggableElements = [...container.querySelectorAll('.section-item:not(.dragging)')];
        
        return draggableElements.reduce((closest, child) => {
            const box = child.getBoundingClientRect();
            const offset = y - box.top - box.height / 2;
            
            if (offset < 0 && offset > closest.offset) {
                return { offset: offset, element: child };
            } else {
                return closest;
            }
        }, { offset: Number.NEGATIVE_INFINITY }).element;
    }
    
    function updatePositions() {
        const items = container.querySelectorAll('.section-item');
        items.forEach((item, index) => {
            const badge = item.querySelector('.position-badge');
            if (badge) {
                badge.textContent = index + 1;
                // Add animation
                badge.style.transform = 'scale(1.2)';
                setTimeout(() => {
                    badge.style.transform = 'scale(1)';
                }, 150);
            }
        });
    }
    
    function saveOrder() {
        const items = container.querySelectorAll('.section-item');
        const sections = Array.from(items).map(item => item.dataset.section);
        
        fetch('{{ route("admin.homepage.section-order") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ sections: sections })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                saveStatus.classList.remove('d-none');
                saveStatus.style.animation = 'fadeIn 0.3s ease';
                setTimeout(() => {
                    saveStatus.classList.add('d-none');
                }, 3000);
            }
        })
        .catch(error => {
            console.error('Error saving section order:', error);
        });
    }
});
</script>
@endpush
