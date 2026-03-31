@extends('admin.layouts.app')

@section('title', 'Activity Logs')

@push('styles')
<style>
    /* Force Bootstrap Icons to display on this page */
    .stat-card-icon i,
    .stat-card-icon i::before,
    .bi::before,
    [class*="bi bi-"]::before {
        display: inline-block !important;
        visibility: visible !important;
        opacity: 1 !important;
        font-family: 'bootstrap-icons' !important;
    }
    
    /* Override icon colors for stat cards */
    .stat-card-primary .stat-card-icon i::before { color: #0d6efd !important; }
    .stat-card-success .stat-card-icon i::before { color: #198754 !important; }
    .stat-card-info .stat-card-icon i::before { color: #0dcaf0 !important; }
    .stat-card-warning .stat-card-icon i::before { color: #ffc107 !important; }
    .stat-card-danger .stat-card-icon i::before { color: #dc3545 !important; }
    .stat-card-secondary .stat-card-icon i::before { color: #6c757d !important; }
    
    /* Make the whole icon colored */
    .stat-card-icon i { color: inherit !important; }
    
    /* Pagination icon sizing - fix large icons */
    .pagination .page-link i,
    .pagination .page-link i::before,
    .pagination .page-link .bi,
    .pagination .page-link .bi::before {
        font-size: 14px !important;
        line-height: 1 !important;
    }
    
    .page-item .page-link i,
    .page-item .page-link i::before,
    .page-item .page-link .bi,
    .page-item .page-link .bi::before {
        font-size: 14px !important;
        line-height: 1 !important;
    }
</style>
@endpush

@section('content')
<div class="mb-4">
    <h4 class="mb-0"><i class="bi bi-journal-text me-2"></i>Activity Logs</h4>
    <p class="text-muted mb-0">Track all system activities and user actions</p>
</div>

<!-- Stats Cards -->
<div class="stat-card-row mb-4">
    <div class="stat-card stat-card-primary">
        <div class="stat-card-icon"><i class="bi bi-journal-text"></i></div>
        <div class="stat-card-content">
            <span class="stat-card-label">Total Logs</span>
            <span class="stat-card-value">{{ number_format($stats['total']) }}</span>
        </div>
    </div>
    <div class="stat-card stat-card-success">
        <div class="stat-card-icon"><i class="bi bi-person-badge"></i></div>
        <div class="stat-card-content">
            <span class="stat-card-label">Admin Logs</span>
            <span class="stat-card-value">{{ number_format($stats['admin']) }}</span>
        </div>
    </div>
    <div class="stat-card stat-card-info">
        <div class="stat-card-icon"><i class="bi bi-people"></i></div>
        <div class="stat-card-content">
            <span class="stat-card-label">Customer Logs</span>
            <span class="stat-card-value">{{ number_format($stats['customer']) }}</span>
        </div>
    </div>
    <div class="stat-card stat-card-warning">
        <div class="stat-card-icon"><i class="bi bi-gear"></i></div>
        <div class="stat-card-content">
            <span class="stat-card-label">System Logs</span>
            <span class="stat-card-value">{{ number_format($stats['system']) }}</span>
        </div>
    </div>
</div>

<!-- Tabs -->
<div class="mb-3">
    <ul class="nav nav-tabs nav-fill">
        <li class="nav-item">
            <a class="nav-link {{ $tab === 'all' ? 'active' : '' }}" href="{{ route('admin.system.activity-logs.index') }}">
                <i class="bi bi-list-ul me-1"></i> All Logs
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $tab === 'admin' ? 'active' : '' }}" href="{{ route('admin.system.activity-logs.index', ['tab' => 'admin']) }}">
                <i class="bi bi-person-badge me-1"></i> Admin
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $tab === 'customer' ? 'active' : '' }}" href="{{ route('admin.system.activity-logs.index', ['tab' => 'customer']) }}">
                <i class="bi bi-people me-1"></i> Customer
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $tab === 'system' ? 'active' : '' }}" href="{{ route('admin.system.activity-logs.index', ['tab' => 'system']) }}">
                <i class="bi bi-gear me-1"></i> System
            </a>
        </li>
    </ul>
</div>

<!-- Filters Card -->
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-3">
        <form method="GET" id="filterForm">
            <input type="hidden" name="tab" value="{{ $tab }}">
            <div class="row g-2 align-items-end">
                <!-- Search Input -->
                <div class="col-lg-4 col-md-4 col-sm-6">
                    <label class="form-label small text-muted">Search</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" id="liveSearch" class="form-control" 
                               placeholder="Search by description..." value="{{ $search }}">
                        <span class="input-group-text" id="searchSpinner" style="display: none;">
                            <div class="spinner-border spinner-border-sm"></div>
                        </span>
                    </div>
                </div>
                
                <!-- Date Range -->
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <label class="form-label small text-muted">Date Range</label>
                    <input type="text" name="date_range" id="dateRange" class="form-control form-control-sm" 
                           placeholder="Select date range" value="{{ $dateRange }}">
                </div>
                
                <!-- Sort By -->
                <div class="col-lg-2 col-md-3 col-sm-6">
                    <label class="form-label small text-muted">Sort By</label>
                    <select name="sort" class="form-select form-select-sm">
                        <option value="recent" {{ $sortBy === 'recent' ? 'selected' : '' }}>Most Recent</option>
                        <option value="oldest" {{ $sortBy === 'oldest' ? 'selected' : '' }}>Oldest First</option>
                    </select>
                </div>
                
                <!-- Filter Buttons -->
                <div class="col-lg-3 col-md-5 col-sm-6">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="bi bi-funnel me-1"></i> Filter
                        </button>
                        <a href="{{ route('admin.system.activity-logs.index', ['tab' => $tab]) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-x-lg me-1"></i> Reset
                        </a>
                        <a href="{{ route('admin.system.activity-logs.export', request()->query()) }}" class="btn btn-sm btn-outline-success">
                            <i class="bi bi-download me-1"></i> Export
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Data Table Card -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h6 class="mb-0"><i class="bi bi-table me-2"></i>Activity Log History</h6>
        <div class="d-flex align-items-center gap-2">
            <span class="text-muted small">Showing {{ $logs->firstItem() ?? 0 }} - {{ $logs->lastItem() ?? 0 }} of {{ $logs->total() }} logs</span>
            @if($logs->total() > 0)
            <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#clearLogsModal">
                <i class="bi bi-trash me-1"></i> Clear Logs
            </button>
            @endif
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th style="width: 100px;">Type</th>
                        <th>Description</th>
                        <th style="width: 150px;">User</th>
                        <th style="width: 130px;">IP Address</th>
                        <th style="width: 150px;">Date</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    @forelse($logs as $index => $log)
                    <tr>
                        <td>{{ $logs->firstItem() + $index }}</td>
                        <td>
                            @if($log->log_name === 'admin')
                            <span class="badge bg-success">
                                <i class="bi bi-person-badge me-1"></i>Admin
                            </span>
                            @elseif($log->log_name === 'customer')
                            <span class="badge bg-info">
                                <i class="bi bi-person me-1"></i>Customer
                            </span>
                            @elseif($log->log_name === 'system')
                            <span class="badge bg-warning">
                                <i class="bi bi-gear me-1"></i>System
                            </span>
                            @else
                            <span class="badge bg-secondary">
                                {{ $log->log_name }}
                            </span>
                            @endif
                        </td>
                        <td>
                            <div class="fw-medium">{{ $log->description }}</div>
                            @if($log->subject_type)
                            <div class="text-muted small">
                                <i class="bi bi-link-45deg me-1"></i>
                                {{ class_basename($log->subject_type) }} #{{ $log->subject_id }}
                            </div>
                            @endif
                            @if($log->properties && isset($log->properties['attributes']))
                            <div class="text-muted small mt-1">
                                <span class="text-info">{{ json_encode($log->properties['attributes']) }}</span>
                            </div>
                            @endif
                        </td>
                        <td>
                            @if($log->causer)
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm bg-primary bg-opacity-10 rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                    <span class="text-primary small">{{ substr($log->causer->name ?? 'U', 0, 1) }}</span>
                                </div>
                                <div>
                                    <div class="small fw-medium">{{ $log->causer->name ?? 'Unknown' }}</div>
                                    <div class="text-muted small">{{ $log->causer->email ?? '' }}</div>
                                </div>
                            </div>
                            @else
                            <span class="text-muted">
                                <i class="bi bi-gear me-1"></i>System
                            </span>
                            @endif
                        </td>
                        <td>
                            <span class="text-muted small">{{ $log->ip_address ?? 'N/A' }}</span>
                        </td>
                        <td>
                            <span class="text-muted small">
                                {{ $log->created_at->format('d M Y, h:i A') }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <i class="bi bi-journal-text text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mb-2 mt-2">No activity logs found</p>
                            <p class="text-muted small">Activity will be logged here when users perform actions</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($logs->hasPages())
        <div class="card-footer bg-white py-3 text-center">
            <div class="d-inline-flex align-items-center gap-3">
                <span class="text-muted small">
                    Showing {{ $logs->firstItem() ?? 0 }} - {{ $logs->lastItem() ?? 0 }} of {{ $logs->total() }} logs
                </span>
                {{ $logs->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Clear Logs Modal -->
<div class="modal fade" id="clearLogsModal" tabindex="-1" aria-labelledby="clearLogsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="clearLogsModalLabel">Clear Activity Logs</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.system.activity-logs.clear') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Are you sure you want to clear activity logs? This action cannot be undone.</p>
                    <div class="mb-3">
                        <label class="form-label">Clear logs for:</label>
                        <select name="log_type" class="form-select">
                            <option value="all">All Logs</option>
                            @if($tab === 'admin' || $tab === 'all')
                            <option value="admin">Admin Logs Only</option>
                            @endif
                            @if($tab === 'customer' || $tab === 'all')
                            <option value="customer">Customer Logs Only</option>
                            @endif
                            @if($tab === 'system' || $tab === 'all')
                            <option value="system">System Logs Only</option>
                            @endif
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Clear Logs</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .table th {
        font-weight: 600;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .table td {
        font-size: 0.9rem;
    }
    .badge {
        font-weight: 500;
    }
    .avatar-sm {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .nav-tabs .nav-link {
        border: 1px solid transparent;
        border-bottom: none;
    }
    .nav-tabs .nav-link.active {
        border-color: #dee2e6 #dee2e6 #fff;
        background-color: #fff;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize date range picker
    const dateRangeInput = document.getElementById('dateRange');
    if (dateRangeInput) {
        flatpickr(dateRangeInput, {
            mode: 'range',
            dateFormat: 'Y-m-d',
            allowInput: true,
            placeholder: 'Select date range'
        });
    }
    
    // Live search with debounce
    let searchTimeout;
    const searchInput = document.getElementById('liveSearch');
    const searchSpinner = document.getElementById('searchSpinner');
    
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const searchTerm = this.value.trim();
            
            // Show spinner
            if (searchSpinner) {
                searchSpinner.style.display = 'block';
            }
            
            // Debounce - wait 500ms after user stops typing
            searchTimeout = setTimeout(() => {
                if (searchSpinner) {
                    searchSpinner.style.display = 'none';
                }
            }, 500);
        });
    }
});
</script>
@endpush
