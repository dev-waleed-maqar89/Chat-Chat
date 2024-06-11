<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'initiator_id',
        'receiver_id',
        'last_message',
        'accepted'
    ];

    protected $appends = [
        'last_message'
    ];
    public function initiator()
    {
        return $this->belongsTo(User::class, 'initiator_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
    public function messages()
    {

        return $this->hasMany(Message::class)->with('sender');
    }

    public function getLastMessageAttribute()
    {
        $lastMessage = $this->messages()->orderBy('created_at', 'desc')->first();
        return $lastMessage ? $lastMessage->message : 'No Conversation yet';
    }
}