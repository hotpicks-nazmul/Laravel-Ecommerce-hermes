@extends('install.layout')
@section('title', 'Requirements - Installer')
@php $currentStep = 2; @endphp

@section('content')
<div class="step-content">
    <h2 class="text-xl font-bold text-gray-900 mb-1">Server Requirements</h2>
    <p class="text-sm text-gray-500 mb-6">Checking if your server meets the minimum requirements.</p>

    <div class="space-y-5">
        <div>
            <h3 class="text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                PHP Version
            </h3>
            <table class="req-table">
                <tr>
                    <td class="text-gray-600">Version <span class="text-gray-400 text-xs">(required: {{ $requirements['php']['required'] }}+)</span></td>
                    <td class="text-right">
                        <span class="badge-pass {{ $requirements['php']['status'] ? 'success' : 'fail' }}">
                            @if ($requirements['php']['status'])
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            @else
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                            @endif
                            {{ $requirements['php']['current'] }}
                        </span>
                    </td>
                </tr>
            </table>
        </div>

        <div>
            <h3 class="text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                PHP Extensions
            </h3>
            <table class="req-table">
                @foreach ($requirements['extensions'] as $ext => $loaded)
                <tr>
                    <td class="text-gray-600">{{ $ext }}</td>
                    <td class="text-right">
                        <span class="badge-pass {{ $loaded ? 'success' : 'fail' }}">
                            @if ($loaded)
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            @else
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                            @endif
                            {{ $loaded ? 'Installed' : 'Missing' }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </table>
        </div>

        @if (!in_array(false, $requirements['optional'], true) === false)
        <div>
            <h3 class="text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
                Optional Extensions
            </h3>
            <table class="req-table">
                @foreach ($requirements['optional'] as $ext => $loaded)
                <tr>
                    <td class="text-gray-600">{{ $ext }}</td>
                    <td class="text-right">
                        <span class="badge-pass {{ $loaded ? 'success' : 'fail' }}">
                            @if ($loaded)
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            @else
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                            @endif
                            {{ $loaded ? 'Installed' : 'Recommended' }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </table>
        </div>
        @endif

        <div>
            <h3 class="text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                </svg>
                Directory Permissions
            </h3>
            <table class="req-table">
                @foreach ($requirements['permissions'] as $path => $writable)
                <tr>
                    <td class="text-gray-600 font-mono text-xs">{{ $path }}</td>
                    <td class="text-right">
                        <span class="badge-pass {{ $writable ? 'success' : 'fail' }}">
                            @if ($writable)
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            @else
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                            @endif
                            {{ $writable ? 'Writable' : 'Not writable' }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </table>
        </div>
    </div>

    @if (!$allPass)
    <div class="mt-6 p-4 bg-amber-50 border border-amber-200 rounded-xl">
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-amber-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
            </svg>
            <div>
                <p class="text-sm font-semibold text-amber-800">Some requirements are not met</p>
                <p class="text-xs text-amber-700 mt-1">Please fix the missing items above before proceeding. For permission issues, try running:</p>
                <pre class="mt-2 text-xs bg-amber-100 p-2 rounded-lg overflow-x-auto text-amber-900">chmod -R 775 storage bootstrap/cache</pre>
            </div>
        </div>
    </div>
    @endif

    <div class="flex justify-between mt-8">
        <a href="{{ route('install.welcome') }}" class="btn-outline">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
            </svg>
            Back
        </a>
        <div class="flex gap-3">
            <a href="{{ route('install.requirements') }}" class="btn-outline">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Re-check
            </a>
            @if ($allPass)
            <a href="{{ route('install.database') }}" class="btn-primary">
                Continue
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
            @else
            <button disabled class="btn-primary opacity-50 cursor-not-allowed">
                Fix issues first
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
            @endif
        </div>
    </div>
</div>
@endsection
