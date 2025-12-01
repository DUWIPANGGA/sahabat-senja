<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrackingObat extends Model
{
    use HasFactory;

    protected $table = 'tracking_obat';
    
    protected $fillable = [
        'jadwal_obat_id',
        'datalansia_id',
        'nama_obat',
        'dosis',
        'waktu',
        'tanggal',
        'jam_pemberian',
        'sudah_diberikan',
        'catatan',
        'perawat_id',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jam_pemberian' => 'datetime:H:i',
        'sudah_diberikan' => 'boolean',
    ];

    // Relasi
    public function jadwalObat()
    {
        return $this->belongsTo(JadwalObat::class, 'jadwal_obat_id');
    }

    public function datalansia()
    {
        return $this->belongsTo(Datalansia::class, 'datalansia_id');
    }

    public function perawat()
    {
        return $this->belongsTo(User::class, 'perawat_id')->where('role', 'perawat');
    }

    // Accessor untuk nama perawat
    public function getNamaPerawatAttribute()
    {
        return $this->perawat ? $this->perawat->name : null;
    }

    // Scope untuk tanggal tertentu
    public function scopeByTanggal($query, $tanggal)
    {
        return $query->where('tanggal', $tanggal);
    }

    // Scope untuk lansia tertentu
    public function scopeByLansia($query, $datalansiaId)
    {
        return $query->where('datalansia_id', $datalansiaId);
    }

    // Scope untuk hari ini
    public function scopeHariIni($query)
    {
        return $query->where('tanggal', now()->toDateString());
    }

    // Check apakah sudah lewat waktu
    public function isTerlambat()
    {
        if ($this->sudah_diberikan) return false;
        
        $jamSekarang = now()->format('H:i');
        $waktuMapping = [
            'Pagi' => '09:00',
            'Siang' => '13:00',
            'Sore' => '17:00',
            'Malam' => '21:00',
        ];
        
        $jamTarget = $waktuMapping[$this->waktu] ?? '12:00';
        return $jamSekarang > $jamTarget;
    }
}