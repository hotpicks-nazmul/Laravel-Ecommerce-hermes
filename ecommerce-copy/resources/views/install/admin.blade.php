@extends('install.layout')
@section('title', 'Admin Account - Installer')
@php $currentStep = 5; @endphp

@section('content')
<div class="step-content">
    <h2 class="text-xl font-bold text-gray-900 mb-1">Admin Account</h2>
    <p class="text-sm text-gray-500 mb-6">Create the super admin account for managing your application.</p>

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

    <form action="{{ route('install.admin.save') }}" method="POST" id="adminForm">
        @csrf

        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="form-label" for="first_name">First Name</label>
                <input type="text" id="first_name" name="first_name" class="form-input" value="{{ old('first_name') }}" placeholder="John" required>
            </div>
            <div>
                <label class="form-label" for="last_name">Last Name</label>
                <input type="text" id="last_name" name="last_name" class="form-input" value="{{ old('last_name') }}" placeholder="Doe" required>
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label" for="email">Email Address</label>
            <input type="email" id="email" name="email" class="form-input" value="{{ old('email') }}" placeholder="admin@example.com" required>
        </div>

        <div class="mb-4">
            <label class="form-label" for="password">Password</label>
            <div class="relative">
                <input type="password" id="password" name="password" class="form-input pr-10" placeholder="Min. 8 characters" required minlength="8" oninput="checkPasswordStrength(this.value)">
                <button type="button" onclick="togglePassword('password', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </button>
            </div>
            <div class="password-strength">
                <div id="strengthBar" class="password-strength-bar" style="width:0%"></div>
            </div>
            <p id="strengthText" class="text-xs text-gray-400 mt-1"></p>
        </div>

        <div class="mb-6">
            <label class="form-label" for="password_confirmation">Confirm Password</label>
            <div class="relative">
                <input type="password" id="password_confirmation" name="password_confirmation" class="form-input pr-10" placeholder="Repeat password" required minlength="8" oninput="checkMatch()">
                <button type="button" onclick="togglePassword('password_confirmation', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </button>
            </div>
            <p id="matchText" class="text-xs mt-1"></p>
        </div>

        <div class="flex justify-between">
            <a href="{{ route('install.config') }}" class="btn-outline">
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

function checkPasswordStrength(value) {
    const bar = document.getElementById('strengthBar');
    const text = document.getElementById('strengthText');
    let score = 0;

    if (value.length >= 8) score += 25;
    if (value.length >= 12) score += 10;
    if (/[a-z]/.test(value)) score += 15;
    if (/[A-Z]/.test(value)) score += 15;
    if (/[0-9]/.test(value)) score += 15;
    if (/[^a-zA-Z0-9]/.test(value)) score += 20;

    bar.style.width = Math.min(score, 100) + '%';

    if (value.length === 0) {
        bar.style.width = '0%';
        text.textContent = '';
    } else if (score < 30) {
        bar.style.background = '#ef4444';
        text.textContent = 'Weak';
        text.className = 'text-xs mt-1 text-red-500';
    } else if (score < 60) {
        bar.style.background = '#f59e0b';
        text.textContent = 'Fair';
        text.className = 'text-xs mt-1 text-amber-500';
    } else if (score < 80) {
        bar.style.background = '#10b981';
        text.textContent = 'Good';
        text.className = 'text-xs mt-1 text-green-500';
    } else {
        bar.style.background = '#059669';
        text.textContent = 'Strong';
        text.className = 'text-xs mt-1 text-green-600 font-semibold';
    }
}

function checkMatch() {
    const pw = document.getElementById('password').value;
    const confirm = document.getElementById('password_confirmation').value;
    const text = document.getElementById('matchText');

    if (confirm.length === 0) {
        text.textContent = '';
    } else if (pw === confirm) {
        text.textContent = '✓ Passwords match';
        text.className = 'text-xs mt-1 text-green-500';
    } else {
        text.textContent = '✗ Passwords do not match';
        text.className = 'text-xs mt-1 text-red-500';
    }
}
</script>
@endpush
