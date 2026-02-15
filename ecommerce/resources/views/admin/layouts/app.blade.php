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
                    <a class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}" href="{{ route('admin.settings.index') }}">
                        <i class="bi bi-gear"></i> Settings
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
    
    <script>
        // Sidebar toggle
        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('show');
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
    </script>
    
    @stack('scripts')
</body>
</html>
