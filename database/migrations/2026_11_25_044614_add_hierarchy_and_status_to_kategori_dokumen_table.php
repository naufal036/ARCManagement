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
        Schema::table('kategori_dokumen', function (Blueprint $table): void {
            $table->string('kode_kategori', 40)->nullable()->after('id');
            $table->foreignId('parent_id')
                ->nullable()
                ->after('kode_kategori')
                ->constrained('kategori_dokumen')
                ->restrictOnDelete();
            $table->string('status', 20)->default('active')->after('deskripsi');
        });

        DB::table('kategori_dokumen')
            ->select(['id'])
            ->orderBy('id')
            ->chunkById(500, function ($categories): void {
                foreach ($categories as $category) {
                    DB::table('kategori_dokumen')
                        ->where('id', $category->id)
                        ->update(['kode_kategori' => sprintf('KAT-%06d', $category->id)]);
                }
            });

        Schema::table('kategori_dokumen', function (Blueprint $table): void {
            $table->string('kode_kategori', 40)->nullable(false)->change();
            $table->unique('kode_kategori');
            $table->index(['parent_id', 'status']);
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE kategori_dokumen ADD CONSTRAINT kategori_dokumen_status_check CHECK (status IN ('active', 'inactive'))");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE kategori_dokumen DROP CHECK kategori_dokumen_status_check');
        }

        Schema::table('kategori_dokumen', function (Blueprint $table): void {
            $table->dropUnique(['kode_kategori']);
            $table->dropIndex(['parent_id', 'status']);
            $table->dropConstrainedForeignId('parent_id');
            $table->dropColumn(['kode_kategori', 'status']);
        });
    }
};
