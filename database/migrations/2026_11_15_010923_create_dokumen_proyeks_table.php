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
        Schema::create('dokumen_proyek', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proyek_id')
                ->constrained('proyek')
                ->restrictOnDelete();
            $table->foreignId('kategori_dokumen_id')
                ->constrained('kategori_dokumen')
                ->restrictOnDelete();
            $table->string('nama_dokumen', 150);
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dokumen_proyek');
    }
};
