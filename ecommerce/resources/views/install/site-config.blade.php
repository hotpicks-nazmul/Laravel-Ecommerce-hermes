<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site Configuration - Installation Wizard</title>
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
            max-width: 800px;
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
        .form-floating {
            margin-bottom: 20px;
        }
        .form-floating input, .form-floating select {
            border-radius: 10px;
            border: 2px solid #e5e7eb;
            padding-left: 45px;
        }
        .form-floating input:focus, .form-floating select:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 0.2rem rgba(79, 70, 229, 0.15);
        }
        .form-floating label {
            padding-left: 45px;
        }
        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            z-index: 5;
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
        .section-title {
            font-size: 1rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e5e7eb;
        }
        .section-title i {
            color: #4f46e5;
            margin-right: 8px;
        }
    </style>
</head>
<body>
    <div class="install-container">
        <div class="install-header">
            <h1><i class="bi bi-gear me-2"></i>Site Configuration</h1>
            <p>Set up your website information and admin account</p>
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
                <div class="step active">
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

            @if(session('error'))
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                </div>
            @endif

            <form action="{{ route('install.save-site-config') }}" method="POST">
                @csrf
                
                <!-- Site Information -->
                <h5 class="section-title"><i class="bi bi-globe"></i>Site Information</h5>
                
                <div class="form-floating position-relative">
                    <i class="bi bi-shop input-icon"></i>
                    <input type="text" class="form-control" id="site_name" name="site_name" 
                           placeholder="Site Name" value="{{ old('site_name') }}" required>
                    <label for="site_name">Site Name</label>
                </div>

                <div class="form-floating position-relative">
                    <i class="bi bi-link-45deg input-icon"></i>
                    <input type="url" class="form-control" id="site_url" name="site_url" 
                           placeholder="Site URL" value="{{ old('site_url', request()->getSchemeAndHttpHost()) }}" required>
                    <label for="site_url">Site URL</label>
                </div>

                <div class="form-floating position-relative">
                    <i class="bi bi-clock input-icon"></i>
                    <select class="form-select" id="timezone" name="timezone" required>
                        <option value="Asia/Dhaka" {{ old('timezone') == 'Asia/Dhaka' ? 'selected' : '' }}>Asia/Dhaka (GMT+6)</option>
                        <option value="UTC" {{ old('timezone') == 'UTC' ? 'selected' : '' }}>UTC</option>
                        <option value="America/New_York" {{ old('timezone') == 'America/New_York' ? 'selected' : '' }}>America/New_York</option>
                        <option value="Europe/London" {{ old('timezone') == 'Europe/London' ? 'selected' : '' }}>Europe/London</option>
                        <option value="Asia/Kolkata" {{ old('timezone') == 'Asia/Kolkata' ? 'selected' : '' }}>Asia/Kolkata</option>
                        <option value="Asia/Dubai" {{ old('timezone') == 'Asia/Dubai' ? 'selected' : '' }}>Asia/Dubai</option>
                    </select>
                    <label for="timezone">Timezone</label>
                </div>

                <!-- Admin Account -->
                <h5 class="section-title mt-4"><i class="bi bi-person-circle"></i>Admin Account</h5>

                <div class="form-floating position-relative">
                    <i class="bi bi-person input-icon"></i>
                    <input type="text" class="form-control" id="admin_name" name="admin_name" 
                           placeholder="Admin Name" value="{{ old('admin_name') }}" required>
                    <label for="admin_name">Admin Name</label>
                </div>

                <div class="form-floating position-relative">
                    <i class="bi bi-envelope input-icon"></i>
                    <input type="email" class="form-control" id="admin_email" name="admin_email" 
                           placeholder="Admin Email" value="{{ old('admin_email') }}" required>
                    <label for="admin_email">Admin Email</label>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating position-relative">
                            <i class="bi bi-lock input-icon"></i>
                            <input type="password" class="form-control" id="admin_password" name="admin_password" 
                                   placeholder="Password" required>
                            <label for="admin_password">Password</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating position-relative">
                            <i class="bi bi-lock-fill input-icon"></i>
                            <input type="password" class="form-control" id="admin_password_confirmation" name="admin_password_confirmation" 
                                   placeholder="Confirm Password" required>
                            <label for="admin_password_confirmation">Confirm Password</label>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('install.database') }}" class="btn btn-back">
                        <i class="bi bi-arrow-left me-2"></i>Back
                    </a>
                    <button type="submit" class="btn btn-primary btn-install">
                        Save & Continue <i class="bi bi-arrow-right ms-2"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
