@extends('admin.layouts.app')

@section('title', 'Live Chat')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Live Chat</h4>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-secondary btn-sm" onclick="refreshConversations()">
            <i class="bi bi-arrow-clockwise me-1"></i> Refresh
        </button>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center py-3">
                <div class="text-muted small text-uppercase">Total Chats</div>
                <div class="h4 mb-0 text-primary" id="statTotal">0</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center py-3">
                <div class="text-muted small text-uppercase">Open</div>
                <div class="h4 mb-0 text-success" id="statOpen">0</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center py-3">
                <div class="text-muted small text-uppercase">Pending</div>
                <div class="h4 mb-0 text-warning" id="statPending">0</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center py-3">
                <div class="text-muted small text-uppercase">Closed</div>
                <div class="h4 mb-0 text-secondary" id="statClosed">0</div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Conversations List -->
    <div class="col-lg-4 col-md-5">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Conversations</h5>
                    <span class="badge bg-primary" id="conversationCount">0</span>
                </div>
            </div>
            <div class="card-body p-0">
                <!-- Filters -->
                <div class="border-bottom p-2">
                    <div class="btn-group w-100">
                        <button class="btn btn-sm btn-outline-secondary filter-btn active" data-filter="all">All</button>
                        <button class="btn btn-sm btn-outline-secondary filter-btn" data-filter="open">Open</button>
                        <button class="btn btn-sm btn-outline-secondary filter-btn" data-filter="pending">Pending</button>
                        <button class="btn btn-sm btn-outline-secondary filter-btn" data-filter="closed">Closed</button>
                    </div>
                </div>
                <!-- Search -->
                <div class="p-2 border-bottom">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" id="conversationSearch" class="form-control" placeholder="Search conversations...">
                    </div>
                </div>
                <!-- Conversations List -->
                <div class="list-group list-group-flush" id="conversationsList" style="max-height: 450px; overflow-y: auto;">
                    <div class="list-group-item text-center text-muted py-4" id="noConversations">
                        <i class="bi bi-chat-dots" style="font-size: 2rem;"></i>
                        <p class="mb-0 mt-2">No conversations yet</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Chat Area -->
    <div class="col-lg-8 col-md-7">
        <div class="card border-0 shadow-sm" id="chatCard" style="min-height: 500px; display: none;">
            <!-- Chat Header -->
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <div class="avatar-circle bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 40px; height: 40px;">
                        <i class="bi bi-person"></i>
                    </div>
                    <div>
                        <h6 class="mb-0" id="chatUserName">Select a conversation</h6>
                        <small class="text-muted" id="chatUserEmail"></small>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <select class="form-select form-select-sm" id="chatStatusSelect" style="width: auto;" onchange="updateChatStatus()">
                        <option value="open">Open</option>
                        <option value="pending">Pending</option>
                        <option value="closed">Closed</option>
                    </select>
                    <button class="btn btn-sm btn-outline-danger" onclick="deleteChat()" title="Delete Chat">
                        <i class="bi bi-trash"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger" onclick="closeChat()" title="Close Chat">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </div>
            
            <!-- Messages Container -->
            <div class="card-body" style="height: 350px; overflow-y: auto;" id="messagesContainer">
                <div class="text-center text-muted py-5" id="noMessages">
                    <i class="bi bi-chat-square-text" style="font-size: 3rem;"></i>
                    <p class="mt-2">Select a conversation to view messages</p>
                </div>
                <div id="messagesList"></div>
            </div>
            
            <!-- Message Input -->
            <div class="card-footer bg-white">
                <!-- Quick Replies -->
                <div class="mb-2" id="quickRepliesContainer">
                    <span class="text-muted small">Loading quick replies...</span>
                </div>
                <form id="sendMessageForm" onsubmit="sendMessage(event)">
                    @csrf
                    <input type="hidden" id="currentConversationId" value="">
                    <div class="input-group">
                        <input type="text" id="messageInput" class="form-control" placeholder="Type your message..." autocomplete="off">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-send"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- No Chat Selected -->
        <div class="card border-0 shadow-sm" id="noChatCard">
            <div class="card-body d-flex align-items-center justify-content-center" style="min-height: 500px;">
                <div class="text-center text-muted">
                    <i class="bi bi-chat-dots" style="font-size: 4rem;"></i>
                    <h5 class="mt-3">Select a Conversation</h5>
                    <p>Choose a conversation from the list to start chatting</p>
                </div>
            </div>
        </div>
        
        <!-- AI Settings Card -->
        <div class="card border-0 shadow-sm mt-3">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-robot me-2"></i>AI Chatbot Settings</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.chat.ai-settings') }}" method="POST" id="ai-settings-form">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="form-check form-switch">
                                <input type="checkbox" name="ai_enabled" class="form-check-input" id="aiEnabled" 
                                    {{ ($aiSettings['enabled'] ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label" for="aiEnabled">
                                    <i class="bi bi-robot text-primary me-1"></i> Enable AI Chatbot
                                </label>
                                <div class="form-text">When enabled, AI will auto-respond to customer messages</div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Welcome Message</label>
                            <input type="text" name="ai_welcome_message" class="form-control" 
                                value="{{ $aiSettings['welcome_message'] ?? 'Hello! How can I help you today?' }}">
                            <div class="form-text">Message shown to new visitors</div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">OpenAI API Key</label>
                        <input type="password" name="openai_api_key" class="form-control" 
                            value="{{ $aiSettings['openai_key'] ?? '' }}" placeholder="sk-...">
                        <div class="form-text">Required for AI chatbot functionality</div>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Floating Save Button -->
        <div class="floating-save-container">
            <button type="submit" form="ai-settings-form" class="btn btn-primary floating-save-btn">
                <i class="bi bi-check-lg me-1"></i> Save AI Settings
            </button>
        </div>
    </div>
</div>

<!-- Notification permission modal -->
<div class="modal fade" id="notificationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Enable Notifications</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Allow notifications to receive alerts for new messages even when this tab is in the background.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Later</button>
                <button type="button" class="btn btn-primary" onclick="requestNotificationPermission()">Enable Notifications</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
<style>
.avatar-circle {
    font-weight: 600;
}
.conversation-item {
    cursor: pointer;
    transition: all 0.2s;
}
.conversation-item:hover {
    background-color: #f8f9fa;
}
.conversation-item.active {
    background-color: #e7f1ff;
    border-left: 3px solid #0d6efd;
}
.message-bubble {
    max-width: 75%;
    word-wrap: break-word;
}
.admin-message {
    background-color: #0d6efd;
    color: white;
    margin-left: auto;
}
.user-message {
    background-color: #f8f9fa;
    color: #212529;
}
.unread-badge {
    position: absolute;
    top: 5px;
    right: 5px;
}
/* Online/Offline Status Indicators */
.online-indicator {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    display: inline-block;
    margin-right: 5px;
}
.online-indicator.online {
    background-color: #28a745;
    box-shadow: 0 0 0 2px rgba(40, 167, 69, 0.3);
    animation: pulse 2s infinite;
}
.online-indicator.offline {
    background-color: #6c757d;
}
.online-indicator.typing {
    background-color: #ffc107;
    animation: pulse 1s infinite;
}
@keyframes pulse {
    0% { box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.4); }
    70% { box-shadow: 0 0 0 6px rgba(40, 167, 69, 0); }
    100% { box-shadow: 0 0 0 0 rgba(40, 167, 69, 0); }
}
/* Attachment preview */
.attachment-preview {
    max-width: 200px;
    border-radius: 8px;
    margin-top: 5px;
}
.attachment-file {
    display: flex;
    align-items: center;
    padding: 8px 12px;
    background: #f8f9fa;
    border-radius: 8px;
    margin-top: 5px;
}
/* Message timestamp */
.message-time {
    font-size: 0.7rem;
    opacity: 0.7;
}
/* Sound notification */
.sound-toggle {
    cursor: pointer;
}
</style>
@endpush

@push('scripts')
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script>
// Pusher Configuration
const PUSHER_APP_KEY = '{{ config("broadcasting.connections.pusher.key") }}';
const PUSHER_CLUSTER = '{{ config("broadcasting.connections.pusher.options.cluster") }}';

// Initialize Pusher
let pusher = null;
let channel = null;
try {
    if (PUSHER_APP_KEY) {
        pusher = new Pusher(PUSHER_APP_KEY, {
            cluster: PUSHER_CLUSTER,
            encrypted: true
        });
    }
} catch (e) {
    console.log('Pusher initialization failed, using polling fallback');
}

// Sound settings
let soundEnabled = true;
const notificationSound = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2teleRQ3xf3s0p12EzWCAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA'); // Short notification sound

let currentConversationId = null;
let currentFilter = 'all';
let refreshInterval = null;
let onlineUsers = {}; // Track online users

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    console.log('Chat page loaded, fetching conversations...');
    loadConversations();
    loadPredefinedMessages();
    
    // Auto-refresh every 10 seconds
    refreshInterval = setInterval(loadConversations, 10000);
    
    // Search listener
    document.getElementById('conversationSearch').addEventListener('input', debounce(loadConversations, 300));
    
    // Filter listeners
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('Filter button clicked:', this.dataset.filter);
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            currentFilter = this.dataset.filter;
            console.log('Filter set to:', currentFilter);
            loadConversations();
        });
    });
    
    // Enter key to send message
    document.getElementById('messageInput').addEventListener('keypress', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage(e);
        }
    });
});

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Load conversations list
function loadConversations() {
    const search = document.getElementById('conversationSearch').value;
    const timestamp = new Date().getTime();
    const url = `{{ route('admin.chat.conversations') }}?filter=${currentFilter}&search=${encodeURIComponent(search)}&_t=${timestamp}`;
    console.log('Loading conversations with filter:', currentFilter, 'URL:', url);
    fetch(url, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(res => {
        console.log('Response status:', res.status);
        return res.json();
    })
    .then(data => {
        console.log('Conversations loaded:', data);
        console.log('Filtered total:', data.filtered_total);
        console.log('Data array:', data.data?.data || data.data || data);
        updateConversationsList(data.data?.data || data.data || data);
        updateStats(data);
    })
    .catch(err => console.error('Error loading conversations:', err));
}

function updateConversationsList(conversations) {
    console.log('updateConversationsList called with:', conversations);
    const list = document.getElementById('conversationsList');
    const countBadge = document.getElementById('conversationCount');
    const noConv = document.getElementById('noConversations');
    
    console.log('Number of conversations:', conversations.length);
    countBadge.textContent = conversations.length;
    
    if (conversations.length === 0) {
        console.log('No conversations found - showing empty message');
        // Rebuild the noConv element if it doesn't exist
        list.innerHTML = '<div class="list-group-item text-center text-muted py-4" id="noConversations">' +
            '<i class="bi bi-chat-dots" style="font-size: 2rem;"></i>' +
            '<p class="mb-0 mt-2">No conversations yet</p>' +
            '</div>';
        return;
    }
    
    // Rebuild list from scratch to avoid stale elements
    list.innerHTML = '';
    
    let html = '';
    conversations.forEach(conv => {
        const isActive = currentConversationId == conv.id ? 'active' : '';
        const userName = conv.user ? conv.user.name : 'Guest';
        const lastMessage = conv.last_message ? conv.last_message.message : 'No messages yet';
        const timeAgo = conv.updated_at ? getTimeAgo(conv.updated_at) : '';
        const unread = conv.unread_count > 0 ? `<span class="badge bg-danger unread-badge">${conv.unread_count}</span>` : '';
        const statusBadge = getStatusBadge(conv.status);
        
        html += `
            <a href="#" class="list-group-item list-group-item-action ${isActive}" data-id="${conv.id}" onclick="selectConversation(${conv.id}, '${userName}', '${conv.status}'); return false;">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-center">
                            <strong>${userName}</strong>
                            ${unread}
                        </div>
                        <small class="d-block text-muted text-truncate">${lastMessage}</small>
                        <small class="text-muted">${timeAgo}</small>
                    </div>
                    <div class="ms-2">
                        ${statusBadge}
                    </div>
                </div>
            </a>
        `;
    });
    
    list.innerHTML = html;
}

function getStatusBadge(status) {
    const badges = {
        'open': '<span class="badge bg-success">Open</span>',
        'pending': '<span class="badge bg-warning">Pending</span>',
        'closed': '<span class="badge bg-secondary">Closed</span>'
    };
    return badges[status] || '';
}

function getTimeAgo(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const diff = Math.floor((now - date) / 1000);
    
    if (diff < 60) return 'Just now';
    if (diff < 3600) return Math.floor(diff / 60) + 'm ago';
    if (diff < 86400) return Math.floor(diff / 3600) + 'h ago';
    if (diff < 604800) return Math.floor(diff / 86400) + 'd ago';
    return date.toLocaleDateString();
}

function updateStats(data) {
    // Use filtered_total if filter is applied, otherwise use total
    const filter = currentFilter;
    if (filter && filter !== 'all') {
        document.getElementById('statTotal').textContent = data.filtered_total || 0;
    } else {
        document.getElementById('statTotal').textContent = data.total || 0;
    }
    document.getElementById('statOpen').textContent = data.open || 0;
    document.getElementById('statPending').textContent = data.pending || 0;
    document.getElementById('statClosed').textContent = data.closed || 0;
}

// Select a conversation
function selectConversation(id, userName, status) {
    currentConversationId = id;
    
    // Update UI
    document.getElementById('noChatCard').style.display = 'none';
    document.getElementById('chatCard').style.display = 'block';
    document.getElementById('chatUserName').textContent = userName;
    document.getElementById('currentConversationId').value = id;
    document.getElementById('chatStatusSelect').value = status;
    
    // Highlight selected conversation immediately
    document.querySelectorAll('#conversationsList .list-group-item').forEach(el => {
        el.classList.remove('active');
    });
    const activeItem = document.querySelector(`#conversationsList .list-group-item[data-id="${id}"]`);
    if (activeItem) {
        activeItem.classList.add('active');
    }
    
    // Load messages
    loadMessages(id);
}

function loadMessages(conversationId) {
    fetch(`{{ route('admin.chat.conversation', ':id') }}`.replace(':id', conversationId), {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        displayMessages(data.messages || []);
        document.getElementById('chatUserEmail').textContent = data.user ? data.user.email : '';
        // Update status dropdown with latest status from database
        if (data.status) {
            document.getElementById('chatStatusSelect').value = data.status;
        }
    })
    .catch(err => console.error('Error loading messages:', err));
}

function displayMessages(messages) {
    const container = document.getElementById('messagesList');
    const noMessages = document.getElementById('noMessages');
    
    if (messages.length === 0) {
        noMessages.style.display = 'block';
        container.innerHTML = '';
        return;
    }
    
    noMessages.style.display = 'none';
    
    let html = '';
    messages.forEach(msg => {
        const isAdmin = msg.sender_type === 'admin';
        const time = new Date(msg.created_at).toLocaleString('en-US', {
            month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit'
        });
        
        html += `
            <div class="mb-3 d-flex ${isAdmin ? 'justify-content-end' : 'justify-content-start'}">
                <div class="message-bubble p-3 rounded ${isAdmin ? 'admin-message' : 'user-message'}">
                    <div>${msg.message}</div>
                    <small class="${isAdmin ? 'text-light' : 'text-muted'}">${time}</small>
                </div>
            </div>
        `;
    });
    
    container.innerHTML = html;
    
    // Scroll to bottom
    const messagesContainer = document.getElementById('messagesContainer');
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
}

function sendMessage(e) {
    e.preventDefault();
    
    const input = document.getElementById('messageInput');
    const message = input.value.trim();
    
    if (!message || !currentConversationId) return;
    
    fetch(`{{ route('admin.chat.send') }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            conversation_id: currentConversationId,
            message: message
        })
    })
    .then(res => res.json())
    .then(data => {
        input.value = '';
        loadMessages(currentConversationId);
        loadConversations(); // Refresh the list
    })
    .catch(err => {
        console.error('Error sending message:', err);
        alert('Failed to send message. Please try again.');
    });
}

function updateChatStatus() {
    if (!currentConversationId) return;
    
    const status = document.getElementById('chatStatusSelect').value;
    
    fetch(`{{ route('admin.chat.update-status', ':id') }}`.replace(':id', currentConversationId), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ status: status })
    })
    .then(res => res.json())
    .then(data => {
        loadConversations();
    })
    .catch(err => console.error('Error updating status:', err));
}

function closeChat() {
    if (!currentConversationId) return;
    
    if (confirm('Are you sure you want to close this chat?')) {
        document.getElementById('chatStatusSelect').value = 'closed';
        updateChatStatus();
        currentConversationId = null;
        document.getElementById('chatCard').style.display = 'none';
        document.getElementById('noChatCard').style.display = 'block';
    }
}

function deleteChat() {
    if (!currentConversationId) return;
    
    if (confirm('Are you sure you want to delete this conversation? This action cannot be undone.')) {
        fetch(`{{ route('admin.chat.destroy', ':id') }}`.replace(':id', currentConversationId), {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert('Conversation deleted successfully.');
                currentConversationId = null;
                document.getElementById('chatCard').style.display = 'none';
                document.getElementById('noChatCard').style.display = 'block';
                loadConversations();
            } else {
                alert('Failed to delete conversation.');
            }
        })
        .catch(err => {
            console.error('Error deleting conversation:', err);
            alert('Error deleting conversation.');
        });
    }
}

function refreshConversations() {
    loadConversations();
    if (currentConversationId) {
        loadMessages(currentConversationId);
    }
}

// Load predefined messages for quick replies
function loadPredefinedMessages() {
    fetch(`{{ route('admin.chat.predefined.messages') }}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        updateQuickReplies(data);
    })
    .catch(err => console.error('Error loading predefined messages:', err));
}

