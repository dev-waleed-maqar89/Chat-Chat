<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'image',
    ];

    public function conversations()
    {
        return Conversation::where('initiator_id', $this->id)->orWhere('receiver_id', $this->id)->with('initiator')->with('receiver');
    }

    public function customChat($userId)
    {
        return Conversation::with('messages')->where(function ($query) use ($userId) {
            $query->where('initiator_id', $this->id)->where('receiver_id', $userId);
        })->orWhere(function ($query) use ($userId) {
            $query->where('initiator_id', $userId)->where('receiver_id', $this->id);
        })->first();
    }

    public function getProfileImageAttribute()
    {
        $path = $this->image ? 'storage/' . $this->image : 'images/users/no-profile-image.jpg';
        return asset($path);
    }

    public function getPartnersAttribute()
    {
        $partners = [];
        foreach ($this->conversations()->get() as $conversation) {
            if ($conversation->initiator_id == $this->id) {
                $partners[] = $conversation->receiver_id;
            } else {
                $partners[] = $conversation->initiator_id;
            }
        }
        return $partners;
    }

    protected $appends = ['profile_image', 'partners'];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
}