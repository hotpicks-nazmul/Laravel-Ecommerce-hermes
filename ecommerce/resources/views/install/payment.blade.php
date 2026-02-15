<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Gateway Setup - Installation Wizard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4f46e5;
            --secondary-color: #7c3aed;
        }
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .install-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            overflow: hidden;
            max-width: 900px;
            width: 100%;
        }
        .install-header {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .install-header h1 {
            font-size: 1.8rem;
            margin-bottom: 5px;
        }
        .install-body {
            padding: 30px;
        }
        .step-indicator {
            display: flex;
            justify-content: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }
        .step {
            display: flex;
            align-items: center;
            color: #9ca3af;
            font-size: 0.9rem;
        }
        .step.active {
            color: #4f46e5;
        }
        .step.completed {
            color: #10b981;
        }
        .step-number {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            border: 2px solid currentColor;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.85rem;
            margin-right: 8px;
        }
        .step.completed .step-number {
            background: #10b981;
            border-color: #10b981;
            color: white;
        }
        .step.active .step-number {
            background: #4f46e5;
            border-color: #4f46e5;
            color: white;
        }
        .step-line {
            width: 30px;
            height: 2px;
            background: #e5e7eb;
            margin: 0 10px;
        }
        .gateway-card {
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
        }
        .gateway-card:hover {
            border-color: #4f46e5;
        }
        .gateway-card.enabled {
            border-color: #10b981;
            background: #f0fdf4;
        }
        .gateway-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 15px;
        }
        .gateway-info {
            display: flex;
            align-items: center;
        }
        .gateway-logo {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            background: #f3f4f6;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 1.5rem;
        }
        .gateway-name {
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 2px;
        }
        .gateway-desc {
            font-size: 0.85rem;
            color: #6b7280;
        }
        .form-check-input:checked {
            background-color: #10b981;
            border-color: #10b981;
        }
        .gateway-fields {
            display: none;
            padding-top: 15px;
            border-top: 1px solid #e5e7eb;
            margin-top: 15px;
        }
        .gateway-card.enabled .gateway-fields {
            display: block;
        }
        .form-floating {
            margin-bottom: 15px;
        }
        .form-floating input {
            border-radius: 8px;
            border: 2px solid #e5e7eb;
        }
        .form-floating input:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 0.2rem rgba(79, 70, 229, 0.15);
        }
        .btn-install {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            border: none;
            padding: 12px 35px;
            font-size: 1rem;
            border-radius: 10px;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .btn-install:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(79, 70, 229, 0.3);
        }
        .btn-back {
            background: #f3f4f6;
            color: #374151;
            border: none;
            padding: 12px 35px;
            font-size: 1rem;
            border-radius: 10px;
        }
        .btn-back:hover {
            background: #e5e7eb;
        }
        .btn-skip {
            background: transparent;
            color: #6b7280;
            border: 2px solid #e5e7eb;
            padding: 12px 35px;
            font-size: 1rem;
            border-radius: 10px;
        }
        .btn-skip:hover {
            background: #f3f4f6;
        }
        .test-mode-toggle {
            font-size: 0.85rem;
        }
    </style>
