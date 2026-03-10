@extends('admin.layouts.app')

@section('title', 'OTP Credentials')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">OTP Credentials</h4>
        <a href="{{ route('admin.otp.configuration') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to OTP Config
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <form id="otpCredentialsForm" method="POST" action="{{ route('admin.otp.credentials.update') }}">
                @csrf
                
                <!-- SMS Gateway Selection -->
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white">
                        <h6 class="mb-0"><i class="bi bi-hdd-network me-2"></i>SMS Gateway</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="sms_gateway" class="form-label">Select SMS Gateway <span class="text-danger">*</span></label>
                            <select id="sms_gateway" name="sms_gateway" class="form-select" required onchange="toggleGatewayFields()">
                                <option value="custom" {{ old('sms_gateway', $credentials['sms_gateway'] ?? '') == 'custom' ? 'selected' : '' }}>Custom API</option>
                                <option value="twilio" {{ old('sms_gateway', $credentials['sms_gateway'] ?? '') == 'twilio' ? 'selected' : '' }}>Twilio</option>
                                <option value="nexmo" {{ old('sms_gateway', $credentials['sms_gateway'] ?? '') == 'nexmo' ? 'selected' : '' }}>Nexmo (Vonage)</option>
                                <option value="msg91" {{ old('sms_gateway', $credentials['sms_gateway'] ?? '') == 'msg91' ? 'selected' : '' }}>MSG91</option>
                                <option value="banglalion" {{ old('sms_gateway', $credentials['sms_gateway'] ?? '') == 'banglalion' ? 'selected' : '' }}>Banglalion</option>
                                <option value="ssl" {{ old('sms_gateway', $credentials['sms_gateway'] ?? '') == 'ssl' ? 'selected' : '' }}>SSL Wireless</option>
                                <option value="mim" {{ old('sms_gateway', $credentials['sms_gateway'] ?? '') == 'mim' ? 'selected' : '' }}>MIM SMS</option>
                            </select>
                            <div class="form-text">Choose which SMS gateway to use for sending OTPs</div>
                        </div>
                    </div>
                </div>
                
                <!-- Custom API Settings -->
                <div id="customGatewayFields" class="card border-0 shadow-sm mb-3" style="{{ in_array(old('sms_gateway', $credentials['sms_gateway'] ?? 'custom'), ['custom', '']) ? '' : 'display:none;' }}">
                    <div class="card-header bg-white">
                        <h6 class="mb-0"><i class="bi bi-code-square me-2"></i>Custom API Settings</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="custom_api_url" class="form-label">API URL</label>
                                <input type="url" id="custom_api_url" name="custom_api_url" class="form-control" value="{{ old('custom_api_url', $credentials['custom_api_url'] ?? '') }}" placeholder="https://api.example.com/send">
                                <div class="form-text">The endpoint URL for sending SMS</div>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="custom_api_key" class="form-label">API Key</label>
                                <div class="input-group">
                                    <input type="password" id="custom_api_key" name="custom_api_key" class="form-control" value="{{ old('custom_api_key', $credentials['custom_api_key'] ?? '') }}" placeholder="Enter API key">
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('custom_api_key')">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="custom_api_secret" class="form-label">API Secret</label>
                                <div class="input-group">
                                    <input type="password" id="custom_api_secret" name="custom_api_secret" class="form-control" value="{{ old('custom_api_secret', $credentials['custom_api_secret'] ?? '') }}" placeholder="Enter API secret">
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('custom_api_secret')">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="custom_sender_id" class="form-label">Sender ID</label>
                                <input type="text" id="custom_sender_id" name="custom_sender_id" class="form-control" value="{{ old('custom_sender_id', $credentials['custom_sender_id'] ?? '') }}" placeholder="HAMKO">
                                <div class="form-text">Your registered sender ID</div>
                            </div>
                            
                            <div class="col-12">
                                <label for="custom_api_method" class="form-label">Request Method</label>
                                <select id="custom_api_method" name="custom_api_method" class="form-select">
                                    <option value="POST" {{ old('custom_api_method', $credentials['custom_api_method'] ?? 'POST') == 'POST' ? 'selected' : '' }}>POST</option>
                                    <option value="GET" {{ old('custom_api_method', $credentials['custom_api_method'] ?? 'POST') == 'GET' ? 'selected' : '' }}>GET</option>
                                </select>
                            </div>
                            
                            <div class="col-12">
                                <label for="custom_request_body" class="form-label">Request Body Template (JSON)</label>
                                @php
                                    $defaultBody = '{"api_key": "{{api_key}}", "sender_id": "{{sender_id}}", "phone": "{{phone}}", "message": "{{message}}"}';
                                    $currentBody = old('custom_request_body', $credentials['custom_request_body'] ?? $defaultBody);
                                @endphp
                                <textarea id="custom_request_body" name="custom_request_body" class="form-control font-monospace" rows="4" placeholder='{{ $defaultBody }}'>{{ $currentBody }}</textarea>
                                <div class="form-text">
                                    Available placeholders: <code>{api_key}</code>, <code>{api_secret}</code>, <code>{sender_id}</code>, <code>{phone}</code>, <code>{message}</code>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Twilio Settings -->
                <div id="twilioFields" class="card border-0 shadow-sm mb-3" style="{{ old('sms_gateway', $credentials['sms_gateway'] ?? '') == 'twilio' ? '' : 'display:none;' }}">
                    <div class="card-header bg-white">
                        <h6 class="mb-0"><i class="bi bi-cloud me-2"></i>Twilio Settings</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="twilio_sid" class="form-label">Account SID</label>
                                <input type="text" id="twilio_sid" name="twilio_sid" class="form-control" value="{{ old('twilio_sid', $credentials['twilio_sid'] ?? '') }}" placeholder="ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx">
                            </div>
                            
                            <div class="col-md-6">
                                <label for="twilio_token" class="form-label">Auth Token</label>
                                <div class="input-group">
                                    <input type="password" id="twilio_token" name="twilio_token" class="form-control" value="{{ old('twilio_token', $credentials['twilio_token'] ?? '') }}" placeholder="Enter auth token">
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('twilio_token')">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="twilio_from" class="form-label">From Number</label>
                                <input type="text" id="twilio_from" name="twilio_from" class="form-control" value="{{ old('twilio_from', $credentials['twilio_from'] ?? '') }}" placeholder="+1234567890">
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- MSG91 Settings -->
                <div id="msg91Fields" class="card border-0 shadow-sm mb-3" style="{{ old('sms_gateway', $credentials['sms_gateway'] ?? '') == 'msg91' ? '' : 'display:none;' }}">
                    <div class="card-header bg-white">
                        <h6 class="mb-0"><i class="bi bi-chat-dots me-2"></i>MSG91 Settings</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="msg91_authkey" class="form-label">Auth Key</label>
                                <input type="text" id="msg91_authkey" name="msg91_authkey" class="form-control" value="{{ old('msg91_authkey', $credentials['msg91_authkey'] ?? '') }}" placeholder="Enter auth key">
                            </div>
                            
                            <div class="col-md-6">
                                <label for="msg91_sender_id" class="form-label">Sender ID</label>
                                <input type="text" id="msg91_sender_id" name="msg91_sender_id" class="form-control" value="{{ old('msg91_sender_id', $credentials['msg91_sender_id'] ?? '') }}" placeholder="HAMKO">
                            </div>
                            
                            <div class="col-md-6">
                                <label for="msg91_route" class="form-label">Route</label>
                                @php
                                    $msg91Route = old('msg91_route', $credentials['msg91_route'] ?? '1');
                                @endphp
                                <select id="msg91_route" name="msg91_route" class="form-select">
                                    <option value="1" {{ $msg91Route == '1' ? 'selected' : '' }}>Transactional</option>
                                    <option value="4" {{ $msg91Route == '4' ? 'selected' : '' }}>Promotional</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- SSL Wireless Settings -->
                <div id="sslFields" class="card border-0 shadow-sm mb-3" style="{{ old('sms_gateway', $credentials['sms_gateway'] ?? '') == 'ssl' ? '' : 'display:none;' }}">
                    <div class="card-header bg-white">
                        <h6 class="mb-0"><i class="bi bi-wifi me-2"></i>SSL Wireless Settings</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="ssl_sms_user" class="form-label">SMS User</label>
                                <input type="text" id="ssl_sms_user" name="ssl_sms_user" class="form-control" value="{{ old('ssl_sms_user', $credentials['ssl_sms_user'] ?? '') }}" placeholder="Enter SMS user">
                            </div>
                            
                            <div class="col-md-6">
                                <label for="ssl_sms_pass" class="form-label">SMS Password</label>
                                <div class="input-group">
                                    <input type="password" id="ssl_sms_pass" name="ssl_sms_pass" class="form-control" value="{{ old('ssl_sms_pass', $credentials['ssl_sms_pass'] ?? '') }}" placeholder="Enter SMS password">
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('ssl_sms_pass')">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="ssl_sid" class="form-label">SID</label>
                                <input type="text" id="ssl_sid" name="ssl_sid" class="form-control" value="{{ old('ssl_sid', $credentials['ssl_sid'] ?? '') }}" placeholder="Enter SID">
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Test SMS -->
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white">
                        <h6 class="mb-0"><i class="bi bi-send me-2"></i>Test SMS</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label for="test_phone" class="form-label">Test Phone Number</label>
                                <input type="text" id="test_phone" name="test_phone" class="form-control" placeholder="Enter phone number (e.g., 01712345678)">
                                <div class="form-text">Enter a valid phone number to test SMS sending</div>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="button" class="btn btn-outline-primary w-100" onclick="sendTestSms()">
                                    <i class="bi bi-send me-1"></i> Send Test SMS
                                </button>
                            </div>
                        </div>
                        <div id="testResult" class="mt-3" style="display: none;"></div>
                    </div>
                </div>
            </form>
        </div>
        
        <div class="col-lg-4">
            <!-- Gateway Status Card -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-check-circle text-success me-2"></i>Gateway Status</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0">
                            <div class="avatar-sm bg-success-subtle rounded-circle d-flex align-items-center justify-content-center">
                                <i class="bi bi-check-circle text-success"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="mb-0 text-muted small">Current Gateway</p>
                            <span class="badge bg-primary">{{ ucfirst(old('sms_gateway', $credentials['sms_gateway'] ?? 'Custom')) }}</span>
                        </div>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">SMS Balance</span>
                        <span class="fw-semibold" id="smsBalance">{{ $credentials['sms_balance'] ?? 'N/A' }}</span>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-secondary w-100 mt-2" onclick="checkBalance()">
                        <i class="bi bi-arrow-clockwise me-1"></i> Check Balance
                    </button>
                </div>
            </div>
            
            <!-- Quick Info Card -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-info-circle text-info me-2"></i>Setup Instructions</h6>
                </div>
                <div class="card-body">
                    <ol class="small text-muted ps-3 mb-0">
                        <li class="mb-2">Select your SMS gateway provider</li>
                        <li class="mb-2">Enter your API credentials</li>
                        <li class="mb-2">Save the configuration</li>
                        <li class="mb-2">Send a test SMS to verify</li>
                        <li>Your OTPs will now be sent via this gateway</li>
                    </ol>
                </div>
            </div>
            
            <!-- Related Settings Card -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-link-45deg me-2"></i>Related Settings</h6>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <a href="{{ route('admin.otp.configuration') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-gear me-2"></i>OTP Configuration</span>
                            <i class="bi bi-chevron-right text-muted"></i>
                        </a>
                        <a href="{{ route('admin.otp.sms-templates') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <span><i class="bi bi-file-text me-2"></i>SMS Templates</span>
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
        <button type="submit" form="otpCredentialsForm" class="btn btn-primary floating-save-btn">
            <i class="bi bi-check-lg me-1"></i> Save Credentials
        </button>
    </div>
@endsection

@push('styles')
<style>
    .content-area {
        padding-bottom: 100px !important;
    }
    .avatar-sm {
        width: 36px;
        height: 36px;
    }
</style>
@endpush

@push('scripts')
<script>
    function toggleGatewayFields() {
        const gateway = document.getElementById('sms_gateway').value;
        
        document.getElementById('customGatewayFields').style.display = (gateway === 'custom') ? 'block' : 'none';
        document.getElementById('twilioFields').style.display = (gateway === 'twilio') ? 'block' : 'none';
        document.getElementById('msg91Fields').style.display = (gateway === 'msg91') ? 'block' : 'none';
        document.getElementById('sslFields').style.display = (gateway === 'ssl') ? 'block' : 'none';
    }
    
    function togglePassword(fieldId) {
        const field = document.getElementById(fieldId);
        field.type = field.type === 'password' ? 'text' : 'password';
    }
    
    function sendTestSms() {
        const phone = document.getElementById('test_phone').value;
        
        if (!phone) {
            showTestResult('Please enter a phone number', 'danger');
            return;
        }
        
        const btn = event.target;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Sending...';
        
        fetch('{{ route("admin.otp.send-test-sms") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ phone: phone })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showTestResult('Test SMS sent successfully!', 'success');
            } else {
                showTestResult('Failed: ' + data.message, 'danger');
            }
        })
        .catch(err => {
            showTestResult('Error: ' + err.message, 'danger');
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-send me-1"></i> Send Test SMS';
        });
    }
    
    function showTestResult(message, type) {
        const resultDiv = document.getElementById('testResult');
        resultDiv.innerHTML = `<div class="alert alert-${type} py-2">${message}</div>`;
        resultDiv.style.display = 'block';
    }
    
    function checkBalance() {
        const btn = event.target;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Checking...';
        
        fetch('{{ route("admin.otp.check-balance") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                document.getElementById('smsBalance').textContent = data.balance;
            } else {
                alert('Failed to check balance: ' + data.message);
            }
        })
        .catch(err => {
            alert('Error: ' + err.message);
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-arrow-clockwise me-1"></i> Check Balance';
        });
    }
</script>
@endpush
