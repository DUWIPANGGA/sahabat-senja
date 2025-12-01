<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatRoom extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'type'];

    /**
     * Relasi ke participants
     */
    public function participants()
    {
        return $this->hasMany(ChatParticipant::class);
    }

    /**
     * Relasi ke users melalui participants
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'chat_participants')
                    ->withTimestamps();
    }

    /**
     * Relasi ke messages
     */
    public function messages()
    {
        return $this->hasMany(ChatMessage::class, 'chat_room_id');
    }

    /**
     * Cari atau buat private chat antara dua user
     */
    public static function findOrCreatePrivateChat($user1, $user2)
    {
        $room = self::whereHas('participants', function($q) use ($user1) {
            $q->where('user_id', $user1);
        })->whereHas('participants', function($q) use ($user2) {
            $q->where('user_id', $user2);
        })->where('type', 'private')->first();

        if (!$room) {
            $room = self::create(['type' => 'private']);
            $room->users()->attach([$user1, $user2]);
        }

        return $room;
    }

    /**
     * Get last message
     */
    public function getLastMessageAttribute()
    {
        return $this->messages()->latest()->first();
    }

    /**
     * Get unread count for user
     */
    public function getUnreadCountAttribute()
    {
        $userId = auth()->id();
        return $this->messages()
            ->where('receiver_id', $userId)
            ->where('is_read', false)
            ->count();
    }
}