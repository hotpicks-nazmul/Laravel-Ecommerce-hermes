<!-- Mobile Menu Overlay -->
<div id="mobileMenuOverlay" class="fixed inset-0 bg-black/50 z-50 hidden" onclick="closeMobileMenu()"></div>

<!-- Mobile Menu -->
<div id="mobileMenu" class="fixed top-0 left-0 w-80 h-full bg-white z-50 transform -translate-x-full transition-transform duration-300 overflow-y-auto">
    <div class="p-4">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center space-x-2">
                <div class="w-10 h-10 gradient-halal rounded-full flex items-center justify-center">
                    <i class="bi bi-shop text-white"></i>
                </div>
                <span class="font-poppins text-xl font-bold text-halal-green">Halal Food</span>
            </div>
            <button onclick="closeMobileMenu()" class="text-gray-500 hover:text-gray-700">
                <i class="bi bi-x-lg text-2xl"></i>
            </button>
        </div>
        
        <!-- Search -->
        <form action="{{ route('products.index') }}" method="GET" class="mb-6">
            <div class="relative">
                <input type="text" name="search" placeholder="Search products..." 
                    class="w-full pl-4 pr-10 py-2 border border-gray-300 rounded-lg focus:border-halal-green focus:outline-none">
                <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-halal-green">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </form>
        
        <!-- User Section -->
        @auth
        <div class="bg-green-50 rounded-lg p-4 mb-6">
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 bg-halal-green rounded-full flex items-center justify-center text-white font-bold">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div>
                    <h4 class="font-medium text-gray-800">{{ Auth::user()->name }}</h4>
                    <p class="text-sm text-gray-500">{{ Auth::user()->email }}</p>
                </div>
            </div>
            <div class="mt-4 space-y-2">
                <a href="{{ route('account.profile') }}" class="flex items-center text-gray-600 hover:text-halal-green py-2">
                    <i class="bi bi-person mr-3"></i> My Profile
                </a>
                <a href="{{ route('account.orders') }}" class="flex items-center text-gray-600 hover:text-halal-green py-2">
                    <i class="bi bi-bag mr-3"></i> My Orders
                </a>
                <a href="{{ route('account.wishlist') }}" class="flex items-center text-gray-600 hover:text-halal-green py-2">
                    <i class="bi bi-heart mr-3"></i> Wishlist
                </a>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="flex items-center text-red-600 hover:text-red-700 py-2">
                        <i class="bi bi-box-arrow-right mr-3"></i> Logout
                    </button>
                </form>
            </div>
        </div>
        @else
        <div class="bg-gray-50 rounded-lg p-4 mb-6">
            <p class="text-gray-600 mb-3">Login to access your account</p>
            <a href="{{ route('login') }}" class="block w-full bg-halal-green text-white text-center py-2 rounded-lg hover:bg-halal-dark transition-colors">
                <i class="bi bi-box-arrow-in-right mr-2"></i> Login
            </a>
            <a href="{{ route('register') }}" class="block w-full border border-halal-green text-halal-green text-center py-2 rounded-lg mt-2 hover:bg-green-50 transition-colors">
                <i class="bi bi-person-plus mr-2"></i> Register
            </a>
        </div>
        @endauth
        
        <!-- Navigation -->
        <nav class="space-y-1">
            <a href="{{ route('home') }}" class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('home') ? 'bg-green-50 text-halal-green' : 'text-gray-700 hover:bg-gray-50' }}">
                <i class="bi bi-house-door mr-3"></i> Home
            </a>
            <a href="{{ route('products.index') }}" class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('products.*') ? 'bg-green-50 text-halal-green' : 'text-gray-700 hover:bg-gray-50' }}">
                <i class="bi bi-grid mr-3"></i> Shop
            </a>
            <a href="{{ route('products.index', ['sort' => 'newest']) }}" class="flex items-center px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-50">
                <i class="bi bi-stars mr-3"></i> New Arrivals
            </a>
            <a href="{{ route('products.index', ['sort' => 'discount']) }}" class="flex items-center px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-50">
                <i class="bi bi-fire mr-3"></i> Deals
            </a>
            <a href="{{ route('blogs.index') }}" class="flex items-center px-4 py-3 rounded-lg {{ request()->routeIs('blogs.*') ? 'bg-green-50 text-halal-green' : 'text-gray-700 hover:bg-gray-50' }}">
                <i class="bi bi-newspaper mr-3"></i> Blog
            </a>
            <a href="{{ route('pages.contact') }}" class="flex items-center px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-50">
                <i class="bi bi-telephone mr-3"></i> Contact
            </a>
        </nav>
        
        <!-- Categories -->
        <div class="mt-6">
            <h4 class="font-medium text-gray-800 mb-3 px-4">Categories</h4>
            <div class="space-y-1">
                @foreach($categories ?? [] as $category)
                <a href="{{ route('products.index', ['category' => $category->slug]) }}" class="flex items-center justify-between px-4 py-2 text-gray-600 hover:text-halal-green hover:bg-gray-50 rounded-lg">
                    <span>{{ $category->name }}</span>
                    <span class="text-xs text-gray-400">({{ $category->products_count ?? 0 }})</span>
                </a>
                @endforeach
            </div>
        </div>
        
        <!-- Contact Info -->
        <div class="mt-6 p-4 bg-halal-dark rounded-lg text-white">
            <h4 class="font-medium mb-3">Need Help?</h4>
            <div class="space-y-2 text-sm">
                <a href="tel:+8801700000000" class="flex items-center text-green-100 hover:text-white">
                    <i class="bi bi-telephone mr-2"></i> +880 1700-000000
                </a>
                <a href="mailto:info@halalfoodstore.com" class="flex items-center text-green-100 hover:text-white">
                    <i class="bi bi-envelope mr-2"></i> info@halalfoodstore.com
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function toggleMobileMenu() {
    document.getElementById('mobileMenu').classList.remove('-translate-x-full');
    document.getElementById('mobileMenuOverlay').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeMobileMenu() {
    document.getElementById('mobileMenu').classList.add('-translate-x-full');
    document.getElementById('mobileMenuOverlay').classList.add('hidden');
    document.body.style.overflow = '';
}
</script>