function updateQuickReplies(messages) {
    const container = document.getElementById('quickRepliesContainer');
    if (!container) return;
    
    if (messages.length === 0) {
        container.innerHTML = '<span class="text-muted small">No quick replies available</span>';
        return;
    }
    
    let html = '';
    messages.forEach(msg => {
        html += `<button type="button" class="btn btn-sm btn-outline-secondary me-1 mb-1" 
            onclick="useQuickReply('${msg.message.replace(/'/g, "\\'")}')" 
            title="${msg.message.substring(0, 50)}...">
            ${msg.title}
        </button>`;
    });
    container.innerHTML = html;
}

function useQuickReply(message) {
    document.getElementById('messageInput').value = message;
    document.getElementById('messageInput').focus();
}

// Cleanup on page unload
window.addEventListener('beforeunload', function() {
    if (refreshInterval) {
        clearInterval(refreshInterval);
    }
    // Unsubscribe from Pusher channels
    if (channel) {
        channel.unbind_all();
        channel.unsubscribe();
    }
});

// ============================================
// Real-time Features
// ============================================

// Initialize Pusher subscriptions
function initPusher() {
    if (!pusher) {
        console.log('Pusher not available, using polling fallback');
        return;
    }
    
    // Subscribe to chat events channel
    channel = pusher.subscribe('chat-users');
    
    // Listen for user status changes
    channel.bind('user.status', function(data) {
        console.log('User status changed:', data);
        onlineUsers[data.user_id] = data.is_online;
        updateOnlineIndicators();
    });
    
    // Subscribe to individual chat channels
    subscribeToChatChannels();
}

