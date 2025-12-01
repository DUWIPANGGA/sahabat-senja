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
        Schema::create('jadwal_aktivitas', function (Blueprint $table) {
            $table->id();
            $table->string('nama_aktivitas');
            $table->time('jam'); // Ubah dari string ke time
            $table->text('keterangan')->nullable();
            $table->enum('hari', ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'])->nullable();
            $table->enum('status', ['pending', 'completed'])->default('pending');
            $table->boolean('completed')->default(false);
            
            // Foreign keys untuk relasi
            $table->foreignId('datalansia_id')->nullable()->constrained('datalansia')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('perawat_id')->nullable()->constrained('users')->onDelete('set null');
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_aktivitas');
    }
};