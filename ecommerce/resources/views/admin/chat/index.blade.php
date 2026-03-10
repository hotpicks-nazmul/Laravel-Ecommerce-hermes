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
    <div class="col-md-2 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center py-3">
                <div class="text-muted small text-uppercase">Total Chats</div>
                <div class="h4 mb-0 text-primary" id="statTotal">0</div>
            </div>
        </div>
    </div>
    <div class="col-md-2 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center py-3">
                <div class="text-muted small text-uppercase">New</div>
                <div class="h4 mb-0 text-info" id="statNew">0</div>
            </div>
        </div>
    </div>
    <div class="col-md-2 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center py-3">
                <div class="text-muted small text-uppercase">Pending</div>
                <div class="h4 mb-0 text-warning" id="statPending">0</div>
            </div>
        </div>
    </div>
    <div class="col-md-2 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center py-3">
                <div class="text-muted small text-uppercase">Replied</div>
                <div class="h4 mb-0 text-success" id="statReplied">0</div>
            </div>
        </div>
    </div>
    <div class="col-md-2 col-sm-6 mb-3">
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
                        <button class="btn btn-sm btn-outline-secondary filter-btn active position-relative" data-filter="all">
                            All <span class="badge rounded-pill bg-secondary" id="badgeAll">0</span>
                        </button>
                        <button class="btn btn-sm btn-outline-secondary filter-btn position-relative" data-filter="new">
                            New <span class="badge rounded-pill bg-info" id="badgeNew">0</span>
                        </button>
                        <button class="btn btn-sm btn-outline-secondary filter-btn position-relative" data-filter="pending">
                            Pending <span class="badge rounded-pill bg-warning" id="badgePending">0</span>
                        </button>
                        <button class="btn btn-sm btn-outline-secondary filter-btn position-relative" data-filter="replied">
                            Replied <span class="badge rounded-pill bg-success" id="badgeReplied">0</span>
                        </button>
                        <button class="btn btn-sm btn-outline-secondary filter-btn position-relative" data-filter="closed">
                            Closed <span class="badge rounded-pill bg-dark" id="badgeClosed">0</span>
                        </button>
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
                    <button class="btn btn-sm btn-outline-secondary" id="markUnreadBtn" onclick="markAsUnread()" title="Mark as Unread" style="display: none;">
                        <i class="bi bi-envelope"></i> Mark as Unread
                    </button>
                    <button class="btn btn-sm btn-outline-success" id="markReadBtn" onclick="markAsRead()" title="Mark as Read">
                        <i class="bi bi-envelope-check"></i> Mark as Read
                    </button>
                    <select class="form-select form-select-sm" id="chatStatusSelect" style="width: auto;" onchange="updateChatStatus()">
                        <option value="new">New</option>
                        <option value="pending">Pending</option>
                        <option value="replied">Replied</option>
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
            
            <!-- Typing Indicator -->
            <div class="px-3 py-2" id="typingIndicator" style="display: none;">
                <div class="d-flex align-items-center">
                    <div class="avatar-circle bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                        <i class="bi bi-person"></i>
                    </div>
                    <div class="bg-light rounded p-2">
                        <div class="d-flex gap-1">
                            <span class="typing-dot" style="width: 8px; height: 8px; background: #0d6efd; border-radius: 50%; animation: typing 1.4s infinite both;"></span>
                            <span class="typing-dot" style="width: 8px; height: 8px; background: #0d6efd; border-radius: 50%; animation: typing 1.4s infinite both; animation-delay: 0.2s;"></span>
                            <span class="typing-dot" style="width: 8px; height: 8px; background: #0d6efd; border-radius: 50%; animation: typing 1.4s infinite both; animation-delay: 0.4s;"></span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Message Input -->
            <div class="card-footer bg-white">
                <!-- Quick Replies -->
                <div class="mb-2">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-muted small fw-medium">Quick Replies</span>
                        <div class="btn-group btn-group-sm" id="quickReplyCategoryTabs">
                            <button type="button" class="btn btn-outline-secondary active" data-category="all">All</button>
                        </div>
                    </div>
                    <div id="quickRepliesContainer">
                        <span class="text-muted small">Loading quick replies...</span>
                    </div>
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
.list-group-item.active {
    background-color: #0d6efd;
    border-left: 3px solid #0d6efd;
}
.list-group-item.active .text-muted,
.list-group-item.active small {
    color: #ffffff !important;
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
    background-color: #e9ecef;
    color: #212529;
}
.user-message .text-muted {
    color: #6c757d !important;
}
/* Typing animation */
@keyframes typing {
    0%, 60%, 100% {
        transform: translateY(0);
    }
    30% {
        transform: translateY(-4px);
    }
}
.typing-indicator {
    display: flex;
    gap: 4px;
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
/* Toast notifications */
.toast-notification {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 9999;
    transform: translateY(100px);
    opacity: 0;
    transition: all 0.3s ease;
}
.toast-notification.show {
    transform: translateY(0);
    opacity: 1;
}
.toast-content {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 20px;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
.toast-success .toast-content {
    border-left: 4px solid #28a745;
}
.toast-success .toast-content i {
    color: #28a745;
}
.toast-error .toast-content {
    border-left: 4px solid #dc3545;
}
.toast-error .toast-content i {
    color: #dc3545;
}
.toast-warning .toast-content {
    border-left: 4px solid #ffc107;
}
.toast-warning .toast-content i {
    color: #ffc107;
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
        // Show user name if logged in, otherwise show guest name with (Guest) suffix
        let userName = 'Guest';
        if (conv.user && conv.user.name) {
            userName = conv.user.name;
        } else if (conv.guest_name) {
            userName = conv.guest_name + ' (Guest)';
        }
        const lastMessage = conv.last_message ? conv.last_message.message : 'No messages yet';
        // Truncate to first 5 words
        const words = lastMessage.split(' ');
        const truncatedMessage = words.slice(0, 5).join(' ') + (words.length > 5 ? '...' : '');
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
                        <small class="d-block text-muted text-truncate">${truncatedMessage}</small>
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
        'new': '<span class="badge bg-info">New</span>',
        'pending': '<span class="badge bg-warning">Pending</span>',
        'replied': '<span class="badge bg-success">Replied</span>',
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
    document.getElementById('statNew').textContent = data.new || 0;
    document.getElementById('statPending').textContent = data.pending || 0;
    document.getElementById('statReplied').textContent = data.replied || 0;
    document.getElementById('statClosed').textContent = data.closed || 0;
    
    // Update filter badges
    document.getElementById('badgeAll').textContent = data.total || 0;
    document.getElementById('badgeNew').textContent = data.new || 0;
    document.getElementById('badgePending').textContent = data.pending || 0;
    document.getElementById('badgeReplied').textContent = data.replied || 0;
    document.getElementById('badgeClosed').textContent = data.closed || 0;
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
    
    // Show mark as unread button
    document.getElementById('markUnreadBtn').style.display = 'inline-block';
    
    // Highlight selected conversation
    document.querySelectorAll('#conversationsList .list-group-item').forEach(el => {
        el.classList.remove('active');
    });
    const activeItem = document.querySelector(`#conversationsList .list-group-item[data-id="${id}"]`);
    if (activeItem) {
        activeItem.classList.add('active');
    }
    
    // Load messages
    loadMessages(id);
    
    // Mark messages as read when conversation is clicked
    markAsRead();
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
    
    // Send typing status as false when message is sent
    sendTypingStatus(false);
    
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

// Typing indicator functions
let typingTimeout = null;

function showTypingIndicator() {
    const typingIndicator = document.getElementById('typingIndicator');
    if (typingIndicator) {
        typingIndicator.style.display = 'block';
        // Auto-hide after 5 seconds
        if (typingTimeout) clearTimeout(typingTimeout);
        typingTimeout = setTimeout(() => {
            hideTypingIndicator();
        }, 5000);
    }
}

function hideTypingIndicator() {
    const typingIndicator = document.getElementById('typingIndicator');
    if (typingIndicator) {
        typingIndicator.style.display = 'none';
    }
}

// Send typing status to server
function sendTypingStatus(isTyping) {
    if (!currentConversationId) return;
    
    fetch(`{{ route('admin.chat.typing') }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            conversation_id: currentConversationId,
            is_typing: isTyping
        })
    }).catch(err => console.log('Error sending typing status:', err));
}

// Setup typing listener
function setupTypingListener() {
    const messageInput = document.getElementById('messageInput');
    if (messageInput) {
        let typingTimer = null;
        messageInput.addEventListener('input', function() {
            sendTypingStatus(true);
            // Clear typing status after user stops typing for 1 second
            if (typingTimer) clearTimeout(typingTimer);
            typingTimer = setTimeout(() => {
                sendTypingStatus(false);
            }, 1000);
        });
    }
}

// Initialize typing listener on page load
document.addEventListener('DOMContentLoaded', function() {
    setupTypingListener();
    // Start polling for typing indicator
    startTypingPolling();
});

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

function markAsUnread() {
    if (!currentConversationId) return;
    
    fetch(`{{ route('admin.chat.mark-unread', ':id') }}`.replace(':id', currentConversationId), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            // Add unread badge back to conversation with actual count
            const activeItem = document.querySelector(`#conversationsList .list-group-item[data-id="${currentConversationId}"]`);
            if (activeItem) {
                let badge = activeItem.querySelector('.unread-badge');
                if (!badge) {
                    badge = document.createElement('span');
                    badge.className = 'badge bg-danger unread-badge';
                    activeItem.querySelector('.d-flex').appendChild(badge);
                }
                badge.textContent = data.unread_count;
            }
            loadConversations();
        }
    })
    .catch(err => console.error('Error marking as unread:', err));
}

function markAsRead() {
    if (!currentConversationId) return;
    
    fetch(`{{ route('admin.chat.mark-read', ':id') }}`.replace(':id', currentConversationId), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            // Remove unread badge from conversation
            const activeItem = document.querySelector(`#conversationsList .list-group-item[data-id="${currentConversationId}"]`);
            if (activeItem) {
                const badge = activeItem.querySelector('.unread-badge');
                if (badge) {
                    badge.remove();
                }
            }
            loadConversations();
        }
    })
    .catch(err => console.error('Error marking as read:', err));
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
let predefinedMessages = [];
let currentQuickReplyCategory = 'all';

