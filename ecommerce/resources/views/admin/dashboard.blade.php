@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')
<!-- Stats Cards with Growth Indicators -->
<div class="row mb-4">
    <!-- Total Sales -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card h-100 border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted mb-1 small text-uppercase fw-semibold">Total Sales</p>
                        <h3 class="mb-1 fw-bold">৳{{ number_format($totalSales, 2) }}</h3>
                        <div class="d-flex align-items-center mt-2">
                            @if($salesGrowth >= 0)
                                <span class="badge bg-success-subtle text-success me-1">
                                    <i class="bi bi-arrow-up-short"></i>{{ abs($salesGrowth) }}%
                                </span>
                            @else
                                <span class="badge bg-danger-subtle text-danger me-1">
                                    <i class="bi bi-arrow-down-short"></i>{{ abs($salesGrowth) }}%
                                </span>
                            @endif
                            <span class="text-muted small">vs last month</span>
                        </div>
                    </div>
                    <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-currency-dollar"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Today's Sales -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card h-100 border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted mb-1 small text-uppercase fw-semibold">Today's Sales</p>
                        <h3 class="mb-1 fw-bold">৳{{ number_format($todaySales, 2) }}</h3>
                        <div class="d-flex align-items-center mt-2">
                            <span class="badge bg-info-subtle text-info me-1">
                                {{ $todayOrders }} orders
                            </span>
                            <span class="text-muted small">today</span>
                        </div>
                    </div>
                    <div class="stat-icon bg-success bg-opacity-10 text-success">
                        <i class="bi bi-graph-up-arrow"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Total Orders -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card h-100 border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted mb-1 small text-uppercase fw-semibold">Total Orders</p>
                        <h3 class="mb-1 fw-bold">{{ $totalOrders }}</h3>
                        <div class="d-flex align-items-center mt-2">
                            @if($orderGrowth >= 0)
                                <span class="badge bg-success-subtle text-success me-1">
                                    <i class="bi bi-arrow-up-short"></i>{{ abs($orderGrowth) }}%
                                </span>
                            @else
                                <span class="badge bg-danger-subtle text-danger me-1">
                                    <i class="bi bi-arrow-down-short"></i>{{ abs($orderGrowth) }}%
                                </span>
                            @endif
                            <span class="text-muted small">vs last month</span>
                        </div>
                    </div>
                    <div class="stat-icon bg-info bg-opacity-10 text-info">
                        <i class="bi bi-cart-check"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Total Customers -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card h-100 border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted mb-1 small text-uppercase fw-semibold">Total Customers</p>
                        <h3 class="mb-1 fw-bold">{{ $totalCustomers }}</h3>
                        <div class="d-flex align-items-center mt-2">
                            @if($customerGrowth >= 0)
                                <span class="badge bg-success-subtle text-success me-1">
                                    <i class="bi bi-arrow-up-short"></i>{{ abs($customerGrowth) }}%
                                </span>
                            @else
                                <span class="badge bg-danger-subtle text-danger me-1">
                                    <i class="bi bi-arrow-down-short"></i>{{ abs($customerGrowth) }}%
                                </span>
                            @endif
                            <span class="text-muted small">vs last month</span>
                        </div>
                    </div>
                    <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                        <i class="bi bi-people"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Charts Row -->
<div class="row mb-4">
    <!-- Sales Overview Chart -->
    <div class="col-xl-8 mb-4">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <div class="d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0 fw-semibold">Sales Overview</h5>
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn btn-outline-primary active" id="btnWeekly">Weekly</button>
                        <button type="button" class="btn btn-outline-primary" id="btnMonthly">Monthly</button>
                        <button type="button" class="btn btn-outline-primary" id="btnYearly">Yearly</button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <canvas id="salesOverviewChart" height="300"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Order Status Doughnut Chart -->
    <div class="col-xl-4 mb-4">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="card-title mb-0 fw-semibold">Order Status</h5>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <div class="position-relative" style="width: 220px; height: 220px;">
                    <canvas id="orderStatusChart"></canvas>
                    <div class="position-absolute top-50 start-50 translate-middle text-center">
                        <h4 class="mb-0 fw-bold">{{ $totalOrders }}</h4>
                        <small class="text-muted">Total</small>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-white border-0 pt-0">
                <div class="row text-center">
                    <div class="col-3">
                        <div class="d-flex align-items-center justify-content-center mb-1">
                            <span class="badge bg-warning rounded-pill px-2 py-1">{{ $pendingOrders }}</span>
                        </div>
                        <small class="text-muted">Pending</small>
                    </div>
                    <div class="col-3">
                        <div class="d-flex align-items-center justify-content-center mb-1">
                            <span class="badge bg-info rounded-pill px-2 py-1">{{ $processingOrders }}</span>
                        </div>
                        <small class="text-muted">Processing</small>
                    </div>
                    <div class="col-3">
                        <div class="d-flex align-items-center justify-content-center mb-1">
                            <span class="badge bg-success rounded-pill px-2 py-1">{{ $completedOrders }}</span>
                        </div>
                        <small class="text-muted">Completed</small>
                    </div>
                    <div class="col-3">
                        <div class="d-flex align-items-center justify-content-center mb-1">
                            <span class="badge bg-danger rounded-pill px-2 py-1">{{ $cancelledOrders ?? 0 }}</span>
                        </div>
                        <small class="text-muted">Cancelled</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Secondary Charts Row -->
