@forelse($warehouses as $warehouse)
<tr>
    <td>{{ $loop->iteration + ($warehouses->currentPage() - 1) * $warehouses->perPage() }}</td>
    <td>
        <div class="d-flex align-items-center">
            <div class="flex-shrink-0">
                <div class="bg-light rounded p-2 text-center" style="width: 40px; height: 40px;">
                    <i class="bi bi-building"></i>
                </div>
            </div>
            <div class="ms-3">
                <div class="fw-semibold">
                    {{ $warehouse->name }}
                    @if($warehouse->is_primary)
                        <span class="badge bg-warning text-dark ms-1">Primary</span>
                    @endif
                </div>
                @if($warehouse->code)
                    <small class="text-muted">Code: {{ $warehouse->code }}</small>
                @endif
            </div>
        </div>
    </td>
    <td>
        <div>{{ $warehouse->address }}</div>
        @if($warehouse->city || $warehouse->state || $warehouse->country)
            <small class="text-muted">
                {{ implode(', ', array_filter([$warehouse->city, $warehouse->state, $warehouse->country])) }}
            </small>
        @endif
    </td>
    <td>
        @if($warehouse->phone)
            <div><i class="bi bi-phone me-1"></i>{{ $warehouse->phone }}</div>
        @endif
        @if($warehouse->email)
            <small class="text-muted"><i class="bi bi-envelope me-1"></i>{{ $warehouse->email }}</small>
        @endif
    </td>
    <td>
        <span class="badge {{ $warehouse->status_badge_class }}">
            {{ $warehouse->status_text }}
        </span>
    </td>
    <td>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.warehouses.show', $warehouse->id) }}" class="btn btn-sm btn-outline-secondary" title="View">
                <i class="bi bi-eye"></i>
            </a>
            <a href="{{ route('admin.warehouses.edit', $warehouse->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                <i class="bi bi-pencil"></i>
            </a>
            <form action="{{ route('admin.warehouses.destroy', $warehouse->id) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this warehouse?')">
                    <i class="bi bi-trash"></i>
                </button>
            </form>
        </div>
    </td>
</tr>
@empty
<tr>
    <td colspan="6" class="text-center py-5">
        <i class="bi bi-building text-muted" style="font-size: 3rem;"></i>
        <p class="text-muted mb-2 mt-2">No warehouses found</p>
        <a href="{{ route('admin.warehouses.create') }}" class="btn btn-sm btn-primary mt-1">
            <i class="bi bi-plus-lg me-1"></i> Add First Warehouse
        </a>
    </td>
</tr>
@endforelse
