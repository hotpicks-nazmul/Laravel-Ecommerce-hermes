<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Chat;
use App\Models\ChatMessage;
use App\Models\PredefinedMessage;
use App\Models\Setting;
use App\Events\ChatMessageSent;
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
        
        // Apply search
        if ($request->search) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            });
        }
        
        $conversations = $query->latest()->paginate(20);
        
        // Add computed fields
        $conversations->getCollection()->transform(function($chat) {
            $chat->unread_count = $chat->messages()
                ->where('sender_type', 'user')
                ->where('is_read', false)
                ->count();
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
            'open' => Chat::where('status', 'open')->count(),
            'pending' => Chat::where('status', 'pending')->count(),
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
        
        // Mark messages as read
        $conversation->messages()
            ->where('sender_type', 'user')
            ->where('is_read', false)
            ->update(['is_read' => true]);

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

        // Update chat status to open if it was closed
        if ($chat->status === 'closed') {
            $chat->status = 'open';
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
