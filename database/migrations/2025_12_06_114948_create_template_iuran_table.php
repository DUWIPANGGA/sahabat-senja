<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('template_iuran', function (Blueprint $table) {
            $table->id();
            $table->string('nama_template');
            $table->text('deskripsi')->nullable();
            $table->decimal('jumlah', 15, 2);
            
            // Target iuran
            $table->enum('target_type', ['semua', 'per_kamar', 'per_lansia', 'per_user'])->default('per_lansia');
            $table->json('target_ids')->nullable(); // ID kamar/lansia/user
            
            // Pengaturan recurring
            $table->boolean('is_recurring')->default(true);
            $table->integer('interval_bulan')->default(1);
            $table->integer('tanggal_jatuh_tempo')->default(1); // tanggal dalam bulan (1-31)
            $table->boolean('include_denda')->default(true);
            $table->decimal('persentase_denda', 5, 2)->default(0.2); // 0.2% per hari
            
            // Metadata
            $table->json('metadata')->nullable();
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('template_iuran');
    }
};