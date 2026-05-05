@extends('admin.layouts.app')

@section('title', 'Add Warehouse')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="bi bi-building me-2"></i>Add Warehouse</h4>
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
                <form id="warehouseForm" method="POST" action="{{ route('admin.warehouses.store') }}">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Warehouse Name <span class="text-danger">*</span></label>
                            <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="code" class="form-label">Warehouse Code</label>
                            <input type="text" id="code" class="form-control @error('code') is-invalid @enderror" value="{{ $autoCode }}" disabled>
                            <input type="hidden" name="code" form="warehouseForm" value="{{ $autoCode }}">
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Auto-generated unique code</div>
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
                    <textarea id="address" name="address" form="warehouseForm" class="form-control @error('address') is-invalid @enderror" rows="2" required></textarea>
                    @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="state_select" class="form-label">State/Province <span class="text-danger">*</span></label>
                        <select id="state_select" class="form-select @error('state') is-invalid @enderror">
                            <option value="">Select State</option>
                        </select>
                        <input type="hidden" name="state" id="state" form="warehouseForm">
                        @error('state')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="city_select" class="form-label">City <span class="text-danger">*</span></label>
                        <select id="city_select" class="form-select @error('city') is-invalid @enderror" disabled>
                            <option value="">Select a state first</option>
                        </select>
                        <input type="hidden" name="city" id="city" form="warehouseForm">
                        @error('city')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="area_select" class="form-label">Area <span class="text-danger">*</span></label>
                        <select id="area_select" class="form-select @error('area') is-invalid @enderror" disabled required>
                            <option value="">Select a city first</option>
                        </select>
                        @error('area')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <input type="hidden" name="area" id="area" form="warehouseForm">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="postcode" class="form-label">Postal Code</label>
                        <input type="text" id="postcode" name="postcode" form="warehouseForm" class="form-control @error('postcode') is-invalid @enderror">
                        @error('postcode')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="country" class="form-label">Country</label>
                        @if($checkoutMode === 'local' && $defaultCountry)
                            <input type="text" id="country" class="form-control" value="{{ $defaultCountry }}" disabled>
                            <input type="hidden" name="country" form="warehouseForm" value="{{ $defaultCountry }}">
                        @else
                            <select id="country" name="country" form="warehouseForm" class="form-select @error('country') is-invalid @enderror">
                                <option value="">Select Country</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country->name }}" {{ old('country') === $country->name ? 'selected' : '' }}>{{ $country->name }}</option>
                                @endforeach
                            </select>
                        @endif
                        @error('country')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="latitude" class="form-label">Latitude</label>
                        <input type="text" id="latitude" name="latitude" form="warehouseForm" class="form-control @error('latitude') is-invalid @enderror" placeholder="-90 to 90">
                        @error('latitude')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="longitude" class="form-label">Longitude</label>
                        <input type="text" id="longitude" name="longitude" form="warehouseForm" class="form-control @error('longitude') is-invalid @enderror" placeholder="-180 to 180">
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
                        <input type="text" id="phone" name="phone" form="warehouseForm" class="form-control @error('phone') is-invalid @enderror">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" id="email" name="email" form="warehouseForm" class="form-control @error('email') is-invalid @enderror">
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
                    <textarea id="opening_hours" name="opening_hours" form="warehouseForm" class="form-control @error('opening_hours') is-invalid @enderror" rows="2" placeholder="e.g., Mon-Fri: 9AM-6PM"></textarea>
                    @error('opening_hours')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="notes" class="form-label">Notes</label>
                    <textarea id="notes" name="notes" form="warehouseForm" class="form-control @error('notes') is-invalid @enderror" rows="2"></textarea>
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
                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" form="warehouseForm" value="1" checked>
                    <label class="form-check-label" for="is_active">
                        <i class="bi bi-check-circle text-success me-1"></i> Active
                    </label>
                    <div class="form-text">Enable or disable this warehouse</div>
                    @error('is_active')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="is_primary" name="is_primary" form="warehouseForm" value="1">
                    <label class="form-check-label" for="is_primary">
                        <i class="bi bi-star text-warning me-1"></i> Primary Warehouse
                    </label>
                    <div class="form-text">Set as the main warehouse</div>
                    @error('is_primary')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
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
                <input type="number" id="sort_order" name="sort_order" form="warehouseForm" class="form-control @error('sort_order') is-invalid @enderror" value="0" min="0">
                @error('sort_order')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">Lower numbers appear first</div>
            </div>
        </div>


    </div>
