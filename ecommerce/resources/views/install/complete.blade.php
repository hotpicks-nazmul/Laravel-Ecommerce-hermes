<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installation Complete - E-Commerce Platform</title>
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
            max-width: 700px;
            width: 100%;
        }
        .install-header {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 50px;
            text-align: center;
        }
        .success-icon {
            width: 100px;
            height: 100px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            animation: scaleIn 0.5s ease-out;
        }
        .success-icon i {
            font-size: 3rem;
        }
        @keyframes scaleIn {
            0% { transform: scale(0); }
            100% { transform: scale(1); }
        }
        .install-header h1 {
            font-size: 2rem;
            margin-bottom: 10px;
        }
        .install-body {
            padding: 40px;
        }
        .feature-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        .feature-item {
            display: flex;
            align-items: center;
            padding: 15px;
            background: #f9fafb;
            border-radius: 10px;
        }
        .feature-item i {
            font-size: 1.5rem;
            color: #10b981;
            margin-right: 15px;
        }
        .feature-item span {
            color: #374151;
            font-weight: 500;
        }
        .btn-admin {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            border: none;
            padding: 15px 40px;
            font-size: 1.1rem;
            border-radius: 10px;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .btn-admin:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(79, 70, 229, 0.3);
        }
        .btn-frontend {
            background: #f3f4f6;
            color: #374151;
            border: none;
            padding: 15px 40px;
            font-size: 1.1rem;
            border-radius: 10px;
        }
        .btn-frontend:hover {
            background: #e5e7eb;
        }
        .security-note {
            background: #fef3c7;
            border-radius: 10px;
            padding: 15px;
            margin-top: 20px;
        }
        .security-note i {
            color: #d97706;
        }
        .confetti {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 1000;
        }
    </style>
</head>
<body>
    <canvas id="confetti" class="confetti"></canvas>
    
    <div class="install-container">
        <div class="install-header">
            <div class="success-icon">
                <i class="bi bi-check-lg"></i>
            </div>
            <h1>Installation Complete!</h1>
            <p>Your e-commerce platform is ready to use</p>
        </div>
        
        <div class="install-body text-center">
            <p class="mb-4">Congratulations! Your multi-purpose e-commerce platform has been successfully installed. You can now start building your online store.</p>

            <div class="feature-grid">
                <div class="feature-item">
                    <i class="bi bi-palette"></i>
                    <span>Dynamic Theme System</span>
                </div>
                <div class="feature-item">
                    <i class="bi bi-credit-card"></i>
                    <span>Multiple Payment Gateways</span>
                </div>
                <div class="feature-item">
                    <i class="bi bi-shield-check"></i>
                    <span>SEO Optimized</span>
                </div>
                <div class="feature-item">
                    <i class="bi bi-chat-dots"></i>
                    <span>Live Chat & AI Bot</span>
                </div>
                <div class="feature-item">
                    <i class="bi bi-box-seam"></i>
                    <span>Product Management</span>
                </div>
                <div class="feature-item">
                    <i class="bi bi-graph-up"></i>
                    <span>Analytics & Reports</span>
                </div>
            </div>

            <div class="d-flex justify-content-center gap-3 mb-4">
                <a href="{{ route('admin.dashboard') }}" class="btn btn-primary btn-admin">
                    <i class="bi bi-speedometer2 me-2"></i>Go to Admin Panel
                </a>
                <a href="{{ route('home') }}" class="btn btn-frontend">
                    <i class="bi bi-shop me-2"></i>View Website
                </a>
            </div>

            <div class="security-note">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <strong>Security Note:</strong> For security reasons, please delete the <code>/install</code> route or restrict access to it after installation. You can do this from the admin panel settings.
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
    <script>
        // Trigger confetti animation
        window.addEventListener('load', function() {
            setTimeout(function() {
                confetti({
                    particleCount: 100,
                    spread: 70,
                    origin: { y: 0.6 }
                });
            }, 500);
        });
    </script>
</body>
</html>
