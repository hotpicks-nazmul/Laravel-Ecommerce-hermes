@extends('themes.general.layouts.app')

@php
$heroSettings = \App\Models\Setting::where('group', 'hero')->get()->keyBy('key');
$hero = function($key, $default = '') use ($heroSettings) {
    return $heroSettings->has($key) ? $heroSettings[$key]->value : $default;
};
$heroJson = function($key) use ($hero) {
    return json_decode($hero($key, '{}'), true);
};

// Get hero type (default to 'standard')
$heroType = $hero('hero_type', 'standard');

// Get sliders if hero type is slider
$sliders = $heroType === 'slider' ? \App\Models\Slider::where('is_active', true)->orderBy('order')->get() : collect();

// Home Page Settings
$homeSettings = \App\Models\Setting::where('group', 'homepage')->get()->keyBy('key');
$home = function($key, $default = '') use ($homeSettings) {
    return $homeSettings->has($key) ? $homeSettings[$key]->value : $default;
};
$productColumns = (int) $home('homepage_product_columns', '6');
$gridClass = 'grid grid-cols-2 md:grid-cols-3 lg:grid-cols-' . $productColumns;

// Individual section column settings
$featuredColumns = (int) $home('homepage_featured_columns', '6');
$featuredGridClass = 'grid grid-cols-2 md:grid-cols-3 lg:grid-cols-' . $featuredColumns;
$newArrivalsColumns = (int) $home('homepage_new_arrivals_columns', '6');
$newArrivalsGridClass = 'grid grid-cols-2 md:grid-cols-3 lg:grid-cols-' . $newArrivalsColumns;
$saleColumns = (int) $home('homepage_sale_columns', '6');
$saleGridClass = 'grid grid-cols-2 md:grid-cols-3 lg:grid-cols-' . $saleColumns;
@endphp

