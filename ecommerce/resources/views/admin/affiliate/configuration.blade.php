@extends('admin.layouts.app')

@section('title', 'Affiliate Configuration')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Affiliate Configuration</h1>
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

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Affiliate Settings</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.affiliate.configuration') }}" method="POST">
                @csrf
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Enable Affiliate System</label>
                        <select class="form-select" name="affiliate_enabled">
                            <option value="1" {{ $settings['affiliate_enabled'] ? 'selected' : '' }}>Yes</option>
                            <option value="0" {{ !$settings['affiliate_enabled'] ? 'selected' : '' }}>No</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Default Commission Rate (%)</label>
                        <input type="number" class="form-control" name="default_commission_rate" step="0.01" min="0" max="100" value="{{ $settings['default_commission_rate'] }}">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Minimum Withdrawal Amount ($)</label>
                        <input type="number" class="form-control" name="min_withdrawal_amount" step="0.01" min="0" value="{{ $settings['min_withdrawal_amount'] }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Cookie Lifetime (Days)</label>
                        <input type="number" class="form-control" name="cookie_lifetime" min="1" max="365" value="{{ $settings['cookie_lifetime'] }}">
                        <small class="text-muted">How long affiliate tracking cookies last</small>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Affiliate Registration</label>
                        <select class="form-select" name="affiliate_registration">
                            <option value="auto" {{ $settings['affiliate_registration'] === 'auto' ? 'selected' : '' }}>Auto Approve</option>
                            <option value="manual" {{ $settings['affiliate_registration'] === 'manual' ? 'selected' : '' }}>Manual Approval</option>
                        </select>
                        <small class="text-muted">Auto approve will automatically approve new affiliate registrations</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Commission Type</label>
                        <select class="form-select" name="commission_type">
                            <option value="percentage" {{ $settings['commission_type'] === 'percentage' ? 'selected' : '' }}>Percentage</option>
                            <option value="fixed" {{ $settings['commission_type'] === 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                        </select>
                    </div>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-2"></i>Save Configuration
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
