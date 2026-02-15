@extends('admin.layouts.app')

@section('title', 'General Settings')

@section('content')
<div class="mb-4">
    <h4 class="mb-0">General Settings</h4>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.settings.general.update') }}" method="POST">
            @csrf
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Site Name</label>
                    <input type="text" name="site_name" class="form-control" value="{{ old('site_name', $settings['site_name'] ?? '') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Site Tagline</label>
                    <input type="text" name="site_tagline" class="form-control" value="{{ old('site_tagline', $settings['site_tagline'] ?? '') }}">
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Site Email</label>
                    <input type="email" name="site_email" class="form-control" value="{{ old('site_email', $settings['site_email'] ?? '') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Site Phone</label>
                    <input type="text" name="site_phone" class="form-control" value="{{ old('site_phone', $settings['site_phone'] ?? '') }}">
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Site Address</label>
                <textarea name="site_address" class="form-control" rows="2">{{ old('site_address', $settings['site_address'] ?? '') }}</textarea>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Currency</label>
                    <select name="currency" class="form-select">
                        <option value="BDT" {{ ($settings['currency'] ?? 'BDT') === 'BDT' ? 'selected' : '' }}>BDT (৳)</option>
                        <option value="USD" {{ ($settings['currency'] ?? '') === 'USD' ? 'selected' : '' }}>USD ($)</option>
                        <option value="EUR" {{ ($settings['currency'] ?? '') === 'EUR' ? 'selected' : '' }}>EUR (€)</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Timezone</label>
                    <select name="timezone" class="form-select">
                        <option value="Asia/Dhaka" {{ ($settings['timezone'] ?? 'Asia/Dhaka') === 'Asia/Dhaka' ? 'selected' : '' }}>Asia/Dhaka</option>
                        <option value="UTC" {{ ($settings['timezone'] ?? '') === 'UTC' ? 'selected' : '' }}>UTC</option>
                    </select>
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Site Logo URL</label>
                <input type="text" name="site_logo" class="form-control" value="{{ old('site_logo', $settings['site_logo'] ?? '') }}">
            </div>
            
            <div class="mb-3">
                <label class="form-label">Site Favicon URL</label>
                <input type="text" name="site_favicon" class="form-control" value="{{ old('site_favicon', $settings['site_favicon'] ?? '') }}">
            </div>
            
            <div class="text-end">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i> Save Settings
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
