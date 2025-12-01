<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migration.
     */
    public function up(): void
    {
        Schema::create('datalansia', function (Blueprint $table) {
            $table->id();
            
            // Data lansia
            $table->string('nama_lansia', 100);
            $table->integer('umur_lansia');
            $table->string('tempat_lahir_lansia', 100)->nullable();
            $table->date('tanggal_lahir_lansia')->nullable();
            $table->enum('jenis_kelamin_lansia', ['Laki-laki', 'Perempuan'])->nullable();
            $table->string('gol_darah_lansia', 5)->nullable();
            $table->string('riwayat_penyakit_lansia', 255)->nullable();
            $table->string('alergi_lansia', 255)->nullable();
            $table->string('obat_rutin_lansia', 255)->nullable();
            
            // Data keluarga (relasi ke users)
            $table->string('nama_anak', 100);
            $table->string('alamat_lengkap', 255);
            $table->string('no_hp_anak', 15);
            $table->string('email_anak', 100);
            
            // Foreign key ke users (keluarga yang menangani)
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            
            $table->timestamps();
        });
    }

    /**
     * Rollback migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('datalansia');
    }
};