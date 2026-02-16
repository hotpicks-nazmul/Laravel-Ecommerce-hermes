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

<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                @if($sliders->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="slidersTable">
                        <thead class="bg-light">
                            <tr>
                                <th width="50">Order</th>
                                <th width="150">Image</th>
                                <th>Title</th>
                                <th>Subtitle</th>
                                <th>Button Text</th>
                                <th>Link</th>
                                <th width="80">Status</th>
                                <th width="120">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sliders as $slider)
                            <tr data-id="{{ $slider->id }}">
                                <td>
                                    <i class="bi bi-grip-vertical text-muted cursor-move" style="cursor: move;"></i>
                                </td>
                                <td>
                                    <img src="{{ Storage::url($slider->image) }}" 
                                        alt="{{ $slider->title }}" 
                                        class="img-fluid rounded" 
                                        style="max-height: 60px; width: 100px; object-fit: cover;">
                                </td>
                                <td>
                                    <span class="fw-medium">{{ $slider->title }}</span>
                                </td>
                                <td>
                                    <span class="text-muted">{{ $slider->subtitle ?? '-' }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $slider->button_text ?? '-' }}</span>
                                </td>
                                <td>
                                    @if($slider->link)
                                    <a href="{{ $slider->link }}" target="_blank" class="text-primary">
                                        <i class="bi bi-link-45deg"></i> {{ Str::limit($slider->link, 30) }}
                                    </a>
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
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.sliders.edit', $slider) }}" class="btn btn-outline-primary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('admin.sliders.destroy', $slider) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this slider?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="Delete">
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
    });
</script>
@endpush
