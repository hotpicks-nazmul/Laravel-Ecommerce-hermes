<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Chat;
use App\Models\ChatMessage;
use App\Models\PredefinedMessage;
use App\Models\Setting;
use App\Events\ChatMessageSent;
use App\Events\UserTyping;
use App\Events\UserStatusChanged;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ChatController extends Controller
{
    /**
     * Display chat management page.
     */
    public function index()
    {
        $conversations = Chat::with('user')
            ->latest()
            ->take(20)
            ->get();

        $aiSettings = [
            'enabled' => Setting::where('key', 'ai_chatbot_enabled')->value('value') === '1',
            'welcome_message' => Setting::where('key', 'ai_chatbot_welcome_message')->value('value') ?? 'Hello! How can I help you today?',
            'openai_key' => Setting::where('key', 'openai_api_key')->value('value') ?? '',
        ];

        return view('admin.chat.index', compact('conversations', 'aiSettings'));
    }

    /**
     * Get all conversations with filters (AJAX).
     */
    public function conversations(Request $request)
    {
        $query = Chat::with('user');
        
        // Apply status filter
        if ($request->filter && $request->filter !== 'all') {
            $query->where('status', $request->filter);
        }
        
        // Apply search - search in both user table and guest fields
        if ($request->search) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                // Search in user table (for logged-in users)
                $q->whereHas('user', function($uq) use ($searchTerm) {
                    $uq->where('name', 'like', "%{$searchTerm}%")
                      ->orWhere('email', 'like', "%{$searchTerm}%");
                })
                // OR search in guest fields
                ->orWhere('guest_name', 'like', "%{$searchTerm}%")
                ->orWhere('guest_phone', 'like', "%{$searchTerm}%");
            });
        }
        
        $conversations = $query->latest()->paginate(20);
        
        // Add computed fields
        $conversations->getCollection()->transform(function($chat) {
            // Find the last admin message (replied message)
            $lastAdminMessage = $chat->messages()
                ->where('sender_type', 'admin')
                ->orderBy('created_at', 'desc')
                ->first();
            
            if ($lastAdminMessage) {
                // Count only unread user messages that came after the last admin message
                $chat->unread_count = $chat->messages()
                    ->where('sender_type', 'user')
                    ->where('is_read', false)
                    ->where('created_at', '>', $lastAdminMessage->created_at)
                    ->count();
            } else {
                // If no admin message, count all unread user messages
                $chat->unread_count = $chat->messages()
                    ->where('sender_type', 'user')
                    ->where('is_read', false)
                    ->count();
            }
            $chat->last_message = $chat->messages()->latest()->first();
            return $chat;
        });
        
        // Calculate stats based on filter
        $statsQuery = Chat::query();
        if ($request->filter && $request->filter !== 'all') {
            $statsQuery->where('status', $request->filter);
        }
        $filteredCount = $statsQuery->count();
        
        $stats = [
            'total' => Chat::count(),
            // Include all users (including guests) in status counts
            'new' => Chat::where('status', 'new')->count(),
            'pending' => Chat::where('status', 'pending')->count(),
            'replied' => Chat::where('status', 'replied')->count(),
            'closed' => Chat::where('status', 'closed')->count(),
            'filtered_total' => $filteredCount,
        ];
        
        return response()->json(array_merge(['data' => $conversations], $stats));
    }

    /**
     * Get single conversation with messages (AJAX).
     */
    public function conversation($id)
    {
        $conversation = Chat::with(['messages', 'user'])->findOrFail($id);
        
        // Don't mark messages as read automatically - let admin decide when to mark as read
        
        return response()->json($conversation);
    }

    /**
     * Send a message to a conversation.
     */
    public function send(Request $request)
    {
        $request->validate([
            'conversation_id' => 'required|exists:chats,id',
            'message' => 'required|string|max:1000',
        ]);

        $chat = Chat::findOrFail($request->conversation_id);
        
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

        $message = $chat->messages()->create([
            'sender_type' => 'admin',
            'sender_id' => auth()->id(),
            'message' => $request->message,
            'attachments' => $attachments,
            'is_read' => true,
        ]);

        // Update chat status when admin replies
        if ($chat->status === 'new') {
            $chat->status = 'pending'; // New conversation, needs admin attention
            $chat->save();
        } elseif ($chat->status === 'pending') {
            $chat->status = 'replied'; // Admin replied to the customer
            $chat->save();
        } elseif ($chat->status === 'closed') {
            $chat->status = 'replied'; // Reopen closed conversation
            $chat->save();
        }

        // Broadcast message for real-time update
        try {
            broadcast(new ChatMessageSent($message))->toOthers();
        } catch (\Exception $e) {
            // Broadcasting failed
        }

        return response()->json([
            'success' => true,
            'message' => 'Message sent successfully',
            'data' => $message
        ]);
    }

    /**
     * Handle typing indicator.
     */
    public function typing(Request $request)
    {
        $conversationId = $request->conversation_id;
        $isTyping = filter_var($request->is_typing, FILTER_VALIDATE_BOOLEAN);
        
        $conversation = Chat::find($conversationId);
        if ($conversation) {
            // Set typing status with 5 second expiration
            $conversation->update([
                'admin_is_typing' => $isTyping,
                'typing_expires_at' => $isTyping ? now()->addSeconds(5) : null,
            ]);
        }
        
        return response()->json(['success' => true]);
    }

    /**
     * Check typing status (for polling).
     */
    public function checkTyping(Request $request)
    {
        $conversationId = $request->conversation_id;
        
        $conversation = Chat::find($conversationId);
        
        if (!$conversation) {
            return response()->json([
                'user_is_typing' => false,
            ]);
        }
        
        // Check if typing has expired
        $userTyping = $conversation->user_is_typing;
        
        if ($conversation->typing_expires_at && now()->greaterThan($conversation->typing_expires_at)) {
            // Typing has expired, reset it
            if ($conversation->user_is_typing) {
                $conversation->update(['user_is_typing' => false, 'typing_expires_at' => null]);
                $userTyping = false;
            }
        }
        
        return response()->json([
            'user_is_typing' => $userTyping,
        ]);
    }

    /**
     * Update chat status.
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:open,pending,closed',
        ]);

        $chat = Chat::findOrFail($id);
        $chat->status = $request->status;
        $chat->save();

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully'
        ]);
    }

    /**
     * Close a conversation.
     */
    public function close($id)
    {
        $chat = Chat::findOrFail($id);
        $chat->status = 'closed';
        $chat->save();

        return response()->json([
            'success' => true,
            'message' => 'Chat closed successfully'
        ]);
    }

    /**
     * Mark conversation as unread.
     */
    public function markAsUnread($id)
    {
        $chat = Chat::findOrFail($id);
        
        // Find the last admin message (replied message)
        $lastAdminMessage = $chat->messages()
            ->where('sender_type', 'admin')
            ->orderBy('created_at', 'desc')
            ->first();
        
        if ($lastAdminMessage) {
            // Mark only messages that came after the last admin message as unread
            $chat->messages()
                ->where('created_at', '>', $lastAdminMessage->created_at)
                ->update(['is_read' => false]);
        } else {
            // If no admin message, mark all messages as unread
            $chat->messages()->update(['is_read' => false]);
        }
        
        // Get the unread count (messages after last admin reply)
        if ($lastAdminMessage) {
            $unreadCount = $chat->messages()
                ->where('created_at', '>', $lastAdminMessage->created_at)
                ->where('is_read', false)
                ->count();
        } else {
            $unreadCount = $chat->messages()->where('is_read', false)->count();
        }

        return response()->json([
            'success' => true,
            'unread_count' => $unreadCount,
            'message' => 'Marked as unread'
        ]);
    }

    /**
     * Mark conversation as read.
     */
    public function markAsRead($id)
    {
        $chat = Chat::findOrFail($id);
        // Mark all messages as read
        $chat->messages()->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Marked as read'
        ]);
    }

    /**
     * AI Settings management.
     */
    public function aiSettings(Request $request)
    {
        Setting::updateOrCreate(['key' => 'ai_chatbot_enabled'], ['value' => $request->has('ai_enabled') ? '1' : '0']);
        Setting::updateOrCreate(['key' => 'ai_chatbot_welcome_message'], ['value' => $request->ai_welcome_message ?? '']);
        Setting::updateOrCreate(['key' => 'openai_api_key'], ['value' => $request->openai_api_key ?? '']);

        return back()->with('success', 'AI Chatbot settings updated successfully.');
    }

    /**
     * Chat Widget Settings management.
     */
    public function widgetSettings(Request $request)
    {
        // Welcome messages
        Setting::updateOrCreate(['key' => 'chat_welcome_message'], ['value' => $request->chat_welcome_message ?? 'Hello! How can I help you today?']);
        Setting::updateOrCreate(['key' => 'chat_welcome_subtitle'], ['value' => $request->chat_welcome_subtitle ?? 'Our team typically replies within minutes']);
        
        // Auto-reply messages
        Setting::updateOrCreate(['key' => 'chat_reply_greeting'], ['value' => $request->chat_reply_greeting ?? '']);
        Setting::updateOrCreate(['key' => 'chat_reply_delivery'], ['value' => $request->chat_reply_delivery ?? '']);
        Setting::updateOrCreate(['key' => 'chat_reply_payment'], ['value' => $request->chat_reply_payment ?? '']);
        Setting::updateOrCreate(['key' => 'chat_reply_track_order'], ['value' => $request->chat_reply_track_order ?? '']);
        Setting::updateOrCreate(['key' => 'chat_reply_return'], ['value' => $request->chat_reply_return ?? '']);
        Setting::updateOrCreate(['key' => 'chat_reply_halal'], ['value' => $request->chat_reply_halal ?? '']);
        Setting::updateOrCreate(['key' => 'chat_reply_price'], ['value' => $request->chat_reply_price ?? '']);

        return back()->with('success', 'Chat Widget settings updated successfully.');
    }

    /**
     * Get online users list.
     */
    public function getOnlineUsers()
    {
        // This would typically use a cache or database to track online users
        // For now, return empty array - real-time tracking handled by Pusher
        return response()->json([
            'users' => [],
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Delete a conversation.
     */
    public function destroy($id)
    {
        $chat = Chat::findOrFail($id);
        $chat->messages()->delete();
        $chat->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Conversation deleted successfully.'
            ]);
        }

        return redirect()->route('admin.chat.index')
            ->with('success', 'Conversation deleted successfully.');
    }

    // =====================================================
    // Predefined Messages CRUD
    // =====================================================

    /**
     * Display predefined messages list.
     */
    public function predefinedMessages(Request $request)
    {
        $query = PredefinedMessage::query();

        // Filter by category
        if ($request->category) {
            $query->where('category', $request->category);
        }

        // Filter by status
        if ($request->status === 'active') {
            $query->where('is_active', true);
        } elseif ($request->status === 'inactive') {
            $query->where('is_active', false);
        }

        // Search
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                  ->orWhere('message', 'like', "%{$request->search}%");
            });
        }

        $messages = $query->ordered()->paginate(20);
        $categories = PredefinedMessage::getCategories();

        return view('admin.chat.predefined.index', compact('messages', 'categories'));
    }

    /**
     * Store a new predefined message.
     */
    public function storePredefinedMessage(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
            'category' => 'nullable|string|max:100',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $message = PredefinedMessage::create([
            'title' => $request->title,
            'message' => $request->message,
            'category' => $request->category,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->has('is_active'),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Predefined message created successfully.',
                'data' => $message
            ]);
        }

        return redirect()->route('admin.chat.predefined.index')
            ->with('success', 'Predefined message created successfully.');
    }

    /**
     * Show edit form for predefined message.
     */
    public function editPredefinedMessage($id)
    {
        $message = PredefinedMessage::findOrFail($id);
        return response()->json($message);
    }

    /**
     * Update a predefined message.
     */
    public function updatePredefinedMessage(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
            'category' => 'nullable|string|max:100',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $message = PredefinedMessage::findOrFail($id);
        $message->update([
            'title' => $request->title,
            'message' => $request->message,
            'category' => $request->category,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->has('is_active'),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Predefined message updated successfully.',
                'data' => $message
            ]);
        }

        return redirect()->route('admin.chat.predefined.index')
            ->with('success', 'Predefined message updated successfully.');
    }

    /**
     * Delete a predefined message.
     */
    public function destroyPredefinedMessage($id)
    {
        $message = PredefinedMessage::findOrFail($id);
        $message->delete();

        return redirect()->route('admin.chat.predefined.index')
            ->with('success', 'Predefined message deleted successfully.');
    }

    /**
     * Toggle predefined message status.
     */
    public function togglePredefinedMessage($id)
    {
        $message = PredefinedMessage::findOrFail($id);
        $message->is_active = !$message->is_active;
        $message->save();

        return response()->json([
            'success' => true,
            'is_active' => $message->is_active,
            'message' => $message->is_active ? 'Message activated successfully.' : 'Message deactivated successfully.'
        ]);
    }

    /**
     * Reorder predefined messages.
     */
    public function reorderPredefinedMessages(Request $request)
    {
        $request->validate([
            'order' => 'required|array',
        ]);

        foreach ($request->order as $index => $id) {
            PredefinedMessage::where('id', $id)->update(['sort_order' => $index]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Messages reordered successfully.'
        ]);
    }

    /**
     * Get predefined messages for quick reply (AJAX).
     */
    public function getPredefinedMessages()
    {
        $messages = PredefinedMessage::active()->ordered()->get();
        return response()->json($messages);
    }
}
