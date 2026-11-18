<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dokumen_proyek', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('proyek_id')->constrained('proyek')->restrictOnDelete();
            $table->foreignId('kategori_dokumen_id')->constrained('kategori_dokumen')->restrictOnDelete();
            $table->foreignId('progress_proyek_id')->nullable()->constrained('progress_proyek')->nullOnDelete();
            $table->string('nama_dokumen', 150);
            $table->text('deskripsi')->nullable();
            $table->timestamps();

            $table->index(['proyek_id', 'kategori_dokumen_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dokumen_proyek');
    }
};
