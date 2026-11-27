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
        Schema::table('users', function (Blueprint $table) {
            $table->string('jabatan')->nullable()->after('phone');
            $table->string('jenis_kelamin', 20)->nullable()->after('jabatan');
            $table->text('keahlian')->nullable()->after('jenis_kelamin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['jabatan', 'jenis_kelamin', 'keahlian']);
        });
    }
};
