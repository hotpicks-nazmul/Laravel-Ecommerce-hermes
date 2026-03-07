<!-- Chat Widget -->
<div class="fixed bottom-6 right-6 z-40">
    <!-- Chat Button -->
    <button id="chatToggle" onclick="toggleChat()" class="w-14 h-14 bg-halal-green text-white rounded-full shadow-lg hover:bg-halal-dark transition-all duration-300 flex items-center justify-center group relative">
        <i class="bi bi-chat-dots-fill text-xl group-hover:scale-110 transition-transform"></i>
        <span id="chatNotification" style="position: absolute; top: -5px; right: -5px; min-width: 20px; height: 20px; background-color: #dc2626; color: white; font-size: 11px; font-weight: bold; border-radius: 50%; display: none; align-items: center; justify-content: center;">0</span>
    </button>
    
    <!-- Chat Window -->
    <div id="chatWindow" class="hidden absolute bottom-16 right-0 w-80 md:w-96 bg-white rounded-2xl shadow-2xl overflow-hidden">
        <!-- Header -->
        <div class="gradient-halal p-4 text-white">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                        <i class="bi bi-headset text-xl"></i>
                    </div>
                    <div>
                        <h4 class="font-medium">Customer Support</h4>
                        <span class="text-xs text-green-100 flex items-center">
                            <span class="w-2 h-2 bg-green-400 rounded-full mr-1 animate-pulse"></span>
                            Online
                        </span>
                    </div>
                </div>
                <button onclick="toggleChat()" class="text-white/80 hover:text-white">
                    <i class="bi bi-x-lg text-xl"></i>
                </button>
            </div>
        </div>
        
        <!-- Messages -->
        <div id="chatMessages" class="h-80 overflow-y-auto p-4 space-y-4 bg-gray-50">
            <!-- Welcome Message -->
            <div class="flex items-start space-x-2">
                <div class="w-8 h-8 bg-halal-green rounded-full flex items-center justify-center flex-shrink-0">
                    <i class="bi bi-headset text-white text-sm"></i>
                </div>
                <div class="bg-white p-3 rounded-lg rounded-tl-none shadow-sm max-w-[80%]">
                    <p class="text-sm text-gray-700">Assalamu Alaikum! 👋 Welcome to Halal Food Store. How can I help you today?</p>
                    <p class="text-xs text-gray-400 mt-1">Our team typically replies within minutes</p>
                </div>
            </div>
        </div>
        
        <!-- Input -->
        <div class="p-4 border-t bg-white">
            <form id="chatForm" onsubmit="sendMessage(event)" class="flex items-center space-x-2">
                <input type="hidden" id="conversationId" value="">
                <input type="text" id="chatInput" placeholder="Type your message..." 
                    class="flex-1 px-4 py-2 border border-gray-200 rounded-full focus:border-halal-green focus:outline-none text-sm">
                <button type="submit" class="w-10 h-10 bg-halal-green text-white rounded-full flex items-center justify-center hover:bg-halal-dark transition-colors">
                    <i class="bi bi-send-fill"></i>
                </button>
            </form>
        </div>
    </div>
</div>

<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script>
// Pusher Configuration
const PUSHER_APP_KEY = '{{ config("broadcasting.connections.pusher.key") }}';
const PUSHER_CLUSTER = '{{ config("broadcasting.connections.pusher.options.cluster") }}';

// Initialize Pusher for real-time updates
let pusher = null;
let channel = null;

function initPusher(convId) {
    try {
        if (PUSHER_APP_KEY && !pusher) {
            pusher = new Pusher(PUSHER_APP_KEY, {
                cluster: PUSHER_CLUSTER,
                encrypted: true
            });
        }
        
        if (pusher && convId) {
            // Unsubscribe from previous channel if exists
            if (channel) {
                pusher.unsubscribe(channel);
            }
            // Subscribe to conversation-specific channel
            channel = pusher.subscribe('chat.' + convId);
            channel.bind('message.sent', function(data) {
                console.log('Received message:', data);
                // Add admin message to chat
                if (data.message && data.sender_type === 'admin') {
                    addMessage(data.message, 'bot');
                }
                // Show notification
                showChatNotification();
            });
        }
    } catch (e) {
        console.log('Pusher initialization failed:', e);
    }
}

function showChatNotification() {
    const notification = document.getElementById('chatNotification');
    if (notification) {
        // Increment unread count
        window.unreadCount = (window.unreadCount || 0) + 1;
        notification.textContent = window.unreadCount;
        notification.style.display = 'flex';
        console.log('Chat notification shown, count:', window.unreadCount);
    }
    
    // Blink page title when chat is not open
    if (!chatOpen) {
        let originalTitle = document.title;
        let blinkInterval = setInterval(() => {
            if (chatOpen) {
                document.title = originalTitle;
                clearInterval(blinkInterval);
                return;
            }
            document.title = document.title === originalTitle ? '💬 New Message!' : originalTitle;
        }, 1000);
        
        // Store interval ID for cleanup
        window.chatTitleBlink = blinkInterval;
    }
}

