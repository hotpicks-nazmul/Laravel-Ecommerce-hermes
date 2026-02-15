<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installation Wizard - E-Commerce Platform</title>
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
            padding: 40px;
            text-align: center;
        }
        .install-header h1 {
            font-size: 2rem;
            margin-bottom: 10px;
        }
        .install-header p {
            opacity: 0.9;
            margin-bottom: 0;
        }
        .install-body {
            padding: 40px;
        }
        .step-indicator {
            display: flex;
            justify-content: center;
            margin-bottom: 40px;
        }
        .step {
            display: flex;
            align-items: center;
            color: #9ca3af;
        }
        .step.active {
            color: #4f46e5;
        }
        .step.completed {
            color: #10b981;
        }
        .step-number {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            border: 2px solid currentColor;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            margin-right: 10px;
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
            width: 50px;
            height: 2px;
            background: #e5e7eb;
            margin: 0 15px;
        }
        .step.completed + .step-line {
            background: #10b981;
        }
        .feature-list {
            list-style: none;
            padding: 0;
        }
        .feature-list li {
            padding: 12px 0;
            border-bottom: 1px solid #f3f4f6;
            display: flex;
            align-items: center;
        }
        .feature-list li:last-child {
            border-bottom: none;
        }
        .feature-list i {
            color: #10b981;
            margin-right: 15px;
            font-size: 1.2rem;
        }
        .btn-install {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            border: none;
            padding: 15px 40px;
            font-size: 1.1rem;
            border-radius: 10px;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .btn-install:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(79, 70, 229, 0.3);
        }
        .logo-icon {
            font-size: 4rem;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="install-container">
        <div class="install-header">
            <i class="bi bi-shop logo-icon"></i>
            <h1>Welcome to E-Commerce Platform</h1>
            <p>Multi-Purpose E-Commerce Solution with Dynamic Theme System</p>
        </div>
        
        <div class="install-body">
            <!-- Step Indicator -->
            <div class="step-indicator">
                <div class="step active">
                    <span class="step-number">1</span>
                    <span>Welcome</span>
                </div>
                <div class="step-line"></div>
                <div class="step">
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

            <div class="text-center mb-4">
                <h4>This wizard will guide you through the installation process</h4>
                <p class="text-muted">Follow the steps below to set up your e-commerce platform</p>
            </div>

            <div class="row justify-content-center">
                <div class="col-md-8">
                    <ul class="feature-list">
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            <span>Check server requirements and permissions</span>
                        </li>
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            <span>Configure database connection</span>
                        </li>
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            <span>Set up site information and admin account</span>
                        </li>
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            <span>Choose your preferred theme</span>
                        </li>
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            <span>Configure payment gateways</span>
                        </li>
                        <li>
                            <i class="bi bi-check-circle-fill"></i>
                            <span>Complete installation and start selling!</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="text-center mt-5">
                <a href="{{ route('install.requirements') }}" class="btn btn-primary btn-install">
                    Start Installation <i class="bi bi-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
