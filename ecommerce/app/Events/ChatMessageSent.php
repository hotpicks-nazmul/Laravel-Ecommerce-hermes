<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\ChatMessage;

class ChatMessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $conversation;

    /**
     * Create a new event instance.
     */
    public function __construct(ChatMessage $message)
    {
        $this->message = $message;
        $this->conversation = $message->conversation;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn()
    {
        // Use a public channel for simplicity - in production use PrivateChannel for security
        return new Channel('chat.' . $this->message->chat_id);
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'message.sent';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->message->id,
            'chat_id' => $this->message->chat_id,
            'sender_type' => $this->message->sender_type,
            'sender_id' => $this->message->sender_id,
            'message' => $this->message->message,
            'attachments' => $this->message->attachments,
            'created_at' => $this->message->created_at->toISOString(),
            'conversation' => [
                'id' => $this->conversation->id,
                'status' => $this->conversation->status,
                'user_id' => $this->conversation->user_id,
            ]
        ];
    }
}
