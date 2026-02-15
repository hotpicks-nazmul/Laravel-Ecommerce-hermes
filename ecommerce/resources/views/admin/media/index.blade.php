@extends('admin.layouts.app')

@section('title', 'Media')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Media Library</h4>
</div>

<div class="card">
    <div class="card-header bg-white">
        <form action="{{ route('admin.media.upload') }}" method="POST" enctype="multipart/form-data" class="d-flex gap-2">
            @csrf
            <input type="file" name="files[]" multiple class="form-control" accept="image/*">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-upload me-1"></i> Upload
            </button>
        </form>
    </div>
    <div class="card-body">
        <div class="row" id="mediaGrid">
            @forelse($media ?? [] as $file)
            <div class="col-md-2 mb-3">
                <div class="card h-100">
                    <img src="{{ asset('storage/' . $file) }}" class="card-img-top" alt="{{ $file }}" style="height: 120px; object-fit: cover;">
                    <div class="card-body p-2">
                        <small class="text-muted d-block text-truncate">{{ $file }}</small>
                    </div>
                    <div class="card-footer p-2 bg-white">
                        <button type="button" class="btn btn-sm btn-outline-danger w-100" onclick="deleteMedia('{{ $file }}')">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center py-5">
                <i class="bi bi-images" style="font-size: 3rem;"></i>
                <h5 class="mt-3">No Media Files</h5>
                <p class="text-muted">Upload your first image using the form above.</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

<form id="deleteForm" action="" method="POST">
    @csrf @method('DELETE')
</form>

<script>
function deleteMedia(file) {
    if (confirm('Are you sure you want to delete this file?')) {
        var form = document.getElementById('deleteForm');
        form.action = '{{ route('admin.media.destroy', 0) }}'.replace('0', encodeURIComponent(file));
        form.submit();
    }
}
</script>
@endsection
