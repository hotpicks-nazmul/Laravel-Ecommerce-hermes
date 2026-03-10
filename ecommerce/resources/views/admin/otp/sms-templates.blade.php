@extends('admin.layouts.app')

@section('title', 'SMS Templates')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">SMS Templates</h4>
        <a href="{{ route('admin.otp.configuration') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to OTP Config
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <form id="smsTemplateForm" method="POST" action="{{ route('admin.otp.sms-templates.update') }}">
                @csrf
                
                <!-- OTP Verification Template -->
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white">
                        <h6 class="mb-0"><i class="bi bi-chat-dots me-2"></i>OTP Verification Message</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="otp_verification_template" class="form-label">Verification Code Message</label>
                            <textarea id="otp_verification_template" name="otp_verification_template" class="form-control" rows="3" placeholder="Enter your OTP verification message">{{ old('otp_verification_template', $templates['otp_verification_template'] ?? 'Your verification code is: {otp}. Valid for {expiry} minutes.') }}</textarea>
                            <div class="form-text">
                                Use <code>{otp}</code> for the OTP code, <code>{expiry}</code> for expiry time in minutes, <code>{site_name}</code> for your website name
                            </div>
                        </div>
                        <div class="p-3 bg-light rounded">
                            <span class="text-muted small">Preview:</span>
                            <p class="mb-0 mt-2 small" id="verificationPreview">
                                Your verification code is: <strong>123456</strong>. Valid for <strong>5</strong> minutes.
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Welcome/Registration Template -->
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white">
                        <h6 class="mb-0"><i class="bi bi-person-plus me-2"></i>Registration Welcome Message</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="registration_template" class="form-label">Welcome Message (Optional)</label>
                            <textarea id="registration_template" name="registration_template" class="form-control" rows="3" placeholder="Enter welcome message after registration">{{ old('registration_template', $templates['registration_template'] ?? 'Welcome to {site_name}! Your account has been created successfully.') }}</textarea>
                            <div class="form-text">
                                This message is sent after successful phone verification during registration. Use <code>{site_name}</code>, <code>{user_name}</code>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Password Reset Template -->
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white">
                        <h6 class="mb-0"><i class="bi bi-key me-2"></i>Password Reset Message</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="password_reset_template" class="form-label">Password Reset OTP Message</label>
                            <textarea id="password_reset_template" name="password_reset_template" class="form-control" rows="3" placeholder="Enter password reset message">{{ old('password_reset_template', $templates['password_reset_template'] ?? 'Your password reset OTP is: {otp}. Do not share this code with anyone.') }}</textarea>
                            <div class="form-text">
                                Use <code>{otp}</code> for the OTP code, <code>{site_name}</code>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Payment Verification Template -->
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white">
                        <h6 class="mb-0"><i class="bi bi-credit-card me-2"></i>Payment Verification Message</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="payment_template" class="form-label">Payment OTP Message</label>
                            <textarea id="payment_template" name="payment_template" class="form-control" rows="3" placeholder="Enter payment verification message">{{ old('payment_template', $templates['payment_template'] ?? 'Your payment verification code is: {otp}. Amount: {amount}.') }}</textarea>
                            <div class="form-text">
                                Use <code>{otp}</code> for the OTP code, <code>{amount}</code> for payment amount, <code>{site_name}</code>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Order Confirmation Template -->
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white">
                        <h6 class="mb-0"><i class="bi bi-bag-check me-2"></i>Order Confirmation Message</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="order_confirmation_template" class="form-label">Order OTP Message</label>
                            <textarea id="order_confirmation_template" name="order_confirmation_template" class="form-control" rows="3" placeholder="Enter order confirmation message">{{ old('order_confirmation_template', $templates['order_confirmation_template'] ?? 'Your order OTP is: {otp}. Order ID: {order_id}.') }}</textarea>
                            <div class="form-text">
                                Use <code>{otp}</code> for the OTP code, <code>{order_id}</code> for order ID, <code>{site_name}</code>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Login Notification Template -->
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white">
                        <h6 class="mb-0"><i class="bi bi-box-arrow-in-right me-2"></i>Login Notification Message</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="login_notification_template" class="form-label">Login OTP Message</label>
                            <textarea id="login_notification_template" name="login_notification_template" class="form-control" rows="3" placeholder="Enter login notification message">{{ old('login_notification_template', $templates['login_notification_template'] ?? 'Your login OTP is: {otp}. If you did not request this, please ignore.') }}</textarea>
                            <div class="form-text">
                                Use <code>{otp}</code> for the OTP code, <code>{site_name}</code>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        
        <div class="col-lg-4">
            <!-- Template Variables Card -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-code-slash text-info me-2"></i>Available Variables</h6>
                </div>
                <div class="card-body">
                    <p class="small text-muted mb-2">Use these variables in your SMS templates:</p>
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <code class="bg-light px-2 py-1 rounded small">{otp}</code>
                        <code class="bg-light px-2 py-1 rounded small">{expiry}</code>
                        <code class="bg-light px-2 py-1 rounded small">{site_name}</code>
                        <code class="bg-light px-2 py-1 rounded small">{user_name}</code>
                        <code class="bg-light px-2 py-1 rounded small">{amount}</code>
                        <code class="bg-light px-2 py-1 rounded small">{order_id}</code>
                        <code class="bg-light px-2 py-1 rounded small">{email}</code>
                        <code class="bg-light px-2 py-1 rounded small">{phone}</code>
                    </div>
                    <hr>
                    <p class="small text-muted mb-1"><strong>Character Count:</strong></p>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">SMS Segments:</span>
                        <span class="badge bg-primary" id="charCount">0/160</span>
                    </div>
                </div>
            </div>
            
            <!-- Quick Info Card -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-info-circle text-info me-2"></i>SMS Guidelines</h6>
                </div>
                <div class="card-body">
                    <ul class="small text-muted ps-3 mb-0">
                        <li class="mb-2">SMS length is limited to 160 characters per segment</li>
                        <li class="mb-2">Longer messages will be split into multiple segments</li>
                        <li class="mb-2">Keep messages concise for cost efficiency</li>
                        <li>Always include your brand name for recognition</li>
                    </ul>
                </div>
            </div>
            
            <!-- Related Settings Card -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-gear me-2"></i>Related Settings</h6>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <a href="{{ route('admin.otp.configuration') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-gear me-2"></i>OTP Configuration</span>
                            <i class="bi bi-chevron-right text-muted"></i>
                        </a>
                        <a href="{{ route('admin.otp.credentials') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-key me-2"></i>OTP Credentials</span>
                            <i class="bi bi-chevron-right text-muted"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Floating Buttons -->
    <div class="floating-save-container">
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary floating-reset-btn">
            <i class="bi bi-x-lg me-1"></i> Cancel
        </a>
        <button type="submit" form="smsTemplateForm" class="btn btn-primary floating-save-btn">
            <i class="bi bi-check-lg me-1"></i> Save Templates
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
    // Update preview when template changes
    document.addEventListener('DOMContentLoaded', function() {
        const verificationTemplate = document.getElementById('otp_verification_template');
        const preview = document.getElementById('verificationPreview');
        
        if (verificationTemplate && preview) {
            verificationTemplate.addEventListener('input', function() {
                let text = this.value
                    .replace('{otp}', '<strong>123456</strong>')
                    .replace('{expiry}', '<strong>5</strong>')
                    .replace('{site_name}', '<strong>Hamko Ecommerce</strong>');
                preview.innerHTML = text || 'Your verification code is: <strong>123456</strong>. Valid for <strong>5</strong> minutes.';
            });
        }
        
        // Character count
        const charCountEl = document.getElementById('charCount');
        if (verificationTemplate && charCountEl) {
            verificationTemplate.addEventListener('input', function() {
                const len = this.value.length;
                const segments = Math.ceil(len / 160);
                charCountEl.textContent = `${len}/160 (${segments} segment${segments > 1 ? 's' : ''})`;
                
                if (len > 160) {
                    charCountEl.classList.remove('bg-primary');
                    charCountEl.classList.add('bg-warning');
                } else {
                    charCountEl.classList.remove('bg-warning');
                    charCountEl.classList.add('bg-primary');
                }
            });
        }
    });
</script>
@endpush