<div class="row mb-4">
    <!-- Revenue by Category -->
    <div class="col-xl-6 mb-4">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="card-title mb-0 fw-semibold">Revenue by Category</h5>
            </div>
            <div class="card-body">
                <canvas id="categoryRevenueChart" height="280"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Monthly Sales Comparison -->
    <div class="col-xl-6 mb-4">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="card-title mb-0 fw-semibold">Monthly Sales & Orders</h5>
            </div>
            <div class="card-body">
                <canvas id="monthlySalesChart" height="280"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Data Tables Row -->
<div class="row">
    <!-- Recent Orders -->
    <div class="col-lg-8 mb-4">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3 d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0 fw-semibold">Recent Orders</h5>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
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
    
    <!-- Top Selling Products -->
    <div class="col-lg-4 mb-4">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="card-title mb-0 fw-semibold">Top Selling Products</h5>
            </div>
            <div class="card-body">
                @forelse($topProducts as $index => $product)
                <div class="d-flex align-items-center mb-3">
                    <div class="me-3">
                        <span class="badge bg-primary rounded-circle p-2">{{ $index + 1 }}</span>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-0 small">{{ $product->product_name }}</h6>
                        <small class="text-muted">{{ $product->total_sold }} sold</small>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-light text-dark">{{ $product->total_sold }}</span>
                    </div>
                </div>
                @empty
                <p class="text-muted text-center">No data available</p>
                @endforelse
            </div>
        </div>
        
        <!-- Product Status -->
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="card-title mb-0 fw-semibold">Product Status</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="d-flex align-items-center">
                        <div class="bg-success bg-opacity-10 rounded p-2 me-2">
                            <i class="bi bi-check-circle text-success"></i>
                        </div>
                        <span>Active Products</span>
                    </div>
                    <span class="fw-semibold">{{ $activeProducts }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="d-flex align-items-center">
                        <div class="bg-danger bg-opacity-10 rounded p-2 me-2">
                            <i class="bi bi-x-circle text-danger"></i>
                        </div>
                        <span>Out of Stock</span>
                    </div>
                    <span class="fw-semibold text-danger">{{ $outOfStockProducts }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <div class="bg-warning bg-opacity-10 rounded p-2 me-2">
                            <i class="bi bi-exclamation-triangle text-warning"></i>
                        </div>
                        <span>Low Stock</span>
                    </div>
                    <span class="fw-semibold text-warning">{{ $lowStockProducts }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .stat-card {
        transition: all 0.3s ease;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.1) !important;
    }
    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }
    .bg-success-subtle { background-color: rgba(25, 135, 84, 0.1) !important; }
    .bg-danger-subtle { background-color: rgba(220, 53, 69, 0.1) !important; }
    .bg-info-subtle { background-color: rgba(13, 202, 240, 0.1) !important; }
    .bg-warning-subtle { background-color: rgba(255, 193, 7, 0.1) !important; }
    .bg-primary-subtle { background-color: rgba(13, 110, 253, 0.1) !important; }
</style>
@endpush

@push('scripts')
<script>
    // Chart.js default configuration
    Chart.defaults.font.family = "'Segoe UI', 'Roboto', 'Helvetica Neue', Arial, sans-serif";
    Chart.defaults.plugins.legend.display = true;
    Chart.defaults.plugins.legend.position = 'bottom';
    
    // Color palette
    const colors = {
        primary: {
            main: '#667eea',
            light: 'rgba(102, 126, 234, 0.1)',
            gradient: ['rgba(102, 126, 234, 0.4)', 'rgba(102, 126, 234, 0.0)']
        },
        success: {
            main: '#10b981',
            light: 'rgba(16, 185, 129, 0.1)',
            gradient: ['rgba(16, 185, 129, 0.4)', 'rgba(16, 185, 129, 0.0)']
        },
        warning: {
            main: '#f59e0b',
            light: 'rgba(245, 158, 11, 0.1)'
        },
        info: {
            main: '#0ea5e9',
            light: 'rgba(14, 165, 233, 0.1)'
        },
        danger: {
            main: '#ef4444',
            light: 'rgba(239, 68, 68, 0.1)'
        }
    };
    
    // Monthly labels
    const monthLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    
    // Sales Overview Chart (Area Chart)
    const salesCtx = document.getElementById('salesOverviewChart').getContext('2d');
    const salesGradient = salesCtx.createLinearGradient(0, 0, 0, 300);
    salesGradient.addColorStop(0, colors.primary.gradient[0]);
    salesGradient.addColorStop(1, colors.primary.gradient[1]);
    
    const weeklyData = @json($last7DaysSales);
    const weeklyLabels = @json($last7DaysLabels);
    const monthlyData = @json($last30DaysSales);
    const monthlyLabels = @json($last30DaysLabels);
    const yearlyData = @json($monthlySalesData);
    
    let salesChart = new Chart(salesCtx, {
        type: 'line',
        data: {
            labels: weeklyLabels,
            datasets: [{
                label: 'Sales (৳)',
                data: weeklyData,
                borderColor: colors.primary.main,
                backgroundColor: salesGradient,
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: colors.primary.main,
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    padding: 12,
                    displayColors: false,
                    callbacks: {
                        label: function(context) {
                            return '৳' + context.parsed.y.toLocaleString();
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    },
                    ticks: {
                        callback: function(value) {
                            return '৳' + value.toLocaleString();
                        }
                    }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
            }
        }
    });
    
    // Chart period switcher
    document.getElementById('btnWeekly').addEventListener('click', function() {
        updateChartPeriod('weekly', this);
    });
    document.getElementById('btnMonthly').addEventListener('click', function() {
        updateChartPeriod('monthly', this);
    });
    document.getElementById('btnYearly').addEventListener('click', function() {
        updateChartPeriod('yearly', this);
    });
    
    function updateChartPeriod(period, btn) {
        document.querySelectorAll('.btn-group .btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        
        if (period === 'weekly') {
            salesChart.data.labels = weeklyLabels;
            salesChart.data.datasets[0].data = weeklyData;
        } else if (period === 'monthly') {
            salesChart.data.labels = monthlyLabels;
            salesChart.data.datasets[0].data = monthlyData;
        } else {
            salesChart.data.labels = monthLabels;
            salesChart.data.datasets[0].data = yearlyData;
        }
        salesChart.update();
    }
    
    // Order Status Doughnut Chart
    const orderStatusCtx = document.getElementById('orderStatusChart').getContext('2d');
    new Chart(orderStatusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Pending', 'Processing', 'Completed', 'Cancelled'],
            datasets: [{
                data: [{{ $pendingOrders }}, {{ $processingOrders }}, {{ $completedOrders }}, {{ $cancelledOrders ?? 0 }}],
                backgroundColor: [
                    colors.warning.main,
                    colors.info.main,
                    colors.success.main,
                    colors.danger.main
                ],
                borderWidth: 0,
                hoverOffset: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = total > 0 ? ((context.parsed / total) * 100).toFixed(1) : 0;
                            return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });
    
    // Category Revenue Chart (Horizontal Bar)
    const categoryCtx = document.getElementById('categoryRevenueChart').getContext('2d');
    const categoryData = @json($salesByCategory);
    new Chart(categoryCtx, {
        type: 'bar',
        data: {
            labels: categoryData.map(item => item.name),
            datasets: [{
                label: 'Revenue (৳)',
                data: categoryData.map(item => item.total_sales),
                backgroundColor: [
                    'rgba(102, 126, 234, 0.8)',
                    'rgba(16, 185, 129, 0.8)',
                    'rgba(245, 158, 11, 0.8)',
                    'rgba(14, 165, 233, 0.8)',
                    'rgba(239, 68, 68, 0.8)',
                    'rgba(139, 92, 246, 0.8)'
                ],
                borderRadius: 8,
                borderSkipped: false
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    callbacks: {
                        label: function(context) {
                            return '৳' + context.parsed.x.toLocaleString();
                        }
                    }
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    },
                    ticks: {
                        callback: function(value) {
                            return '৳' + value.toLocaleString();
                        }
                    }
                },
                y: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
    
    // Monthly Sales & Orders Chart (Combo Chart)
    const monthlyCtx = document.getElementById('monthlySalesChart').getContext('2d');
    const monthlyGradient = monthlyCtx.createLinearGradient(0, 0, 0, 280);
    monthlyGradient.addColorStop(0, colors.success.gradient[0]);
    monthlyGradient.addColorStop(1, colors.success.gradient[1]);
    
    new Chart(monthlyCtx, {
        type: 'bar',
        data: {
            labels: monthLabels,
            datasets: [{
                type: 'line',
                label: 'Sales (৳)',
                data: @json($monthlySalesData),
                borderColor: colors.success.main,
                backgroundColor: 'transparent',
                borderWidth: 3,
                tension: 0.4,
                pointBackgroundColor: colors.success.main,
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 4,
                yAxisID: 'y'
            }, {
                type: 'bar',
                label: 'Orders',
                data: @json($monthlyOrderCounts),
                backgroundColor: 'rgba(102, 126, 234, 0.6)',
                borderRadius: 6,
                yAxisID: 'y1'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false
            },
            plugins: {
                legend: {
                    position: 'bottom'
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    callbacks: {
                        label: function(context) {
                            if (context.dataset.label === 'Sales (৳)') {
                                return 'Sales: ৳' + context.parsed.y.toLocaleString();
                            }
                            return 'Orders: ' + context.parsed.y;
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    }
                },
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    },
                    ticks: {
                        callback: function(value) {
                            return '৳' + value.toLocaleString();
                        }
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    beginAtZero: true,
                    grid: {
                        drawOnChartArea: false
                    }
                }
            }
        }
    });
</script>
@endpush
