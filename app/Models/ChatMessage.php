<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    use HasFactory;

    protected $table = 'chat_messages';
    
    protected $fillable = [
        'sender_id',
        'receiver_id',
        'message',
        'is_read',
        'read_at',
        'type',
        'file_path',
        'file_name',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    protected $appends = ['time_formatted', 'is_sender'];

    /**
     * Relasi ke pengirim
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Relasi ke penerima
     */
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    /**
     * Accessor untuk waktu format
     */
    public function getTimeFormattedAttribute()
    {
        return $this->created_at->format('H:i');
    }

    /**
     * Accessor untuk cek apakah user adalah pengirim
     */
    public function getIsSenderAttribute()
    {
        return auth()->id() == $this->sender_id;
    }

    /**
     * Scope untuk pesan antara dua user
     */
    public function scopeBetweenUsers($query, $user1, $user2)
    {
        return $query->where(function($q) use ($user1, $user2) {
            $q->where('sender_id', $user1)
              ->where('receiver_id', $user2);
        })->orWhere(function($q) use ($user1, $user2) {
            $q->where('sender_id', $user2)
              ->where('receiver_id', $user1);
        });
    }

    /**
     * Scope untuk pesan belum dibaca
     */
    public function scopeUnread($query, $userId = null)
    {
        $userId = $userId ?? auth()->id();
        return $query->where('receiver_id', $userId)
                    ->where('is_read', false);
    }

    /**
     * Tandai sebagai sudah dibaca
     */
    public function markAsRead()
    {
        if (!$this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }
    }
}