@extends('admin.layouts.app')

@section('title', 'Edit Affiliate Link')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Edit Affiliate Link</h1>
        <a href="{{ route('admin.affiliate.links.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back
        </a>
    </div>

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-circle me-2"></i>
        <ul class="mb-0">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Link URL</h5>
        </div>
        <div class="card-body">
            <div class="input-group">
                <input type="text" class="form-control" value="{{ $link->full_url }}" readonly id="linkUrl">
                <button class="btn btn-outline-primary" type="button" onclick="copyLink()">
                    <i class="bi bi-clipboard me-1"></i>Copy
                </button>
            </div>
            <small class="text-muted">This is the URL affiliates should share to track clicks and conversions.</small>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Link Details</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.affiliate.links.update', $link->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Link Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" value="{{ old('name', $link->name) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Affiliate <span class="text-danger">*</span></label>
                        <select class="form-select" name="affiliate_id" required>
                            <option value="">Select Affiliate</option>
                            @foreach($affiliates as $affiliate)
                            <option value="{{ $affiliate->id }}" {{ old('affiliate_id', $link->affiliate_id) == $affiliate->id ? 'selected' : '' }}>
                                {{ $affiliate->user->name ?? 'Unknown' }} ({{ $affiliate->affiliate_code }})
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Product</label>
                        <select class="form-select" name="product_id">
                            <option value="">Select Product (Optional)</option>
                            @foreach($products as $product)
                            <option value="{{ $product->id }}" {{ old('product_id', $link->product_id) == $product->id ? 'selected' : '' }}>
                                {{ $product->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Affiliate Code</label>
                        <input type="text" class="form-control" name="affiliate_code" value="{{ old('affiliate_code', $link->affiliate_code) }}" readonly>
                        <small class="text-muted">Code cannot be changed after creation</small>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Target URL</label>
                        <input type="url" class="form-control" name="target_url" value="{{ old('target_url', $link->target_url) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status">
                            <option value="active" {{ old('status', $link->status) === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $link->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" name="description" rows="3">{{ old('description', $link->description) }}</textarea>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="mb-2">Statistics</h6>
                                <p class="mb-1"><strong>Clicks:</strong> {{ number_format($link->clicks) }}</p>
                                <p class="mb-1"><strong>Conversions:</strong> {{ number_format($link->conversions) }}</p>
                                <p class="mb-0"><strong>Conversion Rate:</strong> {{ $link->clicks > 0 ? number_format(($link->conversions / $link->clicks) * 100, 2) : 0 }}%</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-end">
                    <button type="reset" class="btn btn-secondary me-2">
                        <i class="bi bi-x-circle me-2"></i>Reset
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-2"></i>Update Link
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function copyLink() {
        var copyText = document.getElementById('linkUrl');
        copyText.select();
        copyText.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(copyText.value);
        alert('Link copied to clipboard!');
    }
</script>
@endpush
