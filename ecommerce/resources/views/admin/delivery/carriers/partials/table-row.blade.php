<tr>
    <td>
        <input type="checkbox" class="form-check-input carrier-checkbox" value="{{ $carrier->id }}" onclick="event.stopPropagation();">
    </td>
    <td>
        @php
            $logoUrl = $carrier->logo;
            if($logoUrl && !str_starts_with($logoUrl, '/storage/') && !str_starts_with($logoUrl, 'http')) {
                $logoUrl = '/storage/' . $logoUrl;
            }
        @endphp
        @if($logoUrl)
            <img src="{{ $logoUrl }}" alt="{{ $carrier->name }}" class="rounded" style="width: 40px; height: 40px; object-fit: cover;">
        @else
            <div class="bg-secondary rounded d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                <i class="bi bi-truck text-white"></i>
            </div>
        @endif
    </td>
    <td>
        <div class="fw-medium">{{ $carrier->name }}</div>
        <small class="text-muted">{{ $carrier->slug }}</small>
    </td>
    <td>
        <span class="badge bg-info">{{ $carrier->carrier_type_label }}</span>
    </td>
    <td>
        <span class="badge bg-secondary">{{ $carrier->service_type_label }}</span>
    </td>
    <td>
        {{ formatPrice($carrier->base_rate) }}
    </td>
    <td>
        @if($carrier->supports_tracking)
            <span class="badge bg-success"><i class="bi bi-check-circle"></i></span>
        @else
            <span class="badge bg-light text-dark"><i class="bi bi-x-circle"></i></span>
        @endif
    </td>
    <td>
        @if($carrier->supports_cod)
            <span class="badge bg-success"><i class="bi bi-check-circle"></i></span>
        @else
            <span class="badge bg-light text-dark"><i class="bi bi-x-circle"></i></span>
        @endif
    </td>
    <td>
        <span class="badge {{ $carrier->is_active ? 'bg-success' : 'bg-secondary' }}" id="status-badge-{{ $carrier->id }}">
            {{ $carrier->is_active ? 'Active' : 'Inactive' }}
        </span>
    </td>
    <td>
        <span class="badge {{ $carrier->is_featured ? 'bg-warning text-dark' : 'bg-light text-dark' }}" id="featured-badge-{{ $carrier->id }}">
            @if($carrier->is_featured)
                <i class="bi bi-star-fill me-1"></i>Yes
            @else
                No
            @endif
        </span>
    </td>
    <td>
        <div class="dropdown">
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                <i class="bi bi-three-dots-vertical"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <a class="dropdown-item" href="{{ route('admin.delivery.carriers.edit', $carrier->id) }}">
                        <i class="bi bi-pencil me-2"></i>Edit
                    </a>
                </li>
                <li>
                    <button class="dropdown-item" onclick="toggleStatus({{ $carrier->id }})">
                        <i class="bi bi-{{ $carrier->is_active ? 'pause' : 'play' }}-circle me-2"></i>
                        {{ $carrier->is_active ? 'Deactivate' : 'Activate' }}
                    </button>
                </li>
                <li>
                    <button class="dropdown-item" onclick="toggleFeatured({{ $carrier->id }})">
                        <i class="bi bi-star me-2"></i>
                        {{ $carrier->is_featured ? 'Remove from Featured' : 'Mark as Featured' }}
                    </button>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <button class="dropdown-item text-danger" onclick="deleteCarrier({{ $carrier->id }})">
                        <i class="bi bi-trash me-2"></i>Delete
                    </button>
                </li>
            </ul>
        </div>
    </td>
</tr>

@push('scripts')
<script>
function deleteCarrier(carrierId) {
    if (!confirm('Are you sure you want to delete this carrier?')) return;
    
    fetch(`{{ route('admin.delivery.carriers.destroy', ':id') }}`.replace(':id', carrierId), {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showToast(data.message || 'Carrier deleted successfully', 'success');
            // Reload the page or remove the row
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            showToast(data.message || 'Error deleting carrier', 'error');
        }
    })
    .catch(err => {
        showToast('Error deleting carrier', 'error');
    });
}
</script>
@endpush
