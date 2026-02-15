@extends('themes.general.layouts.app')

@section('title', $blog->title)

@push('styles')
<style>
.blog-hero {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 60px 0;
    position: relative;
    overflow: hidden;
}
.blog-hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
}
.blog-featured-image {
    border-radius: 20px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.15);
    margin-top: -80px;
    position: relative;
    z-index: 10;
}
.blog-content-wrapper {
    background: #fff;
    border-radius: 20px;
    padding: 40px;
    margin-top: -40px;
    position: relative;
    z-index: 5;
    box-shadow: 0 10px 40px rgba(0,0,0,0.05);
}
.blog-content {
    font-size: 1.1rem;
    line-height: 1.9;
    color: #444;
}
.blog-content p {
    margin-bottom: 1.8rem;
}
.blog-content h2, .blog-content h3, .blog-content h4 {
    margin-top: 2.5rem;
    margin-bottom: 1.2rem;
    color: #333;
    font-weight: 700;
}
.blog-content img {
    max-width: 100%;
    height: auto;
    border-radius: 12px;
    margin: 2rem 0;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}
.blog-content ul, .blog-content ol {
    margin-bottom: 1.8rem;
    padding-left: 2rem;
}
.blog-content li {
    margin-bottom: 0.5rem;
}
.blog-content blockquote {
    border-left: 4px solid #667eea;
    padding: 20px 24px;
    margin: 2rem 0;
    font-style: italic;
    color: #666;
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
    border-radius: 0 12px 12px 0;
}
.blog-content a {
    color: #667eea;
    text-decoration: underline;
}
.blog-content a:hover {
    color: #764ba2;
}
.blog-content code {
    background: #f8f9fa;
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 0.9em;
}
.blog-content pre {
    background: #2d3748;
    color: #e2e8f0;
    padding: 20px;
    border-radius: 12px;
    overflow-x: auto;
    margin: 1.5rem 0;
}
.sidebar-wrapper {
    position: sticky;
    top: 100px;
}
.sidebar-card {
    border: none;
    border-radius: 16px;
    overflow: hidden;
    background: #fff;
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
}
.sidebar-card .card-body {
    padding: 24px;
}
.author-box {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
    border-radius: 16px;
    padding: 24px;
    display: flex;
    align-items: center;
    gap: 20px;
}
.author-box img {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #fff;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}
.share-buttons .btn {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0;
    transition: all 0.3s ease;
}
.share-buttons .btn:hover {
    transform: translateY(-3px);
}
.tag-badge {
    display: inline-block;
    padding: 8px 16px;
    background: #f8f9fa;
    border-radius: 20px;
    color: #555;
    font-size: 14px;
    margin: 4px;
    transition: all 0.3s ease;
}
.tag-badge:hover {
    background: #667eea;
    color: #fff;
}
.related-card {
    border: none;
    border-radius: 16px;
    overflow: hidden;
    transition: all 0.3s ease;
}
.related-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}
.related-card img {
    height: 140px;
    object-fit: cover;
}
.recent-post-item {
    display: flex;
    gap: 12px;
    padding: 12px 0;
    border-bottom: 1px solid #f0f0f0;
}
.recent-post-item:last-child {
    border-bottom: none;
}
.recent-post-item img {
    width: 70px;
    height: 70px;
    object-fit: cover;
    border-radius: 10px;
    flex-shrink: 0;
}
.newsletter-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 60px 0;
    margin-top: 60px;
}
</style>
@endpush

