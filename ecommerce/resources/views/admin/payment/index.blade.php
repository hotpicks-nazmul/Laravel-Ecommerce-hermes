@extends('admin.layouts.app')

@section('title', 'Payment Gateways')

@section('content')
<div class="mb-4">
    <h4 class="mb-0">Payment Gateways</h4>
</div>

<div class="row">
    <!-- bKash -->
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <img src="{{ asset('images/payments/bkash.png') }}" alt="bKash" height="30">
                    bKash
                </h5>
                <form action="{{ route('admin.payment.toggle', 'bkash') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-sm {{ ($gateways['bkash']['enabled'] ?? false) ? 'btn-success' : 'btn-outline-secondary' }}">
                        {{ ($gateways['bkash']['enabled'] ?? false) ? 'Enabled' : 'Disabled' }}
                    </button>
                </form>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.payment.bkash.update') }}" method="POST" id="bkash-form">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">API Key</label>
                        <input type="text" name="bkash_api_key" class="form-control" value="{{ $gateways['bkash']['api_key'] ?? '' }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">API Secret</label>
                        <input type="password" name="bkash_api_secret" class="form-control" value="{{ $gateways['bkash']['api_secret'] ?? '' }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Merchant Number</label>
                        <input type="text" name="bkash_merchant_number" class="form-control" value="{{ $gateways['bkash']['merchant_number'] ?? '' }}">
                    </div>
                    <div class="form-check mb-3">
                        <input type="checkbox" name="bkash_sandbox" class="form-check-input" id="bkashSandbox" {{ ($gateways['bkash']['sandbox'] ?? false) ? 'checked' : '' }}>
                        <label class="form-check-label" for="bkashSandbox">Sandbox Mode</label>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- SSLCommerz -->
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <img src="{{ asset('images/payments/sslcommerz.png') }}" alt="SSLCommerz" height="30">
                    SSLCommerz
                </h5>
                <form action="{{ route('admin.payment.toggle', 'sslcommerz') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-sm {{ ($gateways['sslcommerz']['enabled'] ?? false) ? 'btn-success' : 'btn-outline-secondary' }}">
                        {{ ($gateways['sslcommerz']['enabled'] ?? false) ? 'Enabled' : 'Disabled' }}
                    </button>
                </form>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.payment.sslcommerz.update') }}" method="POST" id="sslcommerz-form">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Store ID</label>
                        <input type="text" name="sslcommerz_store_id" class="form-control" value="{{ $gateways['sslcommerz']['store_id'] ?? '' }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Store Password</label>
                        <input type="password" name="sslcommerz_store_password" class="form-control" value="{{ $gateways['sslcommerz']['store_password'] ?? '' }}">
                    </div>
                    <div class="form-check mb-3">
                        <input type="checkbox" name="sslcommerz_sandbox" class="form-check-input" id="sslSandbox" {{ ($gateways['sslcommerz']['sandbox'] ?? false) ? 'checked' : '' }}>
                        <label class="form-check-label" for="sslSandbox">Sandbox Mode</label>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Nagad -->
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <img src="{{ asset('images/payments/nagad.png') }}" alt="Nagad" height="30">
                    Nagad
                </h5>
                <form action="{{ route('admin.payment.toggle', 'nagad') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-sm {{ ($gateways['nagad']['enabled'] ?? false) ? 'btn-success' : 'btn-outline-secondary' }}">
                        {{ ($gateways['nagad']['enabled'] ?? false) ? 'Enabled' : 'Disabled' }}
                    </button>
                </form>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.payment.nagad.update') }}" method="POST" id="nagad-form">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Merchant ID</label>
                        <input type="text" name="nagad_merchant_id" class="form-control" value="{{ $gateways['nagad']['merchant_id'] ?? '' }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">API Key</label>
                        <input type="password" name="nagad_api_key" class="form-control" value="{{ $gateways['nagad']['api_key'] ?? '' }}">
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Cash on Delivery -->
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-cash-coin me-2"></i>
                    Cash on Delivery
                </h5>
                <form action="{{ route('admin.payment.toggle', 'cod') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-sm {{ ($gateways['cod']['enabled'] ?? true) ? 'btn-success' : 'btn-outline-secondary' }}">
                        {{ ($gateways['cod']['enabled'] ?? true) ? 'Enabled' : 'Disabled' }}
                    </button>
                </form>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.payment.cod.update') }}" method="POST" id="cod-form">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">COD Instructions</label>
                        <textarea name="cod_instructions" class="form-control" rows="3">{{ $gateways['cod']['instructions'] ?? 'Pay with cash upon delivery.' }}</textarea>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Floating Save Buttons for Payment Gateways -->
<div class="floating-save-container">
    <div class="btn-group">
        <button type="submit" form="bkash-form" class="btn btn-primary floating-save-btn">
            <i class="bi bi-check-lg me-1"></i> bKash
        </button>
        <button type="submit" form="sslcommerz-form" class="btn btn-primary floating-save-btn">
            <i class="bi bi-check-lg me-1"></i> SSLCommerz
        </button>
        <button type="submit" form="nagad-form" class="btn btn-primary floating-save-btn">
            <i class="bi bi-check-lg me-1"></i> Nagad
        </button>
        <button type="submit" form="cod-form" class="btn btn-primary floating-save-btn">
            <i class="bi bi-check-lg me-1"></i> COD
        </button>
    </div>
</div>
@endsection