let chatOpen = false;
let conversationId = localStorage.getItem('chat_conversation_id') || '';
let messagePollingInterval = null;

// Initialize Pusher if conversation ID exists
if (conversationId) {
    initPusher(conversationId);
    startMessagePolling();
}

// Poll for new messages every 3 seconds
function startMessagePolling() {
    console.log('Starting message polling...');
    if (messagePollingInterval) {
        clearInterval(messagePollingInterval);
    }
    messagePollingInterval = setInterval(() => {
        const convId = document.getElementById('conversationId').value;
        console.log('Polling for messages, conversation ID:', convId);
        if (convId) {
            loadChatHistory();
        }
    }, 3000); // Poll every 3 seconds
}

function stopMessagePolling() {
    if (messagePollingInterval) {
        clearInterval(messagePollingInterval);
        messagePollingInterval = null;
    }
}

// Set initial conversation ID if exists
if (conversationId) {
    document.getElementById('conversationId').value = conversationId;
}

function toggleChat() {
    chatOpen = !chatOpen;
    const chatWindow = document.getElementById('chatWindow');
    const chatToggle = document.getElementById('chatToggle');
    const chatNotification = document.getElementById('chatNotification');
    const whatsappWidget = document.getElementById('whatsapp-widget');
    
    if (chatOpen) {
        // Stop title blinking
        if (window.chatTitleBlink) {
            clearInterval(window.chatTitleBlink);
            window.chatTitleBlink = null;
        }
        // Restore original title
        document.title = 'E-Commerce';
        
        // Reset unread count and hide notification badge
        window.unreadCount = 0;
        if (chatNotification) {
            chatNotification.style.display = 'none';
            chatNotification.textContent = '0';
        }
        
        chatWindow.classList.remove('hidden');
        chatToggle.innerHTML = '<i class="bi bi-x-lg text-xl"></i>';
        // Hide WhatsApp widget when chat is open
        if (whatsappWidget) {
            whatsappWidget.classList.add('hidden');
        }
        // Reset loaded flag to show welcome message
        const messagesContainer = document.getElementById('chatMessages');
        if (messagesContainer) {
            delete messagesContainer.dataset.loaded;
        }
        // Load chat history
        loadChatHistory();
    } else {
        chatWindow.classList.add('hidden');
        chatToggle.innerHTML = '<i class="bi bi-chat-dots-fill text-xl group-hover:scale-110 transition-transform"></i>';
        // Show WhatsApp widget when chat is closed
        if (whatsappWidget) {
            whatsappWidget.classList.remove('hidden');
        }
    }
}

