@forelse($attributes as $attribute)
<tr data-id="{{ $attribute->id }}">
    <td>
        <input type="checkbox" class="form-check-input item-checkbox" value="{{ $attribute->id }}">
    </td>
    <td>
        <a href="{{ route('admin.attributes.edit', $attribute->id) }}" class="text-decoration-none fw-medium">
            {{ $attribute->name }}
        </a>
        @if($attribute->description)
        <br><small class="text-muted">{{ Str::limit($attribute->description, 50) }}</small>
        @endif
    </td>
    <td><code>{{ $attribute->slug }}</code></td>
    <td>
        <span class="badge bg-light text-dark">{{ $attribute->values_count }} values</span>
        @if($attribute->active_values_count > 0)
        <span class="badge bg-success">{{ $attribute->active_values_count }} active</span>
        @endif
    </td>
    <td>{{ $attribute->display_order }}</td>
    <td>
        <button type="button" class="btn btn-sm {{ $attribute->is_active ? 'btn-success' : 'btn-outline-secondary' }}" 
                onclick="toggleStatus({{ $attribute->id }})" title="Toggle Status">
            <i class="bi {{ $attribute->is_active ? 'bi-check-circle' : 'bi-x-circle' }}"></i>
            {{ $attribute->is_active ? 'Active' : 'Inactive' }}
        </button>
    </td>
    <td>
        <button type="button" class="btn btn-sm {{ $attribute->is_filterable ? 'btn-info' : 'btn-outline-secondary' }}" 
                onclick="toggleFilterable({{ $attribute->id }})" title="Toggle Filterable">
            <i class="bi {{ $attribute->is_filterable ? 'bi-funnel' : 'bi-funnel' }}"></i>
            {{ $attribute->is_filterable ? 'Yes' : 'No' }}
        </button>
    </td>
    <td>
        <div class="btn-group btn-group-sm">
            <a href="{{ route('admin.attributes.edit', $attribute->id) }}" class="btn btn-outline-primary" title="Edit">
                <i class="bi bi-pencil"></i>
            </a>
            <button type="button" class="btn btn-outline-danger" onclick="deleteItem({{ $attribute->id }})" title="Delete">
                <i class="bi bi-trash"></i>
            </button>
        </div>
    </td>
</tr>
@empty
<tr>
    <td colspan="8" class="text-center py-5">
        <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
        <p class="text-muted mt-2">No attributes found</p>
        <a href="{{ route('admin.attributes.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i> Add First Attribute
        </a>
    </td>
</tr>
@endforelse
