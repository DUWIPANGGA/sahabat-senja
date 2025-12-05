<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pemasukan extends Model
{
    use HasFactory;

    protected $table = 'pemasukans'; // Sesuaikan dengan nama tabel
    
    protected $fillable = [
        'tanggal',
        'sumber',
        'jumlah',
        'keterangan',
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