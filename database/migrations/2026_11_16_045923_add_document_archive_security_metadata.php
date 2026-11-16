<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('dokumen_proyek', function (Blueprint $table): void {
            $table->foreignId('progress_proyek_id')
                ->nullable()
                ->after('kategori_dokumen_id')
                ->constrained('progress_proyek')
                ->nullOnDelete();
        });

        Schema::table('versi_dokumen', function (Blueprint $table): void {
            $table->string('nama_file_asli', 255)->nullable()->after('nama_file');
            $table->string('nama_file_storage', 255)->nullable()->after('nama_file_asli');
            $table->string('disk', 50)->default('local')->after('path_file');
            $table->string('ekstensi', 20)->nullable()->after('mime_type');
            $table->char('checksum_sha256', 64)->nullable()->after('ukuran_file');
            $table->index(['mime_type', 'ekstensi']);
        });

        DB::table('versi_dokumen')
            ->select(['id', 'nama_file', 'path_file'])
            ->orderBy('id')
            ->chunkById(500, function ($versions): void {
                foreach ($versions as $version) {
                    $originalName = mb_substr((string) $version->nama_file, 0, 255);
                    $storageName = basename((string) $version->path_file);
                    $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

                    if ($extension === '') {
                        $extension = strtolower(pathinfo($storageName, PATHINFO_EXTENSION));
                    }

                    DB::table('versi_dokumen')
                        ->where('id', $version->id)
                        ->update([
                            'nama_file_asli' => $originalName,
                            'nama_file_storage' => mb_substr($storageName, 0, 255),
                            'ekstensi' => $extension !== '' ? mb_substr($extension, 0, 20) : null,
                        ]);
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('versi_dokumen', function (Blueprint $table): void {
            $table->dropIndex(['mime_type', 'ekstensi']);
            $table->dropColumn([
                'nama_file_asli',
                'nama_file_storage',
                'disk',
                'ekstensi',
                'checksum_sha256',
            ]);
        });

        Schema::table('dokumen_proyek', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('progress_proyek_id');
        });
    }
};
