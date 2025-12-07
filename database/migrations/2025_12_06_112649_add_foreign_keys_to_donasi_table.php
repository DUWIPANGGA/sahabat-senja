<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('donasi', function (Blueprint $table) {
            // Pastikan tabel-tabel terkait sudah ada
            if (Schema::hasTable('kampanye_donasi') && Schema::hasTable('users') && Schema::hasTable('datalansias')) {
                
                // Foreign key ke kampanye_donasi
                $table->foreign('kampanye_donasi_id')
                    ->references('id')
                    ->on('kampanye_donasi')
                    ->onDelete('set null')
                    ->onUpdate('cascade');
                
                // Foreign key ke users
                $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('set null')
                    ->onUpdate('cascade');
                
                // Foreign key ke datalansias
                $table->foreign('datalansia_id')
                    ->references('id')
                    ->on('datalansias')
                    ->onDelete('set null')
                    ->onUpdate('cascade');
            }
        });
    }

    public function down()
    {
        Schema::table('donasi', function (Blueprint $table) {
            $table->dropForeign(['kampanye_donasi_id']);
            $table->dropForeign(['user_id']);
            $table->dropForeign(['datalansia_id']);
        });
    }
};