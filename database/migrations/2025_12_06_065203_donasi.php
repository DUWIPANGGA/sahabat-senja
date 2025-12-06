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
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('datalansia_id')->nullable()->constrained('datalansia')->onDelete('set null');
            $table->integer('jumlah');
            $table->string('metode_pembayaran');
            $table->string('status')->default('pending');
            $table->string('bukti_pembayaran')->nullable();
            $table->string('nama_donatur');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('donasi');
    }
};