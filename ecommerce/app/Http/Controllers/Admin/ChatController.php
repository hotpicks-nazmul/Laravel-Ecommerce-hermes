<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Chat;
use App\Models\Setting;

class ChatController extends Controller
{
    public function index()
    {
        $conversations = Chat::with('user')
            ->where('status', 'open')
            ->latest()
            ->get();

        $messages = null;
        $currentConversation = null;
        $aiSettings = [
            'enabled' => Setting::where('key', 'ai_chatbot_enabled')->value('value') === '1',
            'welcome_message' => Setting::where('key', 'ai_chatbot_welcome_message')->value('value') ?? 'Hello! How can I help you today?',
        ];

        return view('admin.chat.index', compact('conversations', 'messages', 'currentConversation', 'aiSettings'));
    }

    public function conversations()
    {
        $conversations = Chat::with('user')
            ->latest()
            ->paginate(20);

        return response()->json($conversations);
    }

    public function conversation($id)
    {
        $conversation = Chat::with(['messages', 'user'])->findOrFail($id);
        
        // Mark messages as read
        $conversation->messages()->where('sender_type', 'user')->update(['is_read' => true]);

        return response()->json($conversation);
    }

    public function send(Request $request)
    {
        $request->validate([
            'conversation_id' => 'required|exists:chats,id',
            'message' => 'required|string|max:1000',
        ]);

        $chat = Chat::findOrFail($request->conversation_id);
        
        $message = $chat->messages()->create([
            'sender_type' => 'admin',
            'sender_id' => auth()->id(),
            'message' => $request->message,
        ]);

        return back()->with('success', 'Message sent successfully.');
    }

    public function aiSettings(Request $request)
    {
        Setting::updateOrCreate(['key' => 'ai_chatbot_enabled'], ['value' => $request->has('ai_enabled') ? '1' : '0']);
        Setting::updateOrCreate(['key' => 'ai_chatbot_welcome_message'], ['value' => $request->ai_welcome_message ?? '']);
        Setting::updateOrCreate(['key' => 'openai_api_key'], ['value' => $request->openai_api_key ?? '']);

        return back()->with('success', 'AI Chatbot settings updated successfully.');
    }
}
