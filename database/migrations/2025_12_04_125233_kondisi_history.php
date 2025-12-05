// 2024_XX_XX_XXXXXX_create_kondisi_history_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kondisi_history', function (Blueprint $table) {
            $table->id();
            
            // Copy dari tabel kondisi
            $table->foreignId('kondisi_id')->nullable()->constrained('kondisis')->onDelete('set null');
            $table->foreignId('datalansia_id')->constrained('datalansia')->onDelete('cascade');
            $table->foreignId('perawat_id')->nullable()->constrained('users')->onDelete('set null');
            
            // Data kondisi (snapshot)
            $table->date('tanggal');
            $table->json('data_kondisi'); // Semua data dalam JSON
            
            // Metadata
            $table->timestamp('archived_at')->useCurrent();
            $table->string('archive_reason')->nullable()->comment('auto_purge, manual, etc');
            
            $table->timestamps();
            
            // Indexes
            $table->index(['datalansia_id', 'tanggal']);
            $table->index('archived_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kondisi_history');
    }
};