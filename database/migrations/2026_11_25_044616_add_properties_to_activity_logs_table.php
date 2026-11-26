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
        Schema::table('activity_logs', function (Blueprint $table): void {
            $table->json('properties')->nullable()->after('ip_address');
            $table->index(['subject_type', 'subject_id', 'created_at'], 'activity_logs_subject_created_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activity_logs', function (Blueprint $table): void {
            $table->dropIndex('activity_logs_subject_created_index');
            $table->dropColumn('properties');
        });
    }
};
