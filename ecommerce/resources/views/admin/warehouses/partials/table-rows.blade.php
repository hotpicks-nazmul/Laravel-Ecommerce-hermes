@forelse($warehouses as $warehouse)
<tr>
    <td>
        <input type="checkbox" class="form-check-input warehouse-checkbox" value="{{ $warehouse->id }}" onchange="updateSelection({{ $warehouse->id }}, this.checked)">
    </td>
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
        <div class="dropdown">
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                Actions
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <a class="dropdown-item" href="{{ route('admin.warehouses.show', $warehouse->id) }}">
                        <i class="bi bi-eye me-2"></i> View
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="{{ route('admin.warehouses.edit', $warehouse->id) }}">
                        <i class="bi bi-pencil me-2"></i> Edit
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form action="{{ route('admin.warehouses.destroy', $warehouse->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Are you sure you want to delete this warehouse?')">
                            <i class="bi bi-trash me-2"></i> Delete
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </td>
</tr>
@empty
<tr>
    <td colspan="7" class="text-center py-5">
        <div class="text-muted">
            <i class="bi bi-building display-4"></i>
            <p class="mt-2">No warehouses found.</p>
            <a href="{{ route('admin.warehouses.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg me-1"></i> Add Warehouse
            </a>
        </div>
    </td>
</tr>
@endforelse
