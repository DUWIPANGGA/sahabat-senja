<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengeluaran extends Model
{
    use HasFactory;

    protected $table = 'pengeluarans'; // Sesuaikan dengan nama tabel
    
    protected $fillable = [
        'tanggal',
        'keterangan',
        'jumlah',
        'bukti',
        'user_id'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jumlah' => 'decimal:2'
    ];

    // Relasi dengan user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}