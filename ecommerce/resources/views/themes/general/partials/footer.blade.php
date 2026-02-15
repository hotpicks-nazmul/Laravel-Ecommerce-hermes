<!-- Footer -->
<footer class="bg-halal-dark text-white">
    <!-- Newsletter Section - Hidden on blog pages -->
    @if(!request()->routeIs('blogs.*'))
    <div class="gradient-halal py-8">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row items-center justify-between">
                <div class="mb-4 md:mb-0">
                    <h3 class="font-poppins text-2xl font-bold">Subscribe to Our Newsletter</h3>
                    <p class="text-green-100">Get updates on new products, special offers, and halal food tips!</p>
                </div>
                <form class="flex w-full md:w-auto">
                    <input type="email" placeholder="Enter your email" 
                        class="px-4 py-3 rounded-l-full w-full md:w-64 focus:outline-none text-gray-700">
                    <button type="submit" class="bg-halal-gold text-white px-6 py-3 rounded-r-full hover:bg-yellow-600 transition-colors font-medium">
                        Subscribe
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endif
    
    <!-- Main Footer -->
    <div class="py-12">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- About -->
                <div>
                    <div class="flex items-center space-x-2 mb-4">
                        <div class="w-10 h-10 bg-halal-gold rounded-full flex items-center justify-center">
                            <i class="bi bi-shop text-white"></i>
                        </div>
                        <span class="font-poppins text-xl font-bold">Halal Food Store</span>
                    </div>
                    <p class="text-gray-400 mb-4">
                        Your trusted source for premium quality halal meat, poultry, seafood, and groceries. 
                        We deliver fresh across Bangladesh.
                    </p>
                    <div class="flex space-x-3">
                        <a href="#" class="w-10 h-10 bg-gray-700 rounded-full flex items-center justify-center hover:bg-halal-green transition-colors">
                            <i class="bi bi-facebook"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-gray-700 rounded-full flex items-center justify-center hover:bg-halal-green transition-colors">
                            <i class="bi bi-instagram"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-gray-700 rounded-full flex items-center justify-center hover:bg-halal-green transition-colors">
                            <i class="bi bi-youtube"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-gray-700 rounded-full flex items-center justify-center hover:bg-halal-green transition-colors">
                            <i class="bi bi-whatsapp"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Quick Links -->
                <div>
                    <h4 class="font-poppins text-lg font-bold mb-4 text-halal-gold">Quick Links</h4>
                    <ul class="space-y-2">
                        <li><a href="{{ route('home') }}" class="text-gray-400 hover:text-white transition-colors"><i class="bi bi-chevron-right text-halal-green mr-2"></i>Home</a></li>
                        <li><a href="{{ route('products.index') }}" class="text-gray-400 hover:text-white transition-colors"><i class="bi bi-chevron-right text-halal-green mr-2"></i>Shop</a></li>
                        <li><a href="{{ route('products.index', ['sort' => 'newest']) }}" class="text-gray-400 hover:text-white transition-colors"><i class="bi bi-chevron-right text-halal-green mr-2"></i>New Arrivals</a></li>
                        <li><a href="{{ route('products.index', ['sort' => 'discount']) }}" class="text-gray-400 hover:text-white transition-colors"><i class="bi bi-chevron-right text-halal-green mr-2"></i>Deals & Offers</a></li>
                        <li><a href="{{ route('blogs.index') }}" class="text-gray-400 hover:text-white transition-colors"><i class="bi bi-chevron-right text-halal-green mr-2"></i>Blog</a></li>
                    </ul>
                </div>
                
                <!-- Customer Service -->
                <div>
                    <h4 class="font-poppins text-lg font-bold mb-4 text-halal-gold">Customer Service</h4>
                    <ul class="space-y-2">
                        <li><a href="{{ route('pages.contact') }}" class="text-gray-400 hover:text-white transition-colors"><i class="bi bi-chevron-right text-halal-green mr-2"></i>Contact Us</a></li>
                        <li><a href="{{ route('pages.show', 'about') }}" class="text-gray-400 hover:text-white transition-colors"><i class="bi bi-chevron-right text-halal-green mr-2"></i>About Us</a></li>
                        <li><a href="{{ route('pages.show', 'faq') }}" class="text-gray-400 hover:text-white transition-colors"><i class="bi bi-chevron-right text-halal-green mr-2"></i>FAQ</a></li>
                        <li><a href="{{ route('pages.show', 'shipping') }}" class="text-gray-400 hover:text-white transition-colors"><i class="bi bi-chevron-right text-halal-green mr-2"></i>Shipping Policy</a></li>
                        <li><a href="{{ route('pages.show', 'returns') }}" class="text-gray-400 hover:text-white transition-colors"><i class="bi bi-chevron-right text-halal-green mr-2"></i>Returns & Refunds</a></li>
                        <li><a href="{{ route('pages.show', 'privacy') }}" class="text-gray-400 hover:text-white transition-colors"><i class="bi bi-chevron-right text-halal-green mr-2"></i>Privacy Policy</a></li>
                    </ul>
                </div>
                
                <!-- Contact Info -->
                <div>
                    <h4 class="font-poppins text-lg font-bold mb-4 text-halal-gold">Contact Us</h4>
                    <ul class="space-y-3">
                        <li class="flex items-start">
                            <i class="bi bi-geo-alt-fill text-halal-green mt-1 mr-3"></i>
                            <span class="text-gray-400">123 Green Market Road<br>Dhaka-1205, Bangladesh</span>
                        </li>
                        <li class="flex items-center">
                            <i class="bi bi-telephone-fill text-halal-green mr-3"></i>
                            <a href="tel:+8801700000000" class="text-gray-400 hover:text-white transition-colors">+880 1700-000000</a>
                        </li>
                        <li class="flex items-center">
                            <i class="bi bi-envelope-fill text-halal-green mr-3"></i>
                            <a href="mailto:info@halalfoodstore.com" class="text-gray-400 hover:text-white transition-colors">info@halalfoodstore.com</a>
                        </li>
                        <li class="flex items-center">
                            <i class="bi bi-clock-fill text-halal-green mr-3"></i>
                            <span class="text-gray-400">Sat - Thu: 8AM - 10PM</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Payment Methods -->
    <div class="border-t border-gray-700 py-6">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row items-center justify-between">
                <div class="mb-4 md:mb-0">
                    <span class="text-gray-400 mr-2">We Accept:</span>
                    <span class="inline-flex items-center space-x-2">
                        <span class="bg-pink-600 text-white px-2 py-1 rounded text-xs font-bold">bKash</span>
                        <span class="bg-orange-500 text-white px-2 py-1 rounded text-xs font-bold">Nagad</span>
                        <span class="bg-purple-600 text-white px-2 py-1 rounded text-xs font-bold">Rocket</span>
                        <span class="bg-blue-600 text-white px-2 py-1 rounded text-xs font-bold">Visa</span>
                        <span class="bg-red-600 text-white px-2 py-1 rounded text-xs font-bold">Mastercard</span>
                    </span>
                </div>
                <div class="flex items-center space-x-4">
                    <img src="https://img.icons8.com/color/48/verified-badge.png" alt="Verified" class="h-8 opacity-70">
                    <span class="text-gray-400 text-sm">100% Secure Payments</span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Copyright -->
    <div class="bg-black py-4">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row items-center justify-between text-sm text-gray-500">
                <p>&copy; {{ date('Y') }} Halal Food Store. All rights reserved.</p>
                <p class="mt-2 md:mt-0">
                    Made with <i class="bi bi-heart-fill text-red-500"></i> in Bangladesh
                </p>
            </div>
        </div>
    </div>
</footer>
