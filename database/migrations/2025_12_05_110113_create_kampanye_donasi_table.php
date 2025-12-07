<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('kampanye_donasi', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->string('slug')->unique();
            $table->text('deskripsi');
            $table->text('deskripsi_singkat');
            $table->decimal('target_dana', 15, 2);
            $table->decimal('dana_terkumpul', 15, 2)->default(0);
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->string('gambar')->nullable();
            $table->string('thumbnail')->nullable();
            $table->json('galeri')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->enum('status', ['draft', 'aktif', 'selesai', 'ditutup'])->default('draft');
            $table->string('kategori');
            $table->unsignedBigInteger('datalansia_id')->nullable();
            $table->text('cerita_lengkap')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->integer('jumlah_donatur')->default(0);
            $table->integer('jumlah_dilihat')->default(0);
            $table->text('terima_kasih_pesan')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Foreign key constraint akan ditambahkan setelah table dibuat
        });
    }

    public function down()
    {
        Schema::dropIfExists('kampanye_donasi');
    }
};