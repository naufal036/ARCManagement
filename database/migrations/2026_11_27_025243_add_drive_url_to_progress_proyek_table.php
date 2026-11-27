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
        Schema::table('progress_proyek', function (Blueprint $table): void {
            $table->string('drive_url', 2048)->nullable()->after('rencana_selanjutnya');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('progress_proyek', function (Blueprint $table): void {
            $table->dropColumn('drive_url');
        });
    }
};
