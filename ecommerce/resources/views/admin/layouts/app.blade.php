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
            --sidebar-width: 280px;
            --sidebar-collapsed-width: 70px;
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
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            z-index: 1000;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
        }
        
        .sidebar.collapsed {
            width: var(--sidebar-collapsed-width);
        }
        
        .sidebar.collapsed .sidebar-header h4 span,
        .sidebar.collapsed .menu-category-title,
        .sidebar.collapsed .menu-badge,
        .sidebar.collapsed .arrow,
        .sidebar.collapsed .submenu {
            display: none;
        }
        
        .sidebar.collapsed .menu-category-header {
            justify-content: center;
            padding: 12px;
        }
        
        .sidebar.collapsed .menu-category-header i.menu-icon {
            margin-right: 0;
            font-size: 1.3rem;
        }
        
        .sidebar.collapsed .sidebar-header h4 {
            text-align: center;
        }
        
        .sidebar.collapsed .sidebar-header h4 i {
            margin-right: 0 !important;
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
            padding: 10px 0;
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
        
        /* Menu Category */
        .menu-category {
            margin-bottom: 5px;
        }
        
        .menu-category-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 20px;
            color: rgba(255,255,255,0.9);
            cursor: pointer;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
            text-decoration: none !important;
        }
        
        .menu-category-header:hover {
            background: rgba(255,255,255,0.05);
            color: white;
            text-decoration: none !important;
        }
        
        .menu-category-header.active {
            background: rgba(255,255,255,0.1);
            border-left-color: #667eea;
            color: white;
            text-decoration: none !important;
        }
        
        .menu-category-header i.menu-icon {
            margin-right: 12px;
            font-size: 1.1rem;
            width: 20px;
            text-align: center;
        }
        
        .menu-category-header i.arrow {
            font-size: 0.8rem;
            transition: transform 0.3s ease;
        }
        
        .menu-category-header[aria-expanded="true"] i.arrow {
            transform: rotate(180deg);
        }
        
        .menu-category-title {
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        /* Submenu */
        .submenu {
            background: rgba(0,0,0,0.15);
            padding: 5px 0;
        }
        
        .submenu .nav-item {
            margin: 1px 10px;
        }
        
        .submenu .nav-link {
            color: rgba(255,255,255,0.7);
            padding: 10px 20px 10px 47px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
            font-size: 0.875rem;
        }
        
        .submenu .nav-link:hover {
            color: white;
            background: rgba(255,255,255,0.08);
        }
        
        .submenu .nav-link.active {
            color: white;
            background: rgba(102, 126, 234, 0.3);
        }
        
        .submenu .nav-link i {
            margin-right: 10px;
            font-size: 0.9rem;
            width: 16px;
        }
        
        /* Badge */
        .menu-badge {
            font-size: 0.7rem;
            padding: 2px 6px;
            border-radius: 10px;
            margin-left: auto;
        }
        
        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            transition: margin-left 0.3s ease;
        }
        
        .main-content.expanded {
            margin-left: var(--sidebar-collapsed-width);
        }
        
        /* Sidebar Toggle Button */
        .sidebar-toggle-btn {
            position: absolute;
            top: 50%;
            right: -12px;
            transform: translateY(-50%);
            width: 24px;
            height: 24px;
            background: #667eea;
            border: none;
            border-radius: 50%;
            color: white;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1001;
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        
        .sidebar-toggle-btn:hover {
            background: #5a6fd6;
            transform: translateY(-50%) scale(1.1);
        }
        
        .sidebar-toggle-btn i {
            font-size: 0.7rem;
            transition: transform 0.3s ease;
        }
        
        .sidebar.collapsed .sidebar-toggle-btn i {
            transform: rotate(180deg);
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
                width: var(--sidebar-width) !important;
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .sidebar.collapsed {
                width: var(--sidebar-width) !important;
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .main-content.expanded {
                margin-left: 0;
            }
            
            .sidebar-toggle-btn {
                display: none;
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
        <button class="sidebar-toggle-btn" id="sidebarCollapseBtn" title="Toggle Sidebar">
            <i class="bi bi-chevron-left"></i>
        </button>
        <div class="sidebar-header">
            <h4><i class="bi bi-shop me-2"></i><span>{{ config('app.name', 'E-Commerce') }}</span></h4>
        </div>
        
        <nav class="sidebar-menu">
            <!-- Dashboard -->
            <div class="menu-category">
                <a class="menu-category-header {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                    <div>
                        <i class="bi bi-speedometer2 menu-icon"></i>
                        <span class="menu-category-title">Dashboard</span>
                    </div>
                </a>
            </div>
            
            <!-- ANALYTICS -->
            <div class="menu-category">
                <a class="menu-category-header {{ request()->routeIs('admin.analytics') ? 'active' : '' }}" href="{{ route('admin.analytics') }}">
                    <div>
                        <i class="bi bi-graph-up-arrow menu-icon"></i>
                        <span class="menu-category-title">Analytics</span>
                    </div>
                    <span class="badge bg-success menu-badge">New</span>
                </a>
            </div>
            
            <!-- PRODUCTS -->
            <div class="menu-category">
                <a class="menu-category-header {{ request()->routeIs('admin.products.*') || request()->routeIs('admin.categories.*') || request()->routeIs('admin.reviews.*') || request()->routeIs('admin.brands.*') || request()->routeIs('admin.attributes.*') || request()->routeIs('admin.colors.*') || request()->routeIs('admin.digital-categories.*') || request()->routeIs('admin.product-qa.*') || request()->routeIs('admin.wishlists.*') || request()->routeIs('admin.inventory.*') ? 'active' : '' }}" data-bs-toggle="collapse" href="#menuProducts" role="button" aria-expanded="{{ request()->routeIs('admin.products.*') || request()->routeIs('admin.categories.*') || request()->routeIs('admin.reviews.*') || request()->routeIs('admin.brands.*') || request()->routeIs('admin.attributes.*') || request()->routeIs('admin.colors.*') || request()->routeIs('admin.digital-categories.*') || request()->routeIs('admin.product-qa.*') || request()->routeIs('admin.wishlist-management.*') || request()->routeIs('admin.inventory.*') ? 'true' : 'false' }}">
                    <div>
                        <i class="bi bi-box-seam menu-icon"></i>
                        <span class="menu-category-title">Products</span>
                    </div>
                    <i class="bi bi-chevron-down arrow"></i>
                </a>
                <div class="collapse {{ request()->routeIs('admin.products.*') || request()->routeIs('admin.categories.*') || request()->routeIs('admin.reviews.*') || request()->routeIs('admin.brands.*') || request()->routeIs('admin.attributes.*') || request()->routeIs('admin.colors.*') || request()->routeIs('admin.digital-categories.*') || request()->routeIs('admin.product-qa.*') || request()->routeIs('admin.wishlists.*') || request()->routeIs('admin.inventory.*') ? 'show' : '' }}" id="menuProducts">
                    <ul class="nav flex-column submenu">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.products.create') ? 'active' : '' }}" href="{{ route('admin.products.create') }}">
                                <i class="bi bi-plus-circle"></i> Add New Product
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.products.index') && !request()->routeIs('admin.products.create') && !request()->routeIs('admin.products.digital.*') ? 'active' : '' }}" href="{{ route('admin.products.index') }}">
                                <i class="bi bi-list-ul"></i> All Products
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" href="{{ route('admin.categories.index') }}">
                                <i class="bi bi-folder"></i> Category
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.products.in-house') ? 'active' : '' }}" href="{{ route('admin.products.in-house') }}">
                                <i class="bi bi-house-door"></i> In-House Products
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.products.digital.*') ? 'active' : '' }}" href="{{ route('admin.products.digital.index') }}">
                                <i class="bi bi-file-earmark-binary"></i> Digital Products
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.digital-categories.*') ? 'active' : '' }}" href="{{ route('admin.digital-categories.index') }}">
                                <i class="bi bi-folder2-open"></i> Digital Categories
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.products.bulk-import*') ? 'active' : '' }}" href="{{ route('admin.products.bulk-import') }}">
                                <i class="bi bi-upload"></i> Bulk Import
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.products.bulk-export') ? 'active' : '' }}" href="{{ route('admin.products.bulk-export') }}">
                                <i class="bi bi-download"></i> Bulk Export
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.products.bulk-discount*') ? 'active' : '' }}" href="{{ route('admin.products.bulk-discount') }}">
                                <i class="bi bi-percent"></i> Bulk Discount
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.brands.*') ? 'active' : '' }}" href="{{ route('admin.brands.index') }}">
                                <i class="bi bi-award"></i> Brand
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.attributes.*') ? 'active' : '' }}" href="{{ route('admin.attributes.index') }}">
                                <i class="bi bi-sliders"></i> Attribute
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.colors.*') ? 'active' : '' }}" href="{{ route('admin.colors.index') }}">
                                <i class="bi bi-palette"></i> Colors
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}" href="{{ route('admin.reviews.index') }}">
                                <i class="bi bi-star"></i> Product Reviews
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.product-qa.*') ? 'active' : '' }}" href="{{ route('admin.product-qa.index') }}">
                                <i class="bi bi-question-circle"></i> Product Q&A
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.wishlists.*') ? 'active' : '' }}" href="{{ route('admin.wishlists.index') }}">
                                <i class="bi bi-heart"></i> Wishlist Management
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.inventory.*') ? 'active' : '' }}" href="{{ route('admin.inventory.index') }}">
                                <i class="bi bi-boxes"></i> Inventory Management
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- SALES -->
            <div class="menu-category">
                <a class="menu-category-header {{ request()->routeIs('admin.orders.*') || request()->routeIs('admin.quotations.*') || request()->routeIs('admin.subscriptions.*') ? 'active' : '' }}" data-bs-toggle="collapse" href="#menuSales" role="button" aria-expanded="{{ request()->routeIs('admin.orders.*') || request()->routeIs('admin.quotations.*') || request()->routeIs('admin.subscriptions.*') ? 'true' : 'false' }}">
                    <div>
                        <i class="bi bi-cart-check menu-icon"></i>
                        <span class="menu-category-title">Sales</span>
                    </div>
                    <i class="bi bi-chevron-down arrow"></i>
                </a>
                <div class="collapse {{ request()->routeIs('admin.orders.*') || request()->routeIs('admin.quotations.*') || request()->routeIs('admin.subscriptions.*') ? 'show' : '' }}" id="menuSales">
                    <ul class="nav flex-column submenu">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.orders.index') ? 'active' : '' }}" href="{{ route('admin.orders.index') }}">
                                <i class="bi bi-list-ul"></i> All Orders
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.orders.in-house*') ? 'active' : '' }}" href="{{ route('admin.orders.in-house') }}">
                                <i class="bi bi-house-door"></i> Inhouse Orders
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.orders.seller*') ? 'active' : '' }}" href="{{ route('admin.orders.seller') }}">
                                <i class="bi bi-people"></i> Seller Orders
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.orders.pickup-point') ? 'active' : '' }}" href="{{ route('admin.orders.pickup-point') }}">
                                <i class="bi bi-geo-alt"></i> Pick-up Point Order
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.quotations.*') ? 'active' : '' }}" href="{{ route('admin.quotations.index') }}">
                                <i class="bi bi-file-earmark-text"></i> Quotations
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.subscriptions.*') ? 'active' : '' }}" href="{{ route('admin.subscriptions.index') }}">
                                <i class="bi bi-arrow-repeat"></i> Subscriptions
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- DELIVERY -->
            <div class="menu-category">
                <a class="menu-category-header {{ request()->routeIs('admin.delivery.*') || request()->routeIs('admin.pickup-points*') ? 'active' : '' }}" data-bs-toggle="collapse" href="#menuDelivery" role="button" aria-expanded="{{ request()->routeIs('admin.delivery.*') || request()->routeIs('admin.pickup-points*') ? 'true' : 'false' }}">
                    <div>
                        <i class="bi bi-truck menu-icon"></i>
                        <span class="menu-category-title">Delivery</span>
                    </div>
                    <i class="bi bi-chevron-down arrow"></i>
                </a>
                <div class="collapse {{ request()->routeIs('admin.delivery.*') || request()->routeIs('admin.pickup-points*') ? 'show' : '' }}" id="menuDelivery">
                    <ul class="nav flex-column submenu">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.delivery.index') ? 'active' : '' }}" href="{{ route('admin.delivery.index') }}">
                                <i class="bi bi-speedometer2"></i> Delivery Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.delivery.partners.index') ? 'active' : '' }}" href="{{ route('admin.delivery.partners.index') }}">
                                <i class="bi bi-building"></i> Delivery Partners
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.delivery.carriers.*') ? 'active' : '' }}" href="{{ route('admin.delivery.carriers.index') }}">
                                <i class="bi bi-truck"></i> Carriers
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.delivery.tracking') ? 'active' : '' }}" href="{{ route('admin.delivery.tracking') }}">
                                <i class="bi bi-geo-alt"></i> Shipment Tracking
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.delivery.zones.index') ? 'active' : '' }}" href="{{ route('admin.delivery.zones.index') }}">
                                <i class="bi bi-map"></i> Delivery Zones
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.delivery.courier-integration') ? 'active' : '' }}" href="{{ route('admin.delivery.courier-integration') }}">
                                <i class="bi bi-plug"></i> Courier Integration
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.delivery.delivery-boys.*') ? 'active' : '' }}" href="{{ route('admin.delivery.delivery-boys.index') }}">
                                <i class="bi bi-person-badge"></i> Delivery Boys
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.pickup-points*') ? 'active' : '' }}" href="{{ route('admin.pickup-points.index') }}">
                                <i class="bi bi-shop"></i> Pick-up Points
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.delivery.schedules*') ? 'active' : '' }}" href="{{ route('admin.delivery.schedules.index') }}">
                                <i class="bi bi-calendar-week"></i> Delivery Schedules
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.delivery.reports*') ? 'active' : '' }}" href="{{ route('admin.delivery.reports') }}">
                                <i class="bi bi-bar-chart"></i> Delivery Reports
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- REFUND -->
            <div class="menu-category">
                <a class="menu-category-header {{ request()->routeIs('admin.refunds.*') ? 'active' : '' }}" data-bs-toggle="collapse" href="#menuRefund" role="button" aria-expanded="{{ request()->routeIs('admin.refunds.*') ? 'true' : 'false' }}">
                    <div>
                        <i class="bi bi-arrow-return-left menu-icon"></i>
                        <span class="menu-category-title">Refund</span>
                    </div>
                    <i class="bi bi-chevron-down arrow"></i>
                </a>
                <div class="collapse {{ request()->routeIs('admin.refunds.*') ? 'show' : '' }}" id="menuRefund">
                    <ul class="nav flex-column submenu">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.refunds.index') ? 'active' : '' }}" href="{{ route('admin.refunds.index') }}">
                                <i class="bi bi-list-ul"></i> All Refunds
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.refunds.requests') ? 'active' : '' }}" href="{{ route('admin.refunds.requests') }}">
                                <i class="bi bi-inbox"></i> Refund Requests
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.refunds.approved') ? 'active' : '' }}" href="{{ route('admin.refunds.approved') }}">
                                <i class="bi bi-check-circle"></i> Approved Refunds
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.refunds.rejected') ? 'active' : '' }}" href="{{ route('admin.refunds.rejected') }}">
                                <i class="bi bi-x-circle"></i> Rejected Refunds
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.refunds.configuration') ? 'active' : '' }}" href="{{ route('admin.refunds.configuration') }}">
                                <i class="bi bi-gear"></i> Refund Configuration
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- CUSTOMERS -->
            <div class="menu-category">
                <a class="menu-category-header {{ request()->routeIs('admin.customers.*') || request()->routeIs('admin.customers.groups.*') || request()->routeIs('admin.customers.segmentation.*') || request()->routeIs('admin.customers.loyalty.*') || request()->routeIs('admin.customers.membership.*') || request()->routeIs('admin.customers.wallet.*') ? 'active' : '' }}" data-bs-toggle="collapse" href="#menuCustomers" role="button" aria-expanded="{{ request()->routeIs('admin.customers.*') || request()->routeIs('admin.customers.groups.*') || request()->routeIs('admin.customers.segmentation.*') || request()->routeIs('admin.customers.loyalty.*') || request()->routeIs('admin.customers.membership.*') || request()->routeIs('admin.customers.wallet.*') ? 'true' : 'false' }}">
                    <div>
                        <i class="bi bi-people menu-icon"></i>
                        <span class="menu-category-title">Customers</span>
                    </div>
                    <i class="bi bi-chevron-down arrow"></i>
                </a>
                <div class="collapse {{ request()->routeIs('admin.customers.*') || request()->routeIs('admin.customers.groups.*') || request()->routeIs('admin.customers.segmentation.*') || request()->routeIs('admin.customers.loyalty.*') || request()->routeIs('admin.customers.membership.*') || request()->routeIs('admin.customers.wallet.*') ? 'show' : '' }}" id="menuCustomers">
                    <ul class="nav flex-column submenu">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.customers.index') ? 'active' : '' }}" href="{{ route('admin.customers.index') }}">
                                <i class="bi bi-list-ul"></i> All Customers
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.customers.groups*') ? 'active' : '' }}" href="{{ route('admin.customers.groups.index') }}">
                                <i class="bi bi-people-fill"></i> Customer Groups
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.customers.segmentation*') ? 'active' : '' }}" href="{{ route('admin.customers.segmentation.index') }}">
                                <i class="bi bi-diagram-3"></i> Customer Segmentation
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.customers.loyalty*') ? 'active' : '' }}" href="{{ route('admin.customers.loyalty.index') }}">
                                <i class="bi bi-star"></i> Loyalty Points
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.customers.membership*') ? 'active' : '' }}" href="{{ route('admin.customers.membership.index') }}">
                                <i class="bi bi-card-checklist"></i> Membership Plans
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.customers.wallet*') ? 'active' : '' }}" href="{{ route('admin.customers.wallet.index') }}">
                                <i class="bi bi-wallet2"></i> Customer Wallet
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- SELLERS (B2B) -->
            <div class="menu-category">
                <a class="menu-category-header {{ request()->routeIs('admin.sellers.*') ? 'active' : '' }}" data-bs-toggle="collapse" href="#menuSellers" role="button" aria-expanded="{{ request()->routeIs('admin.sellers.*') ? 'true' : 'false' }}">
                    <div>
                        <i class="bi bi-shop-window menu-icon"></i>
                        <span class="menu-category-title">Sellers (B2B)</span>
                    </div>
                    <i class="bi bi-chevron-down arrow"></i>
                </a>
                <div class="collapse {{ request()->routeIs('admin.sellers.*') ? 'show' : '' }}" id="menuSellers">
                    <ul class="nav flex-column submenu">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.sellers.index') ? 'active' : '' }}" href="{{ route('admin.sellers.index') }}">
                                <i class="bi bi-list-ul"></i> All Sellers
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.sellers.payouts') ? 'active' : '' }}" href="{{ route('admin.sellers.payouts') }}">
                                <i class="bi bi-cash-stack"></i> Payouts
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.sellers.payout-requests') ? 'active' : '' }}" href="{{ route('admin.sellers.payout-requests') }}">
                                <i class="bi bi-wallet2"></i> Payout Requests
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.sellers.commission') ? 'active' : '' }}" href="{{ route('admin.sellers.commission') }}">
                                <i class="bi bi-percent"></i> Seller Commission
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.sellers.verification') ? 'active' : '' }}" href="{{ route('admin.sellers.verification') }}">
                                <i class="bi bi-patch-check"></i> Seller Verification
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- AFFILIATE -->
            <div class="menu-category">
                <a class="menu-category-header {{ request()->routeIs('admin.affiliate.*') ? 'active' : '' }}" data-bs-toggle="collapse" href="#menuAffiliate" role="button" aria-expanded="{{ request()->routeIs('admin.affiliate.*') ? 'true' : 'false' }}">
                    <div>
                        <i class="bi bi-link-45deg menu-icon"></i>
                        <span class="menu-category-title">Affiliate</span>
                    </div>
                    <i class="bi bi-chevron-down arrow"></i>
                </a>
                <div class="collapse {{ request()->routeIs('admin.affiliate.*') ? 'show' : '' }}" id="menuAffiliate">
                    <ul class="nav flex-column submenu">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.affiliate.users.index') ? 'active' : '' }}" href="{{ route('admin.affiliate.users.index') }}">
                                <i class="bi bi-people"></i> Affiliate Users
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.affiliate.configuration') ? 'active' : '' }}" href="{{ route('admin.affiliate.configuration') }}">
                                <i class="bi bi-gear"></i> Affiliate Configuration
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.affiliate.payouts') ? 'active' : '' }}" href="{{ route('admin.affiliate.payouts') }}">
                                <i class="bi bi-cash-stack"></i> Affiliate Payouts
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.affiliate.requests') ? 'active' : '' }}" href="{{ route('admin.affiliate.requests') }}">
                                <i class="bi bi-inbox"></i> Affiliate Requests
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.affiliate.categories.index') ? 'active' : '' }}" href="{{ route('admin.affiliate.categories.index') }}">
                                <i class="bi bi-folder"></i> Affiliate Categories
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.affiliate.products.index') ? 'active' : '' }}" href="{{ route('admin.affiliate.products.index') }}">
                                <i class="bi bi-box-seam"></i> Affiliate Products
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.affiliate.links.index') ? 'active' : '' }}" href="{{ route('admin.affiliate.links.index') }}">
                                <i class="bi bi-link"></i> Affiliate Links
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.affiliate.banners.index') ? 'active' : '' }}" href="{{ route('admin.affiliate.banners.index') }}">
                                <i class="bi bi-card-image"></i> Affiliate Banners
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.affiliate.reports') ? 'active' : '' }}" href="{{ route('admin.affiliate.reports') }}">
                                <i class="bi bi-graph-up"></i> Affiliate Reports
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.affiliate.withdrawals.index') ? 'active' : '' }}" href="{{ route('admin.affiliate.withdrawals.index') }}">
                                <i class="bi bi-wallet2"></i> Withdrawal Requests
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- MEDIA -->
            <div class="menu-category">
                <a class="menu-category-header {{ request()->routeIs('admin.media.*') ? 'active' : '' }}" href="{{ route('admin.media.index') }}">
                    <div>
                        <i class="bi bi-images menu-icon"></i>
                        <span class="menu-category-title">Media</span>
                    </div>
                    <span class="badge bg-info menu-badge">Files</span>
                </a>
            </div>
            
            <!-- REPORTS -->
            <div class="menu-category">
                <a class="menu-category-header {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}" data-bs-toggle="collapse" href="#menuReports" role="button" aria-expanded="{{ request()->routeIs('admin.reports.*') ? 'true' : 'false' }}">
                    <div>
                        <i class="bi bi-graph-up menu-icon"></i>
                        <span class="menu-category-title">Reports</span>
                    </div>
                    <i class="bi bi-chevron-down arrow"></i>
                </a>
                <div class="collapse {{ request()->routeIs('admin.reports.*') ? 'show' : '' }}" id="menuReports">
                    <ul class="nav flex-column submenu">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.reports.in-house-product-sale') ? 'active' : '' }}" href="{{ route('admin.reports.in-house-product-sale') }}">
                                <i class="bi bi-house-door"></i> In-House Product Sale
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.reports.seller-sales') ? 'active' : '' }}" href="{{ route('admin.reports.seller-sales') }}">
                                <i class="bi bi-shop"></i> Seller Products Sale
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.reports.inventory') ? 'active' : '' }}" href="{{ route('admin.reports.inventory') }}">
                                <i class="bi bi-boxes"></i> Products Stock
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.reports.wishlist') ? 'active' : '' }}" href="{{ route('admin.reports.wishlist') }}">
                                <i class="bi bi-heart"></i> Products Wishlist
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.reports.user-searches') ? 'active' : '' }}" href="{{ route('admin.reports.user-searches') }}">
                                <i class="bi bi-search"></i> User Searches
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.reports.commission-history') ? 'active' : '' }}" href="{{ route('admin.reports.commission-history') }}">
                                <i class="bi bi-currency-dollar"></i> Commission History
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.reports.wallet-history') ? 'active' : '' }}" href="{{ route('admin.reports.wallet-history') }}">
                                <i class="bi bi-wallet"></i> Wallet Recharge History
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- MARKETING -->
            <div class="menu-category">
                <a class="menu-category-header {{ request()->routeIs('admin.marketing.*') || request()->routeIs('admin.coupons.*') ? 'active' : '' }}" data-bs-toggle="collapse" href="#menuMarketing" role="button" aria-expanded="{{ request()->routeIs('admin.marketing.*') || request()->routeIs('admin.coupons.*') ? 'true' : 'false' }}">
                    <div>
                        <i class="bi bi-megaphone menu-icon"></i>
                        <span class="menu-category-title">Marketing</span>
                    </div>
                    <i class="bi bi-chevron-down arrow"></i>
                </a>
                <div class="collapse {{ request()->routeIs('admin.marketing.*') || request()->routeIs('admin.coupons.*') ? 'show' : '' }}" id="menuMarketing">
                    <ul class="nav flex-column submenu">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.marketing.flash-deals.*') ? 'active' : '' }}" href="{{ route('admin.marketing.flash-deals.index') }}">
                                <i class="bi bi-lightning"></i> Flash Deals
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.marketing.newsletters*') ? 'active' : '' }}" href="{{ route('admin.marketing.newsletters.index') }}">
                                <i class="bi bi-envelope"></i> Newsletters
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.marketing.bulk-sms*') ? 'active' : '' }}" href="{{ route('admin.marketing.bulk-sms.index') }}">
                                <i class="bi bi-phone"></i> Bulk SMS
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.marketing.subscribers.*') ? 'active' : '' }}" href="{{ route('admin.marketing.subscribers.index') }}">
                                <i class="bi bi-person-plus"></i> Subscribers
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.coupons.*') ? 'active' : '' }}" href="{{ route('admin.coupons.index') }}">
                                <i class="bi bi-tag"></i> Coupon
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.marketing.abandoned-cart*') ? 'active' : '' }}" href="{{ route('admin.marketing.abandoned-cart.index') }}">
                                <i class="bi bi-cart-x"></i> Abandoned Cart Recovery
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.marketing.gift-cards*') ? 'active' : '' }}" href="{{ route('admin.marketing.gift-cards.index') }}">
                                <i class="bi bi-gift"></i> Gift Cards
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.marketing.push-notifications*') ? 'active' : '' }}" href="{{ route('admin.marketing.push-notifications.index') }}">
                                <i class="bi bi-bell"></i> Push Notifications
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.marketing.price-rules*') ? 'active' : '' }}" href="{{ route('admin.marketing.price-rules.index') }}">
                                <i class="bi bi-percent"></i> Price Rules
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- SUPPORT -->
            <div class="menu-category">
                <a class="menu-category-header {{ request()->routeIs('admin.support.*') || request()->routeIs('admin.chat.*') || request()->routeIs('admin.settings.whatsapp') ? 'active' : '' }}" data-bs-toggle="collapse" href="#menuSupport" role="button" aria-expanded="{{ request()->routeIs('admin.support.*') || request()->routeIs('admin.chat.*') || request()->routeIs('admin.settings.whatsapp') ? 'true' : 'false' }}">
                    <div>
                        <i class="bi bi-headset menu-icon"></i>
                        <span class="menu-category-title">Support</span>
                    </div>
                    <i class="bi bi-chevron-down arrow"></i>
                </a>
                <div class="collapse {{ request()->routeIs('admin.support.*') || request()->routeIs('admin.chat.*') || request()->routeIs('admin.settings.whatsapp') ? 'show' : '' }}" id="menuSupport">
                    <ul class="nav flex-column submenu">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.support.tickets*') ? 'active' : '' }}" href="{{ route('admin.support.tickets.index') }}">
                                <i class="bi bi-ticket-detailed"></i> Ticket
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.chat.index') ? 'active' : '' }}" href="{{ route('admin.chat.index') }}">
                                <i class="bi bi-chat-dots"></i> Live Chat
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.chat.ai-settings*') ? 'active' : '' }}" href="{{ route('admin.chat.ai-settings.index') }}">
                                <i class="bi bi-robot"></i> AI Chatbot Settings
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.chat.widget-settings*') ? 'active' : '' }}" href="{{ route('admin.chat.widget-settings.index') }}">
                                <i class="bi bi-chat-dots-fill"></i> Chat Widget Settings
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.chat.predefined.*') ? 'active' : '' }}" href="{{ route('admin.chat.predefined.index') }}">
                                <i class="bi bi-chat-text"></i> Quick Replies
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.settings.whatsapp') ? 'active' : '' }}" href="{{ route('admin.settings.whatsapp') }}">
                                <i class="bi bi-whatsapp"></i> WhatsApp Chat
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.support.product-queries*') ? 'active' : '' }}" href="{{ route('admin.support.product-queries.index') }}">
                                <i class="bi bi-question-circle"></i> Product Queries
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- OTP SYSTEM -->
            <div class="menu-category">
                <a class="menu-category-header {{ request()->routeIs('admin.otp.*') ? 'active' : '' }}" data-bs-toggle="collapse" href="#menuOtp" role="button" aria-expanded="{{ request()->routeIs('admin.otp.*') ? 'true' : 'false' }}">
                    <div>
                        <i class="bi bi-shield-lock menu-icon"></i>
                        <span class="menu-category-title">OTP System</span>
                    </div>
                    <i class="bi bi-chevron-down arrow"></i>
                </a>
                <div class="collapse {{ request()->routeIs('admin.otp.*') ? 'show' : '' }}" id="menuOtp">
                    <ul class="nav flex-column submenu">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.otp.configuration') ? 'active' : '' }}" href="{{ route('admin.otp.configuration') }}">
                                <i class="bi bi-gear"></i> OTP Configurations
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.otp.sms-templates') ? 'active' : '' }}" href="{{ route('admin.otp.sms-templates') }}">
                                <i class="bi bi-file-text"></i> SMS Templates
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.otp.credentials') ? 'active' : '' }}" href="{{ route('admin.otp.credentials') }}">
                                <i class="bi bi-key"></i> Set OTP Credentials
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- CONTENT -->
            <div class="menu-category">
                <a class="menu-category-header {{ request()->routeIs('admin.pages.*') || request()->routeIs('admin.blogs.*') || request()->routeIs('admin.blog-categories.*') || request()->routeIs('admin.blog-tags.*') || request()->routeIs('admin.form-builder.*') || request()->routeIs('admin.faqs.*') || request()->routeIs('admin.content.widgets.*') ? 'active' : '' }}" data-bs-toggle="collapse" href="#menuContent" role="button" aria-expanded="{{ request()->routeIs('admin.pages.*') || request()->routeIs('admin.blogs.*') || request()->routeIs('admin.blog-categories.*') || request()->routeIs('admin.blog-tags.*') || request()->routeIs('admin.form-builder.*') || request()->routeIs('admin.faqs.*') || request()->routeIs('admin.content.widgets.*') ? 'true' : 'false' }}">
                    <div>
                        <i class="bi bi-file-earmark-text menu-icon"></i>
                        <span class="menu-category-title">Content</span>
                    </div>
                    <i class="bi bi-chevron-down arrow"></i>
                </a>
                <div class="collapse {{ request()->routeIs('admin.pages.*') || request()->routeIs('admin.blogs.*') || request()->routeIs('admin.blog-categories.*') || request()->routeIs('admin.blog-tags.*') || request()->routeIs('admin.form-builder.*') || request()->routeIs('admin.faqs.*') || request()->routeIs('admin.content.widgets.*') ? 'show' : '' }}" id="menuContent">
                    <ul class="nav flex-column submenu">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.pages.*') ? 'active' : '' }}" href="{{ route('admin.pages.index') }}">
                                <i class="bi bi-file-text"></i> Pages
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.blogs.*') ? 'active' : '' }}" href="{{ route('admin.blogs.index') }}">
                                <i class="bi bi-newspaper"></i> Blog Posts
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.blog-categories.*') ? 'active' : '' }}" href="{{ route('admin.blog-categories.index') }}">
                                <i class="bi bi-folder2-open"></i> Blog Categories
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.blog-tags.*') ? 'active' : '' }}" href="{{ route('admin.blog-tags.index') }}">
                                <i class="bi bi-tags"></i> Blog Tags
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.form-builder.*') ? 'active' : '' }}" href="{{ route('admin.form-builder.index') }}">
                                <i class="bi bi-ui-checks"></i> Form Builder
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.faqs.*') ? 'active' : '' }}" href="{{ route('admin.faqs.index') }}">
                                <i class="bi bi-question-diamond"></i> FAQs
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.content.widgets.*') ? 'active' : '' }}" href="{{ route('admin.content.widgets.index') }}">
                                <i class="bi bi-grid-3x3-gap"></i> Widget Manager
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- APPEARANCE -->
            <div class="menu-category">
                <a class="menu-category-header {{ request()->routeIs('admin.themes.*') || request()->routeIs('admin.hero.*') || request()->routeIs('admin.homepage.*') || request()->routeIs('admin.sliders.*') || request()->routeIs('admin.banners.*') || request()->routeIs('admin.menus.*') || request()->routeIs('admin.widgets.*') ? 'active' : '' }}" data-bs-toggle="collapse" href="#menuAppearance" role="button" aria-expanded="{{ request()->routeIs('admin.themes.*') || request()->routeIs('admin.hero.*') || request()->routeIs('admin.homepage.*') || request()->routeIs('admin.sliders.*') || request()->routeIs('admin.banners.*') || request()->routeIs('admin.menus.*') || request()->routeIs('admin.widgets.*') ? 'true' : 'false' }}">
                    <div>
                        <i class="bi bi-palette2 menu-icon"></i>
                        <span class="menu-category-title">Appearance</span>
                    </div>
                    <i class="bi bi-chevron-down arrow"></i>
                </a>
                <div class="collapse {{ request()->routeIs('admin.themes.*') || request()->routeIs('admin.hero.*') || request()->routeIs('admin.homepage.*') || request()->routeIs('admin.sliders.*') || request()->routeIs('admin.banners.*') || request()->routeIs('admin.menus.*') || request()->routeIs('admin.widgets.*') ? 'show' : '' }}" id="menuAppearance">
                    <ul class="nav flex-column submenu">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.themes.*') ? 'active' : '' }}" href="{{ route('admin.themes.index') }}">
                                <i class="bi bi-palette"></i> Themes
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.menus.*') ? 'active' : '' }}" href="{{ route('admin.menus.index') }}">
                                <i class="bi bi-list-nested"></i> Menu Builder
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.sliders.*') ? 'active' : '' }}" href="{{ route('admin.sliders.index') }}">
                                <i class="bi bi-images"></i> Sliders
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.banners.*') ? 'active' : '' }}" href="{{ route('admin.banners.index') }}">
                                <i class="bi bi-card-image"></i> Banners
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
                    </ul>
                </div>
            </div>
            
            <!-- SETTINGS -->
            <div class="menu-category">
                <a class="menu-category-header {{ request()->routeIs('admin.settings.*') || request()->routeIs('admin.payment.*') || request()->routeIs('admin.payment-gateways.*') || request()->routeIs('admin.seo.*') ? 'active' : '' }}" data-bs-toggle="collapse" href="#menuSettings" role="button" aria-expanded="{{ request()->routeIs('admin.settings.*') || request()->routeIs('admin.payment.*') || request()->routeIs('admin.payment-gateways.*') || request()->routeIs('admin.seo.*') ? 'true' : 'false' }}">
                    <div>
                        <i class="bi bi-gear-fill menu-icon"></i>
                        <span class="menu-category-title">Settings</span>
                    </div>
                    <i class="bi bi-chevron-down arrow"></i>
                </a>
                <div class="collapse {{ request()->routeIs('admin.settings.*') || request()->routeIs('admin.payment.*') || request()->routeIs('admin.payment-gateways.*') || request()->routeIs('admin.seo.*') ? 'show' : '' }}" id="menuSettings">
                    <ul class="nav flex-column submenu">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.settings.general') || request()->routeIs('admin.settings.index') ? 'active' : '' }}" href="{{ route('admin.settings.general') }}">
                                <i class="bi bi-sliders"></i> General Settings
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.settings.features') ? 'active' : '' }}" href="{{ route('admin.settings.features') }}">
                                <i class="bi bi-toggle-on"></i> Features Activation
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.settings.languages*') ? 'active' : '' }}" href="{{ route('admin.settings.languages') }}">
                                <i class="bi bi-translate"></i> Languages
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.settings.currency') ? 'active' : '' }}" href="{{ route('admin.settings.currency') }}">
                                <i class="bi bi-currency-exchange"></i> Currency
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.settings.vat-tax') ? 'active' : '' }}" href="{{ route('admin.settings.vat-tax') }}">
                                <i class="bi bi-receipt"></i> VAT & Tax
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.settings.email') ? 'active' : '' }}" href="{{ route('admin.settings.email') }}">
                                <i class="bi bi-envelope"></i> SMTP Settings
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.payment.*') || request()->routeIs('admin.payment-gateways.*') ? 'active' : '' }}" href="{{ route('admin.payment-gateways.index') }}">
                                <i class="bi bi-credit-card"></i> Payment Methods
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.settings.order-configuration') ? 'active' : '' }}" href="{{ route('admin.settings.order-configuration') }}">
                                <i class="bi bi-bag-check"></i> Order Configuration
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.settings.file-system') ? 'active' : '' }}" href="{{ route('admin.settings.file-system') }}">
                                <i class="bi bi-hdd"></i> File System & Cache
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.settings.social-login') ? 'active' : '' }}" href="{{ route('admin.settings.social-login') }}">
                                <i class="bi bi-google"></i> Social Media Logins
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.settings.shipping') ? 'active' : '' }}" href="{{ route('admin.settings.shipping') }}">
                                <i class="bi bi-truck"></i> Shipping
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.seo.*') ? 'active' : '' }}" href="{{ route('admin.seo.index') }}">
                                <i class="bi bi-search"></i> SEO Settings
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.settings.footer') ? 'active' : '' }}" href="{{ route('admin.settings.footer') }}">
                                <i class="bi bi-layout-text-window-reverse"></i> Footer Settings
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.settings.email-templates*') ? 'active' : '' }}" href="{{ route('admin.settings.email-templates.index') }}">
                                <i class="bi bi-envelope-paper"></i> Email Templates
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.settings.notifications*') ? 'active' : '' }}" href="{{ route('admin.settings.notifications.index') }}">
                                <i class="bi bi-bell"></i> Notification Settings
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.settings.security*') ? 'active' : '' }}" href="{{ route('admin.settings.security') }}">
                                <i class="bi bi-shield-check"></i> Security Settings
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.settings.gdpr*') ? 'active' : '' }}" href="{{ route('admin.settings.gdpr') }}">
                                <i class="bi bi-shield-lock"></i> GDPR & Privacy
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.settings.tax-classes*') ? 'active' : '' }}" href="{{ route('admin.settings.tax-classes') }}">
                                <i class="bi bi-calculator"></i> Tax Classes
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.api-keys*') ? 'active' : '' }}" href="{{ route('admin.api-keys.index') }}">
                                <i class="bi bi-key"></i> API Keys & Integrations
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.backup*') ? 'active' : '' }}" href="{{ route('admin.backup') }}">
                                <i class="bi bi-cloud-arrow-up"></i> Backup & Restore
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- WAREHOUSE -->
            <div class="menu-category">
                <a class="menu-category-header {{ request()->routeIs('admin.warehouses.*') ? 'active' : '' }}" data-bs-toggle="collapse" href="#menuWarehouse" role="button" aria-expanded="{{ request()->routeIs('admin.warehouses.*') ? 'true' : 'false' }}">
                    <div>
                        <i class="bi bi-building menu-icon"></i>
                        <span class="menu-category-title">Warehouse</span>
                    </div>
                    <i class="bi bi-chevron-down arrow"></i>
                </a>
                <div class="collapse {{ request()->routeIs('admin.warehouses.*') ? 'show' : '' }}" id="menuWarehouse">
                    <ul class="nav flex-column submenu">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.warehouses.index') ? 'active' : '' }}" href="{{ route('admin.warehouses.index') }}">
                                <i class="bi bi-list-ul"></i> All Warehouses
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.warehouses.create') ? 'active' : '' }}" href="{{ route('admin.warehouses.create') }}">
                                <i class="bi bi-plus-circle"></i> Add Warehouse
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- STAFFS -->
            <div class="menu-category">
                <a class="menu-category-header {{ request()->routeIs('admin.staffs.*') ? 'active' : '' }}" data-bs-toggle="collapse" href="#menuStaffs" role="button" aria-expanded="{{ request()->routeIs('admin.staffs.*') ? 'true' : 'false' }}">
                    <div>
                        <i class="bi bi-person-badge menu-icon"></i>
                        <span class="menu-category-title">Staffs</span>
                    </div>
                    <i class="bi bi-chevron-down arrow"></i>
                </a>
                <div class="collapse {{ request()->routeIs('admin.staffs.*') ? 'show' : '' }}" id="menuStaffs">
                    <ul class="nav flex-column submenu">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.staffs.index') ? 'active' : '' }}" href="{{ route('admin.staffs.index') }}">
                                <i class="bi bi-people"></i> All Staffs
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.staffs.warehouse') ? 'active' : '' }}" href="{{ route('admin.staffs.warehouse') }}">
                                <i class="bi bi-building"></i> Warehouse Staffs
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.staffs.permissions') ? 'active' : '' }}" href="{{ route('admin.staffs.permissions') }}">
                                <i class="bi bi-shield-lock"></i> Staff Permission
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- SYSTEM -->
            <div class="menu-category">
                <a class="menu-category-header" data-bs-toggle="collapse" href="#menuSystem" role="button" aria-expanded="{{ request()->routeIs('admin.system.*') ? 'true' : 'false' }}">
                    <div>
                        <i class="bi bi-cpu menu-icon"></i>
                        <span class="menu-category-title">System</span>
                    </div>
                    <i class="bi bi-chevron-down arrow"></i>
                </a>
                <div class="collapse {{ request()->routeIs('admin.system.*') ? 'show' : '' }}" id="menuSystem">
                    <ul class="nav flex-column submenu">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.system.update') ? 'active' : '' }}" href="{{ route('admin.system.update') }}">
                                <i class="bi bi-arrow-up-circle"></i> Update
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.system.server-status') ? 'active' : '' }}" href="{{ route('admin.system.server-status') }}">
                                <i class="bi bi-activity"></i> Server Status
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.system.activity-logs*') ? 'active' : '' }}" href="{{ route('admin.system.activity-logs.index') }}">
                                <i class="bi bi-journal-text"></i> Activity Logs
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.system.data-export*') ? 'active' : '' }}" href="{{ route('admin.system.data-export.index') }}">
                                <i class="bi bi-database-down"></i> Data Export/Import
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- POS -->
            <div class="menu-category">
                <a class="menu-category-header" data-bs-toggle="collapse" href="#menuPos" role="button" aria-expanded="{{ request()->routeIs('admin.pos.*') ? 'true' : 'false' }}">
                    <div>
                        <i class="bi bi-calculator menu-icon"></i>
                        <span class="menu-category-title">POS</span>
                    </div>
                    <i class="bi bi-chevron-down arrow"></i>
                </a>
                <div class="collapse {{ request()->routeIs('admin.pos.*') ? 'show' : '' }}" id="menuPos">
                    <ul class="nav flex-column submenu">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.pos.terminal') ? 'active' : '' }}" href="{{ route('admin.pos.terminal') }}">
                                <i class="bi bi-terminal"></i> POS Terminal
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.pos.cash-register') ? 'active' : '' }}" href="{{ route('admin.pos.cash-register') }}">
                                <i class="bi bi-cash"></i> Cash Register
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.pos.reports') ? 'active' : '' }}" href="{{ route('admin.pos.reports') }}">
                                <i class="bi bi-graph-up"></i> POS Reports
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- ADDON MANAGER -->
            <div class="menu-category">
                <a class="menu-category-header {{ request()->routeIs('admin.addons.*') ? 'active' : '' }}" href="{{ route('admin.addons.index') }}">
                    <div>
                        <i class="bi bi-puzzle menu-icon"></i>
                        <span class="menu-category-title">Addon Manager</span>
                    </div>
                </a>
            </div>
            
            <!-- MULTI-STORE -->
            <div class="menu-category">
                <a class="menu-category-header" data-bs-toggle="collapse" href="#menuMultiStore" role="button" aria-expanded="{{ request()->routeIs('admin.multi-store.*') ? 'true' : 'false' }}">
                    <div>
                        <i class="bi bi-shop menu-icon"></i>
                        <span class="menu-category-title">Multi-Store</span>
                    </div>
                    <i class="bi bi-chevron-down arrow"></i>
                </a>
                <div class="collapse {{ request()->routeIs('admin.multi-store.*') ? 'show' : '' }}" id="menuMultiStore">
                    <ul class="nav flex-column submenu">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.multi-store.locations') ? 'active' : '' }}" href="{{ route('admin.multi-store.locations') }}">
                                <i class="bi bi-geo-alt"></i> Store Locations
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.multi-store.settings') ? 'active' : '' }}" href="{{ route('admin.multi-store.settings') }}">
                                <i class="bi bi-gear"></i> Store Settings
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.multi-store.inventory') ? 'active' : '' }}" href="{{ route('admin.multi-store.inventory') }}">
                                <i class="bi bi-boxes"></i> Inventory by Store
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </aside>
    
    <!-- Main Content -->
    <div class="main-content" id="mainContent">
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
        // Sidebar toggle for mobile
        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('show');
        });
        
        // Sidebar collapse/expand for desktop
        const sidebarCollapseBtn = document.getElementById('sidebarCollapseBtn');
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        
        // Check for saved sidebar state
        const savedSidebarState = localStorage.getItem('sidebarCollapsed');
        if (savedSidebarState === 'true') {
            sidebar.classList.add('collapsed');
            mainContent.classList.add('expanded');
        }
        
        sidebarCollapseBtn?.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
            
            // Save state to localStorage
            localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
        });
        
        // Save sidebar state (expanded/collapsed menus) before page unload
        window.addEventListener('beforeunload', function() {
            const sidebarMenu = document.querySelector('.sidebar-menu');
            if (sidebarMenu) {
                sessionStorage.setItem('sidebarScrollPosition', sidebarMenu.scrollTop);
                
                // Save expanded menus
                const expandedMenus = [];
                document.querySelectorAll('.collapse.show').forEach(function(collapse) {
                    expandedMenus.push(collapse.id);
                });
                sessionStorage.setItem('expandedMenus', JSON.stringify(expandedMenus));
            }
        });
        
        // Restore sidebar scroll position on page load
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarMenu = document.querySelector('.sidebar-menu');
            const savedPosition = sessionStorage.getItem('sidebarScrollPosition');
            
            // Restore saved scroll position if available
            if (sidebarMenu && savedPosition) {
                sidebarMenu.scrollTop = parseInt(savedPosition);
            } else {
                // Only scroll to active link if no saved position exists
                // This preserves the user's clicked position
                const activeLink = document.querySelector('.submenu .nav-link.active, .menu-category-header.active');
                if (activeLink) {
                    // Small delay to ensure DOM is ready
                    setTimeout(function() {
                        activeLink.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }, 100);
                }
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
            
            // Auto-add has-floating-save class to content-area when floating-save-container exists
            if (document.querySelector('.floating-save-container')) {
                document.querySelector('.content-area').classList.add('has-floating-save');
            }
        });
    </script>
    
    @stack('scripts')
</body>
</html>