</head>
<body>
    <div class="install-container">
        <div class="install-header">
            <h1><i class="bi bi-credit-card me-2"></i>Payment Gateway Setup</h1>
            <p>Configure payment methods for your store</p>
        </div>
        
        <div class="install-body">
            <!-- Step Indicator -->
            <div class="step-indicator">
                <div class="step completed">
                    <span class="step-number"><i class="bi bi-check"></i></span>
                    <span>Welcome</span>
                </div>
                <div class="step-line"></div>
                <div class="step completed">
                    <span class="step-number"><i class="bi bi-check"></i></span>
                    <span>Requirements</span>
                </div>
                <div class="step-line"></div>
                <div class="step completed">
                    <span class="step-number"><i class="bi bi-check"></i></span>
                    <span>Database</span>
                </div>
                <div class="step-line"></div>
                <div class="step completed">
                    <span class="step-number"><i class="bi bi-check"></i></span>
                    <span>Configure</span>
                </div>
                <div class="step-line"></div>
                <div class="step completed">
                    <span class="step-number"><i class="bi bi-check"></i></span>
                    <span>Theme</span>
                </div>
                <div class="step-line"></div>
                <div class="step active">
                    <span class="step-number">6</span>
                    <span>Payment</span>
                </div>
            </div>

            @if(session('error'))
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                </div>
            @endif

            <div class="alert alert-info mb-4">
                <i class="bi bi-info-circle me-2"></i>
                You can configure payment gateways now or skip and configure them later from the admin panel.
            </div>

            <form action="{{ route('install.save-payment') }}" method="POST">
                @csrf
                
                @php
                    $gateways = !empty($gateways) ? $gateways : [
                        'bkash' => ['name' => 'bKash', 'description' => 'Mobile Financial Service', 'fields' => ['app_key', 'app_secret', 'username', 'password']],
                        'nagad' => ['name' => 'Nagad', 'description' => 'Mobile Financial Service', 'fields' => ['merchant_id', 'merchant_number', 'public_key', 'private_key']],
                        'rocket' => ['name' => 'Rocket', 'description' => 'Mobile Financial Service', 'fields' => ['merchant_id', 'merchant_number', 'password']],
                        'sslcommerz' => ['name' => 'SSLCommerz', 'description' => 'Payment Gateway', 'fields' => ['store_id', 'store_password']],
                        'cod' => ['name' => 'Cash on Delivery', 'description' => 'Pay when you receive', 'fields' => []],
                    ];
                @endphp

                @foreach($gateways as $slug => $gateway)
                    <div class="gateway-card" id="gateway-{{ $slug }}">
                        <div class="gateway-header">
                            <div class="gateway-info">
                                <div class="gateway-logo">
                                    @if($slug == 'bkash')
                                        <i class="bi bi-phone text-danger"></i>
                                    @elseif($slug == 'nagad')
                                        <i class="bi bi-phone text-warning"></i>
                                    @elseif($slug == 'rocket')
                                        <i class="bi bi-phone text-purple"></i>
                                    @elseif($slug == 'sslcommerz')
                                        <i class="bi bi-shield-check text-primary"></i>
                                    @elseif($slug == 'cod')
                                        <i class="bi bi-cash-stack text-success"></i>
                                    @else
                                        <i class="bi bi-credit-card"></i>
                                    @endif
                                </div>
                                <div>
                                    <div class="gateway-name">{{ $gateway['name'] }}</div>
                                    <div class="gateway-desc">{{ $gateway['description'] }}</div>
                                </div>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" 
                                       id="{{ $slug }}_enabled" name="{{ $slug }}_enabled" 
                                       value="1" onchange="toggleGateway('{{ $slug }}')">
                                <label class="form-check-label" for="{{ $slug }}_enabled">Enable</label>
                            </div>
                        </div>
                        
                        @if(!empty($gateway['fields']))
                            <div class="gateway-fields">
                                <div class="test-mode-toggle mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" 
                                               id="{{ $slug }}_test_mode" name="{{ $slug }}_test_mode" value="1" checked>
                                        <label class="form-check-label" for="{{ $slug }}_test_mode">
                                            Test Mode (Recommended for development)
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    @foreach($gateway['fields'] as $field)
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="text" class="form-control" 
                                                       id="{{ $slug }}_{{ $field }}" 
                                                       name="{{ $slug }}_credentials[{{ $field }}]"
                                                       placeholder="{{ ucfirst(str_replace('_', ' ', $field)) }}">
                                                <label for="{{ $slug }}_{{ $field }}">{{ ucfirst(str_replace('_', ' ', $field)) }}</label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach

                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('install.theme') }}" class="btn btn-back">
                        <i class="bi bi-arrow-left me-2"></i>Back
                    </a>
                    <div>
                        <button type="submit" class="btn btn-skip me-2" name="skip" value="1">
                            Skip for Now
                        </button>
                        <button type="submit" class="btn btn-primary btn-install">
                            Complete Installation <i class="bi bi-check-lg ms-2"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleGateway(slug) {
            const checkbox = document.getElementById(slug + '_enabled');
            const card = document.getElementById('gateway-' + slug);
            
            if (checkbox.checked) {
                card.classList.add('enabled');
            } else {
                card.classList.remove('enabled');
            }
        }
    </script>
</body>
</html>
