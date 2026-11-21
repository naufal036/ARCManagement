<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('versi_dokumen', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('dokumen_proyek_id')->constrained('dokumen_proyek')->cascadeOnDelete();
            $table->unsignedInteger('nomor_versi');
            $table->string('nama_file_asli', 255);
            $table->string('nama_file_storage', 255);
            $table->string('path_file', 500);
            $table->string('disk', 50)->default('local');
            $table->string('mime_type', 150);
            $table->string('ekstensi', 20);
            $table->unsignedBigInteger('ukuran_file');
            $table->char('checksum_sha256', 64);
            $table->text('catatan')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['dokumen_proyek_id', 'nomor_versi']);
            $table->index(['mime_type', 'ekstensi']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('versi_dokumen');
    }
};
