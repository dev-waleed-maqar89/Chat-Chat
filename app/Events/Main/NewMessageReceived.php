<?php

namespace App\Events\Main;

use App\Http\Resources\Main\ConversationResource;
use App\Http\Resources\Main\MessageResource;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewMessageReceived implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public MessageResource $message, public ConversationResource $conversation)
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
        $id = $this->message->conversation_id;
        return [
            new PrivateChannel('NewMessageInChat.' . $id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'ReceiveNewMessage';
    }
}