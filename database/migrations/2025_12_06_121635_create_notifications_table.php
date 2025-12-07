<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            // Relasi ke user yang menerima notifikasi
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            
            // Relasi ke user yang mengirim notifikasi (perawat/admin yang melaporkan)
            $table->foreignId('sender_id')->nullable()->constrained('users')->onDelete('set null');
            
            // Relasi ke lansia yang terkait (jika notifikasi tentang lansia)
            $table->foreignId('datalansia_id')->nullable()->constrained('datalansia')->onDelete('cascade');
            
            // Informasi notifikasi
            $table->string('type'); // emergency, warning, info, system
            $table->string('category'); // kesehatan, iuran, pengobatan, administrasi, sistem
            $table->string('title'); // judul notifikasi
            $table->text('message'); // pesan notifikasi
            $table->text('data')->nullable(); // data tambahan dalam JSON
            
            // Target action
            $table->string('action_url')->nullable(); // URL ketika diklik
            $table->string('action_text')->nullable(); // teks untuk tombol action
            
            // Level urgensi
            $table->enum('urgency_level', ['low', 'medium', 'high', 'critical'])->default('medium');
            
            // Status notifikasi
            $table->boolean('is_read')->default(false);
            $table->boolean('is_archived')->default(false);
            $table->boolean('is_action_taken')->default(false); // apakah sudah ditindaklanjuti
            
            // Waktu notifikasi
            $table->timestamp('read_at')->nullable();
            $table->timestamp('action_taken_at')->nullable();
            $table->timestamp('scheduled_at')->nullable(); // untuk notifikasi terjadwal
            
            // Expiry (untuk notifikasi yang kadaluarsa)
            $table->timestamp('expires_at')->nullable();
            
            // Metadata
            $table->json('metadata')->nullable(); // data tambahan untuk mobile push notif
            
            // Tambahkan soft deletes
            $table->softDeletes(); // Ini akan menambahkan kolom deleted_at
            $table->timestamps();
            
            // Indexes untuk performa
            $table->index(['user_id', 'is_read', 'created_at']);
            $table->index(['datalansia_id', 'type']);
            $table->index(['urgency_level', 'created_at']);
            $table->index(['category', 'created_at']);
            $table->index(['deleted_at']); // Index untuk soft deletes
        });
    }

    public function down()
    {
        Schema::dropIfExists('notifications');
    }
};