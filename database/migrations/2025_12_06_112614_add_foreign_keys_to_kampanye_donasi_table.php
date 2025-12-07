<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Pastikan tabel datalansias sudah ada
        if (Schema::hasTable('datalansias')) {
            Schema::table('kampanye_donasi', function (Blueprint $table) {
                // Cek apakah kolom datalansia_id sudah ada
                if (!Schema::hasColumn('kampanye_donasi', 'datalansia_id')) {
                    $table->unsignedBigInteger('datalansia_id')->nullable()->after('kategori');
                }
                
                // Tambahkan foreign key constraint
                $table->foreign('datalansia_id')
                    ->references('id')
                    ->on('datalansias')
                    ->onDelete('set null')
                    ->onUpdate('cascade');
            });
        }
    }

    public function down()
    {
        Schema::table('kampanye_donasi', function (Blueprint $table) {
            $table->dropForeign(['datalansia_id']);
        });
    }
};