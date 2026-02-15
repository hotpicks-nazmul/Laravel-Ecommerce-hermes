@extends('themes.general.layouts.app')

@section('title', 'Blog')

@push('styles')
<style>
.blog-hero {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 80px 0;
    margin-bottom: -40px;
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
.blog-card {
    border: none;
    border-radius: 16px;
    overflow: hidden;
    transition: all 0.3s ease;
    background: #fff;
}
.blog-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.1);
}
.blog-card .card-img-top {
    height: 220px;
    object-fit: cover;
    transition: transform 0.5s ease;
}
.blog-card:hover .card-img-top {
    transform: scale(1.05);
}
.blog-card .card-body {
    padding: 24px;
}
.blog-category {
    display: inline-block;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 12px;
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
.search-input {
    border-radius: 12px;
    padding: 12px 20px;
    border: 2px solid #e9ecef;
    transition: all 0.3s ease;
}
.search-input:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
}
.category-list li {
    padding: 10px 0;
    border-bottom: 1px solid #f0f0f0;
}
.category-list li:last-child {
    border-bottom: none;
}
.category-list a {
    color: #555;
    text-decoration: none;
    transition: all 0.3s ease;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.category-list a:hover {
    color: #667eea;
    padding-left: 8px;
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
.pagination .page-link {
    border-radius: 10px;
    margin: 0 4px;
    border: none;
    padding: 12px 18px;
    color: #555;
    font-weight: 500;
}
.pagination .page-item.active .page-link {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
.empty-state {
    padding: 80px 20px;
    text-align: center;
}
.empty-state i {
    font-size: 80px;
    color: #ddd;
    margin-bottom: 24px;
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
<section class="blog-hero text-white text-center">
    <div class="container position-relative">
        <h1 class="display-4 fw-bold mb-3">Our Blog</h1>
        <p class="lead mb-0 opacity-75">Discover insights, tips, and stories from our team</p>
    </div>
</section>

<!-- Blog Section -->
<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <!-- Blog Posts -->
            <div class="col-lg-8">
                @if($blogs->count() > 0)
                    <div class="row g-4">
                        @foreach($blogs as $blog)
                            <div class="col-md-6">
                                <article class="blog-card h-100">
                                    <div class="overflow-hidden">
                                        @if($blog->featured_image)
                                            <a href="{{ route('blogs.show', $blog->slug) }}">
                                                <img src="{{ asset('storage/' . $blog->featured_image) }}" 
                                                     class="card-img-top" 
                                                     alt="{{ $blog->title }}">
                                            </a>
                                        @else
                                            <a href="{{ route('blogs.show', $blog->slug) }}">
                                                <div class="card-img-top bg-gradient d-flex align-items-center justify-content-center" 
                                                     style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); height: 220px;">
                                                    <i class="bi bi-journal-text text-white" style="font-size: 4rem; opacity: 0.5;"></i>
                                                </div>
                                            </a>
                                        @endif
                                    </div>
                                    
                                    <div class="card-body">
                                        @if($blog->category)
                                            <span class="blog-category bg-primary bg-opacity-10 text-primary">
                                                {{ $blog->category->name }}
                                            </span>
                                        @endif
                                        
                                        <h5 class="card-title mb-3">
                                            <a href="{{ route('blogs.show', $blog->slug) }}" class="text-dark text-decoration-none">
                                                {{ $blog->title }}
                                            </a>
                                        </h5>
                                        
                                        <p class="card-text text-muted mb-4">
                                            {{ Str::limit(strip_tags($blog->content), 120) }}
                                        </p>
                                        
                                        <div class="d-flex align-items-center justify-content-between mt-auto">
                                            <div class="d-flex align-items-center">
                                                @if($blog->author)
                                                    <img src="{{ $blog->author->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($blog->author->name ?? 'Admin') . '&background=667eea&color=fff' }}" 
                                                         alt="{{ $blog->author->name ?? 'Admin' }}"
                                                         class="rounded-circle me-2" 
                                                         width="36" height="36">
                                                    <div>
                                                        <small class="d-block fw-medium text-dark">{{ $blog->author->name ?? 'Admin' }}</small>
                                                        <small class="text-muted">
                                                            {{ $blog->published_at ? $blog->published_at->format('M d, Y') : '' }}
                                                        </small>
                                                    </div>
                                                @endif
                                            </div>
                                            <a href="{{ route('blogs.show', $blog->slug) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                                Read More <i class="bi bi-arrow-right ms-1"></i>
                                            </a>
                                        </div>
                                    </div>
                                </article>
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- Pagination -->
                    @if($blogs->hasPages())
                        <div class="mt-5 d-flex justify-content-center">
                            {{ $blogs->links() }}
                        </div>
                    @endif
                @else
                    <div class="empty-state bg-white rounded-4">
                        <i class="bi bi-journal-text"></i>
                        <h4 class="fw-bold">No blog posts yet</h4>
                        <p class="text-muted">Check back later for new content.</p>
                    </div>
                @endif
            </div>
            
            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="sidebar-wrapper">
                    <!-- Search & Categories Combined -->
                    <div class="sidebar-card mb-4">
                        <div class="card-body">
                            <h5 class="fw-bold mb-4">
                                <i class="bi bi-search me-2 text-primary"></i>Search & Filter
                            </h5>
                            <form action="{{ route('blogs.index') }}" method="GET" class="mb-4">
                                <div class="input-group">
                                    <input type="text" class="form-control search-input" name="search" placeholder="Search articles..." value="{{ request('search') }}">
                                    <button class="btn btn-primary px-4" type="submit" style="border-radius: 0 12px 12px 0;">
                                        <i class="bi bi-arrow-right"></i>
                                    </button>
                                </div>
                            </form>
                            
                            <h6 class="fw-bold mb-3">
                                <i class="bi bi-folder me-2 text-primary"></i>Categories
                            </h6>
                            <ul class="category-list list-unstyled mb-0">
                                <li>
                                    <a href="{{ route('blogs.index') }}" class="{{ !request('category') ? 'text-primary fw-bold' : '' }}">
                                        <span>All Posts</span>
                                        <span class="badge bg-light text-dark">{{ \App\Models\Blog::published()->count() }}</span>
                                    </a>
                                </li>
                                @foreach($categories as $category)
                                    <li>
                                        <a href="{{ route('blogs.index', ['category' => $category->slug]) }}" 
                                           class="{{ request('category') == $category->slug ? 'text-primary fw-bold' : '' }}">
                                            <span>{{ $category->name }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    
                    <!-- Recent Posts -->
                    @php
                        $recentPosts = \App\Models\Blog::with('author')->published()->latest('published_at')->take(4)->get();
                    @endphp
                    @if($recentPosts->count() > 0)
                        <div class="sidebar-card">
                            <div class="card-body">
                                <h5 class="fw-bold mb-4">
                                    <i class="bi bi-clock-history me-2 text-primary"></i>Recent Posts
                                </h5>
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
                                                {{ Str::limit($post->title, 45) }}
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
