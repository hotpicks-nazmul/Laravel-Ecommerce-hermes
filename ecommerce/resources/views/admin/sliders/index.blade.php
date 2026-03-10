@extends('admin.layouts.app')

@section('title', 'Sliders Management')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 py-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <a href="{{ route('admin.hero.index') }}" class="btn btn-outline-secondary btn-sm me-3" title="Back to Hero Section">
                            <i class="bi bi-arrow-left"></i>
                        </a>
                        <div>
                            <h4 class="mb-1 fw-bold">
                                <i class="bi bi-images text-primary me-2"></i> Sliders Management
                            </h4>
                            <p class="text-muted mb-0 small">Manage image sliders for your hero section</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.sliders.create') }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-lg me-1"></i> Add New Slider
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filters Card -->
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-3">
        <form method="GET" id="filterForm">
            <div class="row g-2 align-items-end">
                <!-- Search Input -->
                <div class="col-lg-4 col-md-6">
                    <label class="form-label small text-muted">Search</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" id="liveSearch" class="form-control" placeholder="Search by title..." value="{{ request('search') }}">
                    </div>
                </div>
                
                <!-- Status Filter -->
                <div class="col-lg-3 col-md-4">
                    <label class="form-label small text-muted">Status</label>
                    <select name="status" id="filterStatus" class="form-select form-select-sm">
                        <option value="">All Status</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                
                <!-- Reset Button -->
                <div class="col-lg-2 col-md-4">
                    <a href="{{ route('admin.sliders.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-x-lg me-1"></i> Reset
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Table Card -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        @if($sliders->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="slidersTable">
                <thead class="table-light">
                    <tr>
                        <th style="width: 40px;">Order</th>
                        <th style="width: 150px;">Image</th>
                        <th>Title</th>
                        <th>Subtitle</th>
                        <th style="width: 120px;">Button Text</th>
                        <th style="width: 80px;">Status</th>
                        <th style="width: 120px;">Actions</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    @foreach($sliders as $slider)
                    <tr data-id="{{ $slider->id }}">
                        <td>
                            <i class="bi bi-grip-vertical text-muted cursor-move" style="cursor: move;"></i>
                            <span class="ms-2 text-muted small">{{ $slider->order + 1 }}</span>
                        </td>
                        <td>
                            @php
                                $imageUrl = $slider->image;
                                if($imageUrl && !str_starts_with($imageUrl, '/storage/') && !str_starts_with($imageUrl, 'http')) {
                                    $imageUrl = '/storage/' . $imageUrl;
                                }
                            @endphp
                            @if($imageUrl)
                            <img src="{{ $imageUrl }}" 
                                alt="{{ $slider->title }}" 
                                class="img-fluid rounded" 
                                style="max-height: 60px; width: 100px; object-fit: cover;">
                            @else
                            <div class="bg-secondary rounded d-flex align-items-center justify-content-center" style="width: 100px; height: 60px;">
                                <i class="bi bi-image text-white"></i>
                            </div>
                            @endif
                        </td>
                        <td>
                            <span class="fw-medium">{{ $slider->title }}</span>
                        </td>
                        <td>
                            <span class="text-muted">{{ $slider->subtitle ?? '-' }}</span>
                        </td>
                        <td>
                            @if($slider->button_text)
                            <span class="badge bg-info">{{ $slider->button_text }}</span>
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($slider->is_active)
                            <span class="badge bg-success">Active</span>
                            @else
                            <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.sliders.edit', $slider) }}" class="btn btn-outline-primary btn-sm" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.sliders.destroy', $slider) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this slider?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Pagination inside card-footer -->
        @if($sliders->hasPages())
        <div class="card-footer bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="text-muted small">
                Showing {{ $sliders->firstItem() }} - {{ $sliders->lastItem() }} of {{ $sliders->total() }} sliders
            </div>
            <div>
                {{ $sliders->appends(request()->query())->links() }}
            </div>
        </div>
        @endif
        @else
        <div class="text-center py-5">
            <i class="bi bi-images text-muted" style="font-size: 4rem;"></i>
            <h5 class="mt-3 text-muted">No sliders found</h5>
            <p class="text-muted">Create your first slider to display on the hero section.</p>
            <a href="{{ route('admin.sliders.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Add New Slider
            </a>
        </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
    .cursor-move {
        cursor: move;
    }
    #slidersTable tbody tr {
        transition: background-color 0.2s;
    }
    #slidersTable tbody tr.dragging {
        background-color: #f8f9fa;
    }
    #slidersTable tbody tr.drag-over {
        border-top: 2px solid #667eea;
    }
</style>
@endpush

@push('scripts')
<script>
    // Simple drag and drop reordering
    document.addEventListener('DOMContentLoaded', function() {
        const table = document.getElementById('slidersTable');
        if (!table) return;
        
        const tbody = table.querySelector('tbody');
        const rows = tbody.querySelectorAll('tr');
        
        rows.forEach(row => {
            row.draggable = true;
            
            row.addEventListener('dragstart', function(e) {
                this.classList.add('dragging');
                e.dataTransfer.effectAllowed = 'move';
            });
            
            row.addEventListener('dragend', function(e) {
                this.classList.remove('dragging');
                const allRows = tbody.querySelectorAll('tr');
                const order = Array.from(allRows).map(r => r.dataset.id);
                
                // Send reorder request
                fetch('{{ route('admin.sliders.reorder') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ order: order })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        // Optionally show success notification
                    }
                });
            });
            
            row.addEventListener('dragover', function(e) {
                e.preventDefault();
                const dragging = tbody.querySelector('.dragging');
                if (dragging && dragging !== this) {
                    const rect = this.getBoundingClientRect();
                    const midY = rect.top + rect.height / 2;
                    if (e.clientY < midY) {
                        tbody.insertBefore(dragging, this);
                    } else {
                        tbody.insertBefore(dragging, this.nextSibling);
                    }
                }
            });
        });
        
        // Live search functionality
        const searchInput = document.getElementById('liveSearch');
        const filterStatus = document.getElementById('filterStatus');
        
        let searchTimeout;
        
        function performSearch() {
            const params = new URLSearchParams();
            
            if (searchInput.value.trim()) {
                params.set('search', searchInput.value.trim());
            }
            
            if (filterStatus.value !== '') {
                params.set('status', filterStatus.value);
            }
            
            // Keep existing sort and per_page
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('per_page')) params.set('per_page', urlParams.get('per_page'));
            
            // Redirect to filtered URL
            window.location.href = '{{ route('admin.sliders.index') }}?' + params.toString();
        }
        
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(performSearch, 300);
            });
        }
        
        if (filterStatus) {
            filterStatus.addEventListener('change', performSearch);
        }
    });
</script>
@endpush
