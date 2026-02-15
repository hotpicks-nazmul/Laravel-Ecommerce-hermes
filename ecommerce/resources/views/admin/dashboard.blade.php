@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="row mb-4">
    <!-- Total Sales -->
    <div class="col-md-3 mb-4">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="stat-icon bg-primary bg-opacity-10 text-primary me-3">
                        <i class="bi bi-currency-dollar"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">Total Sales</h6>
                        <h3 class="mb-0">৳{{ number_format($totalSales, 2) }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Today's Sales -->
    <div class="col-md-3 mb-4">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="stat-icon bg-success bg-opacity-10 text-success me-3">
                        <i class="bi bi-graph-up-arrow"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">Today's Sales</h6>
                        <h3 class="mb-0">৳{{ number_format($todaySales, 2) }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Total Orders -->
    <div class="col-md-3 mb-4">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="stat-icon bg-info bg-opacity-10 text-info me-3">
                        <i class="bi bi-cart-check"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">Total Orders</h6>
                        <h3 class="mb-0">{{ $totalOrders }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Total Customers -->
    <div class="col-md-3 mb-4">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="stat-icon bg-warning bg-opacity-10 text-warning me-3">
                        <i class="bi bi-people"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-1">Total Customers</h6>
                        <h3 class="mb-0">{{ $totalCustomers }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Orders -->
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">Recent Orders</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentOrders as $order)
                            <tr>
                                <td><strong>{{ $order->order_number }}</strong></td>
                                <td>{{ $order->billing_full_name }}</td>
                                <td>৳{{ number_format($order->total, 2) }}</td>
                                <td>
                                    <span class="badge bg-{{ $order->status_badge_class }}">{{ ucfirst($order->status) }}</span>
                                </td>
                                <td>{{ $order->created_at->format('d M, Y') }}</td>
                                <td>
                                    <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">No orders found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Order Status -->
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">Order Status</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-3">
                    <span>Pending</span>
                    <span class="badge bg-warning">{{ $pendingOrders }}</span>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span>Processing</span>
                    <span class="badge bg-info">{{ $processingOrders }}</span>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span>Completed</span>
                    <span class="badge bg-success">{{ $completedOrders }}</span>
                </div>
                
                <hr>
                
                <h6 class="mb-3">Product Status</h6>
                <div class="d-flex justify-content-between mb-2">
                    <span>Active Products</span>
                    <span>{{ $activeProducts }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Out of Stock</span>
                    <span class="text-danger">{{ $outOfStockProducts }}</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span>Low Stock</span>
                    <span class="text-warning">{{ $lowStockProducts }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Top Selling Products -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">Top Selling Products</h5>
            </div>
            <div class="card-body">
                @forelse($topProducts as $product)
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span>{{ $product->product_name }}</span>
                    <span class="badge bg-primary">{{ $product->total_sold }} sold</span>
                </div>
                @empty
                <p class="text-muted text-center">No data available</p>
                @endforelse
            </div>
        </div>
    </div>
    
    <!-- Category Distribution -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">Category Distribution</h5>
            </div>
            <div class="card-body">
                @forelse($categoryDistribution as $category)
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span>{{ $category->name }}</span>
                    <span class="badge bg-secondary">{{ $category->products_count }} products</span>
                </div>
                @empty
                <p class="text-muted text-center">No data available</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
