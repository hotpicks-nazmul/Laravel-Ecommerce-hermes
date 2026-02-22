<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- SEO Meta Tags -->
    <title>@yield('title', 'Halal Food Store') - Premium Quality Halal Food</title>
    <meta name="description" content="@yield('meta_description', 'Buy premium quality halal meat, poultry, seafood, and groceries online. Fresh delivery across Bangladesh.')">
    <meta name="keywords" content="@yield('meta_keywords', 'halal food, halal meat, online grocery, fresh meat, Bangladesh')">
    
    <!-- Open Graph -->
    <meta property="og:title" content="@yield('og_title', 'Halal Food Store')">
    <meta property="og:description" content="@yield('og_description', 'Premium Quality Halal Food')">
    <meta property="og:type" content="website">
    
    <!-- Google Fonts - Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'poppins': ['Poppins', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            200: '#bbf7d0',
                            300: '#86efac',
                            400: '#4ade80',
                            500: '#22c55e',
                            600: '#16a34a',
                            700: '#15803d',
                            800: '#166534',
                            900: '#14532d',
                        },
                        halal: {
                            green: '#2D5A27',
                            light: '#4A7C43',
                            dark: '#1E3D1A',
                            gold: '#D4AF37',
                            cream: '#FDF8F0',
                        }
                    }
                }
            }
        }
    </script>
    
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
        .gradient-halal {
            background: linear-gradient(135deg, #2D5A27 0%, #4A7C43 100%);
        }
        .gradient-gold {
            background: linear-gradient(135deg, #D4AF37 0%, #F4D03F 100%);
        }
        .hero-pattern {
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
        .product-card:hover .product-image {
            transform: scale(1.05);
        }
        .scroll-smooth {
            scroll-behavior: smooth;
        }
    </style>
    
    @stack('styles')
</head>
<body class="bg-halal-cream scroll-smooth font-poppins">
    <!-- Header -->
    @include('themes.general.partials.header')
    
    <!-- Main Content -->
    <main>
        @yield('content')
    </main>
    
    <!-- Footer -->
    @include('themes.general.partials.footer')
    
    <!-- Cart Sidebar -->
    @include('themes.general.partials.cart-sidebar')
    
    <!-- Wishlist Sidebar -->
    @include('themes.general.partials.wishlist-sidebar')
    
    <!-- Quick View Modal -->
    <div id="quick-view-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white rounded-lg max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold">Quick View</h3>
                    <button onclick="$('#quick-view-modal').addClass('hidden');$('#quick-view-modal').removeClass('flex');" class="text-gray-500 hover:text-gray-700">
                        <i class="bi bi-x-lg text-2xl"></i>
                    </button>
                </div>
                <div id="quick-view-content">
                    <!-- Content loaded via AJAX -->
                </div>
            </div>
        </div>
    </div>
    
    <!-- Chat Widget -->
    @include('themes.general.partials.chat-widget')
    
    <!-- WhatsApp Widget -->
    @include('themes.general.partials.whatsapp-widget')
    
    <!-- Mobile Menu -->
    @include('themes.general.partials.mobile-menu')
    
    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
        // Cart functionality
        function updateCartCount() {
            $.get('{{ route("cart.count") }}', function(data) {
                $('.cart-count').text(data.count);
            });
        }
        
        // Quick View function
        function quickView(productId) {
            $.ajax({
                url: '/api/products/quick-view/' + productId,
                method: 'GET',
                success: function(response) {
                    $('#quick-view-content').html(response);
                    $('#quick-view-modal').removeClass('hidden');
                    $('#quick-view-modal').addClass('flex');
                },
                error: function() {
                    showToast('Failed to load product', 'error');
                }
            });
        }
        
        // Add to Wishlist function
        function addToWishlist(productId) {
            $.ajax({
                url: '{{ route("wishlist.toggle") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    product_id: productId
                },
                success: function(response) {
                    if (response.success) {
                        showToast(response.message, 'success');
                        // Update wishlist count
                        updateWishlistCount(response.added ? 1 : -1);
                        // Update button state if exists
                        var btn = $('.wishlist-btn-' + productId);
                        if (btn.length) {
                            if (response.added) {
                                btn.addClass('bg-red-500 text-white').removeClass('text-gray-900 bg-white');
                                btn.find('svg').attr('fill', 'currentColor');
                            } else {
                                btn.removeClass('bg-red-500 text-white').addClass('text-gray-900 bg-white');
                                btn.find('svg').attr('fill', 'none');
                            }
                        }
                    } else if (response.login_required) {
                        showToast(response.message, 'error');
                        setTimeout(function() {
                            window.location.href = '{{ route("login") }}';
                        }, 1500);
                    }
                },
                error: function() {
                    showToast('Please login to add to wishlist', 'error');
                }
            });
        }
        
        // Update wishlist count in header
        function updateWishlistCount(change) {
            var countEl = $('.wishlist-count');
            var currentCount = parseInt(countEl.text()) || 0;
            var newCount = currentCount + change;
            countEl.text(newCount);
            if (newCount <= 0) {
                countEl.addClass('hidden');
            } else {
                countEl.removeClass('hidden');
            }
        }
        
        $(document).ready(function() {
            updateCartCount();
            
            // Toast notification
            window.showToast = function(message, type = 'info') {
                var toast = $('<div class="fixed bottom-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg text-white ' + 
                    (type === 'success' ? 'bg-green-600' : type === 'error' ? 'bg-red-600' : 'bg-blue-600') + 
                    ' transform transition-all duration-300 translate-x-full">' + message + '</div>');
                $('body').append(toast);
                setTimeout(() => toast.removeClass('translate-x-full'), 100);
                setTimeout(() => {
                    toast.addClass('translate-x-full');
                    setTimeout(() => toast.remove(), 300);
                }, 3000);
            };
        });
    </script>
    
    @stack('scripts')
</body>
</html>
