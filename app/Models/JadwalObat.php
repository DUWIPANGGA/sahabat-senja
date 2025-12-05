<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JadwalObat extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'jadwal_obat';
    
    protected $fillable = [
        'datalansia_id',
        'nama_obat',
        'deskripsi',
        'dosis',
        'waktu',
        'jam_minum',
        'frekuensi',
        'tanggal_mulai',
        'tanggal_selesai',
        'selesai',
        'catatan',
        'user_id',
        'perawat_id',
        'jenis_obat', 
        'auto_generate', 
        'hari',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'jam_minum' => 'datetime:H:i',
        'selesai' => 'boolean',
    ];

    protected $attributes = [
        'selesai' => false,
        'waktu' => 'Pagi',
        'frekuensi' => 'Setiap Hari',
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
     * Scope untuk jadwal yang aktif (belum selesai)
     */
    public function scopeAktif($query)
    {
        return $query->where('selesai', false)
            ->where(function($q) {
                $q->whereNull('tanggal_selesai')
                  ->orWhere('tanggal_selesai', '>=', now());
            });
    }

    /**
     * Scope untuk jadwal berdasarkan lansia
     */
    public function scopeByLansia($query, $datalansiaId)
    {
        return $query->where('datalansia_id', $datalansiaId);
    }

    /**
     * Scope untuk jadwal hari ini
     */
    public function scopeHariIni($query)
    {
        $hariIni = now()->toDateString();
        return $query->where('tanggal_mulai', '<=', $hariIni)
            ->where(function($q) use ($hariIni) {
                $q->whereNull('tanggal_selesai')
                  ->orWhere('tanggal_selesai', '>=', $hariIni);
            });
    }

    /**
     * Check apakah jadwal masih aktif
     */
    public function isAktif()
    {
        if ($this->selesai) return false;
        
        if ($this->tanggal_selesai) {
            return now()->between($this->tanggal_mulai, $this->tanggal_selesai);
        }
        
        return now()->gte($this->tanggal_mulai);
    }

    /**
     * Get status jadwal
     */
    public function getStatusAttribute()
    {
        if ($this->selesai) return 'Selesai';
        if ($this->isAktif()) return 'Aktif';
        return 'Belum Dimulai';
    }

    /**
     * Get nama lansia
     */
    public function getNamaLansiaAttribute()
    {
        return $this->datalansia ? $this->datalansia->nama_lansia : 'Tidak Diketahui';
    }

    /**
     * Get jam minum formatted
     */
    public function getJamMinumFormattedAttribute()
    {
        return $this->jam_minum ? $this->jam_minum->format('H:i') : '-';
    }

    /**
     * Get periode pengobatan
     */
    public function getPeriodeAttribute()
    {
        $mulai = $this->tanggal_mulai->format('d/m/Y');
        $selesai = $this->tanggal_selesai ? $this->tanggal_selesai->format('d/m/Y') : 'Sampai Selesai';
        
        return "$mulai - $selesai";
    }
}