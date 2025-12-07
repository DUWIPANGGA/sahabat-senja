<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('iuran_bulanan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('datalansia_id')->nullable()->constrained('datalansia')->onDelete('cascade');
            
            // Informasi iuran
            $table->string('nama_iuran');
            $table->text('deskripsi')->nullable();
            $table->decimal('jumlah', 15, 2);
            $table->string('periode'); // bulan-tahun (format: YYYY-MM)
            $table->date('tanggal_jatuh_tempo');
            $table->date('tanggal_bayar')->nullable();
            
            // Status pembayaran
            $table->enum('status', ['pending', 'lunas', 'terlambat', 'dibatalkan'])->default('pending');
            $table->string('metode_pembayaran')->nullable();
            $table->string('bukti_pembayaran')->nullable();
            
            // Keterangan admin
            $table->text('catatan_admin')->nullable();
            $table->boolean('is_otomatis')->default(false); // apakah iuran otomatis setiap bulan
            
            // Recurring settings (untuk iuran otomatis)
            $table->integer('interval_bulan')->default(1);
            $table->date('berlaku_dari')->nullable();
            $table->date('berlaku_sampai')->nullable();
            
            // Metadata
            $table->string('kode_iuran')->unique();
            $table->text('metadata')->nullable(); // JSON untuk data tambahan
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['user_id', 'status']);
            $table->index(['datalansia_id', 'status']);
            $table->index('periode');
            $table->index('tanggal_jatuh_tempo');
        });
    }

    public function down()
    {
        Schema::dropIfExists('iuran_bulanan');
    }
};