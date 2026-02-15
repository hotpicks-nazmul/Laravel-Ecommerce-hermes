<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Configuration - Installation Wizard</title>
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
        .form-floating input {
            border-radius: 10px;
            border: 2px solid #e5e7eb;
            padding-left: 45px;
        }
        .form-floating input:focus {
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
        .alert {
            border-radius: 10px;
            border: none;
        }
        .db-info {
            background: #f0f9ff;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .db-info i {
            color: #0ea5e9;
        }
    </style>
</head>
<body>
    <div class="install-container">
        <div class="install-header">
            <h1><i class="bi bi-database me-2"></i>Database Configuration</h1>
            <p>Enter your MySQL database credentials</p>
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
                <div class="step active">
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

            @if(session('error'))
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                </div>
            @endif

            <div class="db-info">
                <i class="bi bi-info-circle me-2"></i>
                <small>Enter your MySQL database credentials. The database will be created automatically if it doesn't exist.</small>
            </div>

            <form action="{{ route('install.setup-database') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-floating position-relative">
                            <i class="bi bi-server input-icon"></i>
                            <input type="text" class="form-control" id="db_host" name="db_host" 
                                   placeholder="Database Host" value="{{ old('db_host', 'localhost') }}" required>
                            <label for="db_host">Database Host</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating position-relative">
                            <i class="bi bi-hash input-icon"></i>
                            <input type="number" class="form-control" id="db_port" name="db_port" 
                                   placeholder="Port" value="{{ old('db_port', '3306') }}" required>
                            <label for="db_port">Port</label>
                        </div>
                    </div>
                </div>

                <div class="form-floating position-relative">
                    <i class="bi bi-database input-icon"></i>
                    <input type="text" class="form-control" id="db_name" name="db_name" 
                           placeholder="Database Name" value="{{ old('db_name') }}" required>
                    <label for="db_name">Database Name</label>
                </div>

                <div class="form-floating position-relative">
                    <i class="bi bi-person input-icon"></i>
                    <input type="text" class="form-control" id="db_username" name="db_username" 
                           placeholder="Database Username" value="{{ old('db_username') }}" required>
                    <label for="db_username">Database Username</label>
                </div>

                <div class="form-floating position-relative">
                    <i class="bi bi-lock input-icon"></i>
                    <input type="password" class="form-control" id="db_password" name="db_password" 
                           placeholder="Database Password" value="{{ old('db_password') }}">
                    <label for="db_password">Database Password</label>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('install.requirements') }}" class="btn btn-back">
                        <i class="bi bi-arrow-left me-2"></i>Back
                    </a>
                    <button type="submit" class="btn btn-primary btn-install">
                        Test Connection <i class="bi bi-arrow-right ms-2"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
