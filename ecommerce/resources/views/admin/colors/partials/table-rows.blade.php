@forelse($colors as $color)
@php
    $search = isset($search) ? $search : request('search');
    $isMatch = $search && (
        stripos($color->name, $search) !== false || 
        stripos($color->code, $search) !== false
    );
@endphp
<tr data-id="{{ $color->id }}" class="{{ $isMatch ? 'table-warning' : '' }}">
    <td>
        <input type="checkbox" class="form-check-input item-checkbox" value="{{ $color->id }}">
    </td>
    <td>
        <span class="color-swatch" style="background-color: {{ $color->hex_code }};" title="{{ $color->name }}"></span>
    </td>
    <td>
        <a href="{{ route('admin.colors.edit', $color->id) }}" class="text-decoration-none fw-medium">
            {{ $color->name }}
        </a>
        @if($color->description)
        <br><small class="text-muted">{{ Str::limit($color->description, 40) }}</small>
        @endif
    </td>
    <td><code>{{ $color->code }}</code></td>
    <td>
        <span class="badge font-monospace" style="background-color: {{ $color->hex_code }}; color: {{ $color->contrast_color }};">
            {{ $color->hex_code }}
        </span>
    </td>
    <td>{{ $color->display_order }}</td>
    <td>
        <span class="badge bg-light text-dark">{{ $color->products_count }} products</span>
    </td>
    <td>
        <button type="button" class="btn btn-sm {{ $color->is_active ? 'btn-success' : 'btn-outline-secondary' }}" 
                onclick="toggleStatus({{ $color->id }})" title="Toggle Status">
            <i class="bi {{ $color->is_active ? 'bi-check-circle' : 'bi-x-circle' }}"></i>
            {{ $color->is_active ? 'Active' : 'Inactive' }}
        </button>
    </td>
    <td>
        <div class="btn-group btn-group-sm">
            <a href="{{ route('admin.colors.edit', $color->id) }}" class="btn btn-outline-primary" title="Edit">
                <i class="bi bi-pencil"></i>
            </a>
            <button type="button" class="btn btn-outline-danger" onclick="deleteItem({{ $color->id }})" title="Delete">
                <i class="bi bi-trash"></i>
            </button>
        </div>
    </td>
</tr>
@empty
<tr>
    <td colspan="9" class="text-center py-5">
        <i class="bi bi-palette text-muted" style="font-size: 3rem;"></i>
        <p class="text-muted mt-2">No colors found</p>
        <a href="{{ route('admin.colors.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i> Add First Color
        </a>
    </td>
</tr>
@endforelse
