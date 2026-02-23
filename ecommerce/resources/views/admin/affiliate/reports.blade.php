@extends('admin.layouts.app')

@section('title', 'Affiliate Reports')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Affiliate Reports</h1>
        <a href="{{ route('admin.affiliate.reports.export') }}" class="btn btn-success">
            <i class="bi bi-download me-2"></i>Export Report
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-primary bg-opacity-10 text-primary me-3">
                            <i class="bi bi-people"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 text-muted">Total Affiliates</h6>
                            <h3 class="mb-0">{{ number_format($stats['total_affiliates']) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-success bg-opacity-10 text-success me-3">
                            <i class="bi bi-currency-dollar"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 text-muted">Total Sales</h6>
                            <h3 class="mb-0">${{ number_format($stats['total_sales'], 2) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-info bg-opacity-10 text-info me-3">
                            <i class="bi bi-graph-up"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 text-muted">Total Commissions</h6>
                            <h3 class="mb-0">${{ number_format($stats['total_commissions'], 2) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-warning bg-opacity-10 text-warning me-3">
                            <i class="bi bi-cursor"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 text-muted">Total Clicks</h6>
                            <h3 class="mb-0">{{ number_format($stats['total_clicks']) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Affiliate Performance Report</h5>
        </div>
        <div class="card-body">
            @if($affiliates->count() > 0)
            <table class="table table-striped" id="affiliateReportsTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Affiliate</th>
                        <th>Code</th>
                        <th>Clicks</th>
                        <th>Sales</th>
                        <th>Total Sales</th>
                        <th>Commission</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($affiliates as $affiliate)
                    <tr>
                        <td>{{ $affiliate->id }}</td>
                        <td>{{ $affiliate->user->name ?? '-' }}</td>
                        <td><code>{{ $affiliate->affiliate_code }}</code></td>
                        <td>{{ number_format($affiliate->clicks_count) }}</td>
                        <td>{{ number_format($affiliate->sales_count) }}</td>
                        <td>${{ number_format($affiliate->total_sales ?? 0, 2) }}</td>
                        <td>${{ number_format($affiliate->total_commission ?? 0, 2) }}</td>
                        <td>
                            @if($affiliate->status === 'approved')
                            <span class="badge bg-success">Active</span>
                            @elseif($affiliate->status === 'pending')
                            <span class="badge bg-warning">Pending</span>
                            @else
                            <span class="badge bg-danger">Suspended</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            <div class="d-flex justify-content-center mt-4">
                {{ $affiliates->links() }}
            </div>
            @else
            <div class="text-center py-5">
                <i class="bi bi-graph-up text-muted" style="font-size: 4rem;"></i>
                <h5 class="mt-3 text-muted">No data available</h5>
                <p class="text-muted">Affiliate performance data will appear here.</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#affiliateReportsTable').DataTable({
            pageLength: 15,
            order: [[5, 'desc']],
            columnDefs: [
                { orderable: false, targets: [] }
            ]
        });
    });
</script>
@endpush
