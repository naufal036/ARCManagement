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
        Schema::table('dokumen_proyek', function (Blueprint $table) {
            $table->string('nomor_dokumen', 100)->nullable()->unique()->after('id');
            $table->string('drive_url', 2048)->nullable()->after('deskripsi');
            $table->foreignId('kategori_dokumen_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dokumen_proyek', function (Blueprint $table) {
            $table->dropUnique(['nomor_dokumen']);
            $table->dropColumn(['nomor_dokumen', 'drive_url']);
            $table->foreignId('kategori_dokumen_id')->nullable(false)->change();
        });
    }
};
