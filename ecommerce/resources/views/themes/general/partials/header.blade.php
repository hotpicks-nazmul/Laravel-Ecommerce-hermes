@php
// Home Page Settings - Batched query (1 query instead of 7)
$homepageSettings = \App\Models\Setting::where('group', 'homepage')->pluck('value', 'key')->toArray();

$topBarPhone = $homepageSettings['top_bar_phone'] ?? '+880 1700-000000';
$topBarEmail = $homepageSettings['top_bar_email'] ?? 'info@halalfoodstore.com';
$topBarDelivery = $homepageSettings['top_bar_delivery_message'] ?? 'Free Delivery on orders over ৳500';

$siteName = $homepageSettings['site_name'] ?? 'Halal Food';
$siteTagline = $homepageSettings['site_tagline'] ?? 'Premium Quality Store';
$siteLogoIcon = $homepageSettings['site_logo_icon'] ?? 'bi bi-shop';
$siteLogoImage = $homepageSettings['site_logo'] ?? '';

// Menu Styling Settings - Batched query (1 query instead of 6)
$menuSettings = \App\Models\Setting::whereIn('key', ['menu_hover_color', 'menu_text_hover_color', 'menu_active_color', 'menu_active_text_color', 'menu_font_size', 'menu_font_weight'])->pluck('value', 'key')->toArray();

$menuHoverColor = $menuSettings['menu_hover_color'] ?? '#ffffff';
$menuTextHoverColor = $menuSettings['menu_text_hover_color'] ?? '#4f46e5';
$menuActiveColor = $menuSettings['menu_active_color'] ?? '#ffffff';
$menuActiveTextColor = $menuSettings['menu_active_text_color'] ?? '#4f46e5';
$menuFontSize = $menuSettings['menu_font_size'] ?? '14';
$menuFontWeight = $menuSettings['menu_font_weight'] ?? '400';
@endphp

<!-- Top Bar -->
<div class="bg-halal-dark text-white py-1">
    <div class="container mx-auto px-4">
        <div class="flex flex-wrap justify-between items-center text-xs">
            <div class="flex items-center space-x-4">
                <span class="flex items-center">
                    <i class="bi bi-telephone-fill mr-2 text-halal-gold"></i>
                    {{ $topBarPhone }}
                </span>
                <span class="hidden md:flex items-center">
                    <i class="bi bi-envelope-fill mr-2 text-halal-gold"></i>
                    {{ $topBarEmail }}
                </span>
            </div>
            <div class="flex items-center space-x-4">
                <span class="flex items-center text-halal-gold">
                    <i class="bi bi-truck mr-1"></i>
                    {{ $topBarDelivery }}
                </span>
                
                <!-- Language Switcher -->
                @include('themes.general.partials.language-switcher')
                
                <!-- Currency Switcher -->
                @include('themes.general.partials.currency-switcher')
            </div>
        </div>
    </div>
</div>

<style>
    .nav-menu-link {
        font-size: {{ $menuFontSize }}px !important;
        font-weight: {{ $menuFontWeight }} !important;
    }
    .nav-menu-link:hover {
        background-color: {{ $menuHoverColor }} !important;
        color: {{ $menuTextHoverColor }} !important;
    }
    .nav-menu-link.active {
        background-color: {{ $menuActiveColor }} !important;
        color: {{ $menuActiveTextColor }} !important;
    }
</style>

