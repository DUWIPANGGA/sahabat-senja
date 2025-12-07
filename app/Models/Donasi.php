<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donasi extends Model
{
    use HasFactory;
protected $table='donasi';
    protected $fillable = [
        'kampanye_donasi_id',
        'user_id',
        'datalansia_id',
        'jumlah',
        'metode_pembayaran',
        'status',
        'bukti_pembayaran',
        'nama_donatur',
        'keterangan',
        'kode_donasi',
        'email',
        'telepon',
        'anonim',
        'doa_harapan'
    ];

    protected $casts = [
        'anonim' => 'boolean',
        'jumlah' => 'integer'
    ];

    // Relasi ke Kampanye
    public function kampanye()
    {
        return $this->belongsTo(KampanyeDonasi::class, 'kampanye_donasi_id');
    }

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke DataLansia
    public function datalansia()
    {
        return $this->belongsTo(DataLansia::class);
    }

    // Generate kode donasi
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($donasi) {
            $donasi->kode_donasi = 'DON-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -8));
        });
    }

    // Format jumlah
    public function getJumlahFormattedAttribute()
    {
        return 'Rp ' . number_format($this->jumlah, 0, ',', '.');
    }

    // Cek status
    public function getIsPendingAttribute()
    {
        return $this->status == 'pending';
    }

    public function getIsSuccessAttribute()
    {
        return $this->status == 'success';
    }

    // Update dana terkumpul ketika donasi sukses
    public static function booted()
    {
        static::updated(function ($donasi) {
            if ($donasi->isDirty('status') && $donasi->status == 'success') {
                if ($donasi->kampanye) {
                    $donasi->kampanye->increment('dana_terkumpul', $donasi->jumlah);
                    $donasi->kampanye->increment('jumlah_donatur');
                }
            }
        });

        static::created(function ($donasi) {
            if ($donasi->status == 'success' && $donasi->kampanye) {
                $donasi->kampanye->increment('dana_terkumpul', $donasi->jumlah);
                $donasi->kampanye->increment('jumlah_donatur');
            }
        });
    }
}