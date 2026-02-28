@forelse($quotations as $quotation)
<tr>
    <td class="ps-3">
        <input type="checkbox" class="form-check-input quotation-checkbox" value="{{ $quotation->id }}">
    </td>
    <td>
        <a href="{{ route('admin.quotations.show', $quotation) }}" class="text-decoration-none fw-medium">
            {{ $quotation->quotation_number }}
        </a>
        @if($quotation->is_expired && $quotation->status !== 'converted')
            <span class="badge bg-secondary ms-1">Expired</span>
        @endif
    </td>
    <td>
        <div class="fw-medium">{{ $quotation->customer_name }}</div>
        @if($quotation->customer_email)
            <small class="text-muted">{{ $quotation->customer_email }}</small>
        @endif
    </td>
    <td>
        <span class="fw-medium">{{ number_format($quotation->total, 2) }}</span>
    </td>
    <td>
        <span class="badge {{ $quotation->status_badge_class }}">
            {{ ucfirst($quotation->status) }}
        </span>
    </td>
    <td>
        <span class="{{ $quotation->is_expired && $quotation->status !== 'converted' ? 'text-danger' : '' }}">
            {{ $quotation->valid_until->format('M d, Y') }}
        </span>
    </td>
    <td>
        <small class="text-muted">{{ $quotation->created_at->format('M d, Y') }}</small>
    </td>
    <td class="text-end pe-3">
        <div class="dropdown">
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <i class="bi bi-three-dots-vertical"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <a class="dropdown-item" href="{{ route('admin.quotations.show', $quotation) }}">
                        <i class="bi bi-eye me-2"></i> View
                    </a>
                </li>
                @if($quotation->can_edit)
                <li>
                    <a class="dropdown-item" href="{{ route('admin.quotations.edit', $quotation) }}">
                        <i class="bi bi-pencil me-2"></i> Edit
                    </a>
                </li>
                @endif
                <li>
                    <a class="dropdown-item" href="{{ route('admin.quotations.print', $quotation) }}" target="_blank">
                        <i class="bi bi-printer me-2"></i> Print
                    </a>
                </li>
                @if($quotation->can_convert)
                <li><hr class="dropdown-divider"></li>
                <li>
                    <button class="dropdown-item text-success" type="button" 
                            onclick="document.getElementById('convertForm{{ $quotation->id }}').submit()">
                        <i class="bi bi-cart-check me-2"></i> Convert to Order
                    </button>
                    <form id="convertForm{{ $quotation->id }}" action="{{ route('admin.quotations.convert-to-order', $quotation) }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </li>
                @endif
                @if($quotation->status !== 'converted')
                <li><hr class="dropdown-divider"></li>
                <li>
                    <button class="dropdown-item text-danger" type="button" 
                            onclick="if(confirm('Are you sure you want to delete this quotation?')) document.getElementById('deleteForm{{ $quotation->id }}').submit()">
                        <i class="bi bi-trash me-2"></i> Delete
                    </button>
                    <form id="deleteForm{{ $quotation->id }}" action="{{ route('admin.quotations.destroy', $quotation) }}" method="POST" class="d-none">
                        @csrf
                        @method('DELETE')
                    </form>
                </li>
                @endif
            </ul>
        </div>
    </td>
</tr>
@empty
<tr>
    <td colspan="8" class="text-center py-4">
        <div class="text-muted">
            <i class="bi bi-inbox display-6"></i>
            <p class="mt-2">No quotations found.</p>
        </div>
    </td>
</tr>
@endforelse
