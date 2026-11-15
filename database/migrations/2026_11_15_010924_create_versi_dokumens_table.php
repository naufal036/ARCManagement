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
        Schema::create('versi_dokumen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dokumen_proyek_id')
                ->constrained('dokumen_proyek')
                ->cascadeOnDelete();
            $table->unsignedInteger('nomor_versi');
            $table->string('nama_file');
            $table->string('path_file');
            $table->string('mime_type', 150);
            $table->unsignedBigInteger('ukuran_file');
            $table->text('catatan')->nullable();
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->unique(['dokumen_proyek_id', 'nomor_versi']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('versi_dokumen');
    }
};
