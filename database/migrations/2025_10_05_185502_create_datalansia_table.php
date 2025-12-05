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

            // Relasi akun keluarga (wajib)
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Kamar lansia
            $table->foreignId('kamar_id')
                ->nullable()
                ->constrained('kamar')
                ->nullOnDelete();

            // Data lansia
            $table->string('nama_lansia', 100);
            $table->integer('umur_lansia')->nullable();
            $table->string('tempat_lahir_lansia', 100)->nullable();
            $table->date('tanggal_lahir_lansia')->nullable();
            $table->enum('jenis_kelamin_lansia', ['Laki-laki', 'Perempuan'])->nullable();
            $table->string('gol_darah_lansia', 5)->nullable();
            $table->string('riwayat_penyakit_lansia', 255)->nullable();
            $table->string('alergi_lansia', 255)->nullable();
            $table->string('obat_rutin_lansia', 255)->nullable();
            $table->text('catatan_khusus')->nullable();

            // Data keluarga & emergency contact
            $table->string('nama_anak', 100)->nullable();
            $table->string('alamat_lengkap', 255)->nullable();
            $table->string('no_hp_anak', 15)->nullable();
            $table->string('email_anak', 100)->nullable();

            $table->string('kontak_darurat_nama', 100)->nullable();
            $table->string('kontak_darurat_hp', 15)->nullable();
            $table->string('kontak_darurat_hubungan', 50)->nullable();

            // Status lansia
            $table->enum('status_lansia', ['aktif', 'pulang', 'meninggal'])
                ->default('aktif');

            // Jadwal (json, array)
            $table->json('jadwal_obat_rutin')->nullable();
            $table->json('jadwal_kegiatan_rutin')->nullable();

            // Index untuk optimasi
            $table->index(['status_lansia', 'kamar_id']);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('datalansia');
    }
};
