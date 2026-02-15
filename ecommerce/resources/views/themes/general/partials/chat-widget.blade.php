<!-- Chat Widget -->
<div class="fixed bottom-6 right-6 z-40">
    <!-- Chat Button -->
    <button id="chatToggle" onclick="toggleChat()" class="w-14 h-14 bg-halal-green text-white rounded-full shadow-lg hover:bg-halal-dark transition-all duration-300 flex items-center justify-center group">
        <i class="bi bi-chat-dots-fill text-xl group-hover:scale-110 transition-transform"></i>
        <span class="absolute -top-1 -right-1 w-4 h-4 bg-halal-gold rounded-full animate-ping"></span>
        <span class="absolute -top-1 -right-1 w-4 h-4 bg-halal-gold rounded-full"></span>
    </button>
    
    <!-- Chat Window -->
    <div id="chatWindow" class="hidden absolute bottom-16 right-0 w-80 md:w-96 bg-white rounded-2xl shadow-2xl overflow-hidden">
        <!-- Header -->
        <div class="gradient-halal p-4 text-white">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                        <i class="bi bi-robot text-xl"></i>
                    </div>
                    <div>
                        <h4 class="font-medium">Halal Food Assistant</h4>
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
                    <i class="bi bi-robot text-white text-sm"></i>
                </div>
                <div class="bg-white p-3 rounded-lg rounded-tl-none shadow-sm max-w-[80%]">
                    <p class="text-sm text-gray-700">Assalamu Alaikum! 👋 Welcome to Halal Food Store. How can I help you today?</p>
                </div>
            </div>
            
            <!-- Quick Options -->
            <div class="flex flex-wrap gap-2 ml-10">
                <button onclick="sendQuickMessage('Track my order')" class="text-xs bg-white border border-halal-green text-halal-green px-3 py-1 rounded-full hover:bg-green-50 transition-colors">
                    Track Order
                </button>
                <button onclick="sendQuickMessage('What are your delivery areas?')" class="text-xs bg-white border border-halal-green text-halal-green px-3 py-1 rounded-full hover:bg-green-50 transition-colors">
                    Delivery Areas
                </button>
                <button onclick="sendQuickMessage('I need help with payment')" class="text-xs bg-white border border-halal-green text-halal-green px-3 py-1 rounded-full hover:bg-green-50 transition-colors">
                    Payment Help
                </button>
            </div>
        </div>
        
        <!-- Input -->
        <div class="p-4 border-t bg-white">
            <form id="chatForm" onsubmit="sendMessage(event)" class="flex items-center space-x-2">
                <input type="text" id="chatInput" placeholder="Type your message..." 
                    class="flex-1 px-4 py-2 border border-gray-200 rounded-full focus:border-halal-green focus:outline-none text-sm">
                <button type="submit" class="w-10 h-10 bg-halal-green text-white rounded-full flex items-center justify-center hover:bg-halal-dark transition-colors">
                    <i class="bi bi-send-fill"></i>
                </button>
            </form>
        </div>
    </div>
</div>

<script>
let chatOpen = false;

function toggleChat() {
    chatOpen = !chatOpen;
    const chatWindow = document.getElementById('chatWindow');
    const chatToggle = document.getElementById('chatToggle');
    
    if (chatOpen) {
        chatWindow.classList.remove('hidden');
        chatToggle.innerHTML = '<i class="bi bi-x-lg text-xl"></i>';
    } else {
        chatWindow.classList.add('hidden');
        chatToggle.innerHTML = '<i class="bi bi-chat-dots-fill text-xl group-hover:scale-110 transition-transform"></i>';
    }
}

function sendMessage(event) {
    event.preventDefault();
    const input = document.getElementById('chatInput');
    const message = input.value.trim();
    
    if (!message) return;
    
    // Add user message
    addMessage(message, 'user');
    input.value = '';
    
    // Simulate AI response
    setTimeout(() => {
        const response = getAIResponse(message);
        addMessage(response, 'bot');
    }, 1000);
}

function sendQuickMessage(message) {
    document.getElementById('chatInput').value = message;
    sendMessage(new Event('submit'));
}

function addMessage(text, type) {
    const messagesContainer = document.getElementById('chatMessages');
    const messageDiv = document.createElement('div');
    
    if (type === 'user') {
        messageDiv.className = 'flex items-start space-x-2 justify-end';
        messageDiv.innerHTML = `
            <div class="bg-halal-green text-white p-3 rounded-lg rounded-tr-none max-w-[80%]">
                <p class="text-sm">${text}</p>
            </div>
            <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center flex-shrink-0">
                <i class="bi bi-person-fill text-gray-600 text-sm"></i>
            </div>
        `;
    } else {
        messageDiv.className = 'flex items-start space-x-2';
        messageDiv.innerHTML = `
            <div class="w-8 h-8 bg-halal-green rounded-full flex items-center justify-center flex-shrink-0">
                <i class="bi bi-robot text-white text-sm"></i>
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
        return "Thank you for your message! For specific queries, you can also contact us at +880 1700-000000 or email info@halalfoodstore.com. Is there anything else I can help with? 😊";
    }
}
</script>
