<?php

use App\Models\Conversation;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('NewMessageInChat.{id}', function ($user, $id) {
    $ocnversation = Conversation::find($id);
    if ($user->id == $ocnversation->initiator_id || $user->id == $ocnversation->receiver_id) {
        return true;
    }
    return false;
});
Broadcast::channel('NewMessageForUser.{id}', function ($user, $id) {
    return $id == $user->id;
});
Broadcast::channel('NewMessageFromUser.{id}', function ($user, $id) {
    return $id == $user->id;
});
Broadcast::channel('ChatAccepted.{id}', function ($user, $id) {
    $ocnversation = Conversation::find($id);
    if ($user->id == $ocnversation->initiator_id || $user->id == $ocnversation->receiver_id) {
        return true;
    }
    return false;
});
Broadcast::channel('MarkAsRead.{id}', function ($user, $id) {
    $ocnversation = Conversation::find($id);
    if ($user->id == $ocnversation->initiator_id || $user->id == $ocnversation->receiver_id) {
        return true;
    }
    return false;
});
Broadcast::channel('NewChatForUser.{id}', function ($user, $id) {
    return $id == $user->id;
});
Broadcast::channel('NewChatFromUser.{id}', function ($user, $id) {
    return $id == $user->id;
});

Broadcast::channel('onlineUser', function ($user) {
    return $user;
});