<?php
// app/Models/User.php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'no_telepon',
        'alamat'
    ];

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
}
