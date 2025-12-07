<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KampanyeDonasi extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'kampanye_donasi';
    
    protected $fillable = [
        'judul',
        'slug',
        'deskripsi',
        'deskripsi_singkat',
        'target_dana',
        'dana_terkumpul',
        'tanggal_mulai',
        'tanggal_selesai',
        'gambar',
        'thumbnail',
        'galeri',
        'is_featured',
        'status',
        'kategori',
        'datalansia_id',
        'cerita_lengkap',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'jumlah_donatur',
        'jumlah_dilihat',
        'terima_kasih_pesan'
    ];

    protected $casts = [
        'galeri' => 'array',
        'target_dana' => 'decimal:2',
        'dana_terkumpul' => 'decimal:2',
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'is_featured' => 'boolean'
    ];

    // Relasi ke DataLansia
    public function datalansia()
    {
        return $this->belongsTo(DataLansia::class);
    }

    // Relasi ke Donasi
    public function donasis()
    {
        return $this->hasMany(Donasi::class);
    }

    // Hitung progress persentase
    public function getProgressAttribute()
    {
        if ($this->target_dana == 0) return 0;
        return min(100, round(($this->dana_terkumpul / $this->target_dana) * 100, 2));
    }

    // Hitung hari tersisa
    public function getHariTersisaAttribute()
    {
        $now = now();
        $end = \Carbon\Carbon::parse($this->tanggal_selesai);
        
        if ($now->gt($end)) {
            return 0;
        }
        
        return $now->diffInDays($end);
    }

    // Cek apakah aktif
    public function getIsActiveAttribute()
    {
        $now = now();
        $start = \Carbon\Carbon::parse($this->tanggal_mulai);
        $end = \Carbon\Carbon::parse($this->tanggal_selesai);
        
        return $this->status == 'aktif' && $now->between($start, $end);
    }

    // Scope untuk kampanye aktif
    public function scopeAktif($query)
    {
        $now = now();
        return $query->where('status', 'aktif')
                    ->where('tanggal_mulai', '<=', $now)
                    ->where('tanggal_selesai', '>=', $now);
    }

    // Scope untuk featured
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    // Format angka
    public function formatDana($value)
    {
        return 'Rp ' . number_format($value, 0, ',', '.');
    }
}