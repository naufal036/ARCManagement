<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! DB::table('kategori_dokumen')->where('nama_kategori', 'BKU')->exists()) {
            DB::table('kategori_dokumen')->insert([
                'nama_kategori' => 'BKU',
                'deskripsi' => 'Buku Kendali Umum proyek.',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('kategori_dokumen')->where('nama_kategori', 'BKU')->delete();
    }
};