// Subscribe to all active chat channels
function subscribeToChatChannels() {
    if (!pusher || !channel) return;
    
    // Subscribe to global chat updates
    const globalChannel = pusher.subscribe('chat-global');
    globalChannel.bind('message.sent', function(data) {
        console.log('New message received:', data);
        handleNewMessage(data);
    });
}

// Handle new incoming message
function handleNewMessage(data) {
    const message = data.message || data;
    
    // If message is for current conversation, add to UI
    if (message.chat_id == currentConversationId) {
        const messagesList = document.getElementById('messagesList');
        const noMessages = document.getElementById('noMessages');
        if (noMessages) noMessages.style.display = 'none';
        
        appendMessage(message);
        
        // Mark as read
        fetch(`{{ route('admin.chat.conversation', ':id') }}`.replace(':id', currentConversationId), {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
    } else {
        // Show notification for other conversations
        showNotification('New Message', message.message || 'You have a new message');
        playNotificationSound();
    }
    
    // Refresh conversation list
    loadConversations();
}

// Subscribe to specific chat channel
function subscribeToChat(chatId) {
    if (!pusher) return;
    
    const chatChannel = pusher.subscribe('chat.' + chatId);
    chatChannel.bind('message.sent', function(data) {
        if (currentConversationId == chatId) {
            appendMessage(data.message || data);
        }
    });
}

// Append message to messages list
function appendMessage(msg) {
    const container = document.getElementById('messagesList');
    if (!container) return;
    
    const isAdmin = msg.sender_type === 'admin';
    const time = new Date(msg.created_at).toLocaleString('en-US', {
        month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit'
    });
    
    let attachmentHtml = '';
    if (msg.attachments) {
        try {
            const att = JSON.parse(msg.attachments);
            if (att.type && att.type.startsWith('image/')) {
                attachmentHtml = `<img src="${att.path}" class="attachment-preview" alt="attachment">`;
            } else {
                attachmentHtml = `<div class="attachment-file"><i class="bi bi-file-earmark"></i> ${att.filename}</div>`;
            }
        } catch (e) {}
    }
    
    const html = `
        <div class="mb-3 d-flex ${isAdmin ? 'justify-content-end' : 'justify-content-start'}">
            <div class="message-bubble p-3 rounded ${isAdmin ? 'admin-message' : 'user-message'}">
                <div>${msg.message}</div>
                ${attachmentHtml}
                <small class="${isAdmin ? 'text-light' : 'text-muted'} message-time">${time}</small>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', html);
    
    // Scroll to bottom
    const messagesContainer = document.getElementById('messagesContainer');
    if (messagesContainer) {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }
}

// ============================================
// Browser Notifications
// ============================================

function requestNotificationPermission() {
    if (!('Notification' in window)) {
        alert('This browser does not support desktop notification');
        return;
    }
    
    Notification.requestPermission().then(function(permission) {
        if (permission === 'granted') {
            showNotification('Notifications Enabled', 'You will now receive notifications for new messages');
            $('#notificationModal').modal('hide');
        }
    });
}

function showNotification(title, body) {
    if (!('Notification' in window)) return;
    
    if (Notification.permission === 'granted') {
        const notification = new Notification(title, {
            body: body,
            icon: '/favicon.ico',
            badge: '/favicon.ico',
            tag: 'chat-notification',
            requireInteraction: false
        });
        
        notification.onclick = function() {
            window.focus();
            this.close();
        };
        
        setTimeout(() => notification.close(), 5000);
    }
}

function playNotificationSound() {
    if (soundEnabled) {
        try {
            notificationSound.play().catch(() => {});
        } catch (e) {}
    }
}

// ============================================
// Online Status Indicators
// ============================================

function updateOnlineIndicators() {
    document.querySelectorAll('.conversation-item').forEach(item => {
        const userId = item.dataset.userId;
        const indicator = item.querySelector('.online-indicator');
        if (indicator && userId) {
            if (onlineUsers[userId]) {
                indicator.className = 'online-indicator online';
            } else {
                indicator.className = 'online-indicator offline';
            }
        }
    });
}

// ============================================
// File Upload
// ============================================

function handleFileSelect(event) {
    const file = event.target.files[0];
    if (!file) return;
    
    const maxSize = 10 * 1024 * 1024; // 10MB
    if (file.size > maxSize) {
        alert('File size must be less than 10MB');
        event.target.value = '';
        return;
    }
    
    // Show file preview
    const preview = document.getElementById('filePreview');
    if (preview) {
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.innerHTML = `<img src="${e.target.result}" style="max-width: 100px; border-radius: 4px;">`;
            };
            reader.readAsDataURL(file);
        } else {
            preview.innerHTML = `<span class="text-muted"><i class="bi bi-file-earmark"></i> ${file.name}</span>`;
        }
    }
}

// Add file input to message form
function addFileUpload() {
    const form = document.getElementById('sendMessageForm');
    if (!form) return;
    
    // Check if file input already exists
    if (document.getElementById('attachmentInput')) return;
    
    const fileContainer = document.createElement('div');
    fileContainer.className = 'mb-2';
    fileContainer.innerHTML = `
        <input type="file" id="attachmentInput" name="attachment" accept="image/*,.pdf,.doc,.docx" 
            onchange="handleFileSelect(event)" style="display: none;">
        <div id="filePreview" class="mb-2"></div>
    `;
    
    const inputGroup = form.querySelector('.input-group');
    form.insertBefore(fileContainer, inputGroup);
    
    // Add attachment button
    const attachBtn = document.createElement('button');
    attachBtn.type = 'button';
    attachBtn.className = 'btn btn-outline-secondary';
    attachBtn.innerHTML = '<i class="bi bi-paperclip"></i>';
    attachBtn.onclick = function() {
        document.getElementById('attachmentInput').click();
    };
    inputGroup.insertBefore(attachBtn, inputGroup.querySelector('button[type="submit"]'));
}

// Update sendMessage to include file upload
const originalSendMessage = sendMessage;
sendMessage = function(e) {
    e.preventDefault();
    
    const input = document.getElementById('messageInput');
    const message = input.value.trim();
    const fileInput = document.getElementById('attachmentInput');
    const file = fileInput ? fileInput.files[0] : null;
    
    if (!message && !file) return;
    if (!currentConversationId && !file) return;
    
    const formData = new FormData();
    formData.append('conversation_id', currentConversationId);
    if (message) formData.append('message', message);
    if (file) formData.append('attachment', file);
    
    fetch(`{{ route('admin.chat.send') }}`, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        input.value = '';
        if (fileInput) fileInput.value = '';
        const preview = document.getElementById('filePreview');
        if (preview) preview.innerHTML = '';
        loadMessages(currentConversationId);
        loadConversations();
    })
    .catch(err => {
        console.error('Error sending message:', err);
        alert('Failed to send message. Please try again.');
    });
};

// ============================================
// Initialize on DOM Ready
// ============================================

document.addEventListener('DOMContentLoaded', function() {
    initPusher();
    addFileUpload();
    
    // Show notification permission modal after 3 seconds
    if (Notification.permission === 'default') {
        setTimeout(() => {
            if (typeof $ !== 'undefined') {
                $('#notificationModal').modal('show');
            }
        }, 3000);
    }
});
</script>
@endpush
