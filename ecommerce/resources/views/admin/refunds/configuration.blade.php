@extends('admin.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Refund Configuration</h4>
    <a href="{{ route('admin.refunds.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> All Refunds
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-gear me-2"></i>Refund Settings</h6>
            </div>
            <div class="card-body">
                <form id="configForm" method="POST" action="{{ route('admin.refunds.configuration.update') }}">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="form-label">Refund Policy</label>
                        <div class="form-text mb-2">Configure your store's refund policy settings</div>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="enable_refunds" name="enable_refunds" checked>
                                    <label class="form-check-label" for="enable_refunds">
                                        <i class="bi bi-check-circle text-success me-1"></i> Enable Refund Requests
                                    </label>
                                    <div class="form-text">Allow customers to request refunds</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="auto_approve" name="auto_approve">
                                    <label class="form-check-label" for="auto_approve">
                                        Auto-Approve Refunds
                                    </label>
                                    <div class="form-text">Automatically approve refund requests</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Refund Reasons</label>
                        <div class="form-text mb-2">Select which refund reasons are available to customers</div>
                        
                        <div class="row g-3">
                            @foreach(App\Models\Refund::getReasonOptions() as $value => $label)
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="reason_{{ $value }}" 
                                           name="reasons[]" value="{{ $value }}" checked>
                                    <label class="form-check-label" for="reason_{{ $value }}">
                                        {{ $label }}
                                    </label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Refund Window (Days)</label>
                        <div class="form-text mb-2">Number of days after delivery during which refunds can be requested</div>
                        
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="input-group">
                                    <input type="number" class="form-control" id="refund_window" name="refund_window" 
                                           value="30" min="1" max="365">
                                    <span class="input-group-text">days</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Refund Method</label>
                        <div class="form-text mb-2">How refunds should be processed</div>
                        
                        <select class="form-select" id="refund_method" name="refund_method">
                            <option value="original_payment" selected>Original Payment Method</option>
                            <option value="store_credit">Store Credit</option>
                            <option value="bank_transfer">Bank Transfer</option>
                        </select>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white">
                <h6 class="mb-0">Quick Stats</h6>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted">Total Refunds</span>
                    <span class="fw-medium">{{ \App\Models\Refund::count() }}</span>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted">Pending</span>
                    <span class="fw-medium text-warning">{{ \App\Models\Refund::pending()->count() }}</span>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted">Approved</span>
                    <span class="fw-medium text-info">{{ \App\Models\Refund::approved()->count() }}</span>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted">Rejected</span>
                    <span class="fw-medium text-danger">{{ \App\Models\Refund::rejected()->count() }}</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Processed</span>
                    <span class="fw-medium text-success">{{ \App\Models\Refund::processed()->count() }}</span>
                </div>
            </div>
        </div>
        
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h6 class="mb-0">Help</h6>
            </div>
            <div class="card-body">
                <p class="small text-muted mb-2">Refund Configuration Tips:</p>
                <ul class="small text-muted mb-0">
                    <li>Enable refund requests to allow customers to request refunds</li>
                    <li>Set an appropriate refund window based on your return policy</li>
                    <li>Choose a refund method that works best for your business</li>
                    <li>Review pending refund requests regularly</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Floating Save Button -->
<div class="floating-save-container">
    <a href="{{ route('admin.refunds.index') }}" class="btn btn-secondary floating-reset-btn">
        <i class="bi bi-x-lg me-1"></i> Cancel
    </a>
    <button type="submit" form="configForm" class="btn btn-primary floating-save-btn">
        <i class="bi bi-check-lg me-1"></i> Save Configuration
    </button>
</div>

@push('styles')
<style>
    .content-area {
        padding-bottom: 100px !important;
    }
</style>
@endpush
@endsection
