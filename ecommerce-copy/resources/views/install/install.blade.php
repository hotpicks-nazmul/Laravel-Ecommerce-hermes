@extends('install.layout')
@section('title', 'Installing - Installer')
@php $currentStep = 6; @endphp

@section('content')
<div class="step-content">
    <h2 class="text-xl font-bold text-gray-900 mb-1">Installing</h2>
    <p class="text-sm text-gray-500 mb-6">Please wait while the application is being installed. Do not close this page.</p>

    <div id="progressContainer" class="mb-6 hidden">
        <div class="flex justify-between text-xs text-gray-500 mb-1">
            <span id="progressLabel">Starting...</span>
            <span id="progressPercent">0%</span>
        </div>
        <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
            <div id="progressBar" class="h-full bg-gradient-to-r from-indigo-500 to-indigo-600 rounded-full transition-all duration-500" style="width:0%"></div>
        </div>
    </div>

    <div id="logContainer" class="space-y-1 mb-6">
        <div class="text-center py-8 text-gray-400">
            <div class="spinner spinner-dark mx-auto mb-3" style="width:2rem;height:2rem;border-width:3px"></div>
            <p class="text-sm">Preparing installation...</p>
        </div>
    </div>

    <div id="errorContainer" class="hidden mb-4 p-4 bg-red-50 border border-red-200 rounded-xl">
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-red-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>
                <p class="text-sm font-semibold text-red-800" id="errorTitle">Installation Error</p>
                <p class="text-xs text-red-600 mt-1" id="errorMessage"></p>
            </div>
        </div>
    </div>

    <div id="doneContainer" class="hidden mb-6 p-4 bg-green-50 border border-green-200 rounded-xl text-center">
        <div class="w-16 h-16 mx-auto mb-3 rounded-full bg-green-100 flex items-center justify-center">
            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <p class="text-sm font-semibold text-green-800">Installation complete!</p>
        <p class="text-xs text-green-600 mt-1">All steps finished successfully.</p>
    </div>

    <div class="flex justify-between items-center">
        <div></div>
        <div class="flex gap-3">
            <button id="retryBtn" class="btn-outline hidden" onclick="retryStep()">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Retry
            </button>
            <a id="completeBtn" href="{{ route('admin.login') }}" class="btn-primary hidden">
                Go to Admin Panel
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let pollInterval = null;
let completed = false;
const totalSteps = 5;

let networkErrors = 0;

function startPolling() {
    pollInterval = setInterval(pollProcess, 3000);
    pollProcess();
}

function pollProcess() {
    if (completed) return;

    fetch('{{ route('install.process') }}', {
        headers: { 'Accept': 'application/json' }
    })
    .then(res => {
        if (!res.ok) throw new Error('HTTP ' + res.status);
        return res.json();
    })
    .then(data => {
        networkErrors = 0;
        if (data.done) {
            renderLog(data.log || []);
            completed = true;
            if (pollInterval) clearInterval(pollInterval);
            document.getElementById('doneContainer').classList.remove('hidden');
            document.getElementById('completeBtn').classList.remove('hidden');
            return;
        }

        renderLog(data.log);

        if (data.error) {
            completed = true;
            clearInterval(pollInterval);
            document.getElementById('errorContainer').classList.remove('hidden');
            document.getElementById('errorMessage').textContent = data.error_message || 'An unexpected error occurred.';
            document.getElementById('retryBtn').classList.remove('hidden');
            return;
        }
    })
    .catch(err => {
        networkErrors++;
        console.warn('Poll error (' + networkErrors + '):', err.message);
        if (networkErrors >= 5) {
            completed = true;
            clearInterval(pollInterval);
            document.getElementById('errorContainer').classList.remove('hidden');
            document.getElementById('errorTitle').textContent = 'Connection Lost';
            document.getElementById('errorMessage').textContent = 'The server is not responding. If the installation was nearly complete, try visiting the admin panel directly.';
            document.getElementById('retryBtn').classList.remove('hidden');
            document.getElementById('completeBtn').classList.remove('hidden');
            document.getElementById('completeBtn').textContent = 'Try Admin Panel';
        }
    });
}

function renderLog(log) {
    const container = document.getElementById('logContainer');
    const progressContainer = document.getElementById('progressContainer');
    const progressBar = document.getElementById('progressBar');
    const progressLabel = document.getElementById('progressLabel');
    const progressPercent = document.getElementById('progressPercent');

    if (!log || log.length === 0) {
        container.innerHTML = '<div class="text-center py-8 text-gray-400"><div class="spinner spinner-dark mx-auto mb-3" style="width:2rem;height:2rem;border-width:3px"></div><p class="text-sm">Preparing installation...</p></div>';
        progressContainer.classList.add('hidden');
        return;
    }

    progressContainer.classList.remove('hidden');

    let done = 0;
    let html = '';
    log.forEach(entry => {
        if (entry.status === 'done') done++;
        let iconHtml = '';
        if (entry.status === 'running') {
            iconHtml = '<div class="log-icon running"></div>';
        } else if (entry.status === 'done') {
            iconHtml = '<div class="log-icon done"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg></div>';
        } else if (entry.status === 'error') {
            iconHtml = '<div class="log-icon error"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg></div>';
        }
        html += '<div class="log-entry ' + entry.status + '">';
        html += iconHtml;
        html += '<div class="flex-1 min-w-0">';
        html += '<p class="text-sm font-medium text-gray-800">' + escapeHtml(entry.label) + '</p>';
        if (entry.message) {
            let msg = entry.message;
            if (msg.length > 120) msg = msg.substring(0, 120) + '...';
            html += '<pre class="text-xs text-gray-500 mt-1 truncate">' + escapeHtml(msg) + '</pre>';
        }
        html += '</div></div>';
    });

    container.innerHTML = html;

    const pct = Math.round((done / totalSteps) * 100);
    progressBar.style.width = Math.min(pct, 100) + '%';
    progressLabel.textContent = done + ' of ' + totalSteps + ' steps completed';
    progressPercent.textContent = pct + '%';
}

function retryStep() {
    document.getElementById('retryBtn').classList.add('hidden');
    document.getElementById('errorContainer').classList.add('hidden');
    completed = false;

    fetch('{{ route('install.retry') }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            pollInterval = setInterval(pollProcess, 2000);
            pollProcess();
        }
    });
}

function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

document.addEventListener('DOMContentLoaded', function () {
    startPolling();
});
</script>
@endpush
