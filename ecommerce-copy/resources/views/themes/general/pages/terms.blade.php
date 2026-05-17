@extends('themes.general.layouts.app')

@section('title', 'Terms of Service')

@push('styles')
<style>
.page-hero {
    background: linear-gradient(135deg, #2D5A27 0%, #4A7C43 100%);
    padding: 80px 0;
    position: relative;
    overflow: hidden;
}
.page-hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
}
.content-card {
    background: #fff;
    border-radius: 16px;
    padding: 40px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
}
.content-card h5 {
    color: #2D5A27;
    margin-top: 2rem;
    margin-bottom: 1rem;
}
.content-card p, .content-card li {
    color: #666;
    line-height: 1.8;
}
</style>
@endpush

@section('content')
<!-- Hero Section -->
<section class="page-hero text-white text-center">
    <div class="container position-relative">
        <h1 class="display-4 fw-bold mb-3">Terms of Service</h1>
        <p class="lead mb-0 opacity-75">Please read these terms carefully</p>
    </div>
</section>

<!-- Terms Content -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="content-card">
                    @if($page)
                        {{-- Content is expected to be HTML from admin editor. Ensure admin input is sanitized. --}}
                        {!! class_exists('Purifier') ? Purifier::clean($page->content) : $page->content !!}
                    @else
                        <h5>1. Introduction</h5>
                        <p>Welcome to Halal Food Store. By accessing and using our website and services, you agree to be bound by these Terms of Service.</p>
                        
                        <h5>2. Products and Services</h5>
                        <p>We offer halal food products for online purchase and delivery. All products are certified halal and meet quality standards.</p>
                        
                        <h5>3. Orders and Payments</h5>
                        <ul>
                            <li>All orders are subject to product availability</li>
                            <li>Prices are subject to change without notice</li>
                            <li>Payment must be made at the time of order</li>
                            <li>We accept bKash, Nagad, Rocket, and credit/debit cards</li>
                        </ul>
                        
                        <h5>4. Delivery</h5>
                        <ul>
                            <li>Delivery times are estimates and not guaranteed</li>
                            <li>We deliver to selected areas in Bangladesh</li>
                            <li>A delivery fee may apply based on location</li>
                            <li>Someone must be available to receive the order</li>
                        </ul>
                        
                        <h5>5. Returns and Refunds</h5>
                        <p>If you receive damaged or unsatisfactory products, contact us within 2 hours of delivery for a replacement or refund.</p>
                        
                        <h5>6. Account Security</h5>
                        <p>You are responsible for maintaining the confidentiality of your account information and for all activities under your account.</p>
                        
                        <h5>7. Privacy</h5>
                        <p>Your use of our services is also governed by our Privacy Policy.</p>
                        
                        <h5>8. Changes to Terms</h5>
                        <p>We reserve the right to modify these terms at any time. Continued use of our services constitutes acceptance of modified terms.</p>
                        
                        <h5>9. Contact</h5>
                        <p>For questions about these terms, please contact us at info@halalfoodstore.com.</p>
                        
                        <p class="text-muted mt-4"><em>Last updated: {{ date('F d, Y') }}</em></p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
