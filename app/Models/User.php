<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'no_telepon',
        'alamat',
                'profile_picture' 

    ];
protected $appends = ['foto_url'];
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
public function getFotoUrlAttribute()
    {
        if ($this->foto_profil) {
            return Storage::disk('public')->url($this->profile_picture);
        }
        return asset('assets/images/default-avatar.png'); // Default avatar
    }
    // Scope untuk filter role
    public function scopeAdmin($query)
    {
        return $query->where('role', 'admin');
    }

    public function scopePerawat($query)
    {
        return $query->where('role', 'perawat');
    }

    public function scopeKeluarga($query)
    {
        return $query->where('role', 'keluarga');
    }

    // Helper methods
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isPerawat()
    {
        return $this->role === 'perawat';
    }

    public function isKeluarga()
    {
        return $this->role === 'keluarga';
    }

    /**
     * Relasi ke datalansia (untuk keluarga)
     */
    public function datalansia(): HasMany
    {
        return $this->hasMany(Datalansia::class, 'user_id');
    }

    /**
     * Relasi ke kondisi yang diinput (untuk perawat)
     */
    public function kondisiInput(): HasMany
    {
        return $this->hasMany(Kondisi::class, 'perawat_id');
    }

    /**
     * Relasi ke pemasukan yang diinput
     */
    public function pemasukan(): HasMany
    {
        return $this->hasMany(Pemasukan::class, 'user_id');
    }

    /**
     * Relasi ke pengeluaran yang diinput
     */
    public function pengeluaran(): HasMany
    {
        return $this->hasMany(Pengeluaran::class, 'user_id');
    }
}