function loadPredefinedMessages() {
    fetch(`{{ route('admin.chat.predefined.messages') }}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        predefinedMessages = data;
        updateQuickReplyCategories(data);
        updateQuickReplies(data);
    })
    .catch(err => console.error('Error loading predefined messages:', err));
}

function updateQuickReplyCategories(messages) {
    const tabsContainer = document.getElementById('quickReplyCategoryTabs');
    if (!tabsContainer) return;
    
    // Extract unique categories
    const categories = [...new Set(messages.map(m => m.category).filter(c => c))];
    
    if (categories.length === 0) {
        tabsContainer.innerHTML = '<button type="button" class="btn btn-outline-secondary active" data-category="all">All</button>';
    } else {
        let html = '<button type="button" class="btn btn-outline-secondary active" data-category="all">All</button>';
        categories.forEach(cat => {
            html += `<button type="button" class="btn btn-outline-secondary" data-category="${cat}">${cat}</button>`;
        });
        tabsContainer.innerHTML = html;
        
        // Add click handlers
        tabsContainer.querySelectorAll('button').forEach(btn => {
            btn.addEventListener('click', function() {
                tabsContainer.querySelectorAll('button').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                currentQuickReplyCategory = this.dataset.category;
                filterQuickReplies();
            });
        });
    }
}

function filterQuickReplies() {
    if (currentQuickReplyCategory === 'all') {
        updateQuickReplies(predefinedMessages);
    } else {
        const filtered = predefinedMessages.filter(m => m.category === currentQuickReplyCategory);
        updateQuickReplies(filtered);
    }
}

function updateQuickReplies(messages) {
    const container = document.getElementById('quickRepliesContainer');
    if (!container) return;
    
    if (messages.length === 0) {
        container.innerHTML = '<span class="text-muted small">No quick replies available</span>';
        return;
    }
    
    let html = '';
    messages.forEach((msg, index) => {
        // Add keyboard shortcut hint (1-9)
        const shortcut = index < 9 ? `<span class="badge bg-secondary ms-1" style="font-size: 0.6rem;">${index + 1}</span>` : '';
        html += `<button type="button" class="btn btn-sm btn-outline-primary me-1 mb-1" 
            onclick="sendQuickReply(${msg.id}, '${msg.message.replace(/'/g, "\\'")}')" 
            title="${msg.message.substring(0, 80)}${msg.message.length > 80 ? '...' : ''}\nClick to send directly | Hold Ctrl+${index + 1}">
            ${msg.title} ${shortcut}
        </button>`;
    });
    container.innerHTML = html;
}

function useQuickReply(message) {
    document.getElementById('messageInput').value = message;
    document.getElementById('messageInput').focus();
}

// Send quick reply directly without typing
function sendQuickReply(id, message) {
    const conversationId = document.getElementById('currentConversationId').value;
    if (!conversationId) {
        showToast('Please select a conversation first', 'warning');
        return;
    }
    
    fetch(`{{ route('admin.chat.send') }}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            conversation_id: conversationId,
            message: message
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            loadMessages(conversationId);
            showToast('Message sent!', 'success');
        } else {
            showToast(data.message || 'Failed to send message', 'error');
        }
    })
    .catch(err => {
        console.error('Error sending quick reply:', err);
        showToast('Failed to send message', 'error');
    });
}

