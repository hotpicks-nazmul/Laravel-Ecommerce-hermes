@extends('themes.general.layouts.app')

@section('title', 'Privacy Policy')

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
        <h1 class="display-4 fw-bold mb-3">Privacy Policy</h1>
        <p class="lead mb-0 opacity-75">How we handle your information</p>
    </div>
</section>

<!-- Privacy Content -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="content-card">
                    @if($page)
                        {!! $page->content !!}
                    @else
                        <h5>1. Information We Collect</h5>
                        <p>We collect information you provide directly, including:</p>
                        <ul>
                            <li>Name, email address, phone number</li>
                            <li>Delivery address</li>
                            <li>Payment information</li>
                            <li>Order history and preferences</li>
                        </ul>
                        
                        <h5>2. How We Use Your Information</h5>
                        <p>We use your information to:</p>
                        <ul>
                            <li>Process and deliver your orders</li>
                            <li>Communicate with you about your orders</li>
                            <li>Send promotional offers (with your consent)</li>
                            <li>Improve our services and customer experience</li>
                        </ul>
                        
                        <h5>3. Information Sharing</h5>
                        <p>We do not sell your personal information. We may share your information with:</p>
                        <ul>
                            <li>Delivery partners for order fulfillment</li>
                            <li>Payment processors for transactions</li>
                            <li>Legal authorities when required by law</li>
                        </ul>
                        
                        <h5>4. Data Security</h5>
                        <p>We implement appropriate security measures to protect your personal information from unauthorized access, alteration, or disclosure.</p>
                        
                        <h5>5. Cookies</h5>
                        <p>We use cookies to enhance your browsing experience, analyze site traffic, and personalize content. You can control cookie settings in your browser.</p>
                        
                        <h5>6. Your Rights</h5>
                        <p>You have the right to:</p>
                        <ul>
                            <li>Access your personal information</li>
                            <li>Correct inaccurate information</li>
                            <li>Request deletion of your data</li>
                            <li>Opt-out of marketing communications</li>
                        </ul>
                        
                        <h5>7. Children's Privacy</h5>
                        <p>Our services are not intended for children under 13. We do not knowingly collect information from children.</p>
                        
                        <h5>8. Changes to Policy</h5>
                        <p>We may update this policy periodically. Continued use of our services constitutes acceptance of changes.</p>
                        
                        <h5>9. Contact Us</h5>
                        <p>For privacy-related questions, contact us at info@halalfoodstore.com.</p>
                        
                        <p class="text-muted mt-4"><em>Last updated: {{ date('F d, Y') }}</em></p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
