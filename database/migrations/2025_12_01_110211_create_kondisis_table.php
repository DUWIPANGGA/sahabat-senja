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
        Schema::create('kondisis', function (Blueprint $table) {
            $table->id();
            
            // Foreign key ke datalansia
            $table->foreignId('datalansia_id')->constrained('datalansia')->onDelete('cascade');
            
            // Foreign key ke user (perawat yang input)
            $table->foreignId('perawat_id')->nullable()->constrained('users')->onDelete('set null');
            
            // Data kondisi
            $table->date('tanggal');
            $table->string('tekanan_darah', 20)->nullable();
            $table->string('nadi', 10)->nullable();
            $table->string('nafsu_makan', 50)->nullable();
            $table->string('status_obat', 50)->nullable();
            $table->text('catatan')->nullable();
            $table->enum('status', ['Stabil', 'Perlu Perhatian', 'Kritis'])->default('Stabil');
            
            $table->timestamps();
            
            // Index untuk pencarian cepat
            $table->index(['datalansia_id', 'tanggal']);
            $table->index('perawat_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kondisis');
    }
};