// Toast notification function
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `toast-notification toast-${type}`;
    toast.innerHTML = `
        <div class="toast-content">
            <i class="bi bi-${type === 'success' ? 'check-circle-fill' : type === 'error' ? 'exclamation-circle-fill' : 'info-circle-fill'}"></i>
            <span>${message}</span>
        </div>
    `;
    document.body.appendChild(toast);
    setTimeout(() => toast.classList.add('show'), 10);
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
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
    if (pusher) {
        const chatChannel = pusher.subscribe('chat.' + chatId);
        chatChannel.bind('message.sent', function(data) {
            if (currentConversationId == chatId) {
                appendMessage(data.message || data);
            }
        });
    }
    
    // Start polling for user typing status (every 1 second) - always start regardless of Pusher
    startTypingPolling();
}

// Polling for typing indicator (database-based)
let typingPollInterval = null;

function startTypingPolling() {
    if (typingPollInterval) return;
    
    console.log('Starting typing polling');
    typingPollInterval = setInterval(() => {
        checkUserTypingStatus();
    }, 1000); // Check every 1 second
}

function checkUserTypingStatus() {
    if (!currentConversationId) return;
    
    fetch('{{ route("admin.chat.check-typing") }}?conversation_id=' + currentConversationId)
        .then(res => res.json())
        .then(data => {
            if (data.user_is_typing) {
                showTypingIndicator();
            } else {
                hideTypingIndicator();
            }
        })
        .catch(err => console.log('Error checking typing status:', err));
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
    
    // Keyboard shortcuts for quick replies (Ctrl+1-9)
    document.addEventListener('keydown', function(e) {
        // Check if Ctrl or Cmd key is pressed with a number 1-9
        if ((e.ctrlKey || e.metaKey) && e.key >= '1' && e.key <= '9') {
            e.preventDefault();
            const index = parseInt(e.key) - 1;
            
            // Get visible quick replies based on current category filter
            let visibleMessages = predefinedMessages;
            if (currentQuickReplyCategory !== 'all') {
                visibleMessages = predefinedMessages.filter(m => m.category === currentQuickReplyCategory);
            }
            
            if (visibleMessages[index]) {
                sendQuickReply(visibleMessages[index].id, visibleMessages[index].message);
            }
        }
        
        // Ctrl+0 to open quick replies panel
        if ((e.ctrlKey || e.metaKey) && e.key === '0') {
            e.preventDefault();
            document.getElementById('messageInput').focus();
        }
    });
    
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
