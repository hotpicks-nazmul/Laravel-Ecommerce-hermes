@extends('admin.layouts.app')

@section('title', 'Footer Settings')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 py-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="mb-1 fw-bold">
                            <i class="bi bi-layout-text-window-reverse text-primary me-2"></i> Footer Settings
                        </h4>
                        <p class="text-muted mb-0 small">Configure your website footer section</p>
                    </div>
                    <a href="{{ route('admin.settings.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left me-1"></i> Back to Settings
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<form action="{{ route('admin.settings.footer.update') }}" method="POST">
    @csrf

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row">
        <!-- About Section -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-semibold"><i class="bi bi-info-circle me-2"></i>About Section</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-medium">About Text</label>
                        <textarea name="footer_about_text" class="form-control" rows="4" placeholder="Your trusted source for premium quality halal meat...">{{ $settings['footer_about_text'] ?? '' }}</textarea>
                        <div class="form-text">Brief description of your business shown in footer</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Social Media Links -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-semibold"><i class="bi bi-share me-2"></i>Social Media Links</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-medium"><i class="bi bi-facebook text-primary me-2"></i>Facebook URL</label>
                        <input type="text" name="footer_facebook_url" class="form-control" value="{{ $settings['footer_facebook_url'] ?? '' }}" placeholder="https://facebook.com/yourpage">
                        <div class="form-text">Enter full URL including https://</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium"><i class="bi bi-instagram text-danger me-2"></i>Instagram URL</label>
                        <input type="text" name="footer_instagram_url" class="form-control" value="{{ $settings['footer_instagram_url'] ?? '' }}" placeholder="https://instagram.com/yourpage">
                        <div class="form-text">Enter full URL including https://</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium"><i class="bi bi-youtube text-danger me-2"></i>YouTube URL</label>
                        <input type="text" name="footer_youtube_url" class="form-control" value="{{ $settings['footer_youtube_url'] ?? '' }}" placeholder="https://youtube.com/yourchannel">
                        <div class="form-text">Enter full URL including https://</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium"><i class="bi bi-twitter text-info me-2"></i>Twitter URL</label>
                        <input type="text" name="footer_twitter_url" class="form-control" value="{{ $settings['footer_twitter_url'] ?? '' }}" placeholder="https://twitter.com/yourpage">
                        <div class="form-text">Enter full URL including https://</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium"><i class="bi bi-linkedin text-primary me-2"></i>LinkedIn URL</label>
                        <input type="text" name="footer_linkedin_url" class="form-control" value="{{ $settings['footer_linkedin_url'] ?? '' }}" placeholder="https://linkedin.com/company/yourcompany">
                        <div class="form-text">Enter full URL including https://</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Contact Information -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-semibold"><i class="bi bi-telephone me-2"></i>Contact Information</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-medium">Address</label>
                        <textarea name="footer_address" class="form-control" rows="2" placeholder="123 Green Market Road, Dhaka-1205, Bangladesh">{{ $settings['footer_address'] ?? '' }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Phone Number</label>
                        <input type="text" name="footer_phone" class="form-control" value="{{ $settings['footer_phone'] ?? '' }}" placeholder="+880 1700-000000">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Email Address</label>
                        <input type="email" name="footer_email" class="form-control" value="{{ $settings['footer_email'] ?? '' }}" placeholder="info@example.com">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Business Hours</label>
                        <input type="text" name="footer_business_hours" class="form-control" value="{{ $settings['footer_business_hours'] ?? '' }}" placeholder="Sat - Thu: 8AM - 10PM">
                    </div>
                </div>
            </div>
        </div>

        <!-- Newsletter Section -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-semibold"><i class="bi bi-envelope me-2"></i>Newsletter Section</h5>
                </div>
                <div class="card-body">
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="footer_newsletter_enabled" id="footer_newsletter_enabled" value="1" {{ ($settings['footer_newsletter_enabled'] ?? '1') === '1' ? 'checked' : '' }}>
                        <label class="form-check-label fw-medium" for="footer_newsletter_enabled">Show Newsletter Section</label>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Newsletter Title</label>
                        <input type="text" name="footer_newsletter_title" class="form-control" value="{{ $settings['footer_newsletter_title'] ?? 'Subscribe to Our Newsletter' }}" placeholder="Subscribe to Our Newsletter">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Newsletter Subtitle</label>
                        <input type="text" name="footer_newsletter_subtitle" class="form-control" value="{{ $settings['footer_newsletter_subtitle'] ?? 'Get updates on new products and special offers!' }}" placeholder="Get updates on new products...">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Column Titles -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-semibold"><i class="bi bi-columns me-2"></i>Column Titles</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-medium">Column 1 Title (Quick Links)</label>
                        <input type="text" name="footer_column1_title" class="form-control" value="{{ $settings['footer_column1_title'] ?? 'Quick Links' }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Column 2 Title (Customer Service)</label>
                        <input type="text" name="footer_column2_title" class="form-control" value="{{ $settings['footer_column2_title'] ?? 'Customer Service' }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Column 3 Title (Contact)</label>
                        <input type="text" name="footer_column3_title" class="form-control" value="{{ $settings['footer_column3_title'] ?? 'Contact Us' }}">
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment & Copyright -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-semibold"><i class="bi bi-credit-card me-2"></i>Payment & Copyright</h5>
                </div>
                <div class="card-body">
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="footer_show_payment_icons" id="footer_show_payment_icons" value="1" {{ ($settings['footer_show_payment_icons'] ?? '1') === '1' ? 'checked' : '' }}>
                        <label class="form-check-label fw-medium" for="footer_show_payment_icons">Show Payment Methods</label>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Payment Methods (comma separated)</label>
                        <input type="text" name="footer_payment_methods" class="form-control" value="{{ $settings['footer_payment_methods'] ?? 'bkash,nagad,rocket,visa,mastercard' }}" placeholder="bkash,nagad,rocket,visa,mastercard">
                        <div class="form-text">Available: bkash, nagad, rocket, visa, mastercard, amex, paypal</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">Copyright Text</label>
                        <input type="text" name="footer_copyright_text" class="form-control" value="{{ $settings['footer_copyright_text'] ?? '' }}" placeholder="© 2024 Your Store. All rights reserved.">
                        <div class="form-text">Leave empty to use default: © Year Site Name. All rights reserved.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Floating Save Button -->
    <div class="floating-save-container">
        <a href="{{ route('admin.settings.footer') }}" class="btn btn-secondary floating-reset-btn">
            <i class="bi bi-arrow-clockwise me-1"></i> Reset
        </a>
        <button type="submit" class="btn btn-primary floating-save-btn">
            <i class="bi bi-check-lg me-1"></i> Save Footer Settings
        </button>
    </div>
</form>
@endsection
