<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JadwalAktivitas extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'jadwal_aktivitas';
    
    protected $fillable = [
        'nama_aktivitas',
        'jam',
        'keterangan',
        'hari',
        'status',
        'completed',
        'datalansia_id',
        'user_id',
        'perawat_id',
    ];

    protected $casts = [
        'jam' => 'datetime:H:i',
        'completed' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $attributes = [
        'status' => 'pending',
        'completed' => false,
    ];

    /**
     * Relasi ke Data Lansia
     */
    public function datalansia()
    {
        return $this->belongsTo(Datalansia::class, 'datalansia_id');
    }

    /**
     * Relasi ke User (Keluarga yang membuat)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke Perawat
     */
    public function perawat()
    {
        return $this->belongsTo(User::class, 'perawat_id')->where('role', 'perawat');
    }

    /**
     * Scope untuk jadwal hari ini
     */
    public function scopeHariIni($query)
    {
        $hari = now()->locale('id')->dayName;
        return $query->where('hari', $hari);
    }

    /**
     * Scope untuk jadwal yang belum selesai
     */
    public function scopeBelumSelesai($query)
    {
        return $query->where('completed', false);
    }

    /**
     * Scope untuk jadwal berdasarkan user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope untuk jadwal berdasarkan lansia
     */
    public function scopeByLansia($query, $datalansiaId)
    {
        return $query->where('datalansia_id', $datalansiaId);
    }

    /**
     * Get nama lansia (jika ada relasi)
     */
    public function getNamaLansiaAttribute()
    {
        return $this->datalansia ? $this->datalansia->nama_lansia : 'Umum';
    }

    /**
     * Get nama user (pembuat)
     */
    public function getNamaPembuatAttribute()
    {
        return $this->user ? $this->user->name : 'Tidak diketahui';
    }

    /**
     * Get nama perawat (jika ada)
     */
    public function getNamaPerawatAttribute()
    {
        return $this->perawat ? $this->perawat->name : '-';
    }

    /**
     * Format jam untuk display
     */
    public function getJamFormattedAttribute()
    {
        return $this->jam ? $this->jam->format('H:i') : '-';
    }

    /**
     * Check apakah jadwal untuk hari ini
     */
    public function isHariIni()
    {
        $hari = now()->locale('id')->dayName;
        return $this->hari === $hari;
    }

    /**
     * Check apakah sudah lewat jamnya
     */
    public function isTerlambat()
    {
        if (!$this->jam) return false;
        
        $jamJadwal = $this->jam->format('H:i');
        $jamSekarang = now()->format('H:i');
        
        return $jamSekarang > $jamJadwal && !$this->completed;
    }
}