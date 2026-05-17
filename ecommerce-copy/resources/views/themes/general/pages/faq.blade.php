@extends('themes.general.layouts.app')

@section('title', 'FAQ')

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
.accordion-item {
    border: none;
    margin-bottom: 16px;
    border-radius: 12px !important;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}
.accordion-button {
    font-weight: 600;
    padding: 20px 24px;
    background: #fff;
}
.accordion-button:not(.collapsed) {
    background: linear-gradient(135deg, #2D5A27 0%, #4A7C43 100%);
    color: #fff;
}
.accordion-button:focus {
    box-shadow: none;
}
.accordion-body {
    padding: 20px 24px;
    background: #fff;
}
</style>
@endpush

@section('content')
<!-- Hero Section -->
<section class="page-hero text-white text-center">
    <div class="container position-relative">
        <h1 class="display-4 fw-bold mb-3">Frequently Asked Questions</h1>
        <p class="lead mb-0 opacity-75">Find answers to common questions</p>
    </div>
</section>

<!-- FAQ Content -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                @if($page)
                    <div class="bg-white rounded-4 p-4 p-md-5 shadow-sm mb-5">
                        {{-- Content is expected to be HTML from admin editor. Ensure admin input is sanitized. --}}
                        {!! class_exists('Purifier') ? Purifier::clean($page->content) : $page->content !!}
                    </div>
                @endif
                
                @if($faqs->count() > 0)
                <div class="accordion" id="faqAccordion">
                    @foreach($faqs as $index => $faq)
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button {{ $index === 0 ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#faq{{ $faq->id }}">
                                {{ $faq->question }}
                            </button>
                        </h2>
                        <div id="faq{{ $faq->id }}" class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                {!! nl2br(e($faq->answer)) !!}
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <!-- Fallback static content when no FAQs in database -->
                <div class="accordion" id="faqAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                What makes your products halal?
                            </button>
                        </h2>
                        <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                All our products are sourced from certified halal suppliers and slaughtered according to Islamic guidelines. 
                                We maintain strict quality control and work with recognized Islamic certification bodies to ensure compliance.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                How do you ensure product freshness?
                            </button>
                        </h2>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                We maintain a cold chain from sourcing to delivery. Our products are stored in temperature-controlled 
                                facilities and delivered in insulated vehicles to ensure maximum freshness.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                What are your delivery areas?
                            </button>
                        </h2>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                We currently deliver to all major areas in Dhaka and are expanding to other cities. 
                                Enter your location at checkout to check if we deliver to your area.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                What payment methods do you accept?
                            </button>
                        </h2>
                        <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                We accept bKash, Nagad, Rocket, credit/debit cards (Visa, Mastercard), and cash on delivery 
                                in selected areas.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq5">
                                Can I return or exchange products?
                            </button>
                        </h2>
                        <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Yes, if you receive damaged or unsatisfactory products, please contact us within 2 hours of delivery. 
                                We will arrange for a replacement or refund.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq6">
                                How can I track my order?
                            </button>
                        </h2>
                        <div id="faq6" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Once your order is confirmed, you will receive an SMS with tracking information. 
                                You can also track your order from your account dashboard.
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                
                <!-- Contact CTA -->
                <div class="text-center mt-5">
                    <p class="text-muted mb-3">Still have questions?</p>
                    <a href="{{ route('pages.contact') }}" class="btn btn-primary px-4">
                        <i class="bi bi-chat-dots me-2"></i>Contact Us
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