function loadChatHistory() {
    const convId = document.getElementById('conversationId').value;
    if (!convId) return;
    
    fetch(`/chat/messages?conversation_id=${convId}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(res => {
        console.log('Messages response status:', res.status);
        return res.json();
    })
    .then(data => {
        console.log('Messages received:', data);
        const messagesContainer = document.getElementById('chatMessages');
        
        // Check if this is first load (no messages displayed yet)
        const isFirstLoad = !messagesContainer.dataset.loaded;
        
        if (isFirstLoad) {
            // First load - show welcome message and all messages
            messagesContainer.innerHTML = `
                <div class="flex items-start space-x-2">
                    <div class="w-8 h-8 bg-halal-green rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="bi bi-headset text-white text-sm"></i>
                    </div>
                    <div class="bg-white p-3 rounded-lg rounded-tl-none shadow-sm max-w-[80%]">
                        <p class="text-sm text-gray-700">Assalamu Alaikum! 👋 Welcome back! How can I help you today?</p>
                    </div>
                </div>
            `;
            
            // Add existing messages
            data.forEach(msg => {
                addMessage(msg.message, msg.sender_type === 'admin' ? 'bot' : 'user');
            });
            
            messagesContainer.dataset.loaded = 'true';
            messagesContainer.dataset.messageCount = data.length;
            console.log('First load, message count:', data.length);
        } else {
            // Polling update - check for new messages
            const storedCount = parseInt(messagesContainer.dataset.messageCount) || 0;
            console.log('Polling - stored:', storedCount, 'server:', data.length);
            
            if (data.length > storedCount) {
                // New messages exist
                const newMessages = data.slice(storedCount);
                console.log('Adding new messages:', newMessages.length);
                newMessages.forEach(msg => {
                    addMessage(msg.message, msg.sender_type === 'admin' ? 'bot' : 'user');
                });
                messagesContainer.dataset.messageCount = data.length;
                
                // Show notification for new admin messages
                const hasNewAdminMessage = newMessages.some(msg => msg.sender_type === 'admin');
                if (hasNewAdminMessage) {
                    showChatNotification();
                }
            }
        }
    })
    .catch(err => console.log('No previous messages'));
}

function sendMessage(event) {
    event.preventDefault();
    const input = document.getElementById('chatInput');
    const message = input.value.trim();
    let convId = document.getElementById('conversationId').value;
    
    if (!message) return;
    
    // Add user message immediately
    addMessage(message, 'user');
    input.value = '';
    
    // Send to backend - let backend handle conversation creation
    fetch('{{ route("chat.send") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            message: message,
            conversation_id: convId || null
        })
    })
    .then(res => {
        if (!res.ok) {
            throw new Error('Network response was not ok');
        }
        return res.json();
    })
    .then(data => {
        if (data.success) {
            // Store conversation ID
            conversationId = data.conversation_id;
            document.getElementById('conversationId').value = conversationId;
            localStorage.setItem('chat_conversation_id', conversationId);
            
            // Initialize Pusher for this conversation
            initPusher(conversationId);
            
            // Start polling for new messages
            startMessagePolling();
            
            // Show AI response
            setTimeout(() => {
                const response = getAIResponse(message);
                addMessage(response, 'bot');
            }, 1000);
        } else {
            console.error('Server error:', data);
        }
    })
    .catch(err => {
        console.error('Error sending message:', err);
        // Still show AI response as fallback
        setTimeout(() => {
            const response = getAIResponse(message);
            addMessage(response, 'bot');
        }, 1000);
    });
}

function sendQuickMessage(message) {
    document.getElementById('chatInput').value = message;
    sendMessage(new Event('submit'));
}

function addMessage(text, type) {
    const messagesContainer = document.getElementById('chatMessages');
    const messageDiv = document.createElement('div');
    
    if (type === 'user') {
        messageDiv.className = 'flex items-start space-x-2 justify-end user-message';
        messageDiv.innerHTML = `
            <div class="bg-halal-green text-white p-3 rounded-lg rounded-tr-none max-w-[80%]">
                <p class="text-sm">${text}</p>
            </div>
            <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="bi bi-person-fill text-gray-600 text-sm"></i>
            </div>
        `;
    } else {
        messageDiv.className = 'flex items-start space-x-2 bot-message';
        messageDiv.innerHTML = `
            <div class="w-8 h-8 bg-halal-green rounded-full flex items-center justify-center flex-shrink-0">
                <i class="bi bi-headset text-white text-sm"></i>
            </div>
            <div class="bg-white p-3 rounded-lg rounded-tl-none shadow-sm max-w-[80%]">
                <p class="text-sm text-gray-700">${text}</p>
            </div>
        `;
    }
    
    messagesContainer.appendChild(messageDiv);
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
}

function getAIResponse(message) {
    const lowerMessage = message.toLowerCase();
    
    if (lowerMessage.includes('track') || lowerMessage.includes('order')) {
        return "To track your order, please provide your order number. You can also check your order status in 'My Orders' section after logging in. 📦";
    } else if (lowerMessage.includes('delivery') || lowerMessage.includes('area')) {
        return "We deliver across Bangladesh! 🇧🇩 Dhaka: Same day delivery. Other areas: 1-3 business days. Free delivery on orders over ৳500!";
    } else if (lowerMessage.includes('payment') || lowerMessage.includes('pay')) {
        return "We accept multiple payment methods: 💳 bKash, Nagad, Rocket, Credit/Debit Cards (Visa, Mastercard), and Cash on Delivery (COD).";
    } else if (lowerMessage.includes('halal') || lowerMessage.includes('quality')) {
        return "All our products are 100% Halal certified! ✅ We source from trusted suppliers and maintain strict quality standards.";
    } else if (lowerMessage.includes('return') || lowerMessage.includes('refund')) {
        return "We have a hassle-free return policy! If you're not satisfied, contact us within 24 hours of delivery for a refund or replacement. 🔄";
    } else if (lowerMessage.includes('price') || lowerMessage.includes('cost')) {
        return "Our prices are competitive and transparent. Check our Deals section for special discounts! 💰 Free delivery on orders over ৳500.";
    } else if (lowerMessage.includes('hello') || lowerMessage.includes('hi') || lowerMessage.includes('salam')) {
        return "Wa Alaikum Assalam! 👋 Welcome to Halal Food Store. How can I assist you today?";
    } else {
        return "Thank you for your message! Our support team has received it and will respond shortly. For urgent queries, call +880 1700-000000. 😊";
    }
}
</script>
