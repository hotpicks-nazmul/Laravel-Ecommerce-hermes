@extends('admin.layouts.app')

@section('title', 'Edit Warehouse')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="bi bi-building me-2"></i>Edit Warehouse</h4>
    <a href="{{ route('admin.warehouses.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back to Warehouses
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <!-- Basic Information -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Basic Information</h6>
            </div>
            <div class="card-body">
                <form id="warehouseForm" method="POST" action="{{ route('admin.warehouses.update', $warehouse->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Warehouse Name <span class="text-danger">*</span></label>
                            <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $warehouse->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="code" class="form-label">Warehouse Code</label>
                            <input type="text" id="code" name="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code', $warehouse->code) }}" placeholder="e.g., WH-001">
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Unique identifier for this warehouse</div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Location Information -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-geo-alt me-2"></i>Location Information</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="address" class="form-label">Address <span class="text-danger">*</span></label>
                    <textarea id="address" name="address" form="warehouseForm" class="form-control @error('address') is-invalid @enderror" rows="2" required>{{ old('address', $warehouse->address) }}</textarea>
                    @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="city" class="form-label">City</label>
                        <input type="text" id="city" name="city" form="warehouseForm" class="form-control @error('city') is-invalid @enderror" value="{{ old('city', $warehouse->city) }}">
                        @error('city')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="state" class="form-label">State/Province</label>
                        <input type="text" id="state" name="state" form="warehouseForm" class="form-control @error('state') is-invalid @enderror" value="{{ old('state', $warehouse->state) }}">
                        @error('state')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="postcode" class="form-label">Postal Code</label>
                        <input type="text" id="postcode" name="postcode" form="warehouseForm" class="form-control @error('postcode') is-invalid @enderror" value="{{ old('postcode', $warehouse->postcode) }}">
                        @error('postcode')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="country" class="form-label">Country</label>
                        <input type="text" id="country" name="country" form="warehouseForm" class="form-control @error('country') is-invalid @enderror" value="{{ old('country', $warehouse->country) }}">
                        @error('country')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="latitude" class="form-label">Latitude</label>
                        <input type="text" id="latitude" name="latitude" form="warehouseForm" class="form-control @error('latitude') is-invalid @enderror" value="{{ old('latitude', $warehouse->latitude) }}" placeholder="-90 to 90">
                        @error('latitude')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="longitude" class="form-label">Longitude</label>
                        <input type="text" id="longitude" name="longitude" form="warehouseForm" class="form-control @error('longitude') is-invalid @enderror" value="{{ old('longitude', $warehouse->longitude) }}" placeholder="-180 to 180">
                        @error('longitude')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-person-lines-fill me-2"></i>Contact Information</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="phone" class="form-label">Phone Number</label>
                        <input type="text" id="phone" name="phone" form="warehouseForm" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $warehouse->phone) }}">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" id="email" name="email" form="warehouseForm" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $warehouse->email) }}">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Information -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-info-lg me-2"></i>Additional Information</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="opening_hours" class="form-label">Opening Hours</label>
                    <textarea id="opening_hours" name="opening_hours" form="warehouseForm" class="form-control @error('opening_hours') is-invalid @enderror" rows="2" placeholder="e.g., Mon-Fri: 9AM-6PM">{{ old('opening_hours', $warehouse->opening_hours) }}</textarea>
                    @error('opening_hours')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="notes" class="form-label">Notes</label>
                    <textarea id="notes" name="notes" form="warehouseForm" class="form-control @error('notes') is-invalid @enderror" rows="2">{{ old('notes', $warehouse->notes) }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Status -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white">
                <h6 class="mb-0">Status</h6>
            </div>
            <div class="card-body">
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" form="warehouseForm" {{ old('is_active', $warehouse->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">
                        <i class="bi bi-check-circle text-success me-1"></i> Active
                    </label>
                    <div class="form-text">Enable or disable this warehouse</div>
                </div>
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="is_primary" name="is_primary" form="warehouseForm" {{ old('is_primary', $warehouse->is_primary) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_primary">
                        <i class="bi bi-star text-warning me-1"></i> Primary Warehouse
                    </label>
                    <div class="form-text">Set as the main warehouse</div>
                </div>
            </div>
        </div>

        <!-- Sort Order -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white">
                <h6 class="mb-0">Display Order</h6>
            </div>
            <div class="card-body">
                <label for="sort_order" class="form-label">Sort Order</label>
                <input type="number" id="sort_order" name="sort_order" form="warehouseForm" class="form-control" value="{{ old('sort_order', $warehouse->sort_order) }}" min="0">
                <div class="form-text">Lower numbers appear first</div>
            </div>
        </div>

        <!-- Service Areas -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-geo-alt me-2"></i>Service Areas</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Cities Served</label>
                    <select name="city_ids[]" form="warehouseForm" class="form-select" multiple style="min-height: 120px;">
                        @foreach(\App\Models\City::active()->ordered()->get() as $city)
                            <option value="{{ $city->id }}" {{ in_array($city->id, $warehouse->cities->pluck('id')->toArray()) ? 'selected' : '' }}>
                                {{ $city->name }} ({{ $city->countryRelation->name ?? $city->country }})
                            </option>
                        @endforeach
                    </select>
                    <div class="form-text">Hold Ctrl/Cmd to select multiple cities</div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Areas Served</label>
                    <select name="area_ids[]" form="warehouseForm" class="form-select" multiple style="min-height: 120px;">
                        @foreach(\App\Models\Area::active()->ordered()->get() as $area)
                            <option value="{{ $area->id }}" {{ in_array($area->id, $warehouse->areas->pluck('id')->toArray()) ? 'selected' : '' }}>
                                {{ $area->name }} ({{ $area->city->name ?? 'N/A' }})
                            </option>
                        @endforeach
                    </select>
                    <div class="form-text">Hold Ctrl/Cmd to select multiple areas</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Floating Buttons -->
<div class="floating-save-container">
    <a href="{{ route('admin.warehouses.index') }}" class="btn btn-secondary floating-reset-btn text-white">
        <i class="bi bi-x-lg me-1"></i> Cancel
    </a>
    <form action="{{ route('admin.warehouses.destroy', $warehouse->id) }}" method="POST" class="d-inline">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-outline-danger floating-reset-btn" onclick="return confirm('Are you sure you want to delete this warehouse?')">
            <i class="bi bi-trash me-1"></i> Delete
        </button>
    </form>
    <button type="submit" form="warehouseForm" class="btn btn-primary floating-save-btn">
        <i class="bi bi-check-lg me-1"></i> Update Warehouse
    </button>
</div>
@endsection

@push('styles')
<style>
/* Add padding at bottom to prevent floating button overlap (Preference.md #2) */
.content-area.has-floating-save {
    padding-bottom: 100px;
}

/* Global Card Styles - matching reference page style */
.card {
    border: none;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}
.card-header.bg-white {
    background-color: #fff !important;
}
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-scroll to first error field
        @if($errors->any())
            var firstErrorField = document.querySelector('.is-invalid');
            if (firstErrorField) {
                setTimeout(function() {
                    firstErrorField.scrollIntoView({ 
                        behavior: 'smooth', 
                        block: 'center' 
                    });
                    firstErrorField.focus();
                }, 100);
            }
        @endif
    });
</script>
@endpush
