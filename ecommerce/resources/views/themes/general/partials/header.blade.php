<!-- Top Bar -->
<div class="bg-halal-dark text-white py-2">
    <div class="container mx-auto px-4">
        <div class="flex flex-wrap justify-between items-center text-sm">
            <div class="flex items-center space-x-4">
                <span class="flex items-center">
                    <i class="bi bi-telephone-fill mr-2 text-halal-gold"></i>
                    +880 1700-000000
                </span>
                <span class="hidden md:flex items-center">
                    <i class="bi bi-envelope-fill mr-2 text-halal-gold"></i>
                    info@halalfoodstore.com
                </span>
            </div>
            <div class="flex items-center space-x-4">
                <span class="flex items-center text-halal-gold">
                    <i class="bi bi-truck mr-1"></i>
                    Free Delivery on orders over ৳500
                </span>
            </div>
        </div>
    </div>
</div>

@php
// Site Branding Settings - Query settings with homepage group
$siteNameSetting = \App\Models\Setting::where('key', 'site_name')->first();
$siteTaglineSetting = \App\Models\Setting::where('key', 'site_tagline')->first();
$siteLogoIconSetting = \App\Models\Setting::where('key', 'site_logo_icon')->first();
$siteLogoImageSetting = \App\Models\Setting::where('key', 'site_logo')->first();

$siteName = $siteNameSetting ? $siteNameSetting->value : 'Halal Food';
$siteTagline = $siteTaglineSetting ? $siteTaglineSetting->value : 'Premium Quality Store';
$siteLogoIcon = $siteLogoIconSetting ? $siteLogoIconSetting->value : 'bi bi-shop';
$siteLogoImage = $siteLogoImageSetting ? $siteLogoImageSetting->value : '';
@endphp

