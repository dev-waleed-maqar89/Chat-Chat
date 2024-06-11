<?php

namespace App\Http\Resources\Main;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConversationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'last_message' => $this->last_message,
            'accepted' => $this->accepted,
            'messages' => MessageResource::collection($this->messages),
            'initiator' => [
                'id' => $this->initiator->id,
                'name' => $this->initiator->name,
                'profile_image' => $this->initiator->profile_image
            ],
            'receiver' => [
                'id' => $this->receiver->id,
                'name' => $this->receiver->name,
                'profile_image' => $this->receiver->profile_image
            ]
        ];
    }
}