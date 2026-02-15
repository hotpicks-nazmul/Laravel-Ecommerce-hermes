@extends('themes.general.layouts.app')

@php
$heroSettings = \App\Models\Setting::where('group', 'hero')->get()->keyBy('key');
$hero = function($key, $default = '') use ($heroSettings) {
    return $heroSettings->has($key) ? $heroSettings[$key]->value : $default;
};
$heroJson = function($key) use ($hero) {
    return json_decode($hero($key, '{}'), true);
};
@endphp

@section('content')
<!-- Hero Section - Modern & Robust -->
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
<section class="py-12">
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="font-poppins text-3xl font-bold text-gray-800">Featured Products</h2>
                <p class="text-gray-600 mt-1">Handpicked premium quality products for you</p>
            </div>
            <a href="{{ route('products.index') }}" class="text-halal-green hover:text-halal-dark font-medium flex items-center">
                View All <i class="bi bi-arrow-right ml-1"></i>
            </a>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($featuredProducts as $product)
                @include('themes.general.partials.product-card', ['product' => $product])
            @endforeach
        </div>
    </div>
</section>

<!-- Banner Section -->
<section class="py-8">
    <div class="container mx-auto px-4">
        <div class="grid md:grid-cols-2 gap-6">
            <div class="bg-gradient-to-r from-halal-green to-green-600 rounded-2xl p-8 text-white relative overflow-hidden">
                <div class="absolute right-0 top-0 opacity-20">
                    <i class="bi bi-piggy-bank text-[150px] transform rotate-12"></i>
                </div>
                <div class="relative z-10">
                    <span class="bg-halal-gold text-white px-3 py-1 rounded-full text-sm font-medium">Special Offer</span>
                    <h3 class="font-poppins text-2xl font-bold mt-4">Weekend Special!</h3>
                    <p class="text-green-100 mt-2">Get 20% off on all beef products this weekend</p>
                    <a href="{{ route('products.index', ['category' => 'beef']) }}" class="inline-block mt-4 bg-white text-halal-green px-6 py-2 rounded-full font-medium hover:bg-green-50 transition-colors">
                        Shop Beef <i class="bi bi-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
            
            <div class="bg-gradient-to-r from-halal-gold to-yellow-500 rounded-2xl p-8 text-white relative overflow-hidden">
                <div class="absolute right-0 top-0 opacity-20">
                    <i class="bi bi-lightning-charge-fill text-[150px] transform rotate-12"></i>
                </div>
                <div class="relative z-10">
                    <span class="bg-halal-dark text-white px-3 py-1 rounded-full text-sm font-medium">Flash Sale</span>
                    <h3 class="font-poppins text-2xl font-bold mt-4">Flash Sale!</h3>
                    <p class="text-yellow-100 mt-2">Limited time offer on fresh chicken</p>
                    <a href="{{ route('products.index', ['category' => 'chicken']) }}" class="inline-block mt-4 bg-white text-halal-gold px-6 py-2 rounded-full font-medium hover:bg-yellow-50 transition-colors">
                        Shop Chicken <i class="bi bi-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- New Arrivals -->
<section class="py-12 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="font-poppins text-3xl font-bold text-gray-800">New Arrivals</h2>
                <p class="text-gray-600 mt-1">Fresh products just arrived in our store</p>
            </div>
            <a href="{{ route('products.index', ['sort' => 'newest']) }}" class="text-halal-green hover:text-halal-dark font-medium flex items-center">
                View All <i class="bi bi-arrow-right ml-1"></i>
            </a>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($latestProducts as $product)
                @include('themes.general.partials.product-card', ['product' => $product])
            @endforeach
        </div>
    </div>
</section>

