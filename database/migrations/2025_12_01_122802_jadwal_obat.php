<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('jadwal_obat', function (Blueprint $table) {
            $table->id();
            
            // Data lansia
            $table->foreignId('datalansia_id')->constrained('datalansia')->onDelete('cascade');
            
            // Informasi obat
            $table->string('nama_obat');
            $table->text('deskripsi')->nullable();
            $table->string('dosis');
            $table->enum('waktu', ['Pagi', 'Siang', 'Sore', 'Malam', 'Sesuai Kebutuhan'])->default('Pagi');
            
            // Jadwal pemberian
            $table->time('jam_minum')->nullable();
            $table->enum('frekuensi', ['Setiap Hari', 'Setiap 2 Hari', 'Mingguan', 'Bulanan', 'Sesuai Kebutuhan'])->default('Setiap Hari');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai')->nullable();
            
            // Status
            $table->boolean('selesai')->default(false);
            $table->text('catatan')->nullable();
            
            // User yang menambahkan
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('perawat_id')->nullable()->constrained('users')->onDelete('set null');
            
            $table->timestamps();
            $table->softDeletes();
            
            // Index untuk pencarian
            $table->index(['datalansia_id', 'selesai']);
            $table->index(['tanggal_mulai', 'tanggal_selesai']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_obat');
    }
};