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
        Schema::table('klien', function (Blueprint $table): void {
            $table->string('jenis_client', 30)->default('Lainnya')->after('nama_klien');
            $table->string('npsn', 20)->nullable()->after('jenis_client');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('klien', function (Blueprint $table): void {
            $table->dropColumn(['jenis_client', 'npsn']);
        });
    }
};
