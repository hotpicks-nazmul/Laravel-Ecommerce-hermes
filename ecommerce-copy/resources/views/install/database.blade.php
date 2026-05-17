@extends('install.layout')
@section('title', 'Database - Installer')
@php $currentStep = 3; @endphp

@section('content')
<div class="step-content">
    <h2 class="text-xl font-bold text-gray-900 mb-1">Database Configuration</h2>
    <p class="text-sm text-gray-500 mb-6">Enter your MySQL database credentials. The database will be created automatically if it doesn't exist.</p>

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

    <form action="{{ route('install.database.save') }}" method="POST" id="dbForm">
        @csrf

        <div class="grid grid-cols-3 gap-4 mb-4">
            <div class="col-span-2">
                <label class="form-label" for="db_host">Host</label>
                <input type="text" id="db_host" name="db_host" class="form-input" value="{{ old('db_host', $dbConfig['host'] ?? '127.0.0.1') }}" placeholder="Database host" required>
            </div>
            <div>
                <label class="form-label" for="db_port">Port</label>
                <input type="number" id="db_port" name="db_port" class="form-input" value="{{ old('db_port', $dbConfig['port'] ?? '3306') }}" placeholder="3306" required>
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label" for="db_name">Database Name</label>
            <input type="text" id="db_name" name="db_name" class="form-input" value="{{ old('db_name', $dbConfig['name'] ?? '') }}" placeholder="e.g. my_app" required>
        </div>

        <div class="mb-4">
            <label class="form-label" for="db_username">Username</label>
            <input type="text" id="db_username" name="db_username" class="form-input" value="{{ old('db_username', $dbConfig['username'] ?? '') }}" placeholder="Database username" required>
        </div>

        <div class="mb-2">
            <label class="form-label" for="db_password">Password</label>
            <div class="relative">
                <input type="password" id="db_password" name="db_password" class="form-input pr-10" value="{{ old('db_password', $dbConfig['password'] ?? '') }}" placeholder="Database password">
                <button type="button" onclick="togglePassword('db_password', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </button>
            </div>
        </div>

        <div class="mb-6">
            <label class="form-label" for="db_prefix">Table Prefix <span class="text-gray-400 font-normal">(optional)</span></label>
            <input type="text" id="db_prefix" name="db_prefix" class="form-input" value="{{ old('db_prefix') }}" placeholder="e.g. app_">
        </div>

        <div id="testResult" class="mb-4 hidden"></div>

        <div class="flex justify-between items-center">
            <a href="{{ route('install.requirements') }}" class="btn-outline">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                </svg>
                Back
            </a>
            <div class="flex gap-3">
                <button type="button" onclick="testConnection()" id="testBtn" class="btn-outline">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    Test Connection
                </button>
                <button type="submit" id="saveBtn" class="btn-primary">
                    Save & Continue
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function togglePassword(id, btn) {
    const input = document.getElementById(id);
    if (input.type === 'password') {
        input.type = 'text';
        btn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>';
    } else {
        input.type = 'password';
        btn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>';
    }
}

function testConnection() {
    const btn = document.getElementById('testBtn');
    const result = document.getElementById('testResult');
    const original = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner spinner-dark"></span> Testing...';

    result.classList.add('hidden');

    const form = document.getElementById('dbForm');
    const formData = new FormData(form);

    fetch('{{ route('install.database.test') }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        },
        body: formData,
    })
    .then(res => res.json())
    .then(data => {
        result.classList.remove('hidden');
        if (data.success) {
            result.className = 'mb-4 p-4 bg-green-50 border border-green-200 rounded-xl';
            result.innerHTML = '<div class="flex items-start gap-3"><svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><p class="text-sm text-green-700">' + data.message + '</p></div>';
        } else {
            result.className = 'mb-4 p-4 bg-red-50 border border-red-200 rounded-xl';
            result.innerHTML = '<div class="flex items-start gap-3"><svg class="w-5 h-5 text-red-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><p class="text-sm text-red-700">' + data.message + '</p></div>';
        }
    })
    .catch(err => {
        result.classList.remove('hidden');
        result.className = 'mb-4 p-4 bg-red-50 border border-red-200 rounded-xl';
        result.innerHTML = '<div class="flex items-start gap-3"><svg class="w-5 h-5 text-red-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><p class="text-sm text-red-700">Network error. Please try again.</p></div>';
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = original;
    });
}
</script>
@endpush