@section('content')
@if($heroType === 'slider' && $sliders->count() > 0)
<!-- Hero Slider Section -->
<section class="hero-slider-section">
    <div id="heroSlider" class="hero-slider">
        <!-- Indicators -->
        <div class="hero-slider-indicators">
            @foreach($sliders as $index => $slider)
            <button type="button" data-slide="{{ $index }}" 
                class="hero-indicator {{ $index === 0 ? 'active' : '' }}"></button>
            @endforeach
        </div>
        
        <!-- Slides -->
        <div class="hero-slider-wrapper">
            @foreach($sliders as $index => $slider)
            <div class="hero-slide {{ $index === 0 ? 'active' : '' }}" data-index="{{ $index }}">
                <img src="{{ Storage::url($slider->image) }}" class="hero-slide-img" alt="{{ $slider->title }}">
                <div class="hero-slide-overlay"></div>
                <div class="hero-slide-content">
                    <div class="container mx-auto px-4">
                        <div class="max-w-2xl text-white">
                            @if($slider->subtitle)
                            <p class="hero-slide-subtitle">{{ $slider->subtitle }}</p>
                            @endif
                            <h1 class="hero-slide-title">{{ $slider->title }}</h1>
                            <a href="{{ $slider->link ?? route('products.index') }}" class="hero-slide-btn">
                                {{ $slider->button_text ?? 'Shop Now' }}
                                <i class="bi bi-arrow-right ml-2"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
        <!-- Controls -->
        <button class="hero-slider-prev" type="button">
            <i class="bi bi-chevron-left"></i>
        </button>
        <button class="hero-slider-next" type="button">
            <i class="bi bi-chevron-right"></i>
        </button>
    </div>
    
    <!-- Features Bar -->
    <div class="hero-features-bar">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 py-4">
                <div class="flex items-center justify-center text-white py-2">
                    <div class="w-12 h-12 bg-halal-gold/20 rounded-xl flex items-center justify-center mr-3">
                        <i class="{{ $hero('hero_feature1_icon', 'bi bi-truck') }} text-halal-gold text-xl"></i>
                    </div>
                    <div>
                        <p class="font-medium">{{ $hero('hero_feature1_title', 'Free Delivery') }}</p>
                        <p class="text-xs text-gray-300">{{ $hero('hero_feature1_subtitle', 'Orders over  Tk500') }}</p>
                    </div>
                </div>
                <div class="flex items-center justify-center text-white py-2">
                    <div class="w-12 h-12 bg-green-500/20 rounded-xl flex items-center justify-center mr-3">
                        <i class="{{ $hero('hero_feature2_icon', 'bi bi-shield-check') }} text-green-400 text-xl"></i>
                    </div>
                    <div>
                        <p class="font-medium">{{ $hero('hero_feature2_title', '100% Halal') }}</p>
                        <p class="text-xs text-gray-300">{{ $hero('hero_feature2_subtitle', 'Certified Quality') }}</p>
                    </div>
                </div>
                <div class="flex items-center justify-center text-white py-2">
                    <div class="w-12 h-12 bg-blue-500/20 rounded-xl flex items-center justify-center mr-3">
                        <i class="{{ $hero('hero_feature3_icon', 'bi bi-cash-coin') }} text-blue-400 text-xl"></i>
                    </div>
                    <div>
                        <p class="font-medium">{{ $hero('hero_feature3_title', 'Best Prices') }}</p>
                        <p class="text-xs text-gray-300">{{ $hero('hero_feature3_subtitle', 'Guaranteed') }}</p>
                    </div>
                </div>
                <div class="flex items-center justify-center text-white py-2">
                    <div class="w-12 h-12 bg-purple-500/20 rounded-xl flex items-center justify-center mr-3">
                        <i class="{{ $hero('hero_feature4_icon', 'bi bi-headset') }} text-purple-400 text-xl"></i>
                    </div>
                    <div>
                        <p class="font-medium">{{ $hero('hero_feature4_title', '24/7 Support') }}</p>
                        <p class="text-xs text-gray-300">{{ $hero('hero_feature4_subtitle', 'Always Here') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@else
<!-- Hero Section - Standard (Default) -->
<section class="relative min-h-[600px] md:min-h-[700px] overflow-hidden">
    <!-- Background with overlay -->
    <div class="absolute inset-0">
        <img src="{{ $hero('hero_background_image', 'https://images.unsplash.com/photo-1542838132-92c53300491e?w=1920') }}" 
            alt="Fresh Food Background" 
            class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-r from-halal-dark/95 via-halal-dark/85 to-halal-green/70"></div>
    </div>
    
    <!-- Animated shapes -->
    <div class="absolute inset-0 overflow-hidden">
        <div class="absolute -top-20 -right-20 w-72 h-72 bg-halal-gold/20 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute -bottom-20 -left-20 w-96 h-96 bg-halal-green/30 rounded-full blur-3xl animate-pulse" style="animation-delay: 1s;"></div>
        <div class="absolute top-1/2 right-1/4 w-64 h-64 bg-yellow-400/10 rounded-full blur-2xl animate-pulse" style="animation-delay: 2s;"></div>
    </div>
    
    <div class="container mx-auto px-4 py-16 md:py-24 relative z-10">
        <div class="flex flex-col md:flex-row items-center">
            <div class="md:w-1/2 text-white mb-8 md:mb-0">
                <!-- Badge -->
                <div class="inline-flex items-center bg-halal-gold/20 border border-halal-gold/30 text-halal-gold px-4 py-2 rounded-full text-sm font-medium mb-6 backdrop-blur-sm">
                    <i class="{{ $hero('hero_badge_icon', 'bi bi-patch-check-fill') }} mr-2"></i> 
                    <span>{{ $hero('hero_badge_text', 'Trusted by 10,000+ Customers') }}</span>
                </div>
                
                <!-- Main Heading -->
                <h1 class="font-poppins text-4xl md:text-5xl lg:text-6xl font-bold mb-6 leading-tight">
                    {{ $hero('hero_title_line1', 'Fresh') }} <span class="text-halal-gold">{{ $hero('hero_title_highlight1', 'Halal Food') }}</span><br>
                    {{ $hero('hero_title_line2', 'Delivered Fresh') }}<br>
                    <span class="text-green-300">{{ $hero('hero_title_line3', 'To Your Door') }}</span>
                </h1>
                
                <!-- Description -->
                <p class="text-lg md:text-xl text-gray-200 mb-8 max-w-lg leading-relaxed">
                    {{ $hero('hero_description', 'Premium quality halal meat, poultry, seafood & groceries. 100% certified halal, farm-fresh, delivered within hours across Bangladesh.') }}
                </p>
                
                <!-- CTA Buttons -->
                <div class="flex flex-wrap gap-4 mb-8">
                    <a href="{{ route($hero('hero_cta1_link', 'products.index')) }}" class="group bg-halal-gold text-white px-8 py-4 rounded-xl font-semibold hover:bg-yellow-500 transition-all duration-300 inline-flex items-center shadow-lg shadow-halal-gold/30 hover:shadow-xl hover:shadow-halal-gold/40 hover:-translate-y-1">
                        <i class="{{ $hero('hero_cta1_icon', 'bi bi-cart3') }} mr-2 group-hover:animate-bounce"></i> 
                        {{ $hero('hero_cta1_text', 'Shop Now') }}
                        <i class="bi bi-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                    </a>
                    <a href="{{ route($hero('hero_cta2_link', 'products.index'), $heroJson('hero_cta2_params') ?? ['sort' => 'discount']) }}" class="group bg-white/10 backdrop-blur-sm border border-white/20 text-white px-8 py-4 rounded-xl font-semibold hover:bg-white/20 transition-all duration-300 inline-flex items-center">
                        <i class="{{ $hero('hero_cta2_icon', 'bi bi-fire') }} text-orange-400 mr-2"></i> 
                        {{ $hero('hero_cta2_text', 'Hot Deals') }}
                        <span class="ml-2 bg-red-500 text-white text-xs px-2 py-0.5 rounded-full">{{ $hero('hero_cta2_badge', 'UP TO 30% OFF') }}</span>
                    </a>
                </div>
            </div>
            
            <!-- Hero Image Side -->
            <div class="md:w-1/2 relative">
                <!-- Main Image -->
                <div class="relative">
                    <!-- Decorative circles -->
                    <div class="absolute -top-4 -right-4 w-20 h-20 bg-halal-gold/30 rounded-full animate-ping"></div>
                    <div class="absolute top-1/2 -left-8 w-16 h-16 bg-green-400/30 rounded-full animate-pulse"></div>
                    
                    <!-- Main product image -->
                    <div class="relative z-10 rounded-3xl overflow-hidden shadow-2xl shadow-black/30 transform hover:scale-[1.02] transition-transform duration-500">
                        <img src="{{ $hero('hero_main_image', 'https://images.unsplash.com/photo-1607623814075-e51df1bdc82f?w=600') }}" 
                            alt="{{ $hero('hero_main_image_alt', 'Fresh Halal Meat') }}" 
                            class="w-full h-[350px] md:h-[450px] object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                        
                        <!-- Floating badge on image -->
                        <div class="absolute bottom-4 left-4 right-4 bg-white/95 backdrop-blur-sm rounded-2xl p-4 shadow-lg">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-500">{{ $hero('hero_special_label', "Today's Special") }}</p>
                                    <p class="font-bold text-halal-dark text-lg">{{ $hero('hero_special_title', 'Premium Beef - 20% OFF') }}</p>
                                </div>
                                <a href="{{ route($hero('hero_special_link', 'products.index'), $heroJson('hero_special_params') ?? ['category' => 'fresh-meat']) }}" class="bg-halal-green text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-halal-dark transition-colors">
                                    {{ $hero('hero_special_button', 'Order Now') }}
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Floating cards -->
                    <div class="absolute bottom-24 left-4 bg-white rounded-2xl p-4 shadow-xl z-20 animate-bounce hidden md:block" style="animation-duration: 3s;">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                                <i class="{{ $hero('hero_delivery_icon', 'bi bi-clock') }} text-halal-green text-xl"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">{{ $hero('hero_delivery_label', 'Delivery Time') }}</p>
                                <p class="font-bold text-gray-800">{{ $hero('hero_delivery_value', '30-60 Min') }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="absolute top-4 right-4 md:right-8 bg-white rounded-2xl p-3 shadow-xl z-20">
                        <div class="flex items-center space-x-2">
                            <div class="flex -space-x-2">
                                <div class="w-8 h-8 bg-halal-green rounded-full flex items-center justify-center text-white text-xs font-bold">R</div>
                                <div class="w-8 h-8 bg-halal-gold rounded-full flex items-center justify-center text-white text-xs font-bold">A</div>
                                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-xs font-bold">S</div>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">{{ $hero('hero_customers_label', 'Happy') }}</p>
                                <p class="font-bold text-gray-800 text-sm">{{ $hero('hero_customers_value', 'Customers') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Features Bar -->
    <div class="absolute bottom-0 left-0 right-0 bg-white/10 backdrop-blur-md border-t border-white/10">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 py-4">
                <div class="flex items-center justify-center text-white py-2">
                    <div class="w-12 h-12 bg-halal-gold/20 rounded-xl flex items-center justify-center mr-3">
                        <i class="{{ $hero('hero_feature1_icon', 'bi bi-truck') }} text-halal-gold text-xl"></i>
                    </div>
                    <div>
                        <p class="font-medium">{{ $hero('hero_feature1_title', 'Free Delivery') }}</p>
                        <p class="text-xs text-gray-300">{{ $hero('hero_feature1_subtitle', 'Orders over  Tk500') }}</p>
                    </div>
                </div>
                <div class="flex items-center justify-center text-white py-2">
                    <div class="w-12 h-12 bg-green-500/20 rounded-xl flex items-center justify-center mr-3">
                        <i class="{{ $hero('hero_feature2_icon', 'bi bi-shield-check') }} text-green-400 text-xl"></i>
                    </div>
                    <div>
                        <p class="font-medium">{{ $hero('hero_feature2_title', '100% Halal') }}</p>
                        <p class="text-xs text-gray-300">{{ $hero('hero_feature2_subtitle', 'Certified Quality') }}</p>
                    </div>
                </div>
                <div class="flex items-center justify-center text-white py-2">
                    <div class="w-12 h-12 bg-blue-500/20 rounded-xl flex items-center justify-center mr-3">
                        <i class="{{ $hero('hero_feature3_icon', 'bi bi-cash-coin') }} text-blue-400 text-xl"></i>
                    </div>
                    <div>
                        <p class="font-medium">{{ $hero('hero_feature3_title', 'Best Prices') }}</p>
                        <p class="text-xs text-gray-300">{{ $hero('hero_feature3_subtitle', 'Guaranteed') }}</p>
                    </div>
                </div>
                <div class="flex items-center justify-center text-white py-2">
                    <div class="w-12 h-12 bg-purple-500/20 rounded-xl flex items-center justify-center mr-3">
                        <i class="{{ $hero('hero_feature4_icon', 'bi bi-headset') }} text-purple-400 text-xl"></i>
                    </div>
                    <div>
                        <p class="font-medium">{{ $hero('hero_feature4_title', '24/7 Support') }}</p>
                        <p class="text-xs text-gray-300">{{ $hero('hero_feature4_subtitle', 'Always Here') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endif

<!-- Categories Section - Modern & Attractive -->
<section class="py-16 bg-gradient-to-b from-white via-gray-50 to-white relative overflow-hidden">
    <!-- Decorative Background Elements -->
    <div class="absolute inset-0 pointer-events-none">
        <div class="absolute top-10 left-10 w-32 h-32 bg-halal-green/5 rounded-full blur-2xl"></div>
        <div class="absolute bottom-10 right-10 w-40 h-40 bg-halal-gold/5 rounded-full blur-2xl"></div>
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-green-50/50 rounded-full blur-3xl"></div>
    </div>
    
    <div class="container mx-auto px-4 relative z-10">
        <!-- Section Header -->
        <div class="text-center mb-12">
            <div class="inline-flex items-center bg-halal-green/10 text-halal-green px-4 py-2 rounded-full text-sm font-medium mb-4">
                <i class="bi bi-grid-3x3-gap-fill mr-2"></i>
                Explore Our Collection
            </div>
            <h2 class="font-poppins text-3xl md:text-4xl font-bold text-gray-800 mb-3">
                Shop by <span class="text-halal-green">Category</span>
            </h2>
            <p class="text-gray-600 max-w-2xl mx-auto text-lg">Browse our wide range of premium halal products, carefully sourced and quality assured</p>
            
            <!-- Decorative Line -->
            <div class="flex items-center justify-center mt-6 space-x-2">
                <div class="w-12 h-1 bg-halal-green rounded-full"></div>
                <div class="w-3 h-3 bg-halal-gold rounded-full"></div>
                <div class="w-12 h-1 bg-halal-green rounded-full"></div>
            </div>
        </div>
        
        <!-- Categories Grid -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-5">
            @php
                $categoryIcons = [
                    'fresh-meat' => 'bi bi-cup-hot-fill',
                    'poultry' => 'bi bi-egg-fill',
                    'seafood' => 'bi bi-fish',
                    'fruits-vegetables' => 'bi bi-tree-fill',
                    'dairy-eggs' => 'bi bi-cup-straw',
                    'grocery' => 'bi bi-basket3-fill',
                ];
                $categoryColors = [
                    'fresh-meat' => ['bg' => 'from-red-500 to-red-600', 'light' => 'bg-red-50', 'text' => 'text-red-500'],
                    'poultry' => ['bg' => 'from-orange-500 to-orange-600', 'light' => 'bg-orange-50', 'text' => 'text-orange-500'],
                    'seafood' => ['bg' => 'from-blue-500 to-blue-600', 'light' => 'bg-blue-50', 'text' => 'text-blue-500'],
                    'fruits-vegetables' => ['bg' => 'from-green-500 to-green-600', 'light' => 'bg-green-50', 'text' => 'text-green-500'],
                    'dairy-eggs' => ['bg' => 'from-yellow-500 to-yellow-600', 'light' => 'bg-yellow-50', 'text' => 'text-yellow-600'],
                    'grocery' => ['bg' => 'from-purple-500 to-purple-600', 'light' => 'bg-purple-50', 'text' => 'text-purple-500'],
                ];
                $defaultIcon = 'bi bi-tag-fill';
                $defaultColor = ['bg' => 'from-halal-green to-green-600', 'light' => 'bg-green-50', 'text' => 'text-halal-green'];
            @endphp
            
            @foreach($categories as $category)
                @php
                    $icon = $categoryIcons[$category->slug] ?? $defaultIcon;
                    $color = $categoryColors[$category->slug] ?? $defaultColor;
                @endphp
                <a href="{{ route('products.index', ['category' => $category->slug]) }}" 
                   class="group relative bg-white rounded-2xl p-5 text-center shadow-sm hover:shadow-xl transition-all duration-500 transform hover:-translate-y-2 border border-gray-100 hover:border-transparent overflow-hidden">
                    
                    <!-- Hover Background Gradient -->
                    <div class="absolute inset-0 bg-gradient-to-br {{ $color['bg'] }} opacity-0 group-hover:opacity-100 transition-opacity duration-500 rounded-2xl"></div>
                    
                    <!-- Card Content -->
                    <div class="relative z-10">
                        <!-- Icon Container -->
                        <div class="w-20 h-20 mx-auto mb-4 {{ $color['light'] }} rounded-2xl flex items-center justify-center group-hover:bg-white/20 transition-all duration-500 transform group-hover:scale-110 group-hover:rotate-3">
                            <i class="{{ $icon }} text-3xl {{ $color['text'] }} group-hover:text-white transition-colors duration-300"></i>
                        </div>
                        
                        <!-- Category Name -->
                        <h3 class="font-semibold text-gray-800 group-hover:text-white transition-colors duration-300 mb-1 text-sm md:text-base">
                            {{ $category->name }}
                        </h3>
                        
                        <!-- Product Count Badge -->
                        <span class="inline-flex items-center text-xs {{ $color['text'] }} group-hover:text-white/80 transition-colors duration-300">
                            <i class="bi bi-box-seam mr-1"></i>
                            {{ $category->products_count ?? 0 }} Products
                        </span>
                        
                        <!-- Arrow Indicator -->
                        <div class="mt-3 opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-y-2 group-hover:translate-y-0">
                            <span class="inline-flex items-center text-white text-sm font-medium">
                                Explore <i class="bi bi-arrow-right ml-1"></i>
                            </span>
                        </div>
                    </div>
                    
                    <!-- Corner Decoration -->
                    <div class="absolute -top-4 -right-4 w-16 h-16 {{ $color['light'] }} rounded-full opacity-50 group-hover:opacity-0 transition-opacity duration-300"></div>
                    <div class="absolute -bottom-4 -left-4 w-12 h-12 {{ $color['light'] }} rounded-full opacity-50 group-hover:opacity-0 transition-opacity duration-300"></div>
                </a>
            @endforeach
        </div>
        
        <!-- View All Categories Button -->
        <div class="text-center mt-10">
            <a href="{{ route('products.index') }}" 
               class="inline-flex items-center bg-halal-dark text-white px-8 py-3 rounded-full font-medium hover:bg-halal-green transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                <i class="bi bi-grid-fill mr-2"></i>
                View All Categories
                <i class="bi bi-arrow-right ml-2"></i>
            </a>
        </div>
    </div>
</section>

<!-- Featured Products -->
@if($home('homepage_show_featured_section', '1') == '1')
<section class="py-12">
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="font-poppins text-3xl font-bold text-gray-800">{{ $home('homepage_featured_title', 'Featured Products') }}</h2>
                <p class="text-gray-600 mt-1">{{ $home('homepage_featured_subtitle', 'Handpicked premium quality products for you') }}</p>
            </div>
            <a href="{{ route('products.index') }}" class="text-halal-green hover:text-halal-dark font-medium flex items-center">
                View All <i class="bi bi-arrow-right ml-1"></i>
            </a>
        </div>
        
        <div class="{{ $featuredGridClass }} gap-5">
            @foreach($featuredProducts as $product)
                @include('themes.general.partials.product-card', ['product' => $product])
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Banner Section -->
@if($home('homepage_show_banner_section', '1') == '1')
@php
    // Count visible banners for dynamic grid
    $visibleBanners = [];
    for($j = 1; $j <= 4; $j++) {
        if($home('banner' . $j . '_visible', '1') == '1') {
            $visibleBanners[] = $j;
        }
    }
    $bannerCount = count($visibleBanners);
    $bannerGridClass = match($bannerCount) {
        1 => 'grid md:grid-cols-1 max-w-md mx-auto',
        2 => 'grid md:grid-cols-2',
        3 => 'grid md:grid-cols-3',
        default => 'grid md:grid-cols-2 lg:grid-cols-4',
    };
    $bannerConfigs = [
        1 => ['gradient' => 'from-halal-green to-green-600', 'badge_bg' => 'bg-halal-gold', 'text_muted' => 'text-green-100'],
        2 => ['gradient' => 'from-halal-gold to-yellow-500', 'badge_bg' => 'bg-halal-dark', 'text_muted' => 'text-yellow-100'],
        3 => ['gradient' => 'from-blue-500 to-blue-600', 'badge_bg' => 'bg-white', 'badge_text' => 'text-blue-600', 'text_muted' => 'text-blue-100'],
        4 => ['gradient' => 'from-red-500 to-red-600', 'badge_bg' => 'bg-white', 'badge_text' => 'text-red-600', 'text_muted' => 'text-red-100'],
    ];
@endphp
@if($bannerCount > 0)
<section class="py-8">
    <div class="container mx-auto px-4">
        <div class="{{ $bannerGridClass }} gap-6">
            @foreach($visibleBanners as $i)
                @php
                    $title = $home('banner' . $i . '_title', '');
                    $hasContent = !empty($title);
                @endphp
                @if($hasContent)
                <div class="bg-gradient-to-r {{ $bannerConfigs[$i]['gradient'] }} rounded-2xl p-6 text-white relative overflow-hidden">
                    <div class="absolute right-0 top-0 opacity-20">
                        <i class="bi {{ $home('banner' . $i . '_icon', 'bi-star-fill') }} text-[100px] transform rotate-12"></i>
                    </div>
                    <div class="relative z-10">
                        <span class="{{ $bannerConfigs[$i]['badge_bg'] }} {{ $bannerConfigs[$i]['badge_text'] ?? 'text-white' }} px-3 py-1 rounded-full text-sm font-medium">{{ $home('banner' . $i . '_badge', 'Offer') }}</span>
                        <h3 class="font-poppins text-xl font-bold mt-3">{{ $title }}</h3>
                        <p class="{{ $bannerConfigs[$i]['text_muted'] }} mt-2 text-sm">{{ $home('banner' . $i . '_description', '') }}</p>
                        @php $link = $home('banner' . $i . '_link', ''); @endphp
                        @if($link)
                            <a href="{{ route('products.index', ['category' => $link]) }}" class="inline-block mt-3 bg-white text-gray-800 px-4 py-2 rounded-full text-sm font-medium hover:bg-gray-100 transition-colors">
                                {{ $home('banner' . $i . '_button_text', 'Shop Now') }} <i class="bi bi-arrow-right ml-1"></i>
                            </a>
                        @else
                            <span class="inline-block mt-3 bg-white/20 text-white px-4 py-2 rounded-full text-sm font-medium">
                                {{ $home('banner' . $i . '_button_text', 'Shop Now') }} <i class="bi bi-arrow-right ml-1"></i>
                            </span>
                        @endif
                    </div>
                </div>
                @endif
            @endforeach
        </div>
    </div>
</section>
@endif
@endif

<!-- New Arrivals -->
@if($home('homepage_show_new_arrivals_section', '1') == '1')
<section class="py-12 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="font-poppins text-3xl font-bold text-gray-800">{{ $home('homepage_new_arrivals_title', 'New Arrivals') }}</h2>
                <p class="text-gray-600 mt-1">{{ $home('homepage_new_arrivals_subtitle', 'Fresh products just arrived in our store') }}</p>
            </div>
            <a href="{{ route('products.index', ['sort' => 'newest']) }}" class="text-halal-green hover:text-halal-dark font-medium flex items-center">
                View All <i class="bi bi-arrow-right ml-1"></i>
            </a>
        </div>
        
        <div class="{{ $newArrivalsGridClass }} gap-5">
            @foreach($latestProducts as $product)
                @include('themes.general.partials.product-card', ['product' => $product])
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Why Choose Us -->
@if($home('homepage_show_why_choose_us_section', '1') == '1')
<section class="py-16 bg-halal-dark text-white">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="font-poppins text-3xl font-bold">{{ $home('why_choose_us_title', 'Why Choose Us?') }}</h2>
            <p class="text-gray-400 mt-2">{{ $home('why_choose_us_subtitle', 'We are committed to providing the best halal products') }}</p>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
            @for($i = 1; $i <= 4; $i++)
                @php
                    $icon = $home('why_choose_us_icon_' . $i, 'bi-patch-check-fill');
                    $title = $home('why_choose_us_title_' . $i, 'Feature ' . $i);
                    $desc = $home('why_choose_us_desc_' . $i, 'Feature description');
                @endphp
                @if($title && $title !== 'Feature ' . $i)
                <div class="text-center">
                    <div class="w-20 h-20 mx-auto bg-halal-green rounded-full flex items-center justify-center mb-4">
                        <i class="bi {{ $icon }} text-3xl text-halal-gold"></i>
                    </div>
                    <h3 class="font-poppins text-xl font-bold mb-2">{{ $title }}</h3>
                    <p class="text-gray-400">{{ $desc }}</p>
                </div>
                @endif
            @endfor
        </div>
    </div>
</section>
@endif

<!-- Sale Products -->
@if($saleProducts->count() > 0 && $home('homepage_show_sale_section', '1') == '1')
<section class="py-12">
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="font-poppins text-3xl font-bold text-gray-800">
                    <i class="bi bi-fire text-red-500"></i> {{ $home('homepage_sale_title', 'Hot Deals') }}
                </h2>
                <p class="text-gray-600 mt-1">{{ $home('homepage_sale_subtitle', 'Limited time offers - Grab them before they are gone!') }}</p>
            </div>
            <a href="{{ route('products.index', ['sort' => 'discount']) }}" class="text-halal-green hover:text-halal-dark font-medium flex items-center">
                View All <i class="bi bi-arrow-right ml-1"></i>
            </a>
        </div>
        
        <div class="{{ $saleGridClass }} gap-5">
            @foreach($saleProducts as $product)
                @include('themes.general.partials.product-card', ['product' => $product])
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Testimonials -->
@if($home('homepage_show_testimonials_section', '1') == '1')
<section class="py-12 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="text-center mb-10">
            <h2 class="font-poppins text-3xl font-bold text-gray-800">{{ $home('testimonials_title', 'What Our Customers Say') }}</h2>
            <p class="text-gray-600 mt-2">{{ $home('testimonials_subtitle', 'Trusted by thousands of customers across Bangladesh') }}</p>
        </div>
        
        <div class="grid md:grid-cols-3 gap-6">
            @for($i = 1; $i <= 3; $i++)
                @php
                    $name = $home('testimonial' . $i . '_name', '');
                    $location = $home('testimonial' . $i . '_location', '');
                    $text = $home('testimonial' . $i . '_text', '');
                    $rating = (int) $home('testimonial' . $i . '_rating', '5');
                    $colors = ['bg-halal-green', 'bg-halal-gold', 'bg-halal-dark'];
                @endphp
                @if($name && $text)
                <div class="bg-white p-6 rounded-xl shadow-md">
                    <div class="flex text-halal-gold mb-4">
                        @for($s = 1; $s <= 5; $s++)
                            @if($s <= $rating)
                                <i class="bi bi-star-fill"></i>
                            @elseif($s - 0.5 <= $rating)
                                <i class="bi bi-star-half"></i>
                            @else
                                <i class="bi bi-star"></i>
                            @endif
                        @endfor
                    </div>
                    <p class="text-gray-600 mb-4">"{{ $text }}"</p>
                    <div class="flex items-center">
                        <div class="w-12 h-12 {{ $colors[$i - 1] ?? 'bg-halal-green' }} rounded-full flex items-center justify-center text-white font-bold">
                            {{ strtoupper(substr($name, 0, 1) . substr($name, strpos($name, ' ') !== false ? strpos($name, ' ') + 1 : 1, 1)) }}
                        </div>
                        <div class="ml-3">
                            <h4 class="font-medium text-gray-800">{{ $name }}</h4>
                            <p class="text-sm text-gray-500">{{ $location }}</p>
                        </div>
                    </div>
                </div>
                @endif
            @endfor
        </div>
    </div>
</section>
@endif

<!-- Blog Section -->
@if($latestBlogs->count() > 0 && $home('homepage_show_blog_section', '1') == '1')
<section class="py-12">
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="font-poppins text-3xl font-bold text-gray-800">From Our Blog</h2>
                <p class="text-gray-600 mt-1">Tips, recipes, and halal food insights</p>
            </div>
            <a href="{{ route('blogs.index') }}" class="text-halal-green hover:text-halal-dark font-medium flex items-center">
                View All <i class="bi bi-arrow-right ml-1"></i>
            </a>
        </div>
        
        <div class="grid md:grid-cols-3 gap-6">
            @foreach($latestBlogs as $blog)
            <article class="bg-white rounded-xl shadow-md overflow-hidden group">
                <div class="overflow-hidden">
                    <img src="{{ $blog->image ?? 'https://via.placeholder.com/400x250?text=Blog' }}" 
                        alt="{{ $blog->title }}" 
                        class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300">
                </div>
                <div class="p-5">
                    <div class="flex items-center text-sm text-gray-500 mb-2">
                        <i class="bi bi-calendar3 mr-1"></i>
                        {{ $blog->created_at->format('M d, Y') }}
                        <span class="mx-2">-</span>
                        <i class="bi bi-person mr-1"></i>
                        {{ $blog->author->name ?? 'Admin' }}
                    </div>
                    <h3 class="font-poppins text-lg font-bold text-gray-800 mb-2 hover:text-halal-green transition-colors">
                        <a href="{{ route('blogs.show', $blog->slug) }}">{{ Str::limit($blog->title, 50) }}</a>
                    </h3>
                    <p class="text-gray-600 text-sm">{{ Str::limit(strip_tags($blog->content), 100) }}</p>
                    <a href="{{ route('blogs.show', $blog->slug) }}" class="inline-block mt-3 text-halal-green font-medium hover:text-halal-dark">
                        Read More <i class="bi bi-arrow-right ml-1"></i>
                    </a>
                </div>
            </article>
            @endforeach
        </div>
    </div>
</section>
@endif
@endsection

@push('styles')
<style>
    /* Hero Slider Styles - Custom Implementation */
    .hero-slider-section {
        position: relative;
        overflow: hidden;
        min-height: 500px;
    }
    
    @media (min-width: 768px) {
        .hero-slider-section {
            min-height: 600px;
        }
    }
    
    .hero-slider {
        position: relative;
        height: 500px;
        width: 100%;
    }
    
    @media (min-width: 768px) {
        .hero-slider {
            height: 600px;
        }
    }
    
    .hero-slider-wrapper {
        position: relative;
        height: 100%;
        width: 100%;
    }
    
    .hero-slide {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.8s ease-in-out, visibility 0.8s;
        z-index: 1;
    }
    
    .hero-slide.active {
        opacity: 1;
        visibility: visible;
        z-index: 2;
    }
    
    .hero-slide-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
    }
    
    .hero-slide-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(to right, rgba(30, 58, 38, 0.9), rgba(30, 58, 38, 0.7), transparent);
        z-index: 1;
    }
    
    .hero-slide-content {
        position: absolute;
        top: 50%;
        left: 0;
        right: 0;
        transform: translateY(-50%);
        z-index: 2;
        padding: 0 2rem;
    }
    
    .hero-slide-subtitle {
        color: #D4AF37;
        font-size: 1.25rem;
        font-weight: 500;
        margin-bottom: 0.5rem;
    }
    
    .hero-slide-title {
        font-family: 'Poppins', sans-serif;
        font-size: 2.5rem;
        font-weight: 700;
        line-height: 1.2;
        margin-bottom: 1.5rem;
        color: white;
    }
    
    @media (min-width: 768px) {
        .hero-slide-title {
            font-size: 3.5rem;
        }
        .hero-slide-content {
            padding: 0 4rem;
        }
    }
    
    @media (min-width: 1024px) {
        .hero-slide-title {
            font-size: 4.5rem;
        }
        .hero-slide-content {
            padding: 0 6rem;
        }
    }
    
    .hero-slide-btn {
        display: inline-flex;
        align-items: center;
        background-color: #D4AF37;
        color: white;
        padding: 1rem 2rem;
        border-radius: 0.75rem;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
        box-shadow: 0 10px 25px rgba(212, 175, 55, 0.3);
    }
    
    .hero-slide-btn:hover {
        background-color: #e5b919;
        transform: translateY(-2px);
        box-shadow: 0 15px 30px rgba(212, 175, 55, 0.4);
        color: white;
    }
    
    .hero-features-bar {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        z-index: 20;
    }
    
    /* Slider Controls */
    .hero-slider-prev,
    .hero-slider-next {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 50px;
        height: 50px;
        background-color: rgba(30, 58, 38, 0.5);
        border: none;
        border-radius: 50%;
        color: white;
        font-size: 1.5rem;
        cursor: pointer;
        z-index: 10;
        transition: background-color 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .hero-slider-prev:hover,
    .hero-slider-next:hover {
        background-color: rgba(30, 58, 38, 0.8);
    }
    
    .hero-slider-prev {
        left: 20px;
    }
    
    .hero-slider-next {
        right: 20px;
    }
    
    /* Slider Indicators */
    .hero-slider-indicators {
        position: absolute;
        bottom: 100px;
        left: 50%;
        transform: translateX(-50%);
        display: flex;
        gap: 10px;
        z-index: 10;
    }
    
    .hero-indicator {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        border: none;
        background-color: rgba(255, 255, 255, 0.5);
        cursor: pointer;
        transition: background-color 0.3s ease;
        padding: 0;
    }
    
    .hero-indicator.active {
        background-color: #D4AF37;
    }
    
    .hero-indicator:hover {
        background-color: rgba(255, 255, 255, 0.8);
    }
</style>
@endpush

@push('scripts')
<script>
    // Custom Hero Slider
    document.addEventListener('DOMContentLoaded', function() {
        const slider = document.getElementById('heroSlider');
        if (!slider) return;
        
        const slides = slider.querySelectorAll('.hero-slide');
        const indicators = slider.querySelectorAll('.hero-indicator');
        const prevBtn = slider.querySelector('.hero-slider-prev');
        const nextBtn = slider.querySelector('.hero-slider-next');
        
        if (slides.length === 0) return;
        
        let currentSlide = 0;
        let slideInterval;
        
        function showSlide(index) {
            // Handle wrap around
            if (index >= slides.length) index = 0;
            if (index < 0) index = slides.length - 1;
            
            // Update slides
            slides.forEach((slide, i) => {
                if (i === index) {
                    slide.classList.add('active');
                } else {
                    slide.classList.remove('active');
                }
            });
            
            // Update indicators
            indicators.forEach((indicator, i) => {
                if (i === index) {
                    indicator.classList.add('active');
                } else {
                    indicator.classList.remove('active');
                }
            });
            
            currentSlide = index;
        }
        
        function nextSlide() {
            showSlide(currentSlide + 1);
        }
        
        function prevSlide() {
            showSlide(currentSlide - 1);
        }
        
        function startAutoPlay() {
            slideInterval = setInterval(nextSlide, 5000);
        }
        
        function stopAutoPlay() {
            clearInterval(slideInterval);
        }
        
        // Event listeners
        if (prevBtn) prevBtn.addEventListener('click', () => {
            prevSlide();
            stopAutoPlay();
            startAutoPlay();
        });
        
        if (nextBtn) nextBtn.addEventListener('click', () => {
            nextSlide();
            stopAutoPlay();
            startAutoPlay();
        });
        
        indicators.forEach((indicator, index) => {
            indicator.addEventListener('click', () => {
                showSlide(index);
                stopAutoPlay();
                startAutoPlay();
            });
        });
        
        // Start auto-play
        startAutoPlay();
    });
</script>
@endpush
