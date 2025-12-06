<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donasi extends Model
{
    use HasFactory;

    protected $table = 'donasi';
    
    protected $fillable = [
        'user_id',
        'datalansia_id',
        'jumlah',
        'metode_pembayaran',
        'status',
        'bukti_pembayaran',
        'nama_donatur',
        'keterangan',
    ];

    protected $casts = [
        'jumlah' => 'integer',
    ];

    /**
     * Relasi ke user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke datalansia
     */
    public function datalansia()
    {
        return $this->belongsTo(Datalansia::class);
    }

    /**
     * Scope untuk donasi yang sukses
     */
    public function scopeSukses($query)
    {
        return $query->where('status', 'sukses');
    }

    /**
     * Scope untuk donasi pending
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}