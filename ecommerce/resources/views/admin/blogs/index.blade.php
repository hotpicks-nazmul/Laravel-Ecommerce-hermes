@extends('admin.layouts.app')

@section('title', 'Blog Posts')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Blog Posts</h4>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.blog-settings.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-gear me-1"></i> Settings
        </a>
        <a href="{{ route('admin.blogs.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Add New Post
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="blogsTable">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Status</th>
                        <th>Published</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($blogs ?? [] as $blog)
                    <tr>
                        <td>
                            @if($blog->featured_image)
                            <img src="{{ asset('storage/' . $blog->featured_image) }}" alt="{{ $blog->title }}" width="60" height="40" class="rounded">
                            @else
                            <div class="bg-secondary rounded" style="width:60px;height:40px;display:flex;align-items:center;justify-content:center;">
                                <i class="bi bi-image text-white"></i>
                            </div>
                            @endif
                        </td>
                        <td>{{ $blog->title }}</td>
                        <td>{{ $blog->author->name ?? 'N/A' }}</td>
                        <td>
                            @php $isPublished = $blog->status === 'published' && $blog->published_at && $blog->published_at->isPast(); @endphp
                            <span class="badge {{ $isPublished ? 'bg-success' : 'bg-secondary' }}">
                                {{ $isPublished ? 'Published' : 'Draft' }}
                            </span>
                        </td>
                        <td>{{ $blog->published_at ? $blog->published_at->format('d M, Y') : 'Not published' }}</td>
                        <td>
                            <a href="{{ route('admin.blogs.edit', $blog->slug) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.blogs.destroy', $blog->slug) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4">No blog posts found. <a href="{{ route('admin.blogs.create') }}">Add your first post</a></td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    @if(isset($blogs) && $blogs->count() > 0)
    $('#blogsTable').DataTable({
        pageLength: 25,
        order: [[4, 'desc']]
    });
    @endif
});
</script>
@endpush