<!-- Main Header -->
<header class="bg-white shadow-md sticky top-0 z-40">
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-between py-2">
            <!-- Logo -->
            <a href="{{ route('home') }}" class="flex items-center space-x-2">
                @php
                    $logoPath = trim($siteLogoImage ?? '');
                    $hasLogo = !empty($logoPath) && $logoPath !== '';
                @endphp
                @if($hasLogo)
                    <img src="{{ $logoPath }}" alt="{{ $siteName }}" class="h-8 w-auto object-contain">
                @else
                    <div class="w-8 h-8 gradient-halal rounded-full flex items-center justify-center">
                        <i class="{{ $siteLogoIcon }} text-white text-sm"></i>
                    </div>
                @endif
                <div>
                    <h1 class="font-poppins text-lg font-bold text-halal-green">{{ $siteName }}</h1>
                    <p class="text-[10px] text-gray-500 -mt-1">{{ $siteTagline }}</p>
                </div>
            </a>
            
            <!-- Search Bar -->
            <div class="hidden md:flex flex-1 max-w-xl mx-8">
                <form action="{{ route('products.index') }}" method="GET" class="w-full">
                    <div class="relative" id="searchContainer">
                        <input type="text" name="search" id="searchInput" placeholder="Search for fresh halal meat, groceries..." 
                            class="w-full pl-4 pr-14 py-2 border border-gray-200 rounded-full focus:border-halal-green focus:outline-none transition-colors text-sm"
                            autocomplete="off">
                        <button type="submit" class="absolute right-1 top-1/2 -translate-y-1/2 bg-halal-green text-white w-8 h-8 rounded-full hover:bg-halal-dark transition-colors flex items-center justify-center">
                            <i class="bi bi-search text-sm"></i>
                        </button>
                        
                        <!-- Live Search Results Dropdown -->
                        <div id="searchResults" class="absolute top-full left-0 right-0 mt-2 bg-white rounded-xl shadow-2xl border border-gray-100 max-h-96 overflow-y-auto z-50 hidden">
                            <!-- Results will be populated by JavaScript -->
                        </div>
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
                @php $wishlistCount = auth()->check() ? \Cache::remember('wishlist_count_' . auth()->id(), 300, function() { return \App\Models\Wishlist::where('user_id', auth()->id())->count(); }) : 0; @endphp
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
    
</header>
    <nav class="bg-halal-green text-white">
        <div class="container mx-auto px-4">
            <div class="flex items-center">
                <!-- Categories Dropdown -->
                <div class="relative group">
                    <button class="flex items-center space-x-2 bg-halal-dark px-4 py-2 hover:bg-halal-light transition-colors">
                        <i class="bi bi-grid-3x3-gap-fill"></i>
                        <span class="font-medium">All Categories</span>
                        <i class="bi bi-chevron-down"></i>
                    </button>
                    <div class="absolute left-0 mt-0 w-64 bg-white text-gray-700 rounded-b-lg shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                        @foreach($categories ?? [] as $category)
                        <a href="{{ route('products.index', ['category' => $category->slug]) }}" class="flex items-center px-3 py-2 hover:bg-green-50 hover:text-halal-green border-b border-gray-100">
                            <i class="bi bi-dot text-halal-green mr-2"></i>
                            {{ $category->name }}
                            <span class="ml-auto text-xs text-gray-400">({{ $category->products_count ?? 0 }})</span>
                        </a>
                        @endforeach
                    </div>
                </div>
                
                <!-- Nav Links -->
                <div class="hidden lg:flex items-center space-x-1 ml-4">
                    <a href="{{ route('home') }}" class="px-3 py-2 hover:bg-halal-dark rounded transition-colors nav-menu-link {{ request()->routeIs('home') ? 'bg-halal-dark active' : '' }}">Home</a>
                    <a href="{{ route('products.index') }}" class="px-3 py-2 hover:bg-halal-dark rounded transition-colors nav-menu-link {{ request()->routeIs('products.*') ? 'bg-halal-dark active' : '' }}">Shop</a>
                    <a href="{{ route('products.index', ['sort' => 'newest']) }}" class="px-3 py-2 hover:bg-halal-dark rounded transition-colors nav-menu-link">New Arrivals</a>
                    <a href="{{ route('products.index', ['sort' => 'discount']) }}" class="px-3 py-2 hover:bg-halal-dark rounded transition-colors nav-menu-link">Deals</a>
                    <a href="{{ route('blogs.index') }}" class="px-3 py-2 hover:bg-halal-dark rounded transition-colors nav-menu-link {{ request()->routeIs('blogs.*') ? 'bg-halal-dark active' : '' }}">Blog</a>
                    <a href="{{ route('pages.contact') }}" class="px-3 py-2 hover:bg-halal-dark rounded transition-colors nav-menu-link">Contact</a>
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

<!-- Sticky Search Bar (appears on scroll) -->
<div id="stickySearch" class="fixed top-0 left-0 right-0 z-40 bg-white shadow-md py-2 hidden">
    <div class="container mx-auto px-4">
        <form action="{{ route('products.index') }}" method="GET" class="max-w-xl mx-auto">
            <div class="relative" id="stickySearchContainer">
                <input type="text" name="search" id="stickySearchInput" placeholder="Search for fresh halal meat, groceries..." 
                    class="w-full pl-4 pr-14 py-2 border border-gray-200 rounded-full focus:border-halal-green focus:outline-none transition-colors text-sm"
                    autocomplete="off">
                <button type="submit" class="absolute right-1 top-1/2 -translate-y-1/2 bg-halal-green text-white w-8 h-8 rounded-full hover:bg-halal-dark transition-colors flex items-center justify-center">
                    <i class="bi bi-search text-sm"></i>
                </button>
                <div id="stickySearchResults" class="absolute top-full left-0 right-0 mt-2 bg-white rounded-xl shadow-2xl border border-gray-100 max-h-96 overflow-y-auto z-50 hidden">
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Live Search Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    let searchTimeout;
    const searchInput = document.getElementById('searchInput');
    const searchResults = document.getElementById('searchResults');
    const stickySearch = document.getElementById('stickySearch');
    const stickySearchInput = document.getElementById('stickySearchInput');
    const stickySearchResults = document.getElementById('stickySearchResults');

    // Sticky search visibility on scroll
    if (stickySearch) {
        const header = document.querySelector('header');
        let stickySearchVisible = false;
        window.addEventListener('scroll', function() {
            if (!header) return;
            const headerBottom = header.getBoundingClientRect().bottom;
            if (headerBottom <= 0 && !stickySearchVisible) {
                stickySearch.classList.remove('hidden');
                stickySearchVisible = true;
            } else if (headerBottom > 0 && stickySearchVisible) {
                stickySearch.classList.add('hidden');
                stickySearchVisible = false;
                if (stickySearchResults) stickySearchResults.classList.add('hidden');
            }
        });
    }

    // Sticky search input handler
    if (stickySearchInput) {
        stickySearchInput.addEventListener('input', function(e) {
            clearTimeout(searchTimeout);
            const query = e.target.value.trim();
            if (query.length < 2) {
                if (stickySearchResults) stickySearchResults.classList.add('hidden');
                if (stickySearchResults) stickySearchResults.innerHTML = '';
                return;
            }
            if (stickySearchResults) {
                stickySearchResults.classList.remove('hidden');
                stickySearchResults.innerHTML = '<div class="p-4 text-center text-gray-500"><i class="bi bi-arrow-repeat animate-spin text-2xl"></i><p class="mt-2">Searching...</p></div>';
            }
            searchTimeout = setTimeout(() => {
                fetch('{{ route('search.suggestions') }}?q=' + encodeURIComponent(query))
                    .then(response => response.json())
                    .then(data => {
                        if (stickySearchResults) renderSearchResults(data, query, stickySearchResults);
                    })
                    .catch(() => {
                        if (stickySearchResults) stickySearchResults.innerHTML = '<div class="p-4 text-center text-red-500"><i class="bi bi-exclamation-circle text-2xl"></i><p class="mt-2">Error loading results</p></div>';
                    });
            }, 300);
        });
        document.addEventListener('click', function(e) {
            const container = document.getElementById('stickySearchContainer');
            if (container && !container.contains(e.target) && stickySearchResults) {
                stickySearchResults.classList.add('hidden');
            }
        });
        stickySearchInput.addEventListener('focus', function(e) {
            if (e.target.value.trim().length >= 2 && stickySearchResults) {
                stickySearchResults.classList.remove('hidden');
            }
        });
    }

    // Header search input handler
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            clearTimeout(searchTimeout);
            const query = e.target.value.trim();
            if (query.length < 2) {
                searchResults.classList.add('hidden');
                searchResults.innerHTML = '';
                return;
            }
            searchResults.classList.remove('hidden');
            searchResults.innerHTML = '<div class="p-4 text-center text-gray-500"><i class="bi bi-arrow-repeat animate-spin text-2xl"></i><p class="mt-2">Searching...</p></div>';
            searchTimeout = setTimeout(() => {
                fetch('{{ route('search.suggestions') }}?q=' + encodeURIComponent(query))
                    .then(response => response.json())
                    .then(data => {
                        renderSearchResults(data, query, searchResults);
                    })
                    .catch(error => {
                        console.error('Search error:', error);
                        searchResults.innerHTML = '<div class="p-4 text-center text-red-500"><i class="bi bi-exclamation-circle text-2xl"></i><p class="mt-2">Error loading results</p></div>';
                    });
            }, 300);
        });
        document.addEventListener('click', function(e) {
            const searchContainer = document.getElementById('searchContainer');
            if (searchContainer && !searchContainer.contains(e.target)) {
                searchResults.classList.add('hidden');
            }
        });
        searchInput.addEventListener('focus', function(e) {
            if (e.target.value.trim().length >= 2) {
                searchResults.classList.remove('hidden');
            }
        });
    }

    function renderSearchResults(data, query, resultsContainer) {
        const { products, categories } = data;
        if (products.length === 0 && categories.length === 0) {
            resultsContainer.innerHTML = '<div class="p-6 text-center"><i class="bi bi-search text-4xl text-gray-300"></i><p class="mt-2 text-gray-500">No results found for "' + query + '"</p><p class="text-sm text-gray-400 mt-1">Try different keywords</p></div>';
            return;
        }
        let html = '';
        if (categories.length > 0) {
            html += '<div class="p-3 bg-gray-50 border-b border-gray-100"><span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Categories</span></div>';
            categories.forEach(category => {
                const imageUrl = category.image || 'https://placehold.co/50x50?text=C';
                html += '<a href="{{ route('products.index') }}?category=' + category.slug + '" class="flex items-center p-3 hover:bg-green-50 transition-colors border-b border-gray-50"><img src="' + escapeHtml(imageUrl) + '" alt="' + escapeHtml(category.name) + '" class="w-10 h-10 rounded-lg object-cover bg-gray-100"><div class="ml-3"><p class="font-medium text-gray-800">' + highlightMatch(category.name, query) + '</p><p class="text-xs text-gray-500">Browse category</p></div><i class="bi bi-chevron-right ml-auto text-gray-400"></i></a>';
            });
        }
        if (products.length > 0) {
            html += '<div class="p-3 bg-gray-50 border-b border-gray-100"><span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Products</span></div>';
            products.forEach(product => {
                const price = product.sale_price || product.price;
                const originalPrice = product.sale_price ? product.price : null;
                let imageUrl = product.featured_image || 'https://placehold.co/60x60?text=P';
                if (imageUrl && !imageUrl.startsWith('http') && !imageUrl.startsWith('/storage/') && !imageUrl.startsWith('/uploads/')) {
                    imageUrl = '/storage/' + imageUrl;
                }
                html += '<a href="{{ route('products.show', '') }}/' + product.slug + '" class="flex items-center p-3 hover:bg-green-50 transition-colors border-b border-gray-50 last:border-b-0"><img src="' + escapeHtml(imageUrl) + '" alt="' + escapeHtml(product.name) + '" class="w-14 h-14 rounded-lg object-cover bg-gray-100"><div class="ml-3 flex-1"><p class="font-medium text-gray-800 line-clamp-1">' + highlightMatch(product.name, query) + '</p><div class="flex items-center mt-1"><span class="text-halal-green font-semibold">৳' + Number(price).toLocaleString() + '</span>' + (originalPrice ? '<span class="text-gray-400 text-sm line-through ml-2">৳' + Number(originalPrice).toLocaleString() + '</span>' : '') + '</div>' + (product.category ? '<span class="text-xs text-gray-500">' + escapeHtml(product.category.name) + '</span>' : '') + '</div><i class="bi bi-arrow-right ml-2 text-gray-400"></i></a>';
            });
        }
        html += '<a href="{{ route('products.index') }}?search=' + encodeURIComponent(query) + '" class="block p-3 text-center bg-halal-green text-white hover:bg-halal-dark transition-colors rounded-b-xl"><i class="bi bi-search mr-2"></i>View all results for "' + query + '"</a>';
        resultsContainer.innerHTML = html;
    }

    function escapeHtml(unsafe) {
        return String(unsafe).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
    }

    function highlightMatch(text, query) {
        if (!query) return escapeHtml(text);
        const escaped = escapeHtml(text);
        const regex = new RegExp('(' + query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + ')', 'gi');
        return escaped.replace(regex, '<mark class="bg-yellow-200 px-0.5 rounded">$1</mark>');
    }
});
</script>
