<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kamar', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_kamar', 10)->unique();
            $table->string('nama_kamar')->nullable();
            $table->integer('kapasitas')->default(1);
            $table->enum('tipe', ['standar', 'vip', 'vvip'])->default('standar');
            $table->text('fasilitas')->nullable();
            $table->enum('status', ['tersedia', 'terisi', 'maintenance'])->default('tersedia');
            $table->decimal('harga_per_bulan', 15, 2)->nullable();
            
            // Perawat yang bertanggung jawab
            $table->foreignId('perawat_id')
                ->nullable()
                ->constrained('users')
                ->where('role', 'perawat')
                ->onDelete('set null');
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['status', 'kapasitas']);
            $table->index('perawat_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kamar');
    }
};