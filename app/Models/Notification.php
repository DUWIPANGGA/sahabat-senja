<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Notification extends Model
{
    use HasFactory, HasUuids, SoftDeletes; // Tambahkan SoftDeletes trait

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    
    protected $fillable = [
        'user_id',
        'sender_id',
        'datalansia_id',
        'type',
        'category',
        'title',
        'message',
        'data',
        'action_url',
        'action_text',
        'urgency_level',
        'is_read',
        'is_archived',
        'is_action_taken',
        'read_at',
        'action_taken_at',
        'scheduled_at',
        'expires_at',
        'metadata'
    ];

    protected $casts = [
        'data' => 'array',
        'metadata' => 'array',
        'is_read' => 'boolean',
        'is_archived' => 'boolean',
        'is_action_taken' => 'boolean',
        'read_at' => 'datetime',
        'action_taken_at' => 'datetime',
        'scheduled_at' => 'datetime',
        'expires_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime' // Tambahkan ini
    ];


    /**
     * Relasi ke user penerima
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke user pengirim
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Relasi ke data lansia
     */
    public function datalansia()
    {
        return $this->belongsTo(Datalansia::class);
    }

    /**
     * Scope untuk notifikasi darurat
     */
    public function scopeEmergency($query)
    {
        return $query->where('type', 'emergency')
                    ->where('urgency_level', 'critical');
    }

    /**
     * Scope untuk notifikasi belum dibaca
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope untuk notifikasi aktif (belum expired)
     */
    public function scopeActive($query)
    {
        return $query->where(function($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }

    /**
     * Mark as read
     */
    public function markAsRead()
    {
        $this->update([
            'is_read' => true,
            'read_at' => now()
        ]);
    }

    /**
     * Mark as action taken
     */
    public function markAsActionTaken()
    {
        $this->update([
            'is_action_taken' => true,
            'action_taken_at' => now()
        ]);
    }

    /**
     * Check if notification is urgent
     */
    public function isUrgent()
    {
        return in_array($this->urgency_level, ['high', 'critical']);
    }

    /**
     * Check if notification is expired
     */
    public function isExpired()
    {
        return $this->expires_at && $this->expires_at->lt(now());
    }
}