
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('progress_dokumentasi', function (Blueprint $table) {
            $table->id();

            $table->foreignId('progress_proyek_id')
                ->constrained('progress_proyek')
                ->cascadeOnDelete();

            $table->string('nama_file_asli');
            $table->string('path_file');
            $table->text('keterangan')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('progress_dokumentasi');
    }
};
