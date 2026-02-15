@extends('themes.general.layouts.app')

@section('title', $page->title)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">{{ $page->title }}</h1>
        
        @if($page->featured_image)
        <div class="mb-6">
            <img src="{{ asset('storage/' . $page->featured_image) }}" alt="{{ $page->title }}" class="w-full h-64 object-cover rounded-lg">
        </div>
        @endif
        
        <div class="prose prose-lg max-w-none">
            {!! $page->content !!}
        </div>
    </div>
</div>
@endsection
