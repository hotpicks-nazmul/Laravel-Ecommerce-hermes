@extends('admin.layouts.app')

@section('title', 'Pages')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Pages</h4>
    <a href="{{ route('admin.pages.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Add New Page
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover data-table" id="pagesTable">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Slug</th>
                        <th>Status</th>
                        <th>Updated</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pages ?? [] as $page)
                    <tr>
                        <td>{{ $page->title }}</td>
                        <td><code>{{ $page->slug }}</code></td>
                        <td>
                            <span class="badge {{ $page->is_published ? 'bg-success' : 'bg-secondary' }}">
                                {{ $page->is_published ? 'Published' : 'Draft' }}
                            </span>
                        </td>
                        <td>{{ $page->updated_at->format('d M, Y') }}</td>
                        <td>
                            <a href="{{ route('admin.pages.edit', $page->id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.pages.destroy', $page->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            @if(empty($pages) || count($pages) == 0)
            <div class="text-center py-4 text-muted">
                No pages found. <a href="{{ route('admin.pages.create') }}">Add your first page</a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
