<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Server Requirements - Installation Wizard</title>
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
        .step.completed + .step-line {
            background: #10b981;
        }
        .requirement-card {
            background: #f9fafb;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .requirement-card h5 {
            color: #374151;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }
        .requirement-card h5 i {
            margin-right: 10px;
            color: #4f46e5;
        }
        .requirement-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .requirement-item:last-child {
            border-bottom: none;
        }
        .status-badge {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }
        .status-success {
            background: #d1fae5;
            color: #065f46;
        }
        .status-error {
            background: #fee2e2;
            color: #991b1b;
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
        .btn-install:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
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
        .version-info {
            font-size: 0.85rem;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="install-container">
        <div class="install-header">
            <h1><i class="bi bi-server me-2"></i>Server Requirements</h1>
            <p>Checking your server configuration</p>
        </div>
        
        <div class="install-body">
            <!-- Step Indicator -->
            <div class="step-indicator">
                <div class="step completed">
                    <span class="step-number"><i class="bi bi-check"></i></span>
                    <span>Welcome</span>
                </div>
                <div class="step-line"></div>
                <div class="step active">
                    <span class="step-number">2</span>
                    <span>Requirements</span>
                </div>
                <div class="step-line"></div>
                <div class="step">
                    <span class="step-number">3</span>
                    <span>Database</span>
                </div>
                <div class="step-line"></div>
                <div class="step">
                    <span class="step-number">4</span>
                    <span>Configure</span>
                </div>
                <div class="step-line"></div>
                <div class="step">
                    <span class="step-number">5</span>
                    <span>Theme</span>
                </div>
                <div class="step-line"></div>
                <div class="step">
                    <span class="step-number">6</span>
                    <span>Payment</span>
                </div>
            </div>

            @php
                $allPassed = true;
                foreach ($requirements['extensions'] as $ext) {
                    if (!$ext) $allPassed = false;
                }
                foreach ($requirements['permissions'] as $perm) {
                    if (!$perm) $allPassed = false;
                }
            @endphp

            <!-- PHP Version -->
            <div class="requirement-card">
                <h5><i class="bi bi-code-square"></i>PHP Version</h5>
                <div class="requirement-item">
                    <span>
                        PHP Version
                        <span class="version-info">(Required: {{ $requirements['php']['version'] }}+)</span>
                    </span>
                    @if($requirements['php']['status'])
                        <span class="status-badge status-success">
                            <i class="bi bi-check-circle me-1"></i>{{ $requirements['php']['current'] }}
                        </span>
                    @else
                        <span class="status-badge status-error">
                            <i class="bi bi-x-circle me-1"></i>{{ $requirements['php']['current'] }}
                        </span>
                        @php $allPassed = false; @endphp
                    @endif
                </div>
            </div>

            <!-- PHP Extensions -->
            <div class="requirement-card">
                <h5><i class="bi bi-puzzle"></i>PHP Extensions</h5>
                @foreach($requirements['extensions'] as $ext => $status)
                    <div class="requirement-item">
                        <span>{{ strtoupper($ext) }}</span>
                        @if($status)
                            <span class="status-badge status-success">
                                <i class="bi bi-check-circle me-1"></i>Enabled
                            </span>
                        @else
                            <span class="status-badge status-error">
                                <i class="bi bi-x-circle me-1"></i>Required
                            </span>
                            @php $allPassed = false; @endphp
                        @endif
                    </div>
                @endforeach
            </div>

            <!-- File Permissions -->
            <div class="requirement-card">
                <h5><i class="bi bi-folder2-open"></i>File Permissions</h5>
                @foreach($requirements['permissions'] as $path => $writable)
                    <div class="requirement-item">
                        <span>
                            <i class="bi bi-folder text-warning me-2"></i>
                            {{ $path }}
                        </span>
                        @if($writable)
                            <span class="status-badge status-success">
                                <i class="bi bi-check-circle me-1"></i>Writable
                            </span>
                        @else
                            <span class="status-badge status-error">
                                <i class="bi bi-x-circle me-1"></i>Not Writable
                            </span>
                            @php $allPassed = false; @endphp
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="d-flex justify-content-between mt-4">
                <a href="{{ route('install.welcome') }}" class="btn btn-back">
                    <i class="bi bi-arrow-left me-2"></i>Back
                </a>
                @if($allPassed)
                    <a href="{{ route('install.database') }}" class="btn btn-primary btn-install">
                        Continue <i class="bi bi-arrow-right ms-2"></i>
                    </a>
                @else
                    <button class="btn btn-primary btn-install" disabled>
                        <i class="bi bi-exclamation-triangle me-2"></i>Fix Issues First
                    </button>
                @endif
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
