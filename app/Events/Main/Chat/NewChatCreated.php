<?php

namespace App\Events\Main\Chat;

use App\Http\Resources\Main\ConversationResource;
use App\Models\Conversation;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewChatCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public ConversationResource $conversation)
    {
        //
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        $receiverId = $this->conversation->receiver_id;
        $initiatorId = $this->conversation->initiator_id;
        return [
            new PrivateChannel('NewChatForUser.' . $receiverId),
            new PrivateChannel('NewChatFromUser.' . $initiatorId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'NewChat';
    }
}