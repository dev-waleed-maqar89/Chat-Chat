<?php

namespace App\Http\Controllers\Main;

use App\Events\Main\Chat\ChatAccepted;
use App\Events\Main\Chat\ChatUpdated;
use App\Events\Main\Chat\MarkAsRead;
use App\Events\Main\Chat\NewChatCreated;
use App\Events\Main\NewMessageReceived;
use App\Http\Controllers\Controller;
use App\Http\Resources\Main\ConversationResource;
use App\Http\Resources\Main\MessageResource;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        $chats = Auth::user()->conversations()->orderBy('last_message', 'desc')->get();

        return response()->json(['chats' => ConversationResource::collection($chats)]);
    }

    public function search(Request $request)
    {
        if ($request->key_word) {
            $chats = Conversation::with('messages')
                ->with('initiator')
                ->with('receiver')
                ->where(function ($query) {
                    $query->where('initiator_id', Auth::user()->id)
                        ->orWhere('receiver_id', Auth::user()->id);
                })
                ->whereHas('messages', function ($query) use ($request) {
                    $query->where('message', 'like', '%' . $request->key_word . '%');
                })
                ->orderBy('last_message', 'desc')->get();
        } else {
            $chats = Auth::user()->conversations()->orderBy('last_message', 'desc')->get();
        }
        return response()->json(['chats' => ConversationResource::collection($chats)]);
    }
    public function show($id)
    {
        $chat = Conversation::with('messages')->find($id);
        $messages = MessageResource::collection($chat->messages);
        if ($chat->initiator_id == Auth::user()->id) {
            $partner = $chat->receiver();
        } else {
            $partner = $chat->initiator();
        }
        $profileImage = $partner->profile_image;
        $chat = new ConversationResource($chat);
        return response()->json(compact('chat', 'messages', 'partner', 'profileImage'));
    }


    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|max:255'
        ]);
        $message = Message::create([
            'user_id' => Auth::user()->id,
            'conversation_id' => $request->conversationId,
            'message' => $request->message
        ]);
        $conversation = Conversation::find($request->conversationId);
        $conversation->last_message = now()->toDateTimeString();
        $conversation->save();
        $conversation->load('messages', 'initiator', 'receiver');
        broadcast(new NewMessageReceived(new MessageResource($message), new ConversationResource($conversation)))->toOthers();
        broadcast(new ChatUpdated(new ConversationResource($conversation)));
        return response()->json(['message' => new MessageResource($message)]);
    }

    public function accept($id)
    {

        $conversation = Conversation::find($id);
        if ($conversation->initiator_id != Auth::id()) {
            $conversation->accepted = 1;
            $conversation->last_message = now()->toDateTimeString();
            $conversation->save();
        }
        $conversation->load('messages', 'initiator', 'receiver');
        broadcast(new ChatAccepted(new ConversationResource($conversation)))->toOthers();
        broadcast(new ChatUpdated(new ConversationResource($conversation)));
    }

    public function markAsRead($id)
    {
        $conversation = Conversation::find($id);
        $messages = $conversation->messages()->where('is_read', 0)->get();
        foreach ($messages as $message) {
            $message->is_read = 1;
            $message->save();
            broadcast(new MarkAsRead($id))->toOthers();
        }
    }
}