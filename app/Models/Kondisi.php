<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Kondisi extends Model
{
    use HasFactory;

    protected $table = 'kondisis';
    
    protected $fillable = [
        'datalansia_id',
        'perawat_id',
        'tanggal',
        'tekanan_darah',
        'nadi',
        'nafsu_makan',
        'status_obat',
        'catatan',
        'status',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    /**
     * Relasi ke datalansia
     */
    public function datalansia(): BelongsTo
    {
        return $this->belongsTo(Datalansia::class);
    }

    /**
     * Relasi ke perawat (user dengan role perawat)
     */
    public function perawat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'perawat_id');
    }

    /**
     * Scope untuk kondisi hari ini
     */
    public function scopeHariIni($query)
    {
        return $query->whereDate('tanggal', today());
    }

    /**
     * Scope untuk kondisi berdasarkan lansia
     */
    public function scopeByDatalansia($query, $datalansiaId)
    {
        return $query->where('datalansia_id', $datalansiaId);
    }

    /**
     * Scope untuk kondisi berdasarkan perawat
     */
    public function scopeByPerawat($query, $perawatId)
    {
        return $query->where('perawat_id', $perawatId);
    }

    /**
     * Accessor untuk nama lansia
     */
    public function getNamaLansiaAttribute()
    {
        return $this->datalansia->nama_lansia ?? null;
    }
}