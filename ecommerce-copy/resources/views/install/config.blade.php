@extends('install.layout')
@section('title', 'Configuration - Installer')
@php $currentStep = 4; @endphp

@section('content')
<div class="step-content">
    <h2 class="text-xl font-bold text-gray-900 mb-1">Application Configuration</h2>
    <p class="text-sm text-gray-500 mb-6">Set up your application name, URL, and environment preferences.</p>

    @if ($errors->any())
    <div class="mb-5 p-4 bg-red-50 border border-red-200 rounded-xl">
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-red-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>
                <p class="text-sm font-semibold text-red-800">Validation Error</p>
                @foreach ($errors->all() as $error)
                    <p class="text-xs text-red-600 mt-0.5">{{ $error }}</p>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <form action="{{ route('install.config.save') }}" method="POST">
        @csrf

        <div class="mb-4">
            <label class="form-label" for="app_name">Application Name</label>
            <input type="text" id="app_name" name="app_name" class="form-input" value="{{ old('app_name', $appConfig['name'] ?? config('app.name')) }}" placeholder="My Store" required>
        </div>

        <div class="mb-4">
            <label class="form-label" for="app_url">Application URL</label>
            <input type="url" id="app_url" name="app_url" class="form-input" value="{{ old('app_url', $appConfig['url'] ?? request()->getSchemeAndHttpHost()) }}" placeholder="https://example.com" required>
            <p class="text-xs text-gray-400 mt-1">Auto-detected from current request. Change if needed.</p>
        </div>

        <div class="mb-4">
            <label class="form-label" for="app_env">Environment</label>
            <select id="app_env" name="app_env" class="form-input">
                <option value="production" {{ (old('app_env', $appConfig['env'] ?? '') == 'production') ? 'selected' : '' }}>Production</option>
                <option value="staging" {{ (old('app_env', $appConfig['env'] ?? '') == 'staging') ? 'selected' : '' }}>Staging</option>
                <option value="local" {{ (old('app_env', $appConfig['env'] ?? 'local') == 'local') ? 'selected' : '' }}>Local</option>
            </select>
        </div>

        <div class="mb-6">
            <label class="flex items-center gap-3 cursor-pointer">
                <div class="relative">
                    <input type="checkbox" name="app_debug" value="1" {{ old('app_debug', $appConfig['debug'] ?? false) ? 'checked' : '' }} class="sr-only peer" onchange="toggleDebug(this)">
                    <div class="w-10 h-6 bg-gray-200 rounded-full peer peer-checked:bg-indigo-600 transition-colors"></div>
                    <div class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full shadow peer-checked:translate-x-4 transition-transform"></div>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-700">Debug Mode</span>
                    <p class="text-xs text-gray-400">Enable detailed error messages. Recommended OFF in production.</p>
                </div>
            </label>
        </div>

        <div class="flex justify-between">
            <a href="{{ route('install.database') }}" class="btn-outline">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                </svg>
                Back
            </a>
            <button type="submit" class="btn-primary">
                Continue
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
        </div>
    </form>
</div>
@endsection
