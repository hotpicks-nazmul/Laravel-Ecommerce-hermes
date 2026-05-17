<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Installer') - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            200: '#c7d2fe',
                            300: '#a5b4fc',
                            400: '#818cf8',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                            800: '#3730a3',
                            900: '#312e81',
                        }
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.4s ease-out',
                        'slide-up': 'slideUp 0.4s ease-out',
                        'pulse-slow': 'pulse 3s infinite',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0', transform: 'translateY(10px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        },
                        slideUp: {
                            '0%': { opacity: '0', transform: 'translateY(20px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        },
                    }
                }
            }
        }
    </script>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }
        .card {
            max-width: 700px;
            width: 100%;
            background: #ffffff;
            border-radius: 1.5rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            overflow: hidden;
            animation: fadeIn 0.4s ease-out;
        }
        .card-body { padding: 2.5rem; }
        .step-circle {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.875rem;
            transition: all 0.3s ease;
            flex-shrink: 0;
        }
        .step-circle.completed {
            background: #4f46e5;
            color: white;
            border: 2px solid #4f46e5;
        }
        .step-circle.current {
            background: white;
            color: #4f46e5;
            border: 2px solid #4f46e5;
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.15);
        }
        .step-circle.future {
            background: white;
            color: #9ca3af;
            border: 2px solid #d1d5db;
        }
        .step-line {
            flex: 1;
            height: 2px;
            margin: 0 0.5rem;
            transition: background 0.3s ease;
        }
        .step-line.completed { background: #4f46e5; }
        .step-line.future { background: #e5e7eb; }
        .step-label {
            font-size: 0.65rem;
            font-weight: 500;
            margin-top: 0.25rem;
            text-align: center;
            transition: color 0.3s ease;
        }
        .step-label.completed { color: #4f46e5; }
        .step-label.current { color: #4f46e5; font-weight: 600; }
        .step-label.future { color: #9ca3af; }
        .step-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            min-width: 3.5rem;
        }
        .form-input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1.5px solid #d1d5db;
            border-radius: 0.75rem;
            font-size: 0.925rem;
            transition: all 0.2s ease;
            background: #fff;
            outline: none;
        }
        .form-input:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }
        .form-input.error {
            border-color: #ef4444;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
        }
        .form-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.375rem;
        }
        .btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.75rem 2rem;
            background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
            color: white;
            font-weight: 600;
            font-size: 0.925rem;
            border: none;
            border-radius: 0.75rem;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .btn-primary:hover:not(:disabled) {
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(79, 70, 229, 0.3);
        }
        .btn-primary:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        .btn-outline {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.75rem 2rem;
            background: white;
            color: #374151;
            font-weight: 600;
            font-size: 0.925rem;
            border: 1.5px solid #d1d5db;
            border-radius: 0.75rem;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .btn-outline:hover {
            background: #f9fafb;
            border-color: #9ca3af;
        }
        .error-text {
            color: #ef4444;
            font-size: 0.8rem;
            margin-top: 0.25rem;
        }
        .spinner {
            display: inline-block;
            width: 1.25rem;
            height: 1.25rem;
            border: 2px solid rgba(255,255,255,0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
        }
        .spinner-dark {
            border-color: rgba(79,70,229,0.2);
            border-top-color: #4f46e5;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
        .step-content {
            animation: fadeIn 0.4s ease-out;
        }
        .progress-steps {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem 2rem 0.5rem 2rem;
        }
        .check-icon { color: #10b981; font-style: normal; }
        .cross-icon { color: #ef4444; font-style: normal; }
        .req-table { width: 100%; border-collapse: collapse; }
        .req-table td {
            padding: 0.625rem 0.75rem;
            border-bottom: 1px solid #f3f4f6;
            font-size: 0.875rem;
        }
        .req-table tr:last-child td { border-bottom: none; }
        .badge-pass {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .badge-pass.success { background: #d1fae5; color: #065f46; }
        .badge-pass.fail { background: #fee2e2; color: #991b1b; }
        .badge-pass.running { background: #dbeafe; color: #1e40af; }
        .password-strength {
            height: 0.375rem;
            border-radius: 9999px;
            background: #e5e7eb;
            margin-top: 0.375rem;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        .password-strength-bar {
            height: 100%;
            border-radius: 9999px;
            transition: all 0.3s ease;
        }
        .log-entry {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            border-radius: 0.75rem;
            background: #f9fafb;
            margin-bottom: 0.5rem;
            transition: all 0.3s ease;
            font-size: 0.875rem;
        }
        .log-entry.running { background: #eef2ff; border: 1px solid #c7d2fe; }
        .log-entry.done { background: #f0fdf4; border: 1px solid #bbf7d0; }
        .log-entry.error { background: #fef2f2; border: 1px solid #fecaca; }
        .log-icon {
            width: 1.5rem;
            height: 1.5rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 0.75rem;
        }
        .log-icon.running {
            border: 2px solid #4f46e5;
            border-top-color: transparent;
            animation: spin 0.6s linear infinite;
        }
        .log-icon.done { background: #10b981; color: white; }
        .log-icon.error { background: #ef4444; color: white; }
        .fade-enter {
            opacity: 0;
            transform: translateY(8px);
        }
        .fade-enter-active {
            opacity: 1;
            transform: translateY(0);
            transition: all 0.3s ease-out;
        }
    </style>
</head>
<body>
    <div class="card">
        @if (!request()->routeIs('install.complete'))
        <div class="progress-steps">
            @php
                $steps = [
                    1 => 'Welcome',
                    2 => 'Server',
                    3 => 'Database',
                    4 => 'Config',
                    5 => 'Admin',
                    6 => 'Install',
                    7 => 'Done',
                ];
                $currentStep = $currentStep ?? 1;
            @endphp
            @foreach ($steps as $num => $label)
                @php
                    $status = $num < $currentStep ? 'completed' : ($num == $currentStep ? 'current' : 'future');
                @endphp
                @if ($num > 1)
                    <div class="step-line {{ $num <= $currentStep ? 'completed' : 'future' }}"></div>
                @endif
                <div class="step-wrapper">
                    <div class="step-circle {{ $status }}">
                        @if ($status === 'completed')
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        @else
                            {{ $num }}
                        @endif
                    </div>
                    <span class="step-label {{ $status }}">{{ $label }}</span>
                </div>
            @endforeach
        </div>
        @endif

        <div class="card-body">
            @yield('content')
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const forms = document.querySelectorAll('form[data-validate]');
            forms.forEach(form => {
                form.addEventListener('submit', function (e) {
                    const btn = this.querySelector('[type="submit"]');
                    if (btn) {
                        btn.disabled = true;
                        const original = btn.innerHTML;
                        btn.innerHTML = '<span class="spinner"></span> Processing...';
                        setTimeout(() => {
                            btn.disabled = false;
                            btn.innerHTML = original;
                        }, 30000);
                    }
                });
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
