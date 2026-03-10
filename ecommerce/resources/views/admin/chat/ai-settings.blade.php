@extends('admin.layouts.app')

@section('title', 'AI Chatbot Settings')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="bi bi-robot me-2"></i>AI Chatbot Settings</h4>
    <a href="{{ route('admin.chat.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back to Live Chat
    </a>
</div>

<!-- Success Message -->
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row">
    <!-- Settings Form -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-gear me-2"></i>Chatbot Configuration</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.chat.ai-settings') }}" method="POST" id="ai-settings-form">
                    @csrf
                    
                    <!-- Enable AI Chatbot -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input type="checkbox" name="ai_enabled" class="form-check-input" id="aiEnabled" 
                                    {{ ($aiSettings['enabled'] ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="aiEnabled">
                                    <i class="bi bi-robot text-primary me-1"></i> Enable AI Chatbot
                                </label>
                                <div class="form-text">When enabled, AI will auto-respond to customer messages using OpenAI API</div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Welcome Message -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Welcome Message</label>
                        <input type="text" name="ai_welcome_message" class="form-control" 
                            value="{{ $aiSettings['welcome_message'] ?? 'Hello! How can I help you today?' }}">
                        <div class="form-text">Message shown to new visitors when they start a chat</div>
                    </div>

                    <!-- OpenAI API Key -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">
                            <i class="bi bi-key me-1"></i> OpenAI API Key
                        </label>
                        <div class="input-group">
                            <input type="password" name="openai_api_key" class="form-control" 
                                value="{{ $aiSettings['openai_key'] ?? '' }}" placeholder="sk-..." id="apiKeyInput">
                            <button type="button" class="btn btn-outline-secondary" onclick="toggleApiKey()">
                                <i class="bi bi-eye" id="toggleIcon"></i>
                            </button>
                        </div>
                        <div class="form-text">
                            Required for AI chatbot functionality. 
                            <a href="https://platform.openai.com/api-keys" target="_blank">Get your API key here</a>
                        </div>
                    </div>

                    <!-- API Key Status -->
                    <div class="mb-3">
                        @if(!empty($aiSettings['openai_key']))
                        <div class="alert alert-success d-flex align-items-center">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            <div>API Key is configured</div>
                        </div>
                        @else
                        <div class="alert alert-warning d-flex align-items-center">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <div>API Key is not configured. AI Chatbot will not work without it.</div>
                        </div>
                        @endif
                    </div>

                    <hr class="my-4">

                    <!-- AI Behavior Settings -->
                    <h6 class="fw-bold mb-3"><i class="bi bi-sliders me-2"></i>AI Behavior Settings</h6>
                    
                    <div class="mb-3">
                        <label class="form-label">AI Model</label>
                        <select class="form-select" name="ai_model">
                            <option value="gpt-3.5-turbo" selected>gpt-3.5-turbo (Fast & Cost-effective)</option>
                            <option value="gpt-4">gpt-4 (More Capable)</option>
                            <option value="gpt-4-turbo">gpt-4-turbo (Latest & Fast)</option>
                        </select>
                        <div class="form-text">Select the OpenAI model to use for responses</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Max Response Length</label>
                        <input type="number" class="form-control" name="ai_max_tokens" value="500" min="50" max="2000">
                        <div class="form-text">Maximum number of tokens in AI response (1 token ≈ 4 characters)</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Temperature (Creativity)</label>
                        <input type="range" class="form-range" name="ai_temperature" min="0" max="1" step="0.1" value="0.7" id="temperatureRange">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted small">Precise (0)</span>
                            <span class="text-muted small">Creative (1)</span>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- System Prompt -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">System Prompt</label>
                        <textarea class="form-control" name="ai_system_prompt" rows="5" placeholder="You are a helpful customer support assistant for an e-commerce store...">{{ $aiSettings['system_prompt'] ?? 'You are a helpful and friendly customer support assistant for an e-commerce store. You help customers with their inquiries about products, orders, shipping, and general questions. Keep your responses concise and helpful.' }}</textarea>
                        <div class="form-text">Define how the AI should behave and respond to customers</div>
                    </div>

                    <!-- Save Button -->
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i> Save Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Preview Panel -->
    <div class="col-lg-4">
        <!-- Widget Preview -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-eye me-2"></i>Widget Preview</h6>
            </div>
            <div class="card-body p-0">
                <div class="bg-light p-3" style="min-height: 200px;">
                    <div class="bg-white rounded p-3 shadow-sm">
                        <div class="d-flex align-items-center mb-2">
                            <div class="bg-primary rounded-circle p-2 me-2">
                                <i class="bi bi-robot text-white"></i>
                            </div>
                            <div>
                                <strong>AI Assistant</strong>
                                <div class="small text-muted">Online</div>
                            </div>
                        </div>
                        <div class="bg-primary text-white rounded p-2 mb-2">
                            <small>{{ $aiSettings['welcome_message'] ?? 'Hello! How can I help you today?' }}</small>
                        </div>
                        <div class="text-muted small">
                            <i>Customer message preview...</i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Help Card -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-question-circle me-2"></i>Help & Information</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <h6 class="fw-bold">How AI Chatbot Works</h6>
                    <p class="small text-muted mb-0">
                        The AI chatbot automatically responds to customer messages using OpenAI's language models. 
                        It can answer product questions, order status, shipping info, and more.
                    </p>
                </div>
                <div class="mb-3">
                    <h6 class="fw-bold">Pricing</h6>
                    <p class="small text-muted mb-0">
                        OpenAI pricing is pay-as-you-go. GPT-3.5-turbo is very affordable (~$0.002 per 1K tokens).
                    </p>
                </div>
                <div>
                    <h6 class="fw-bold">Tips</h6>
                    <ul class="small text-muted mb-0 ps-3">
                        <li>Keep system prompt concise</li>
                        <li>Test responses regularly</li>
                        <li>Monitor usage in OpenAI dashboard</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleApiKey() {
    const input = document.getElementById('apiKeyInput');
    const icon = document.getElementById('toggleIcon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    }
}

// Update temperature display
document.getElementById('temperatureRange').addEventListener('input', function(e) {
    // Could add live preview of temperature effect here
});
</script>
@endpush