<!-- Why Choose Us -->
<section class="py-16 bg-halal-dark text-white">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="font-poppins text-3xl font-bold">Why Choose Us?</h2>
            <p class="text-gray-400 mt-2">We are committed to providing the best halal products</p>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="text-center">
                <div class="w-20 h-20 mx-auto bg-halal-green rounded-full flex items-center justify-center mb-4">
                    <i class="bi bi-patch-check-fill text-3xl text-halal-gold"></i>
                </div>
                <h3 class="font-poppins text-xl font-bold mb-2">100% Halal Certified</h3>
                <p class="text-gray-400">All our products are certified halal by recognized Islamic authorities</p>
            </div>
            
            <div class="text-center">
                <div class="w-20 h-20 mx-auto bg-halal-green rounded-full flex items-center justify-center mb-4">
                    <i class="bi bi-thermometer-snow text-3xl text-halal-gold"></i>
                </div>
                <h3 class="font-poppins text-xl font-bold mb-2">Fresh & Cold Storage</h3>
                <p class="text-gray-400">Maintained at optimal temperature from farm to your door</p>
            </div>
            
            <div class="text-center">
                <div class="w-20 h-20 mx-auto bg-halal-green rounded-full flex items-center justify-center mb-4">
                    <i class="bi bi-truck text-3xl text-halal-gold"></i>
                </div>
                <h3 class="font-poppins text-xl font-bold mb-2">Fast Delivery</h3>
                <p class="text-gray-400">Same day delivery in Dhaka, 1-2 days nationwide</p>
            </div>
            
            <div class="text-center">
                <div class="w-20 h-20 mx-auto bg-halal-green rounded-full flex items-center justify-center mb-4">
                    <i class="bi bi-hand-thumbs-up-fill text-3xl text-halal-gold"></i>
                </div>
                <h3 class="font-poppins text-xl font-bold mb-2">Quality Guarantee</h3>
                <p class="text-gray-400">Not satisfied? We offer easy returns and refunds</p>
            </div>
        </div>
    </div>
</section>

<!-- Sale Products -->
@if($saleProducts->count() > 0)
<section class="py-12">
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="font-poppins text-3xl font-bold text-gray-800">
                    <i class="bi bi-fire text-red-500"></i> Hot Deals
                </h2>
                <p class="text-gray-600 mt-1">Limited time offers - Grab them before they're gone!</p>
            </div>
            <a href="{{ route('products.index', ['sort' => 'discount']) }}" class="text-halal-green hover:text-halal-dark font-medium flex items-center">
                View All <i class="bi bi-arrow-right ml-1"></i>
            </a>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($saleProducts as $product)
                @include('themes.general.partials.product-card', ['product' => $product])
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Testimonials -->
<section class="py-12 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="text-center mb-10">
            <h2 class="font-poppins text-3xl font-bold text-gray-800">What Our Customers Say</h2>
            <p class="text-gray-600 mt-2">Trusted by thousands of customers across Bangladesh</p>
        </div>
        
        <div class="grid md:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-xl shadow-md">
                <div class="flex text-halal-gold mb-4">
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                </div>
                <p class="text-gray-600 mb-4">"The quality of meat is exceptional! Fresh, clean, and properly packed. Best halal meat store in Dhaka!"</p>
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-halal-green rounded-full flex items-center justify-center text-white font-bold">RA</div>
                    <div class="ml-3">
                        <h4 class="font-medium text-gray-800">Rahim Ahmed</h4>
                        <p class="text-sm text-gray-500">Dhaka</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white p-6 rounded-xl shadow-md">
                <div class="flex text-halal-gold mb-4">
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                </div>
                <p class="text-gray-600 mb-4">"Fast delivery and great customer service. The beef was so tender and fresh. Highly recommended!"</p>
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-halal-gold rounded-full flex items-center justify-center text-white font-bold">SK</div>
                    <div class="ml-3">
                        <h4 class="font-medium text-gray-800">Sarah Khan</h4>
                        <p class="text-sm text-gray-500">Chittagong</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white p-6 rounded-xl shadow-md">
                <div class="flex text-halal-gold mb-4">
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-half"></i>
                </div>
                <p class="text-gray-600 mb-4">"Finally found a reliable halal meat shop online. The prices are reasonable and quality is top-notch!"</p>
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-halal-dark rounded-full flex items-center justify-center text-white font-bold">MH</div>
                    <div class="ml-3">
                        <h4 class="font-medium text-gray-800">Mohammad Hossain</h4>
                        <p class="text-sm text-gray-500">Sylhet</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Blog Section -->
@if($latestBlogs->count() > 0)
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