</div>

<!-- Floating Buttons -->
<div class="floating-save-container">
    <a href="{{ route('admin.warehouses.index') }}" class="btn btn-secondary floating-reset-btn text-white">
        <i class="bi bi-x-lg me-1"></i> Cancel
    </a>
    <button type="submit" form="warehouseForm" class="btn btn-primary floating-save-btn">
        <i class="bi bi-check-lg me-1"></i> Create Warehouse
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

        // Cascading State/City/Area dropdowns
        const stateSelect = document.getElementById('state_select');
        const citySelect = document.getElementById('city_select');
        const areaSelect = document.getElementById('area_select');
        const stateHidden = document.getElementById('state');
        const cityHidden = document.getElementById('city');
        const areaHidden = document.getElementById('area');

        // Load states on page load
        fetch('/checkout/get-states')
            .then(res => res.json())
            .then(data => {
                if (data.success && data.states) {
                    stateSelect.innerHTML = '<option value="">Select State</option>';
                    data.states.forEach(s => {
                        const opt = document.createElement('option');
                        opt.value = s.id;
                        opt.textContent = s.name;
                        stateSelect.appendChild(opt);
                    });
                }
            })
            .catch(e => console.error('Error loading states:', e));

        // On state change, load cities
        stateSelect.addEventListener('change', function() {
            const stateId = this.value;
            const stateName = this.options[this.selectedIndex]?.text || '';
            stateHidden.value = stateName;

            // Reset city and area
            citySelect.innerHTML = '<option value="">Select a state first</option>';
            citySelect.disabled = true;
            cityHidden.value = '';
            areaSelect.innerHTML = '<option value="">Select a city first</option>';
            areaSelect.disabled = true;
            areaHidden.value = '';

            if (stateId) {
                citySelect.innerHTML = '<option value="">Loading...</option>';
                fetch(`/checkout/get-cities?state_id=${stateId}`)
                    .then(res => res.json())
                    .then(data => {
                        citySelect.innerHTML = '<option value="">Select City</option>';
                        if (data.success && data.cities) {
                            data.cities.forEach(c => {
                                const opt = document.createElement('option');
                                opt.value = c.id;
                                opt.textContent = c.name;
                                citySelect.appendChild(opt);
                            });
                            citySelect.disabled = false;
                        } else {
                            citySelect.innerHTML = '<option value="">No cities available</option>';
                        }
                    })
                    .catch(e => console.error('Error loading cities:', e));
            }
        });

        // On city change, load areas
        citySelect.addEventListener('change', function() {
            const cityId = this.value;
            const cityName = this.options[this.selectedIndex]?.text || '';
            cityHidden.value = cityName;

            // Reset area
            areaSelect.innerHTML = '<option value="">Select a city first</option>';
            areaSelect.disabled = true;
            areaHidden.value = '';

            if (cityId) {
                areaSelect.innerHTML = '<option value="">Loading...</option>';
                fetch(`/checkout/get-areas?city_id=${cityId}`)
                    .then(res => res.json())
                    .then(data => {
                        areaSelect.innerHTML = '<option value="">Select Area</option>';
                        if (data.success && data.areas) {
                            data.areas.forEach(a => {
                                const opt = document.createElement('option');
                                opt.value = a.id;
                                opt.textContent = a.name;
                                areaSelect.appendChild(opt);
                            });
                            areaSelect.disabled = false;
                        } else {
                            areaSelect.innerHTML = '<option value="">No areas available</option>';
                        }
                    })
                    .catch(e => console.error('Error loading areas:', e));
            }
        });

        // On area change, store the name
        areaSelect.addEventListener('change', function() {
            const areaName = this.options[this.selectedIndex]?.text || '';
            areaHidden.value = areaName;
        });
    });
</script>
@endpush
