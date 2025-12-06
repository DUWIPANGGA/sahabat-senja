<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class DataPerawat extends Model
{
    use HasFactory;

    protected $table = 'DataPerawat';
    protected $fillable = ['nama', 'email', 'alamat', 'no_hp', 'jenis_kelamin'];
}