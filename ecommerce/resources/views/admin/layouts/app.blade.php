<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Admin Panel</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    
    <style>
        :root {
            --sidebar-width: 260px;
            --header-height: 60px;
        }
        
        body {
            background-color: #f8f9fa;
        }
        
        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            z-index: 1000;
            transition: transform 0.3s ease;
            display: flex;
            flex-direction: column;
        }
        
        .sidebar-header {
            padding: 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            flex-shrink: 0;
        }
        
        .sidebar-header h4 {
            color: white;
            margin: 0;
            font-weight: 600;
        }
        
        .sidebar-menu {
            padding: 15px 0;
            overflow-y: auto;
            flex: 1;
            scrollbar-width: thin;
            scrollbar-color: rgba(255,255,255,0.3) transparent;
        }
        
        .sidebar-menu::-webkit-scrollbar {
            width: 6px;
        }
        
        .sidebar-menu::-webkit-scrollbar-track {
            background: transparent;
        }
        
        .sidebar-menu::-webkit-scrollbar-thumb {
            background-color: rgba(255,255,255,0.3);
            border-radius: 3px;
        }
        
        .sidebar-menu::-webkit-scrollbar-thumb:hover {
            background-color: rgba(255,255,255,0.5);
        }
        
        .sidebar-menu .nav-item {
            margin: 2px 10px;
        }
        
        .sidebar-menu .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
        }
        
        .sidebar-menu .nav-link:hover {
            color: white;
            background: rgba(255,255,255,0.1);
        }
        
        .sidebar-menu .nav-link.active {
            color: white;
            background: rgba(255,255,255,0.2);
        }
        
        .sidebar-menu .nav-link i {
            margin-right: 10px;
            font-size: 1.1rem;
        }
        
        .sidebar-menu .nav-header {
            color: rgba(255,255,255,0.5);
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 15px 20px 5px;
            font-weight: 600;
        }
        
        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
        }
        
        /* Header */
        .main-header {
            height: var(--header-height);
            background: white;
            border-bottom: 1px solid #e9ecef;
            position: sticky;
            top: 0;
            z-index: 999;
        }
        
        .main-header .navbar {
            height: 100%;
        }
        
        /* Content Area */
        .content-area {
            padding: 20px;
        }
        
        /* Cards */
        .stat-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: transform 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-card .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        
        /* Responsive */
        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
        }
        
        /* Floating Save Button */
        .floating-save-container {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
            display: flex;
            gap: 10px;
            align-items: center;
        }
        
        .floating-save-btn {
            padding: 12px 24px;
            border-radius: 50px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
            font-weight: 500;
        }
        
        .floating-save-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25);
        }
        
        .floating-save-btn.btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }
        
        .floating-save-btn.btn-primary:hover {
            background: linear-gradient(135deg, #5a6fd6 0%, #6a4190 100%);
        }
        
        .floating-reset-btn {
            padding: 12px 20px;
            border-radius: 50px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
            background-color: #6c757d;
            border: none;
            color: white;
        }
        
        .floating-reset-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
            background-color: #5c636a;
            color: white;
        }
        
        /* Add padding at bottom of content to prevent overlap with floating button */
        .content-area.has-floating-save {
            padding-bottom: 100px;
        }
        
        /* Admin Toast Notification */
        .admin-toast-container {
            position: fixed;
            top: 80px;
            right: 20px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        
        .admin-toast {
            min-width: 300px;
            max-width: 400px;
            padding: 16px 20px;
            border-radius: 12px;
            background: white;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            display: flex;
            align-items: center;
            gap: 12px;
            transform: translateX(120%);
            opacity: 0;
            transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }
        
        .admin-toast.show {
            transform: translateX(0);
            opacity: 1;
        }
        
        .admin-toast.hide {
            transform: translateX(120%);
            opacity: 0;
        }
        
        .admin-toast.success {
            border-left: 4px solid #10b981;
        }
        
        .admin-toast.error {
            border-left: 4px solid #ef4444;
        }
        
        .admin-toast.warning {
            border-left: 4px solid #f59e0b;
        }
        
        .admin-toast-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        
        .admin-toast.success .admin-toast-icon {
            background: rgba(16, 185, 129, 0.1);
            color: #10b981;
        }
        
        .admin-toast.error .admin-toast-icon {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
        }
        
        .admin-toast.warning .admin-toast-icon {
            background: rgba(245, 158, 11, 0.1);
            color: #f59e0b;
        }
        
        .admin-toast-content {
            flex: 1;
        }
        
        .admin-toast-title {
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 2px;
        }
        
        .admin-toast-message {
            font-size: 0.875rem;
            color: #6b7280;
        }
        
        .admin-toast-close {
            background: none;
            border: none;
            color: #9ca3af;
            cursor: pointer;
            padding: 4px;
            transition: color 0.2s;
        }
        
        .admin-toast-close:hover {
            color: #4b5563;
        }
        
        /* Hide original save buttons when floating is active */
        .original-save-container {
            display: none;
        }
        
        @media (max-width: 575.98px) {
            .floating-save-container {
                bottom: 15px;
                right: 15px;
                left: 15px;
                justify-content: center;
            }
            
            .floating-save-btn,
            .floating-reset-btn {
                padding: 10px 18px;
                font-size: 0.9rem;
            }
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h4><i class="bi bi-shop me-2"></i>{{ config('app.name', 'E-Commerce') }}</h4>
        </div>
        
        <nav class="sidebar-menu">
            <div class="nav-header">Main</div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                </li>
            </ul>
            
            <div class="nav-header">Catalog</div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}" href="{{ route('admin.products.index') }}">
                        <i class="bi bi-box-seam"></i> Products
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" href="{{ route('admin.categories.index') }}">
                        <i class="bi bi-folder"></i> Categories
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.coupons.*') ? 'active' : '' }}" href="{{ route('admin.coupons.index') }}">
                        <i class="bi bi-tag"></i> Coupons
                    </a>
                </li>
            </ul>
            
            <div class="nav-header">Sales</div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}" href="{{ route('admin.orders.index') }}">
                        <i class="bi bi-cart-check"></i> Orders
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}" href="{{ route('admin.reviews.index') }}">
                        <i class="bi bi-star"></i> Reviews
                    </a>
                </li>
            </ul>
            
            <div class="nav-header">Customers</div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}" href="{{ route('admin.customers.index') }}">
                        <i class="bi bi-people"></i> Customers
                    </a>
                </li>
            </ul>
            
            <div class="nav-header">Content</div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.pages.*') ? 'active' : '' }}" href="{{ route('admin.pages.index') }}">
                        <i class="bi bi-file-text"></i> Pages
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.blogs.*') ? 'active' : '' }}" href="{{ route('admin.blogs.index') }}">
                        <i class="bi bi-newspaper"></i> Blog
                    </a>
                </li>
            </ul>
            
            <div class="nav-header">Appearance</div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.themes.*') ? 'active' : '' }}" href="{{ route('admin.themes.index') }}">
                        <i class="bi bi-palette"></i> Themes
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.hero.*') ? 'active' : '' }}" href="{{ route('admin.hero.index') }}">
                        <i class="bi bi-image"></i> Hero Section
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.homepage.*') ? 'active' : '' }}" href="{{ route('admin.homepage.index') }}">
                        <i class="bi bi-house-door"></i> Home Page Settings
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.media.*') ? 'active' : '' }}" href="{{ route('admin.media.index') }}">
                        <i class="bi bi-images"></i> Media
                    </a>
                </li>
            </ul>
            
            <div class="nav-header">Settings</div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.settings.index') ? 'active' : '' }}" href="{{ route('admin.settings.index') }}">
                        <i class="bi bi-gear"></i> Settings
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.settings.social-login') ? 'active' : '' }}" href="{{ route('admin.settings.social-login') }}">
                        <i class="bi bi-google"></i> Social Login
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.settings.whatsapp') ? 'active' : '' }}" href="{{ route('admin.settings.whatsapp') }}">
                        <i class="bi bi-whatsapp"></i> WhatsApp Chat
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.settings.footer') ? 'active' : '' }}" href="{{ route('admin.settings.footer') }}">
                        <i class="bi bi-layout-text-window-reverse"></i> Footer Settings
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.payment-gateways.*') ? 'active' : '' }}" href="{{ route('admin.payment-gateways.index') }}">
                        <i class="bi bi-credit-card"></i> Payment Gateways
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.seo.*') ? 'active' : '' }}" href="{{ route('admin.seo.index') }}">
                        <i class="bi bi-search"></i> SEO
                    </a>
                </li>
            </ul>
            
            <div class="nav-header">Support</div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.chat.*') ? 'active' : '' }}" href="{{ route('admin.chat.index') }}">
                        <i class="bi bi-chat-dots"></i> Live Chat
                    </a>
                </li>
            </ul>
            
            <div class="nav-header">Reports</div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}" href="{{ route('admin.reports.sales') }}">
                        <i class="bi bi-graph-up"></i> Reports
                    </a>
                </li>
            </ul>
        </nav>
    </aside>
    
    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <header class="main-header">
            <nav class="navbar navbar-expand-lg">
                <div class="container-fluid">
                    <button class="btn btn-link d-lg-none" id="sidebarToggle">
                        <i class="bi bi-list fs-4"></i>
                    </button>
                    
                    <div class="ms-auto d-flex align-items-center">
                        <!-- View Store -->
                        <a href="{{ route('home') }}" target="_blank" class="btn btn-outline-primary btn-sm me-3">
                            <i class="bi bi-box-arrow-up-right me-1"></i> View Store
                        </a>
                        
                        <!-- Notifications -->
                        <div class="dropdown me-3">
                            <button class="btn btn-light position-relative" data-bs-toggle="dropdown">
                                <i class="bi bi-bell"></i>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    3
                                </span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><h6 class="dropdown-header">Notifications</h6></li>
                                <li><a class="dropdown-item" href="#">New order received</a></li>
                                <li><a class="dropdown-item" href="#">Low stock alert</a></li>
                                <li><a class="dropdown-item" href="#">New review pending</a></li>
                            </ul>
                        </div>
                        
                        <!-- User Menu -->
                        <div class="dropdown">
                            <button class="btn btn-light dropdown-toggle d-flex align-items-center" data-bs-toggle="dropdown">
                                <img src="{{ Auth::user()->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) }}" 
                                     alt="{{ Auth::user()->name }}" class="rounded-circle me-2" width="32" height="32">
                                {{ Auth::user()->name }}
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="{{ route('admin.profile') }}"><i class="bi bi-person me-2"></i> Profile</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form id="logout-form" action="{{ route('admin.logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item" onclick="event.stopPropagation(); document.getElementById('logout-form').submit(); return false;"><i class="bi bi-box-arrow-right me-2"></i> Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>
        </header>
        
        <!-- Content -->
        <div class="content-area">
            @yield('content')
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    
    <script>
        // Sidebar toggle
        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('show');
        });
        
        // Save sidebar scroll position before page unload
        window.addEventListener('beforeunload', function() {
            const sidebar = document.querySelector('.sidebar-menu');
            if (sidebar) {
                sessionStorage.setItem('sidebarScrollPosition', sidebar.scrollTop);
            }
        });
        
        // Restore sidebar scroll position on page load
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.querySelector('.sidebar-menu');
            const savedPosition = sessionStorage.getItem('sidebarScrollPosition');
            
            if (sidebar && savedPosition) {
                sidebar.scrollTop = parseInt(savedPosition);
            }
            
            // Scroll active menu item into view
            const activeLink = document.querySelector('.sidebar-menu .nav-link.active');
            if (activeLink) {
                // Small delay to ensure DOM is ready
                setTimeout(function() {
                    activeLink.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }, 100);
            }
        });
        
        // Initialize DataTables only on tables with data-table class
        $(document).ready(function() {
            $('.data-table').each(function() {
                // Check if table has proper structure (no colspan in tbody)
                var hasColspan = $(this).find('tbody td[colspan]').length > 0;
                if (!hasColspan && !$.fn.DataTable.isDataTable(this)) {
                    $(this).DataTable({
                        pageLength: 25,
                        responsive: true,
                        language: {
                            emptyTable: 'No data available'
                        }
                    });
                }
            });
        });
        
        // Admin Toast Notification System
        window.adminToast = function(type, title, message, duration = 4000) {
            let container = document.querySelector('.admin-toast-container');
            if (!container) {
                container = document.createElement('div');
                container.className = 'admin-toast-container';
                document.body.appendChild(container);
            }
            
            const toast = document.createElement('div');
            toast.className = `admin-toast ${type}`;
            
            const icons = {
                success: 'bi-check-circle-fill',
                error: 'bi-x-circle-fill',
                warning: 'bi-exclamation-triangle-fill'
            };
            
            toast.innerHTML = `
                <div class="admin-toast-icon">
                    <i class="bi ${icons[type]} fs-5"></i>
                </div>
                <div class="admin-toast-content">
                    <div class="admin-toast-title">${title}</div>
                    <div class="admin-toast-message">${message}</div>
                </div>
                <button class="admin-toast-close" onclick="this.parentElement.remove()">
                    <i class="bi bi-x-lg"></i>
                </button>
            `;
            
            container.appendChild(toast);
            
            // Trigger animation
            setTimeout(() => toast.classList.add('show'), 10);
            
            // Auto remove
            setTimeout(() => {
                toast.classList.remove('show');
                toast.classList.add('hide');
                setTimeout(() => toast.remove(), 400);
            }, duration);
        };
        
        // Show session messages as toasts
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('success'))
                adminToast('success', 'Success!', '{{ session('success') }}');
            @endif
            @if(session('error'))
                adminToast('error', 'Error!', '{{ session('error') }}');
            @endif
            @if(session('warning'))
                adminToast('warning', 'Warning!', '{{ session('warning') }}');
            @endif
        });
    </script>
    
    @stack('scripts')
</body>
</html>
