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
        Schema::table('proyek', function (Blueprint $table) {
            $table->string('nama_kegiatan')->nullable();
            $table->string('instansi')->nullable();
            $table->string('lokasi')->nullable();
            $table->unsignedInteger('waktu_pengerjaan')->nullable();
            $table->string('nama_kontrak')->nullable();
            $table->decimal('nilai_kontrak', 15, 2)->nullable();
            $table->string('project_type')->nullable();
            $table->string('drive_url')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proyek', function (Blueprint $table) {
            $table->dropColumn(['nama_kegiatan', 'instansi', 'lokasi', 'waktu_pengerjaan', 'nama_kontrak', 'nilai_kontrak', 'project_type', 'drive_url']);
        });
    }
};
