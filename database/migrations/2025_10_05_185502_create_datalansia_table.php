<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('datalansia', function (Blueprint $table) {
            $table->id();

            // Relasi ke user keluarga / pemilik akun
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Foreign key kamar
            $table->foreignId('kamar_id')
                ->nullable()
                ->constrained('kamar')
                ->nullOnDelete();

            // Foreign key perawat utama
            $table->foreignId('perawat_utama_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Data lansia
            $table->string('nama', 100);
            $table->string('nik', 50)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();

            // 🔥 Nomor HP Anak (yang kamu minta)
            $table->string('no_hp_anak', 15)->nullable();

            // Tanggal masuk / keluar
            $table->date('tanggal_masuk')->nullable();
            $table->date('tanggal_keluar')->nullable();

            // Status lansia
            $table->enum('status', ['aktif', 'keluar', 'meninggal'])->default('aktif');

            // Kontak darurat
            $table->string('kontak_darurat_nama', 100)->nullable();
            $table->string('kontak_darurat_hp', 15)->nullable();
            $table->string('kontak_darurat_hubungan', 50)->nullable();

            // Index untuk optimasi query
            $table->index(['kamar_id', 'status']);
            $table->index(['perawat_utama_id', 'status']);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('datalansia');
    }
};
