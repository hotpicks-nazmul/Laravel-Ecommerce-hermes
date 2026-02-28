@extends('admin.layouts.app')

@section('title', 'Add Pick-up Point')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0"><i class="bi bi-shop me-2"></i>Add Pick-up Point</h4>
            <a href="{{ route('admin.pickup-points.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back to List
            </a>
        </div>

        <form id="pickupPointForm" action="{{ route('admin.pickup-points.store') }}" method="POST">
            @csrf
            
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Basic Information</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Location Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Code <small class="text-muted">(Optional)</small></label>
                            <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code') }}">
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Unique identifier for this location</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone <span class="text-danger">*</span></label>
                            <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" required>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email <small class="text-muted">(Optional)</small></label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-geo-alt me-2"></i>Address Information</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Address <span class="text-danger">*</span></label>
                            <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="2" required>{{ old('address') }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">City <span class="text-danger">*</span></label>
                            <input type="text" name="city" class="form-control @error('city') is-invalid @enderror" value="{{ old('city') }}" required>
                            @error('city')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">State/Province <small class="text-muted">(Optional)</small></label>
                            <input type="text" name="state" class="form-control @error('state') is-invalid @enderror" value="{{ old('state') }}">
                            @error('state')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Postcode <small class="text-muted">(Optional)</small></label>
                            <input type="text" name="postcode" class="form-control @error('postcode') is-invalid @enderror" value="{{ old('postcode') }}">
                            @error('postcode')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Country <span class="text-danger">*</span></label>
                            <input type="text" name="country" class="form-control @error('country') is-invalid @enderror" value="{{ old('country', 'Bangladesh') }}" required>
                            @error('country')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-clock me-2"></i>Additional Information</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Opening Hours <small class="text-muted">(Optional)</small></label>
                            <textarea name="opening_hours" class="form-control @error('opening_hours') is-invalid @enderror" rows="3" placeholder="e.g.&#10;Mon-Fri: 9:00 AM - 6:00 PM&#10;Sat: 10:00 AM - 4:00 PM&#10;Sun: Closed">{{ old('opening_hours') }}</textarea>
                            @error('opening_hours')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">Notes <small class="text-muted">(Optional)</small></label>
                            <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="2">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="col-lg-4">
        <!-- Settings Card -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-gear me-2"></i>Settings</h6>
            </div>
            <div class="card-body">
                <form id="settingsForm">
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="is_active" class="form-check-input" id="isActive" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="isActive">Active</label>
                        </div>
                        <small class="text-muted">Inactive locations won't be available for selection</small>
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Sort Order</label>
                        <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', 0) }}" min="0">
                        <small class="text-muted">Lower numbers appear first</small>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tips Card -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-lightbulb me-2"></i>Tips</h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Use a clear, recognizable name</li>
                    <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Provide accurate address details</li>
                    <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Include opening hours if applicable</li>
                    <li><i class="bi bi-check-circle text-success me-2"></i>Mark inactive when temporarily closed</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Floating Save Buttons -->
<div class="floating-save-container">
    <a href="{{ route('admin.pickup-points.index') }}" class="btn btn-secondary floating-reset-btn">
        <i class="bi bi-x-lg me-1"></i> Cancel
    </a>
    <button type="submit" form="pickupPointForm" class="btn btn-primary floating-save-btn">
        <i class="bi bi-check-lg me-1"></i> Create Pick-up Point
    </button>
</div>
@endsection

@push('styles')
<style>
    .content-area {
        padding-bottom: 100px !important;
    }
</style>
@endpush

@push('scripts')
<script>
    // Sync settings form with main form
    document.getElementById('settingsForm').addEventListener('change', function() {
        const mainForm = document.getElementById('pickupPointForm');
        const settingsForm = document.getElementById('settingsForm');
        
        // Update or create hidden inputs in main form
        ['is_active', 'sort_order'].forEach(field => {
            let input = mainForm.querySelector(`input[name="${field}"]`);
            const settingsInput = settingsForm.querySelector(`[name="${field}"]`);
            
            if (!input) {
                input = document.createElement('input');
                input.type = 'hidden';
                input.name = field;
                mainForm.appendChild(input);
            }
            
            if (settingsInput.type === 'checkbox') {
                input.value = settingsInput.checked ? '1' : '0';
            } else {
                input.value = settingsInput.value;
            }
        });
    });
    
    // Trigger initial sync
    document.getElementById('settingsForm').dispatchEvent(new Event('change'));
</script>
@endpush
