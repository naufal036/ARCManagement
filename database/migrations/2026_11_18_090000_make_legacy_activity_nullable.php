<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Allow new application writes to use `activity` while retaining old values in `aktivitas`.
     */
    public function up(): void
    {
        if (Schema::hasTable('activity_logs') && Schema::hasColumn('activity_logs', 'aktivitas')) {
            Schema::table('activity_logs', function (Blueprint $table): void {
                $table->string('aktivitas')->nullable()->change();
            });
        }
    }

    /**
     * Keep the legacy column nullable so future activity records remain compatible.
     */
    public function down(): void
    {
        // Intentionally keep this compatibility change to avoid blocking or losing log data.
    }
};
