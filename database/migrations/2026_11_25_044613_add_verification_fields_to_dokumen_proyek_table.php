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
            $table->string('nomor_dokumen', 40)->nullable()->after('id');
            $table->string('status_verifikasi', 30)->default('draft')->after('deskripsi');
            $table->unsignedInteger('verification_cycle')->default(0)->after('status_verifikasi');
            $table->unsignedInteger('lock_version')->default(0)->after('verification_cycle');
        });

        DB::table('dokumen_proyek')
            ->select(['id'])
            ->orderBy('id')
            ->chunkById(500, function ($documents): void {
                foreach ($documents as $document) {
                    DB::table('dokumen_proyek')
                        ->where('id', $document->id)
                        ->update(['nomor_dokumen' => sprintf('DOC-%06d', $document->id)]);
                }
            });

        Schema::table('dokumen_proyek', function (Blueprint $table): void {
            $table->string('nomor_dokumen', 40)->nullable(false)->change();
            $table->unique('nomor_dokumen');
            $table->index(['status_verifikasi', 'updated_at']);
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE dokumen_proyek ADD CONSTRAINT dokumen_proyek_status_verifikasi_check CHECK (status_verifikasi IN ('draft', 'pending_review', 'approved', 'rejected', 'revision_required'))");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE dokumen_proyek DROP CHECK dokumen_proyek_status_verifikasi_check');
        }

        Schema::table('dokumen_proyek', function (Blueprint $table): void {
            $table->dropUnique(['nomor_dokumen']);
            $table->dropIndex(['status_verifikasi', 'updated_at']);
            $table->dropColumn([
                'nomor_dokumen',
                'status_verifikasi',
                'verification_cycle',
                'lock_version',
            ]);
        });
    }
};
