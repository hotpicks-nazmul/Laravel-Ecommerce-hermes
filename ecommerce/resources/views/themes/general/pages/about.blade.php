@extends('themes.general.layouts.app')

@section('title', 'About Us')

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
.feature-card {
    border: none;
    border-radius: 16px;
    padding: 32px;
    transition: all 0.3s ease;
    background: #fff;
}
.feature-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 50px rgba(0,0,0,0.1);
}
.feature-icon {
    width: 80px;
    height: 80px;
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    margin-bottom: 20px;
}
</style>
@endpush

@section('content')
<!-- Hero Section -->
<section class="page-hero text-white text-center">
    <div class="container position-relative">
        <h1 class="display-4 fw-bold mb-3">About Us</h1>
        <p class="lead mb-0 opacity-75">Learn more about our story and mission</p>
    </div>
</section>

<!-- About Content -->
<section class="py-5">
    <div class="container">
        @if($page)
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="bg-white rounded-4 p-4 p-md-5 shadow-sm">
                        {!! $page->content !!}
                    </div>
                </div>
            </div>
        @else
            <!-- Default Content -->
            <div class="row align-items-center g-5 mb-5">
                <div class="col-lg-6">
                    <img src="https://images.unsplash.com/photo-1542838132-92c53300491e?w=600" alt="About Us" class="img-fluid rounded-4 shadow">
                </div>
                <div class="col-lg-6">
                    <h2 class="fw-bold mb-4">Our Story</h2>
                    <p class="text-muted mb-4">
                        Welcome to Halal Food Store, your trusted source for premium quality halal meat, poultry, seafood, and groceries. 
                        We started our journey with a simple mission: to provide fresh, high-quality halal food to families across Bangladesh.
                    </p>
                    <p class="text-muted mb-4">
                        Our commitment to quality and customer satisfaction has made us one of the leading halal food providers in the region. 
                        We work directly with certified suppliers to ensure that every product meets the highest standards of halal certification.
                    </p>
                    <p class="text-muted">
                        From farm to your table, we maintain strict quality control and cold chain management to deliver the freshest products 
                        right to your doorstep.
                    </p>
                </div>
            </div>
            
            <!-- Features -->
            <div class="row g-4 mt-5">
                <div class="col-md-4">
                    <div class="feature-card text-center h-100">
                        <div class="feature-icon bg-success bg-opacity-10 text-success mx-auto">
                            <i class="bi bi-patch-check"></i>
                        </div>
                        <h5 class="fw-bold mb-3">100% Halal Certified</h5>
                        <p class="text-muted mb-0">All our products are certified halal by recognized Islamic authorities.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card text-center h-100">
                        <div class="feature-icon bg-primary bg-opacity-10 text-primary mx-auto">
                            <i class="bi bi-truck"></i>
                        </div>
                        <h5 class="fw-bold mb-3">Fast Delivery</h5>
                        <p class="text-muted mb-0">We deliver fresh products to your doorstep within hours.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card text-center h-100">
                        <div class="feature-icon bg-warning bg-opacity-10 text-warning mx-auto">
                            <i class="bi bi-star"></i>
                        </div>
                        <h5 class="fw-bold mb-3">Premium Quality</h5>
                        <p class="text-muted mb-0">We source only the finest quality products for our customers.</p>
                    </div>
                </div>
            </div>
            
            <!-- Mission & Vision -->
            <div class="row g-4 mt-5">
                <div class="col-md-6">
                    <div class="feature-card h-100">
                        <h5 class="fw-bold mb-3"><i class="bi bi-bullseye text-success me-2"></i>Our Mission</h5>
                        <p class="text-muted mb-0">
                            To provide families with fresh, high-quality halal food products while maintaining the highest standards 
                            of quality, hygiene, and customer service.
                        </p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="feature-card h-100">
                        <h5 class="fw-bold mb-3"><i class="bi bi-eye text-primary me-2"></i>Our Vision</h5>
                        <p class="text-muted mb-0">
                            To become the most trusted halal food provider in Bangladesh, known for quality, reliability, 
                            and exceptional customer experience.
                        </p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</section>
@endsection
