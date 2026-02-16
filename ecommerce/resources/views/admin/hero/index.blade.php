@extends('admin.layouts.app')

@section('title', 'Hero Section Settings')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 py-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="mb-1 fw-bold">
                            <i class="bi bi-image-fill text-primary me-2"></i> Hero Section Settings
                        </h4>
                        <p class="text-muted mb-0 small">Customize the hero section displayed on your homepage</p>
                    </div>
                    <div class="btn-group">
                        <a href="{{ route('home') }}" target="_blank" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-eye me-1"></i> Preview
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hero Type Selector -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-semibold">
                    <i class="bi bi-toggle-on text-primary me-2"></i>
                    Hero Section Type
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.hero.update-type') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Select Hero Type</label>
                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="hero_type" id="hero_standard" value="standard" 
                                    {{ ($heroSettings['hero_type']->value ?? 'standard') === 'standard' ? 'checked' : '' }}>
                                <label class="btn btn-outline-primary" for="hero_standard">
                                    <i class="bi bi-card-image me-1"></i> Standard Hero
                                </label>
                                
                                <input type="radio" class="btn-check" name="hero_type" id="hero_slider" value="slider"
                                    {{ ($heroSettings['hero_type']->value ?? 'standard') === 'slider' ? 'checked' : '' }}>
                                <label class="btn btn-outline-primary" for="hero_slider">
                                    <i class="bi bi-images me-1"></i> Image Slider
                                </label>
                            </div>
                            <div class="form-text mt-2">
                                <strong>Standard Hero:</strong> Displays a static hero section with customizable content.<br>
                                <strong>Image Slider:</strong> Displays a rotating image slider using slides from the Sliders management.
                            </div>
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i> Update Hero Type
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Slider Info Card (shown when slider type is selected) -->
@if(($heroSettings['hero_type']->value ?? 'standard') === 'slider')
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm border-start border-4 border-info">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="bi bi-info-circle-fill text-info fs-3"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-1 fw-semibold">Image Slider Mode Active</h6>
                        <p class="mb-0 text-muted small">
                            The homepage will display an image slider. You can manage slider images from the 
                            <a href="{{ route('admin.sliders.index') }}" class="text-primary fw-medium">Sliders Management</a> page.
                        </p>
                    </div>
                    <div>
                        <a href="{{ route('admin.sliders.index') }}" class="btn btn-info btn-sm">
                            <i class="bi bi-gear me-1"></i> Manage Sliders
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Slider Preview -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-semibold">
                    <i class="bi bi-images text-primary me-2"></i>
                    Active Sliders Preview
                </h5>
            </div>
            <div class="card-body">
                @if($sliders->count() > 0)
                <div id="sliderPreview" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-indicators">
                        @foreach($sliders as $index => $slider)
                        <button type="button" data-bs-target="#sliderPreview" data-bs-slide-to="{{ $index }}" 
                            class="{{ $index === 0 ? 'active' : '' }}"></button>
                        @endforeach
                    </div>
                    <div class="carousel-inner rounded overflow-hidden">
                        @foreach($sliders as $index => $slider)
                        <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                            <img src="{{ Storage::url($slider->image) }}" class="d-block w-100" alt="{{ $slider->title }}" style="max-height: 400px; object-fit: cover;">
                            <div class="carousel-caption d-none d-md-block" style="background: rgba(0,0,0,0.5); border-radius: 10px;">
                                <h5>{{ $slider->title }}</h5>
                                <p>{{ $slider->subtitle }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#sliderPreview" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#sliderPreview" data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                    </button>
                </div>
                @else
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-images fs-1"></i>
                    <p class="mt-2">No active sliders found.</p>
                    <a href="{{ route('admin.sliders.create') }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus me-1"></i> Add Slider
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endif

<!-- Standard Hero Settings (shown when standard type is selected) -->
@if(($heroSettings['hero_type']->value ?? 'standard') === 'standard')
<form action="{{ route('admin.hero.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="row">
        <!-- Left Column -->
        <div class="col-lg-8">
            <!-- Background Image -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-semibold">
                        <span class="badge bg-primary me-2">1</span>
                        Background Image
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <label class="form-label fw-medium">Upload Background Image</label>
                            <input type="file" name="hero_background_image" 
                                class="form-control" accept="image/*">
                            <div class="form-text">Recommended size: 1920x1080px. JPG or PNG format.</div>
                        </div>
                        <div class="col-md-4">
                            @if($heroSettings->has('hero_background_image') && $heroSettings['hero_background_image']->value)
                            <div class="position-relative">
                                <img src="{{ $heroSettings['hero_background_image']->value }}" 
                                    alt="Current Background" 
                                    class="img-fluid rounded shadow-sm" style="max-height: 120px;">
                                <span class="badge bg-success position-absolute top-0 end-0 m-1">Current</span>
                            </div>
                            @else
                            <div class="text-center text-muted py-3 border rounded bg-light">
                                <i class="bi bi-image fs-1"></i>
                                <p class="mb-0 small">No image uploaded</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Badge Section -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-semibold">
                        <span class="badge bg-warning text-dark me-2">2</span>
                        Badge Section
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">
                                <i class="bi bi-bootstrap me-1"></i> Icon Class
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-star-fill"></i></span>
                                <input type="text" name="hero_badge_icon" 
                                    value="{{ $heroSettings['hero_badge_icon']->value ?? 'bi bi-patch-check-fill' }}"
                                    class="form-control" placeholder="bi bi-patch-check-fill">
                            </div>
                            <div class="form-text">Use Bootstrap Icons classes (e.g., bi bi-patch-check-fill)</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Badge Text</label>
                            <input type="text" name="hero_badge_text" 
                                value="{{ $heroSettings['hero_badge_text']->value ?? '' }}"
                                class="form-control" placeholder="Trusted by 10,000+ Customers">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Title Section -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-semibold">
                        <span class="badge bg-info me-2">3</span>
                        Title Section
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Title Line 1</label>
                            <input type="text" name="hero_title_line1" 
                                value="{{ $heroSettings['hero_title_line1']->value ?? '' }}"
                                class="form-control" placeholder="Fresh">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">
                                Title Highlight 1 
                                <span class="badge bg-warning text-dark ms-1">Gold</span>
                            </label>
                            <input type="text" name="hero_title_highlight1" 
                                value="{{ $heroSettings['hero_title_highlight1']->value ?? '' }}"
                                class="form-control" placeholder="Halal Food">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Title Line 2</label>
                            <input type="text" name="hero_title_line2" 
                                value="{{ $heroSettings['hero_title_line2']->value ?? '' }}"
                                class="form-control" placeholder="Delivered Fresh">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">
                                Title Line 3
                                <span class="badge bg-success ms-1">Green</span>
                            </label>
                            <input type="text" name="hero_title_line3" 
                                value="{{ $heroSettings['hero_title_line3']->value ?? '' }}"
                                class="form-control" placeholder="To Your Door">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-semibold">
                        <span class="badge bg-secondary me-2">4</span>
                        Description
                    </h5>
                </div>
                <div class="card-body">
                    <label class="form-label fw-medium">Hero Description</label>
                    <textarea name="hero_description" rows="3"
                        class="form-control" placeholder="Premium quality halal meat, poultry, seafood & groceries...">{{ $heroSettings['hero_description']->value ?? '' }}</textarea>
                    <div class="form-text">Describe your products and services in an engaging way.</div>
                </div>
            </div>

            <!-- CTA Buttons -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-semibold">
                        <span class="badge bg-danger me-2">5</span>
                        Call-to-Action Buttons
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <!-- Primary Button -->
                        <div class="col-md-6">
                            <div class="card h-100 border">
                                <div class="card-header bg-warning bg-opacity-10 py-2">
                                    <h6 class="mb-0">
                                        <i class="bi bi-star-fill text-warning me-2"></i>
                                        Primary Button
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label small">Button Text</label>
                                        <input type="text" name="hero_cta1_text" 
                                            value="{{ $heroSettings['hero_cta1_text']->value ?? '' }}"
                                            class="form-control form-control-sm" placeholder="Shop Now">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small">Route Name</label>
                                        <input type="text" name="hero_cta1_link" 
                                            value="{{ $heroSettings['hero_cta1_link']->value ?? '' }}"
                                            class="form-control form-control-sm" placeholder="products.index">
                                    </div>
                                    <div>
                                        <label class="form-label small">Icon Class</label>
                                        <input type="text" name="hero_cta1_icon" 
                                            value="{{ $heroSettings['hero_cta1_icon']->value ?? '' }}"
                                            class="form-control form-control-sm" placeholder="bi bi-cart3">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Secondary Button -->
                        <div class="col-md-6">
                            <div class="card h-100 border">
                                <div class="card-header bg-light py-2">
                                    <h6 class="mb-0">
                                        <i class="bi bi-fire text-danger me-2"></i>
                                        Secondary Button
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label small">Button Text</label>
                                        <input type="text" name="hero_cta2_text" 
                                            value="{{ $heroSettings['hero_cta2_text']->value ?? '' }}"
                                            class="form-control form-control-sm" placeholder="Hot Deals">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small">Route Name</label>
                                        <input type="text" name="hero_cta2_link" 
                                            value="{{ $heroSettings['hero_cta2_link']->value ?? '' }}"
                                            class="form-control form-control-sm" placeholder="products.index">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small">Route Params (JSON)</label>
                                        <input type="text" name="hero_cta2_params" 
                                            value="{{ $heroSettings['hero_cta2_params']->value ?? '' }}"
                                            class="form-control form-control-sm" placeholder='{"sort":"discount"}'>
                                    </div>
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <label class="form-label small">Icon</label>
                                            <input type="text" name="hero_cta2_icon" 
                                                value="{{ $heroSettings['hero_cta2_icon']->value ?? '' }}"
                                                class="form-control form-control-sm" placeholder="bi bi-fire">
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label small">Badge</label>
                                            <input type="text" name="hero_cta2_badge" 
                                                value="{{ $heroSettings['hero_cta2_badge']->value ?? '' }}"
                                                class="form-control form-control-sm" placeholder="UP TO 30% OFF">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Features Bar -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-semibold">
                        <span class="badge bg-success me-2">6</span>
                        Features Bar
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @for ($i = 1; $i <= 4; $i++)
                        <div class="col-md-6 col-lg-3">
                            <div class="card h-100 border bg-light">
                                <div class="card-body p-3">
                                    <h6 class="card-title small text-muted mb-3">Feature {{ $i }}</h6>
                                    <div class="mb-2">
                                        <label class="form-label small">Icon</label>
                                        <input type="text" name="hero_feature{{ $i }}_icon" 
                                            value="{{ $heroSettings["hero_feature{$i}_icon"]->value ?? '' }}"
                                            class="form-control form-control-sm" placeholder="bi bi-truck">
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label small">Title</label>
                                        <input type="text" name="hero_feature{{ $i }}_title" 
                                            value="{{ $heroSettings["hero_feature{$i}_title"]->value ?? '' }}"
                                            class="form-control form-control-sm" placeholder="Free Delivery">
                                    </div>
                                    <div>
                                        <label class="form-label small">Subtitle</label>
                                        <input type="text" name="hero_feature{{ $i }}_subtitle" 
                                            value="{{ $heroSettings["hero_feature{$i}_subtitle"]->value ?? '' }}"
                                            class="form-control form-control-sm" placeholder="Orders over Tk500">
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endfor
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-lg-4">
            <!-- Main Image -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-semibold">
                        <i class="bi bi-card-image text-primary me-2"></i>
                        Main Product Image
                    </h5>
                </div>
                <div class="card-body">
                    <label class="form-label fw-medium">Upload Image</label>
                    <input type="file" name="hero_main_image" 
                        class="form-control mb-3" accept="image/*">
                    <div class="form-text mb-3">Recommended: 600x450px</div>
                    
                    @if($heroSettings->has('hero_main_image') && $heroSettings['hero_main_image']->value)
                    <div class="position-relative mb-3">
                        <img src="{{ $heroSettings['hero_main_image']->value }}" 
                            alt="Current Image" 
                            class="img-fluid rounded shadow-sm w-100">
                        <span class="badge bg-success position-absolute top-0 end-0 m-2">Current</span>
                    </div>
                    @endif
                    
                    <label class="form-label fw-medium">Alt Text</label>
                    <input type="text" name="hero_main_image_alt" 
                        value="{{ $heroSettings['hero_main_image_alt']->value ?? '' }}"
                        class="form-control" placeholder="Fresh Halal Meat">
                </div>
            </div>

            <!-- Floating Cards -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-semibold">
                        <i class="bi bi-card-text text-info me-2"></i>
                        Floating Cards
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Today's Special -->
                    <div class="mb-4">
                        <h6 class="text-muted small text-uppercase mb-2">
                            <i class="bi bi-star me-1"></i> Today's Special
                        </h6>
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="form-label small">Label</label>
                                <input type="text" name="hero_special_label" 
                                    value="{{ $heroSettings['hero_special_label']->value ?? '' }}"
                                    class="form-control form-control-sm" placeholder="Today's Special">
                            </div>
                            <div class="col-6">
                                <label class="form-label small">Title</label>
                                <input type="text" name="hero_special_title" 
                                    value="{{ $heroSettings['hero_special_title']->value ?? '' }}"
                                    class="form-control form-control-sm" placeholder="Premium Beef">
                            </div>
                            <div class="col-6">
                                <label class="form-label small">Button</label>
                                <input type="text" name="hero_special_button" 
                                    value="{{ $heroSettings['hero_special_button']->value ?? '' }}"
                                    class="form-control form-control-sm" placeholder="Order Now">
                            </div>
                            <div class="col-6">
                                <label class="form-label small">Route</label>
                                <input type="text" name="hero_special_link" 
                                    value="{{ $heroSettings['hero_special_link']->value ?? '' }}"
                                    class="form-control form-control-sm" placeholder="products.index">
                            </div>
                        </div>
                    </div>

                    <!-- Delivery Time -->
                    <div class="mb-4">
                        <h6 class="text-muted small text-uppercase mb-2">
                            <i class="bi bi-clock me-1"></i> Delivery Time
                        </h6>
                        <div class="row g-2">
                            <div class="col-4">
                                <label class="form-label small">Icon</label>
                                <input type="text" name="hero_delivery_icon" 
                                    value="{{ $heroSettings['hero_delivery_icon']->value ?? '' }}"
                                    class="form-control form-control-sm" placeholder="bi bi-clock">
                            </div>
                            <div class="col-4">
                                <label class="form-label small">Label</label>
                                <input type="text" name="hero_delivery_label" 
                                    value="{{ $heroSettings['hero_delivery_label']->value ?? '' }}"
                                    class="form-control form-control-sm" placeholder="Delivery">
                            </div>
                            <div class="col-4">
                                <label class="form-label small">Value</label>
                                <input type="text" name="hero_delivery_value" 
                                    value="{{ $heroSettings['hero_delivery_value']->value ?? '' }}"
                                    class="form-control form-control-sm" placeholder="30-60 Min">
                            </div>
                        </div>
                    </div>

                    <!-- Happy Customers -->
                    <div>
                        <h6 class="text-muted small text-uppercase mb-2">
                            <i class="bi bi-emoji-smile me-1"></i> Happy Customers
                        </h6>
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="form-label small">Label</label>
                                <input type="text" name="hero_customers_label" 
                                    value="{{ $heroSettings['hero_customers_label']->value ?? '' }}"
                                    class="form-control form-control-sm" placeholder="Happy">
                            </div>
                            <div class="col-6">
                                <label class="form-label small">Value</label>
                                <input type="text" name="hero_customers_value" 
                                    value="{{ $heroSettings['hero_customers_value']->value ?? '' }}"
                                    class="form-control form-control-sm" placeholder="Customers">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Save Button -->
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <button type="submit" class="btn btn-primary w-100 py-2">
                        <i class="bi bi-check-lg me-2"></i> Save All Settings
                    </button>
                    <p class="text-center text-muted small mt-2 mb-0">
                        Changes will reflect immediately on your homepage
                    </p>
                </div>
            </div>
        </div>
    </div>
</form>
@endif
@endsection

@push('styles')
<style>
    .card {
        transition: all 0.2s ease;
    }
    .card:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important;
    }
    .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
    .form-label {
        color: #495057;
    }
    .badge {
        font-weight: 500;
    }
    .carousel-item img {
        max-height: 400px;
        object-fit: cover;
    }
</style>
@endpush

@push('scripts')
<script>
    // Auto-submit on hero type change
    document.querySelectorAll('input[name="hero_type"]').forEach(function(radio) {
        radio.addEventListener('change', function() {
            this.form.submit();
        });
    });
</script>
@endpush
