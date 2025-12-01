<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tracking_obat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_obat_id')->constrained('jadwal_obat')->onDelete('cascade');
            $table->foreignId('datalansia_id')->constrained('datalansia')->onDelete('cascade');
            
            // Info obat (snapshot saat tracking dibuat)
            $table->string('nama_obat');
            $table->string('dosis');
            $table->enum('waktu', ['Pagi', 'Siang', 'Sore', 'Malam']);
            
            // Tracking data
            $table->date('tanggal');
            $table->time('jam_pemberian')->nullable();
            $table->boolean('sudah_diberikan')->default(false);
            $table->text('catatan')->nullable();
            
            // User yang melakukan tracking
            $table->foreignId('perawat_id')->nullable()->constrained('users')->onDelete('set null');
            
            $table->timestamps();
            
            // Index untuk pencarian
            $table->index(['tanggal', 'sudah_diberikan']);
            $table->index(['datalansia_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tracking_obat');
    }
};