<!-- Main Header -->
<header class="bg-white shadow-md sticky top-0 z-40">
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-between py-4">
            <!-- Logo -->
            <a href="{{ route('home') }}" class="flex items-center space-x-2">
                @php
                    $logoPath = trim($siteLogoImage ?? '');
                    $hasLogo = !empty($logoPath) && $logoPath !== '';
                @endphp
                @if($hasLogo)
                    <img src="{{ $logoPath }}" alt="{{ $siteName }}" class="h-12 w-auto object-contain">
                @else
                    <div class="w-12 h-12 gradient-halal rounded-full flex items-center justify-center">
                        <i class="{{ $siteLogoIcon }} text-white text-xl"></i>
                    </div>
                @endif
                <div>
                    <h1 class="font-poppins text-2xl font-bold text-halal-green">{{ $siteName }}</h1>
                    <p class="text-xs text-gray-500 -mt-1">{{ $siteTagline }}</p>
                </div>
            </a>
            
            <!-- Search Bar -->
            <div class="hidden md:flex flex-1 max-w-xl mx-8">
                <form action="{{ route('products.index') }}" method="GET" class="w-full">
                    <div class="relative">
                        <input type="text" name="search" placeholder="Search for fresh halal meat, groceries..." 
                            class="w-full pl-4 pr-12 py-3 border-2 border-gray-200 rounded-full focus:border-halal-green focus:outline-none transition-colors">
                        <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 bg-halal-green text-white p-2 rounded-full hover:bg-halal-dark transition-colors">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Right Actions -->
            <div class="flex items-center space-x-4">
                <!-- Account -->
                @auth
                <div class="relative group">
                    <button class="flex items-center space-x-1 text-gray-700 hover:text-halal-green transition-colors">
                        <i class="bi bi-person-circle text-2xl"></i>
                        <span class="hidden lg:block">{{ Auth::user()->name }}</span>
                        <i class="bi bi-chevron-down text-xs"></i>
                    </button>
                    <div class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200">
                        <a href="{{ route('account.dashboard') }}" class="flex items-center px-4 py-2 text-gray-700 hover:bg-green-50 hover:text-halal-green">
                            <i class="bi bi-grid mr-2"></i>Dashboard
                        </a>
                        <a href="{{ route('account.profile') }}" class="flex items-center px-4 py-2 text-gray-700 hover:bg-green-50 hover:text-halal-green">
                            <i class="bi bi-person mr-2"></i>My Profile
                        </a>
                        <a href="{{ route('account.orders') }}" class="flex items-center px-4 py-2 text-gray-700 hover:bg-green-50 hover:text-halal-green">
                            <i class="bi bi-bag mr-2"></i>My Orders
                        </a>
                        <a href="{{ route('account.wishlist') }}" class="flex items-center px-4 py-2 text-gray-700 hover:bg-green-50 hover:text-halal-green">
                            <i class="bi bi-heart mr-2"></i>Wishlist
                        </a>
                        <a href="{{ route('account.addresses') }}" class="flex items-center px-4 py-2 text-gray-700 hover:bg-green-50 hover:text-halal-green">
                            <i class="bi bi-geo-alt mr-2"></i>Addresses
                        </a>
                        <hr class="my-2">
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full text-left flex items-center px-4 py-2 text-red-600 hover:bg-red-50">
                                <i class="bi bi-box-arrow-right mr-2"></i>Logout
                            </button>
                        </form>
                    </div>
                </div>
                @else
                <div class="flex items-center space-x-3">
                    <a href="{{ route('login') }}" class="flex items-center space-x-1 text-gray-700 hover:text-halal-green transition-colors">
                        <i class="bi bi-person-circle text-2xl"></i>
                        <span class="hidden lg:block">Login</span>
                    </a>
                    <a href="{{ route('register') }}" class="bg-halal-green text-white px-4 py-2 rounded-full hover:bg-halal-dark transition-colors text-sm font-medium">
                        Register
                    </a>
                </div>
                @endauth
                
                <!-- Wishlist -->
                @php $wishlistCount = auth()->check() ? \App\Models\Wishlist::where('user_id', auth()->id())->count() : 0; @endphp
                <button onclick="openWishlistSidebar()" class="relative text-gray-700 hover:text-halal-green transition-colors">
                    <i class="bi bi-heart text-2xl"></i>
                    <span class="absolute -top-1 -right-1 w-5 h-5 bg-halal-gold text-white text-xs rounded-full flex items-center justify-center wishlist-count {{ $wishlistCount == 0 ? 'hidden' : '' }}">{{ $wishlistCount }}</span>
                </button>
                
                <!-- Cart -->
                <button onclick="openCartSidebar()" class="relative flex items-center space-x-2 bg-halal-green text-white px-4 py-2 rounded-full hover:bg-halal-dark transition-colors">
                    <i class="bi bi-cart3 text-xl"></i>
                    <span class="hidden lg:block font-medium">Cart</span>
                    <span class="cart-count bg-halal-gold text-white text-xs w-5 h-5 rounded-full flex items-center justify-center">0</span>
                </button>
                
                <!-- Mobile Menu Toggle -->
                <button onclick="toggleMobileMenu()" class="lg:hidden text-gray-700">
                    <i class="bi bi-list text-2xl"></i>
                </button>
            </div>
        </div>
    </div>
    
    <!-- Navigation -->
    <nav class="bg-halal-green text-white">
        <div class="container mx-auto px-4">
            <div class="flex items-center">
                <!-- Categories Dropdown -->
                <div class="relative group">
                    <button class="flex items-center space-x-2 bg-halal-dark px-6 py-3 hover:bg-halal-light transition-colors">
                        <i class="bi bi-grid-3x3-gap-fill"></i>
                        <span class="font-medium">All Categories</span>
                        <i class="bi bi-chevron-down"></i>
                    </button>
                    <div class="absolute left-0 mt-0 w-64 bg-white text-gray-700 rounded-b-lg shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                        @foreach($categories ?? [] as $category)
                        <a href="{{ route('products.index', ['category' => $category->slug]) }}" class="flex items-center px-4 py-3 hover:bg-green-50 hover:text-halal-green border-b border-gray-100">
                            <i class="bi bi-dot text-halal-green mr-2"></i>
                            {{ $category->name }}
                            <span class="ml-auto text-xs text-gray-400">({{ $category->products_count ?? 0 }})</span>
                        </a>
                        @endforeach
                    </div>
                </div>
                
                <!-- Nav Links -->
                <div class="hidden lg:flex items-center space-x-1 ml-4">
                    <a href="{{ route('home') }}" class="px-4 py-3 hover:bg-halal-dark rounded transition-colors {{ request()->routeIs('home') ? 'bg-halal-dark' : '' }}">Home</a>
                    <a href="{{ route('products.index') }}" class="px-4 py-3 hover:bg-halal-dark rounded transition-colors {{ request()->routeIs('products.*') ? 'bg-halal-dark' : '' }}">Shop</a>
                    <a href="{{ route('products.index', ['sort' => 'newest']) }}" class="px-4 py-3 hover:bg-halal-dark rounded transition-colors">New Arrivals</a>
                    <a href="{{ route('products.index', ['sort' => 'discount']) }}" class="px-4 py-3 hover:bg-halal-dark rounded transition-colors">Deals</a>
                    <a href="{{ route('blogs.index') }}" class="px-4 py-3 hover:bg-halal-dark rounded transition-colors {{ request()->routeIs('blogs.*') ? 'bg-halal-dark' : '' }}">Blog</a>
                    <a href="{{ route('pages.contact') }}" class="px-4 py-3 hover:bg-halal-dark rounded transition-colors">Contact</a>
                </div>
                
                <!-- Special Offer -->
                <div class="ml-auto hidden lg:flex items-center">
                    <span class="flex items-center text-halal-gold">
                        <i class="bi bi-fire text-lg mr-2"></i>
                        <span class="font-medium">Today's Deal: Up to 30% Off!</span>
                    </span>
                </div>
            </div>
        </div>
    </nav>
</header>
