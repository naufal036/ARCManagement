
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proyek', function (Blueprint $table) {
            $table->id();
            $table->string('kode_proyek', 30)->unique();
            $table->string('nama_proyek', 150);

            $table->foreignId('klien_id')
                ->constrained('klien')
                ->restrictOnDelete();

            $table->foreignId('jenis_pekerjaan_id')
                ->constrained('jenis_pekerjaan')
                ->restrictOnDelete();

            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai')->nullable();
            $table->text('deskripsi')->nullable();
            $table->string('status', 30)->default('Perencanaan');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proyek');
    }
};
