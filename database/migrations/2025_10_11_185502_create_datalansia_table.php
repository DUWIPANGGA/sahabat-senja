// 2024_XX_XX_XXXXXX_add_kamar_to_datalansia_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('datalansia', function (Blueprint $table) {
            // Tambah kolom kamar
            $table->foreignId('kamar_id')
                ->nullable()
                ->after('user_id')
                ->constrained('kamar')
                ->onDelete('set null');
            
            // Tambah kolom perawat utama
            $table->foreignId('perawat_utama_id')
                ->nullable()
                ->after('kamar_id')
                ->constrained('users')
                ->where('role', 'perawat')
                ->onDelete('set null');
            
            // Informasi tambahan
            $table->date('tanggal_masuk')->nullable();
            $table->date('tanggal_keluar')->nullable();
            $table->enum('status', ['aktif', 'keluar', 'meninggal'])->default('aktif');
            
            // Emergency contact
            $table->string('kontak_darurat_nama', 100)->nullable();
            $table->string('kontak_darurat_hp', 15)->nullable();
            $table->string('kontak_darurat_hubungan', 50)->nullable();
            
            // Indexes
            $table->index(['kamar_id', 'status']);
            $table->index(['perawat_utama_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('datalansia', function (Blueprint $table) {
            $table->dropForeign(['kamar_id']);
            $table->dropForeign(['perawat_utama_id']);
            
            $table->dropColumn([
                'kamar_id',
                'perawat_utama_id',
                'tanggal_masuk',
                'tanggal_keluar',
                'status',
                'kontak_darurat_nama',
                'kontak_darurat_hp',
                'kontak_darurat_hubungan'
            ]);
        });
    }
};