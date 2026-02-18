@extends('admin.layouts.app')

@section('title', 'Social Login Settings')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 py-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="mb-1 fw-bold">
                            <i class="bi bi-google text-primary me-2"></i> Social Login Settings
                        </h4>
                        <p class="text-muted mb-0 small">Configure Google and Facebook social login</p>
                    </div>
                    <a href="{{ route('admin.settings.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left me-1"></i> Back to Settings
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<form action="{{ route('admin.settings.social-login.update') }}" method="POST" id="social-login-form">
    @csrf
    @method('PUT')

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row">
        <!-- Google Settings -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 d-flex align-items-center">
                    <img src="https://www.google.com/favicon.ico" alt="Google" class="me-2" style="width: 24px;">
                    <h5 class="mb-0 fw-semibold">Google Login</h5>
                </div>
                <div class="card-body">
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="google_enabled" id="google_enabled" value="1" {{ $settings['google_enabled'] ?? '0' === '1' ? 'checked' : '' }}>
                        <label class="form-check-label fw-medium" for="google_enabled">Enable Google Login</label>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">Google Client ID</label>
                        <input type="text" name="google_client_id" class="form-control" value="{{ $settings['google_client_id'] ?? '' }}" placeholder="Enter Google Client ID">
                        <div class="form-text">Get your Client ID from <a href="https://console.cloud.google.com/apis/credentials" target="_blank">Google Cloud Console</a></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">Google Client Secret</label>
                        <input type="password" name="google_client_secret" class="form-control" value="{{ $settings['google_client_secret'] ?? '' }}" placeholder="Enter Google Client Secret">
                        <div class="form-text">Keep this secret! Never share it publicly.</div>
                    </div>

                    <div class="alert alert-info">
                        <h6 class="alert-heading"><i class="bi bi-info-circle me-1"></i> Setup Instructions</h6>
                        <ol class="mb-0 small">
                            <li>Go to <a href="https://console.cloud.google.com" target="_blank">Google Cloud Console</a></li>
                            <li>Create a new project or select existing</li>
                            <li>Enable Google+ API</li>
                            <li>Create OAuth 2.0 credentials</li>
                            <li>Add redirect URI: <code>{{ url('/login/google/callback') }}</code></li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Facebook Settings -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 d-flex align-items-center">
                    <img src="https://www.facebook.com/favicon.ico" alt="Facebook" class="me-2" style="width: 24px;">
                    <h5 class="mb-0 fw-semibold">Facebook Login</h5>
                </div>
                <div class="card-body">
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="facebook_enabled" id="facebook_enabled" value="1" {{ $settings['facebook_enabled'] ?? '0' === '1' ? 'checked' : '' }}>
                        <label class="form-check-label fw-medium" for="facebook_enabled">Enable Facebook Login</label>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">Facebook App ID</label>
                        <input type="text" name="facebook_client_id" class="form-control" value="{{ $settings['facebook_client_id'] ?? '' }}" placeholder="Enter Facebook App ID">
                        <div class="form-text">Get your App ID from <a href="https://developers.facebook.com/apps" target="_blank">Facebook Developers</a></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">Facebook App Secret</label>
                        <input type="password" name="facebook_client_secret" class="form-control" value="{{ $settings['facebook_client_secret'] ?? '' }}" placeholder="Enter Facebook App Secret">
                        <div class="form-text">Keep this secret! Never share it publicly.</div>
                    </div>

                    <div class="alert alert-info">
                        <h6 class="alert-heading"><i class="bi bi-info-circle me-1"></i> Setup Instructions</h6>
                        <ol class="mb-0 small">
                            <li>Go to <a href="https://developers.facebook.com/apps" target="_blank">Facebook Developers</a></li>
                            <li>Create a new app</li>
                            <li>Add Facebook Login product</li>
                            <li>Configure OAuth settings</li>
                            <li>Add redirect URI: <code>{{ url('/login/facebook/callback') }}</code></li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Floating Save Button -->
<div class="floating-save-container">
    <button type="submit" form="social-login-form" class="btn btn-primary floating-save-btn">
        <i class="bi bi-check-lg me-1"></i> Save Settings
    </button>
</div>
@endsection
