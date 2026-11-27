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
        Schema::create('laporan_pengawasans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proyek_id')->constrained('proyek')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->date('periode_mulai');
            $table->date('periode_selesai');
            $table->string('jenis_laporan', 100);
            $table->string('nama_pengawas')->nullable();
            $table->text('rencana_kegiatan')->nullable();
            $table->text('kegiatan_selesai')->nullable();
            $table->text('catatan')->nullable();
            $table->json('inspection_checklist')->nullable();
            $table->json('photo_references')->nullable();
            $table->string('status', 30)->default('Draft');
            $table->timestamps();
            $table->index(['proyek_id', 'periode_mulai']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_pengawasans');
    }
};
