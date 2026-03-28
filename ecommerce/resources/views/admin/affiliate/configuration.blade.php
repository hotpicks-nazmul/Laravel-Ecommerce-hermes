@extends('admin.layouts.app')

@section('title', 'Affiliate Configuration')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Affiliate Configuration</h4>
    <a href="{{ route('admin.affiliate.users.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back to Affiliates
    </a>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

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

<form id="settingsForm" method="POST" action="{{ route('admin.affiliate.configuration') }}">
    @csrf
    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-gear me-2"></i>Affiliate Settings</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Enable Affiliate System</label>
                            <select class="form-select" name="affiliate_enabled" form="settingsForm">
                                <option value="1" {{ $settings['affiliate_enabled'] ? 'selected' : '' }}>Yes</option>
                                <option value="0" {{ !$settings['affiliate_enabled'] ? 'selected' : '' }}>No</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Default Commission Rate (%) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="default_commission_rate" form="settingsForm" step="0.01" min="0" max="100" value="{{ $settings['default_commission_rate'] }}" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Minimum Withdrawal Amount ($) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="min_withdrawal_amount" form="settingsForm" step="0.01" min="0" value="{{ $settings['min_withdrawal_amount'] }}" required>
                            <div class="form-text">Minimum amount affiliates can withdraw</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Cookie Lifetime (Days) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="cookie_lifetime" form="settingsForm" min="1" max="365" value="{{ $settings['cookie_lifetime'] }}" required>
                            <div class="form-text">How long affiliate tracking cookies last</div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Affiliate Registration</label>
                            <select class="form-select" name="affiliate_registration" form="settingsForm">
                                <option value="auto" {{ $settings['affiliate_registration'] === 'auto' ? 'selected' : '' }}>Auto Approve</option>
                                <option value="manual" {{ $settings['affiliate_registration'] === 'manual' ? 'selected' : '' }}>Manual Approval</option>
                            </select>
                            <div class="form-text">Auto approve will automatically approve new affiliate registrations</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Commission Type</label>
                            <select class="form-select" name="commission_type" form="settingsForm">
                                <option value="percentage" {{ $settings['commission_type'] === 'percentage' ? 'selected' : '' }}>Percentage</option>
                                <option value="fixed" {{ $settings['commission_type'] === 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white">
                    <h6 class="mb-0">Quick Info</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-2">Affiliate system allows users to earn commission by referring customers.</p>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">System Status:</span>
                        <span class="badge bg-{{ $settings['affiliate_enabled'] ? 'success' : 'secondary' }}">
                            {{ $settings['affiliate_enabled'] ? 'Enabled' : 'Disabled' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<div class="floating-save-container">
    <a href="{{ route('admin.affiliate.users.index') }}" class="btn btn-secondary floating-reset-btn">
        <i class="bi bi-x-lg me-1"></i> Cancel
    </a>
    <button type="submit" form="settingsForm" class="btn btn-primary floating-save-btn">
        <i class="bi bi-check-lg me-1"></i> Save Configuration
    </button>
</div>
@endsection
