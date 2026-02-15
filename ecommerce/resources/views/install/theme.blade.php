<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Theme Selection - Installation Wizard</title>
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
            max-width: 1000px;
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
        .theme-card {
            border: 3px solid #e5e7eb;
            border-radius: 15px;
            overflow: hidden;
            transition: all 0.3s ease;
            cursor: pointer;
            height: 100%;
        }
        .theme-card:hover {
            border-color: #4f46e5;
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(79, 70, 229, 0.2);
        }
        .theme-card.selected {
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.3);
        }
        .theme-preview {
            height: 180px;
            background-size: cover;
            background-position: center;
            position: relative;
        }
        .theme-preview::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 50%;
            background: linear-gradient(transparent, rgba(0,0,0,0.7));
        }
        .theme-category {
            position: absolute;
            top: 15px;
            right: 15px;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
            z-index: 1;
        }
        .category-food { background: #ff6b35; color: white; }
        .category-technology { background: #3b82f6; color: white; }
        .category-education { background: #10b981; color: white; }
        .category-virtual { background: #8b5cf6; color: white; }
        .category-general { background: #6b7280; color: white; }
        .theme-info {
            padding: 20px;
        }
        .theme-info h5 {
            margin-bottom: 10px;
            color: #1f2937;
        }
        .theme-info p {
            color: #6b7280;
            font-size: 0.9rem;
            margin-bottom: 15px;
        }
        .theme-features {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }
        .theme-feature {
            background: #f3f4f6;
            padding: 4px 10px;
            border-radius: 15px;
            font-size: 0.75rem;
            color: #4b5563;
        }
        .theme-check {
            position: absolute;
            top: 15px;
            left: 15px;
            width: 30px;
            height: 30px;
            background: #4f46e5;
            border-radius: 50%;
            display: none;
            align-items: center;
            justify-content: center;
            color: white;
            z-index: 2;
        }
        .theme-card.selected .theme-check {
            display: flex;
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
    </style>
</head>
<body>
    <div class="install-container">
        <div class="install-header">
            <h1><i class="bi bi-palette me-2"></i>Theme Selection</h1>
            <p>Choose a theme for your e-commerce website</p>
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
                <div class="step active">
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

            <form action="{{ route('install.save-theme') }}" method="POST" id="themeForm">
                @csrf
                <input type="hidden" name="theme" id="selectedTheme" value="">
                
                <div class="row g-4">
                    @php
                        $defaultThemes = [
                            'food' => [
                                'name' => 'Food Theme',
                                'slug' => 'food',
                                'description' => 'Perfect for restaurants, grocery stores, and food delivery services.',
                                'category' => 'food',
                                'features' => ['Menu Style', 'Order Online', 'Delivery Tracking', 'Reviews'],
                                'preview' => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=400'
                            ],
                            'technology' => [
                                'name' => 'Technology Theme',
                                'slug' => 'technology',
                                'description' => 'Modern design for electronics, gadgets, and tech products.',
                                'category' => 'technology',
                                'features' => ['Product Compare', 'Specs Table', 'Warranty Info', 'Reviews'],
                                'preview' => 'https://images.unsplash.com/photo-1518770660439-4636190af475?w=400'
                            ],
                            'education' => [
                                'name' => 'Education Theme',
                                'slug' => 'education',
                                'description' => 'Clean design for courses, books, and educational materials.',
                                'category' => 'education',
                                'features' => ['Course List', 'Instructor Profile', 'Progress Track', 'Certificates'],
                                'preview' => 'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?w=400'
                            ],
                            'virtual' => [
                                'name' => 'Virtual Products Theme',
                                'slug' => 'virtual',
                                'description' => 'Minimal design for digital products, software, and downloads.',
                                'category' => 'virtual',
                                'features' => ['Instant Download', 'License Keys', 'Version History', 'Support'],
                                'preview' => 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=400'
                            ],
                            'general' => [
                                'name' => 'General Theme',
                                'slug' => 'general',
                                'description' => 'Versatile design suitable for any type of products.',
                                'category' => 'general',
                                'features' => ['Flexible Layout', 'Product Grid', 'Quick View', 'Wishlist'],
                                'preview' => 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=400'
                            ]
                        ];
                        $themes = !empty($themes) ? $themes : $defaultThemes;
                    @endphp
                    
                    @foreach($themes as $slug => $theme)
                        <div class="col-md-6 col-lg-4">
                            <div class="theme-card" data-theme="{{ $slug }}" onclick="selectTheme('{{ $slug }}')">
                                <div class="theme-preview" style="background-image: url('{{ $theme['preview'] ?? $theme['preview_image'] ?? '' }}')">
                                    <div class="theme-check">
                                        <i class="bi bi-check-lg"></i>
                                    </div>
                                    <span class="theme-category category-{{ $theme['category'] ?? 'general' }}">
                                        {{ ucfirst($theme['category'] ?? 'general') }}
                                    </span>
                                </div>
                                <div class="theme-info">
                                    <h5>{{ $theme['name'] }}</h5>
                                    <p>{{ $theme['description'] }}</p>
                                    <div class="theme-features">
                                        @foreach(($theme['features'] ?? []) as $feature)
                                            <span class="theme-feature">{{ $feature }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('install.site-config') }}" class="btn btn-back">
                        <i class="bi bi-arrow-left me-2"></i>Back
                    </a>
                    <button type="submit" class="btn btn-primary btn-install" id="submitBtn" disabled>
                        Continue <i class="bi bi-arrow-right ms-2"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function selectTheme(themeSlug) {
            // Remove selected class from all cards
            document.querySelectorAll('.theme-card').forEach(card => {
                card.classList.remove('selected');
            });
            
            // Add selected class to clicked card
            document.querySelector(`.theme-card[data-theme="${themeSlug}"]`).classList.add('selected');
            
            // Update hidden input
            document.getElementById('selectedTheme').value = themeSlug;
            
            // Enable submit button
            document.getElementById('submitBtn').disabled = false;
        }
    </script>
</body>
</html>
