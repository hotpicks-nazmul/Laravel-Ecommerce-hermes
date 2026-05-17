<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installation Complete</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    animation: {
                        'fade-in': 'fadeIn 0.5s ease-out',
                        'scale-in': 'scaleIn 0.5s ease-out',
                        'bounce-in': 'bounceIn 0.6s ease-out',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0', transform: 'translateY(10px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        },
                        scaleIn: {
                            '0%': { opacity: '0', transform: 'scale(0.5)' },
                            '100%': { opacity: '1', transform: 'scale(1)' },
                        },
                        bounceIn: {
                            '0%': { opacity: '0', transform: 'scale(0.3)' },
                            '50%': { transform: 'scale(1.05)' },
                            '70%': { transform: 'scale(0.9)' },
                            '100%': { opacity: '1', transform: 'scale(1)' },
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
            max-width: 600px;
            width: 100%;
            background: #ffffff;
            border-radius: 1.5rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            overflow: hidden;
        }
        .success-header {
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
            padding: 3rem 2rem;
            text-align: center;
            color: white;
        }
        .checkmark-circle {
            width: 5rem;
            height: 5rem;
            border-radius: 50%;
            background: rgba(255,255,255,0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.25rem;
        }
        .checkmark-circle svg {
            width: 2.5rem;
            height: 2.5rem;
        }
        .card-body { padding: 2rem; }
        .summary-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid #f3f4f6;
            font-size: 0.875rem;
        }
        .summary-item:last-child { border-bottom: none; }
        .summary-label { color: #6b7280; font-weight: 500; }
        .summary-value { color: #111827; font-weight: 600; }
        .btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
            color: white;
            font-weight: 600;
            font-size: 0.875rem;
            border: none;
            border-radius: 0.75rem;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(79, 70, 229, 0.3);
        }
        .btn-outline {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            background: white;
            color: #374151;
            font-weight: 600;
            font-size: 0.875rem;
            border: 1.5px solid #d1d5db;
            border-radius: 0.75rem;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        .btn-outline:hover {
            background: #f9fafb;
            border-color: #9ca3af;
        }
        .confetti-canvas {
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
    <canvas id="confettiCanvas" class="confetti-canvas"></canvas>

    <div class="card">
        <div class="success-header">
            <div class="checkmark-circle">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold">Installation Complete!</h1>
            <p class="text-white/80 mt-2">Your application is ready to use.</p>
        </div>

        <div class="card-body">
            <div class="bg-gray-50 rounded-xl p-5 mb-6">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">Installation Summary</h3>
                <div class="summary-item">
                    <span class="summary-label">Application</span>
                    <span class="summary-value">{{ $appConfig['name'] }}</span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">URL</span>
                    <span class="summary-value text-indigo-600">{{ $appConfig['url'] }}</span>
                </div>
                <div class="summary-item">
                    <span class="summary-label">Admin Email</span>
                    <span class="summary-value">{{ $admin['email'] }}</span>
                </div>
            </div>

            <p class="text-sm text-gray-500 mb-6 text-center">You can now log in to the admin panel and start configuring your application.</p>

            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <a href="{{ route('admin.login') }}" class="btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Go to Admin Panel
                </a>
                <a href="{{ route('home') }}" class="btn-outline">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Visit Site
                </a>
            </div>

            <div class="mt-6 p-3 bg-amber-50 border border-amber-200 rounded-xl flex items-start gap-2">
                <svg class="w-4 h-4 text-amber-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
                <p class="text-xs text-amber-700">For security, the <code class="text-xs bg-amber-100 px-1 rounded">/install</code> routes are now disabled. The installation lock file has been created.</p>
            </div>
        </div>
    </div>

    <script>
        window.addEventListener('load', function () {
            const canvas = document.getElementById('confettiCanvas');
            const ctx = canvas.getContext('2d');
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;

            const particles = [];
            const colors = ['#4f46e5', '#6366f1', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899'];

            for (let i = 0; i < 150; i++) {
                particles.push({
                    x: Math.random() * canvas.width,
                    y: Math.random() * canvas.height - canvas.height,
                    w: Math.random() * 10 + 5,
                    h: Math.random() * 6 + 3,
                    color: colors[Math.floor(Math.random() * colors.length)],
                    vx: (Math.random() - 0.5) * 2,
                    vy: Math.random() * 3 + 1,
                    rotation: Math.random() * 360,
                    rotSpeed: (Math.random() - 0.5) * 10,
                });
            }

            function animate() {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                let active = false;

                particles.forEach(p => {
                    if (p.y < canvas.height + 20) {
                        active = true;
                        p.x += p.vx;
                        p.y += p.vy;
                        p.vy += 0.02;
                        p.rotation += p.rotSpeed;

                        ctx.save();
                        ctx.translate(p.x, p.y);
                        ctx.rotate((p.rotation * Math.PI) / 180);
                        ctx.fillStyle = p.color;
                        ctx.fillRect(-p.w / 2, -p.h / 2, p.w, p.h);
                        ctx.restore();
                    }
                });

                if (active) {
                    requestAnimationFrame(animate);
                }
            }

            setTimeout(animate, 300);
        });
    </script>
</body>
</html>
