<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('notification_templates', function (Blueprint $table) {
            $table->id();
            
            // Informasi template
            $table->string('template_code')->unique(); // kode unik template
            $table->string('name'); // nama template
            $table->string('subject'); // subjek notifikasi
            $table->text('content'); // konten notifikasi (dengan placeholder)
            $table->text('variables')->nullable(); // variabel yang tersedia dalam JSON
            $table->text('description')->nullable(); // deskripsi template
            
            // Jenis notifikasi
            $table->enum('type', ['email', 'sms', 'push', 'in_app', 'whatsapp', 'all'])->default('all');
            $table->enum('category', [
                'medical',           // kesehatan
                'financial',         // keuangan/iuran
                'medication',        // pengobatan
                'appointment',       // janji temu
                'emergency',         // darurat
                'reminder',          // pengingat
                'system',            // sistem
                'announcement'       // pengumuman
            ])->default('system');
            
            // Target penerima
            $table->enum('target_audience', [
                'all_users',         // semua user
                'family_members',    // keluarga lansia
                'nurses',            // perawat
                'admins',            // admin
                'specific_users',    // user tertentu
                'specific_lansia'    // lansia tertentu
            ])->default('all_users');
            
            // Trigger conditions
            $table->json('trigger_conditions')->nullable(); // kondisi trigger dalam JSON
            
            // Status
            $table->boolean('is_active')->default(true);
            $table->boolean('is_auto_send')->default(false); // otomatis dikirim
            
            // Scheduling
            $table->integer('delay_minutes')->nullable(); // delay pengiriman
            $table->string('cron_schedule')->nullable(); // jadwal cron
            
            // Metadata
            $table->json('metadata')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['template_code', 'is_active']);
            $table->index(['category', 'type']);
            $table->index(['is_active', 'category']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('notification_templates');
    }
};