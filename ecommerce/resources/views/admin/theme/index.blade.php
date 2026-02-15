@extends('admin.layouts.app')

@section('title', 'Themes')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Themes</h4>
</div>

<div class="row">
    @forelse($themes ?? [] as $theme)
    <div class="col-md-4 mb-4">
        <div class="card h-100 {{ $theme['active'] ?? false ? 'border-primary' : '' }}">
            @if(isset($theme['screenshot']))
            <img src="{{ asset($theme['screenshot']) }}" class="card-img-top" alt="{{ $theme['name'] }}" style="height: 200px; object-fit: cover;">
            @else
            <div class="card-img-top bg-secondary d-flex align-items-center justify-content-center" style="height: 200px;">
                <i class="bi bi-palette text-white" style="font-size: 4rem;"></i>
            </div>
            @endif
            <div class="card-body">
                <h5 class="card-title">{{ $theme['name'] ?? 'Unknown' }}</h5>
                <p class="card-text text-muted">{{ $theme['description'] ?? 'No description available.' }}</p>
                <p class="card-text"><small class="text-muted">Version: {{ $theme['version'] ?? '1.0.0' }}</small></p>
            </div>
            <div class="card-footer bg-white">
                @if($theme['active'] ?? false)
                    <span class="btn btn-success disabled"><i class="bi bi-check-circle me-1"></i> Active</span>
                @else
                    <form action="{{ route('admin.theme.activate') }}" method="POST">
                        @csrf
                        <input type="hidden" name="theme" value="{{ $theme['directory'] ?? $theme['name'] }}">
                        <button type="submit" class="btn btn-outline-primary w-100">
                            <i class="bi bi-check-lg me-1"></i> Activate
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="bi bi-palette" style="font-size: 3rem;"></i>
                <h5 class="mt-3">No Themes Found</h5>
                <p class="text-muted">No themes are available in the themes directory.</p>
            </div>
        </div>
    </div>
    @endforelse
</div>
@endsection
