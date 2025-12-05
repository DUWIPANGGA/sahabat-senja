<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            
            // Relasi
            $table->foreignId('perawat_id')
                ->constrained('users')
                ->where('role', 'perawat')
                ->onDelete('cascade');
                
            $table->foreignId('kamar_id')
                ->constrained('kamar')
                ->onDelete('cascade');
            
            // Periode penugasan
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai')->nullable();
            
            // Status
            $table->enum('status', ['active', 'inactive', 'completed'])->default('active');
            $table->text('catatan')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->unique(['perawat_id', 'kamar_id', 'tanggal_mulai']);
            $table->index(['status', 'tanggal_mulai']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};