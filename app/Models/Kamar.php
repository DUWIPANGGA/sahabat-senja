<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class Kamar extends Model
{
        protected $table = 'kamar';

    protected $fillable = ['nomor_kamar', 'kapasitas', 'status'];
    public function lansia()
    {
        return $this->hasMany(Datalansia::class, 'kamar_id');
    }
    
    public function perawat()
    {
        return $this->belongsTo(User::class, 'perawat_id')->where('role', 'perawat');
    }
}

// app/Models/Assignment.php
class Assignment extends Model
{
    protected $fillable = ['perawat_id', 'kamar_id', 'tanggal_mulai', 'tanggal_selesai'];
    
    public function perawat()
    {
        return $this->belongsTo(User::class, 'perawat_id');
    }
    
    public function kamar()
    {
        return $this->belongsTo(Kamar::class);
    }
}