@section('content')
<!-- Hero Section -->
<section class="blog-hero text-white">
    <div class="container position-relative">
        <div class="row justify-content-center">
            <div class="col-lg-10 text-center">
                @if($blog->category)
                    <span class="badge bg-white text-primary px-3 py-2 rounded-pill mb-3">
                        {{ $blog->category->name }}
                    </span>
                @endif
                <h1 class="display-5 fw-bold mb-4">{{ $blog->title }}</h1>
                <div class="d-flex flex-wrap align-items-center justify-content-center gap-4">
                    @if($blog->author)
                        <div class="d-flex align-items-center">
                            <img src="{{ $blog->author->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($blog->author->name ?? 'Admin') . '&background=fff&color=667eea' }}" 
                                 alt="{{ $blog->author->name ?? 'Admin' }}"
                                 class="rounded-circle me-2" 
                                 width="40" height="40">
                            <span>{{ $blog->author->name ?? 'Admin' }}</span>
                        </div>
                    @endif
                    <span class="opacity-75">
                        <i class="bi bi-calendar3 me-1"></i>
                        {{ $blog->published_at ? $blog->published_at->format('F d, Y') : '' }}
                    </span>
                    @if($blog->reading_time)
                        <span class="opacity-75">
                            <i class="bi bi-clock me-1"></i>
                            {{ $blog->reading_time }} min read
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Blog Content -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- Featured Image -->
                @if($blog->featured_image)
                    <img src="{{ asset('storage/' . $blog->featured_image) }}" 
                         alt="{{ $blog->title }}"
                         class="blog-featured-image img-fluid w-100"
                         style="max-height: 500px; object-fit: cover;">
                @endif
                
                <!-- Content -->
                <div class="blog-content-wrapper">
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="blog-content">
                                {!! $blog->content !!}
                            </div>
                            
                            <!-- Tags -->
                            @if($blog->tags)
                                <div class="mt-5 pt-4 border-top">
                                    <h6 class="fw-bold mb-3">
                                        <i class="bi bi-tags me-2"></i>Tags
                                    </h6>
                                    <div>
                                        @foreach(json_decode($blog->tags) ?? [] as $tag)
                                            <a href="{{ route('blogs.index', ['search' => $tag]) }}" class="tag-badge text-decoration-none">
                                                {{ $tag }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                            
                            <!-- Share -->
                            <div class="mt-5 pt-4 border-top">
                                <h6 class="fw-bold mb-3">
                                    <i class="bi bi-share me-2"></i>Share this article
                                </h6>
                                <div class="share-buttons d-flex gap-2">
                                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" 
                                       target="_blank"
                                       class="btn btn-primary">
                                        <i class="bi bi-facebook"></i>
                                    </a>
                                    <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($blog->title) }}" 
                                       target="_blank"
                                       class="btn btn-info text-white">
                                        <i class="bi bi-twitter"></i>
                                    </a>
                                    <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(url()->current()) }}&title={{ urlencode($blog->title) }}" 
                                       target="_blank"
                                       class="btn btn-primary" style="background: #0077b5; border-color: #0077b5;">
                                        <i class="bi bi-linkedin"></i>
                                    </a>
                                    <a href="https://api.whatsapp.com/send?text={{ urlencode($blog->title . ' - ' . url()->current()) }}" 
                                       target="_blank"
                                       class="btn btn-success">
                                        <i class="bi bi-whatsapp"></i>
                                    </a>
                                </div>
                            </div>
                            
                            <!-- Author Box -->
                            @if($blog->author)
                                <div class="author-box mt-5">
                                    <img src="{{ $blog->author->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($blog->author->name ?? 'Admin') . '&background=667eea&color=fff' }}" 
                                         alt="{{ $blog->author->name ?? 'Admin' }}">
                                    <div>
                                        <h5 class="fw-bold mb-1">Written by {{ $blog->author->name ?? 'Admin' }}</h5>
                                        <p class="text-muted mb-0">{{ $blog->author->bio ?? 'Content Writer' }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Sidebar -->
                        <div class="col-lg-4 mt-5 mt-lg-0">
                            <div class="sidebar-wrapper">
                                <!-- Recent Posts -->
                                @php
                                    $recentPosts = \App\Models\Blog::with('author')->published()->latest('published_at')->take(4)->get();
                                @endphp
                                @if($recentPosts->count() > 0)
                                    <div class="sidebar-card">
                                        <div class="card-body">
                                            <h6 class="fw-bold mb-4">
                                                <i class="bi bi-clock-history me-2 text-primary"></i>Recent Posts
                                            </h6>
                                            @foreach($recentPosts as $post)
                                                <a href="{{ route('blogs.show', $post->slug) }}" class="recent-post-item text-decoration-none">
                                                    @if($post->featured_image)
                                                        <img src="{{ asset('storage/' . $post->featured_image) }}" 
                                                             alt="{{ $post->title }}">
                                                    @else
                                                        <div class="bg-gradient d-flex align-items-center justify-content-center" 
                                                             style="width: 70px; height: 70px; border-radius: 10px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); flex-shrink: 0;">
                                                            <i class="bi bi-journal text-white" style="font-size: 1.5rem; opacity: 0.7;"></i>
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <h6 class="text-dark mb-1" style="font-size: 14px; line-height: 1.4;">
                                                            {{ Str::limit($post->title, 40) }}
                                                        </h6>
                                                        <small class="text-muted">
                                                            {{ $post->published_at ? $post->published_at->format('M d, Y') : '' }}
                                                        </small>
                                                    </div>
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Related Posts -->
                @if($relatedBlogs->count() > 0)
                    <div class="mt-5 pt-5">
                        <h4 class="fw-bold mb-4">
                            <i class="bi bi-journal-text me-2 text-primary"></i>Related Articles
                        </h4>
                        <div class="row g-4">
                            @foreach($relatedBlogs as $related)
                                <div class="col-md-4">
                                    <article class="related-card bg-white">
                                        @if($related->featured_image)
                                            <a href="{{ route('blogs.show', $related->slug) }}">
                                                <img src="{{ asset('storage/' . $related->featured_image) }}" 
                                                     class="w-100"
                                                     alt="{{ $related->title }}">
                                            </a>
                                        @else
                                            <div class="bg-gradient d-flex align-items-center justify-content-center" 
                                                 style="height: 140px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                                <i class="bi bi-journal text-white" style="font-size: 2.5rem; opacity: 0.5;"></i>
                                            </div>
                                        @endif
                                        <div class="p-3">
                                            <h6 class="fw-bold">
                                                <a href="{{ route('blogs.show', $related->slug) }}" class="text-dark text-decoration-none">
                                                    {{ Str::limit($related->title, 50) }}
                                                </a>
                                            </h6>
                                            <small class="text-muted">
                                                <i class="bi bi-calendar me-1"></i>
                                                {{ $related->published_at ? $related->published_at->format('M d, Y') : '' }}
                                            </small>
                                        </div>
                                    </article>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

<!-- Newsletter Section -->
<section class="newsletter-section text-white text-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <i class="bi bi-envelope-paper-heart mb-3" style="font-size: 3rem;"></i>
                <h3 class="fw-bold mb-2">Subscribe to Our Newsletter</h3>
                <p class="opacity-75 mb-4">Get the latest posts delivered right to your inbox</p>
                <form class="d-flex gap-2">
                    <input type="email" class="form-control" placeholder="Your email address" style="border-radius: 12px; padding: 14px 20px;">
                    <button type="submit" class="btn btn-light fw-bold px-4" style="border-radius: 12px;">
                        Subscribe
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection
