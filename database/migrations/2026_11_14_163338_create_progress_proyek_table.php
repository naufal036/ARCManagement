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
        Schema::create('progress_proyek', function (Blueprint $table) {
            $table->id();

            // Relasi dengan tabel proyek
            $table->unsignedBigInteger('proyek_id')->nullable();
            $table->foreign('proyek_id')->references('id')->on('proyek')->onDelete('cascade');

            // Relasi dengan tabel users
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Informasi laporan progress
            $table->date('tanggal_laporan');
            $table->decimal('persentase_progress', 5, 2);

            // Detail pekerjaan
            $table->text('uraian_pekerjaan');
            $table->text('kendala')->nullable();
            $table->text('solusi')->nullable();
            $table->text('catatan')->nullable();
            $table->string('rencana_selanjutnya')->nullable();

            // Timestamp
            $table->timestamps();

            // Index pencarian proyek dan tanggal
            $table->index(['proyek_id', 'tanggal_laporan']);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('progress_proyek');
    }
};
