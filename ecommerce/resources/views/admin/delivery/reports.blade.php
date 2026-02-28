@extends('admin.layouts.app')

@section('title', 'Delivery Reports')

@push('styles')
<style>
    .report-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        transition: transform 0.3s ease;
        cursor: pointer;
    }
    .report-card:hover {
        transform: translateY(-5px);
    }
    .report-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }
</style>
@endpush

@section('content')
<div class="content-area">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1"><i class="bi bi-bar-chart me-2"></i>Delivery Reports</h4>
            <p class="text-muted mb-0">View comprehensive delivery analytics and reports</p>
        </div>
        
        <!-- Date Filter -->
        <form method="GET" class="d-flex align-items-center gap-2">
            <select name="date_range" class="form-select form-select-sm" style="width: 150px;">
                <option value="this_week">This Week</option>
                <option value="this_month" selected>This Month</option>
                <option value="last_30_days">Last 30 Days</option>
                <option value="this_year">This Year</option>
            </select>
            <button type="submit" class="btn btn-sm btn-primary">
                <i class="bi bi-filter me-1"></i> Apply
            </button>
        </form>
    </div>

    <!-- Report Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card report-card bg-white h-100">
                <div class="card-body text-center">
                    <div class="report-icon bg-primary bg-opacity-10 text-primary mx-auto mb-3">
                        <i class="bi bi-graph-up"></i>
                    </div>
                    <h6>Delivery Performance</h6>
                    <p class="text-muted small mb-0">Success rate, average time, and efficiency metrics</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card report-card bg-white h-100">
                <div class="card-body text-center">
                    <div class="report-icon bg-success bg-opacity-10 text-success mx-auto mb-3">
                        <i class="bi bi-currency-dollar"></i>
                    </div>
                    <h6>Revenue Reports</h6>
                    <p class="text-muted small mb-0">Shipping revenue and cost analysis</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card report-card bg-white h-100">
                <div class="card-body text-center">
                    <div class="report-icon bg-warning bg-opacity-10 text-warning mx-auto mb-3">
                        <i class="bi bi-person-badge"></i>
                    </div>
                    <h6>Delivery Boy Performance</h6>
                    <p class="text-muted small mb-0">Individual delivery personnel statistics</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card report-card bg-white h-100">
                <div class="card-body text-center">
                    <div class="report-icon bg-info bg-opacity-10 text-info mx-auto mb-3">
                        <i class="bi bi-geo-alt"></i>
                    </div>
                    <h6>Zone Analysis</h6>
                    <p class="text-muted small mb-0">Performance breakdown by delivery zones</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card report-card bg-white h-100">
                <div class="card-body text-center">
                    <div class="report-icon bg-danger bg-opacity-10 text-danger mx-auto mb-3">
                        <i class="bi bi-exclamation-triangle"></i>
                    </div>
                    <h6>Failed Deliveries</h6>
                    <p class="text-muted small mb-0">Failed and returned shipments analysis</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card report-card bg-white h-100">
                <div class="card-body text-center">
                    <div class="report-icon bg-secondary bg-opacity-10 text-secondary mx-auto mb-3">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <h6>Delivery Time Analysis</h6>
                    <p class="text-muted small mb-0">Average delivery times and trends</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Placeholder Message -->
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="text-center py-5">
                <i class="bi bi-bar-chart text-primary" style="font-size: 4rem;"></i>
                <h5 class="mt-3">Detailed Reports Coming Soon</h5>
                <p class="text-muted">The detailed reporting system is under development. Check back soon for:</p>
                <ul class="text-start d-inline-block">
                    <li>Comprehensive delivery performance analytics</li>
                    <li>Exportable reports (PDF, Excel, CSV)</li>
                    <li>Custom date range analysis</li>
                    <li>Comparison with previous periods</li>
                    <li>Automated scheduled reports</li>
                </ul>
                <div class="mt-4">
                    <span class="badge bg-primary">Coming Soon</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
