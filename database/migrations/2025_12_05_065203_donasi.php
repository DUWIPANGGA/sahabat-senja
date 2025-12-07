<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('donasi', function (Blueprint $table) {
            $table->id();
            
            // Pastikan tipe data sama dengan tabel kampanye_donasi
            $table->unsignedBigInteger('kampanye_donasi_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('datalansia_id')->nullable();
            
            $table->integer('jumlah');
            $table->string('metode_pembayaran');
            $table->string('status')->default('pending');
            $table->string('bukti_pembayaran')->nullable();
            $table->string('nama_donatur');
            $table->text('keterangan')->nullable();
            $table->string('kode_donasi')->unique();
            $table->string('email')->nullable();
            $table->string('telepon')->nullable();
            $table->boolean('anonim')->default(false);
            $table->text('doa_harapan')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('donasi');
    }
};