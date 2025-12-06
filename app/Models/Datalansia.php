<?php

namespace App\Models;

use App\Models\User;
use App\Models\Kamar;
use App\Models\JadwalObat;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Datalansia extends Model
{
    use HasFactory;

    protected $table = 'datalansia';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'nama_lansia',
        'umur_lansia',
        'tempat_lahir_lansia',
        'tanggal_lahir_lansia',
        'jenis_kelamin_lansia',
        'gol_darah_lansia',
        'riwayat_penyakit_lansia',
        'alergi_lansia',
        'obat_rutin_lansia',
        'nama_anak',
        'alamat_lengkap',
        'no_hp_anak',
        'email_anak',
        'user_id',
        'kamar_id',
        'perawat_id', // perawat utama
        'obat_rutin_json', 
        'jadwal_kegiatan_json',
    ];

    protected $casts = [
        'tanggal_lahir_lansia' => 'date',
    ];

    /**
     * Relasi ke user (keluarga yang menangani)
     */
    public function keluarga(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
public function user()
{
    return $this->keluarga();
}
    /**
     * Relasi ke kondisi (riwayat kesehatan)
     */
    public function kondisi(): HasMany
    {
        return $this->hasMany(Kondisi::class);
    }

    /**
     * Relasi ke kondisi hari ini
     */
    public function kondisiHariIni()
    {
        return $this->hasOne(Kondisi::class)->whereDate('tanggal', today());
    }
public function jadwalObat()
{
    return $this->hasMany(JadwalObat::class, 'datalansia_id');
}
    /**
     * Scope untuk mencari berdasarkan email anak
     */
    public function scopeByEmailAnak($query, $email)
    {
        return $query->where('email_anak', $email);
    }

    /**
     * Scope untuk mencari berdasarkan user_id
     */
    public function scopeByKeluarga($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
     public function kamar()
    {
        return $this->belongsTo(Kamar::class, 'kamar_id');
    }
    
    public function perawat()
    {
        return $this->belongsTo(User::class, 'perawat_id')->where('role', 'perawat');
    }
}
