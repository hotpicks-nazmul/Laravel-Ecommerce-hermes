<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Chat;
use App\Models\ChatMessage;
use App\Models\Setting;
use App\Events\ChatMessageSent;
use App\Events\UserStatusChanged;
use Illuminate\Support\Facades\Http;

class ChatController extends Controller
{
    /**
     * Display chat interface.
     */
    public function index()
    {
        if (auth()->check()) {
            $conversations = Chat::where('user_id', auth()->id())
                ->latest()
                ->get();
        } else {
            $conversations = collect();
        }

        return view('themes.general.chat.index', compact('conversations'));
    }

    /**
     * Send a chat message.
     */
    public function send(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        // Get or create conversation
        if ($request->conversation_id) {
            // Verify conversation exists, if not create new one
            $conversation = Chat::find($request->conversation_id);
            if (!$conversation) {
                $conversation = Chat::create([
                    'user_id' => auth()->id() ?? null,
                    'session_id' => session()->getId(),
                    'status' => 'open',
                ]);
            }
        } else {
            $conversation = Chat::create([
                'user_id' => auth()->id() ?? null,
                'session_id' => session()->getId(),
                'status' => 'open',
            ]);
        }

        // Handle attachments
        $attachments = null;
        if ($request->hasFile('attachment')) {
            $attachment = $request->file('attachment');
            $path = $attachment->store('chat-attachments', 'public');
            $attachments = json_encode([
                'filename' => $attachment->getClientOriginalName(),
                'path' => $path,
                'type' => $attachment->getMimeType(),
            ]);
        }

        // Store user message
        $userMessage = $conversation->messages()->create([
            'sender_type' => 'user',
            'sender_id' => auth()->id() ?? null,
            'message' => $request->message,
            'attachments' => $attachments,
        ]);

        // Broadcast message for live chat
        try {
            broadcast(new ChatMessageSent($userMessage))->toOthers();
        } catch (\Exception $e) {
            // Broadcasting failed, continue without broadcasting
        }

        return response()->json([
            'success' => true,
            'conversation_id' => $conversation->id,
            'message' => $userMessage,
        ]);
    }

    /**
     * Get chat messages.
     */
    public function messages(Request $request)
    {
        // Allow both with and without conversation_id for flexibility
        if (!$request->conversation_id) {
            return response()->json([]);
        }
        
        // Verify conversation exists
        $chat = Chat::find($request->conversation_id);
        if (!$chat) {
            return response()->json([]);
        }

        $messages = ChatMessage::where('chat_id', $request->conversation_id)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($messages);
    }

    /**
     * Update user online status.
     */
    public function updateStatus(Request $request)
    {
        $request->validate([
            'is_online' => 'required|boolean',
        ]);

        $userId = auth()->id() ?? $request->session_id;
        $userName = auth()->user()->name ?? 'Guest';

        try {
            broadcast(new UserStatusChanged($userId, $request->is_online, $userName))->toOthers();
        } catch (\Exception $e) {
            // Broadcasting failed
        }

        return response()->json(['success' => true]);
    }

    /**
     * AI Chatbot response.
     */
    public function aiChat(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $apiKey = Setting::where('key', 'openai_api_key')->value('value');

        if (!$apiKey) {
            // Return a default response if OpenAI is not configured
            return response()->json([
                'success' => true,
                'reply' => $this->getLocalResponse($request->message),
            ]);
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a helpful customer support assistant for an e-commerce website. Be friendly and helpful.'],
                    ['role' => 'user', 'content' => $request->message],
                ],
                'max_tokens' => 500,
            ]);

            $data = $response->json();

            return response()->json([
                'success' => true,
                'reply' => $data['choices'][0]['message']['content'] ?? 'I apologize, I could not process your request. Please try again.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => true,
                'reply' => $this->getLocalResponse($request->message),
            ]);
        }
    }

    /**
     * Live chat page.
     */
    public function live()
    {
        return view('themes.general.chat.live');
    }

    /**
     * Get local response for common queries.
     */
    private function getLocalResponse($message)
    {
        $message = strtolower($message);
        
        if (strpos($message, 'track') !== false || strpos($message, 'order') !== false) {
            return 'To track your order, please log in to your account and visit the "My Orders" section. You can also contact our support with your order number for assistance.';
        }
        
        if (strpos($message, 'delivery') !== false || strpos($message, 'shipping') !== false) {
            return 'We offer free delivery on orders over ৳500! Standard delivery takes 1-3 business days within Dhaka and 3-5 days outside Dhaka. Express delivery is available for urgent orders.';
        }
        
        if (strpos($message, 'payment') !== false || strpos($message, 'pay') !== false) {
            return 'We accept multiple payment methods: bKash, Nagad, Rocket, Credit/Debit Cards, and Cash on Delivery. All online payments are secure and encrypted.';
        }
        
        if (strpos($message, 'return') !== false || strpos($message, 'refund') !== false) {
            return 'We have a 7-day return policy for most products. If you\'re not satisfied with your purchase, please contact our support team within 7 days of delivery.';
        }
        
        if (strpos($message, 'halal') !== false) {
            return 'All our meat and food products are 100% Halal certified. We source from trusted suppliers who follow strict halal guidelines.';
        }
        
        if (strpos($message, 'deal') !== false || strpos($message, 'discount') !== false || strpos($message, 'offer') !== false) {
            return 'Check our "Deals" section for the latest offers! We have weekly specials and seasonal discounts. Subscribe to our newsletter to stay updated.';
        }
        
        if (strpos($message, 'hello') !== false || strpos($message, 'hi') !== false || strpos($message, 'assalam') !== false) {
            return 'Wa Alaikum Assalam! Welcome to Halal Food Store. How can I help you today?';
        }
        
        return 'Thank you for your message! For immediate assistance, please call our helpline at +880 1700-000000 or email us at info@halalfoodstore.com. Our team is available 24/7 to help you.';
    }
}
