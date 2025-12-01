<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pemasukans', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('sumber');
            $table->decimal('jumlah', 15, 2);
            $table->string('keterangan')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // user yang input
            $table->timestamps();
        });

        
    }

    public function down(): void
    {
        Schema::dropIfExists('pemasukans');
    }
};