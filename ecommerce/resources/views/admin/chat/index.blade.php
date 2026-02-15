@extends('admin.layouts.app')

@section('title', 'Chat Management')

@section('content')
<div class="mb-4">
    <h4 class="mb-0">Chat Management</h4>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Conversations</h5>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush" style="max-height: 500px; overflow-y: auto;">
                    @forelse($conversations ?? [] as $conversation)
                    <a href="{{ route('admin.chat.conversation', $conversation->id) }}" class="list-group-item list-group-item-action {{ request('id') == $conversation->id ? 'active' : '' }}">
                        <div class="d-flex justify-content-between">
                            <div>
                                <strong>{{ $conversation->user->name ?? 'Guest' }}</strong>
                                <small class="d-block text-muted">{{ Str::limit($conversation->last_message ?? 'No messages', 30) }}</small>
                            </div>
                            <small class="text-muted">{{ $conversation->updated_at->diffForHumans() }}</small>
                        </div>
                    </a>
                    @empty
                    <div class="list-group-item text-center text-muted py-4">
                        No conversations yet
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Messages</h5>
            </div>
            <div class="card-body" style="height: 400px; overflow-y: auto;" id="messagesContainer">
                @if(isset($messages))
                    @forelse($messages as $message)
                    <div class="mb-3 {{ $message->is_from_admin ? 'text-end' : '' }}">
                        <div class="d-inline-block p-3 rounded {{ $message->is_from_admin ? 'bg-primary text-white' : 'bg-light' }}" style="max-width: 70%;">
                            {{ $message->message }}
                        </div>
                        <small class="d-block text-muted mt-1">{{ $message->created_at->format('H:i, d M') }}</small>
                    </div>
                    @empty
                    <div class="text-center text-muted py-5">
                        Select a conversation to view messages
                    </div>
                    @endforelse
                @else
                    <div class="text-center text-muted py-5">
                        Select a conversation from the left panel
                    </div>
                @endif
            </div>
            @if(isset($currentConversation))
            <div class="card-footer bg-white">
                <form action="{{ route('admin.chat.send') }}" method="POST">
                    @csrf
                    <input type="hidden" name="conversation_id" value="{{ $currentConversation->id }}">
                    <div class="input-group">
                        <input type="text" name="message" class="form-control" placeholder="Type your message..." required>
                        <button type="submit" class="btn btn-primary">Send</button>
                    </div>
                </form>
            </div>
            @endif
        </div>
        
        <div class="card mt-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">AI Chatbot Settings</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.chat.ai-settings') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="ai_enabled" class="form-check-input" id="aiEnabled" {{ ($aiSettings['enabled'] ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label" for="aiEnabled">Enable AI Chatbot</label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">OpenAI API Key</label>
                        <input type="password" name="openai_api_key" class="form-control" value="{{ $aiSettings['openai_key'] ?? '' }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Welcome Message</label>
                        <textarea name="ai_welcome_message" class="form-control" rows="2">{{ $aiSettings['welcome_message'] ?? 'Hello! How can I help you today?' }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Save AI Settings</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Auto scroll to bottom of messages
var container = document.getElementById('messagesContainer');
if (container) {
    container.scrollTop = container.scrollHeight;
}
</script>
@endpush
