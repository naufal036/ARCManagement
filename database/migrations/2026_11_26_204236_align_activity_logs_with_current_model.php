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
        if (Schema::hasColumn('activity_logs', 'aktivitas') && ! Schema::hasColumn('activity_logs', 'activity')) {
            Schema::table('activity_logs', function (Blueprint $table): void {
                $table->renameColumn('aktivitas', 'activity');
            });
        } elseif (! Schema::hasColumn('activity_logs', 'activity')) {
            Schema::table('activity_logs', function (Blueprint $table): void {
                $table->string('activity')->nullable();
            });
        }

        if (! Schema::hasColumn('activity_logs', 'description')) {
            Schema::table('activity_logs', function (Blueprint $table): void {
                $table->text('description')->nullable()->after('activity');
            });
        }

        if (! Schema::hasColumn('activity_logs', 'subject_type')) {
            Schema::table('activity_logs', function (Blueprint $table): void {
                $table->string('subject_type')->nullable()->after('description');
            });
        }

        if (! Schema::hasColumn('activity_logs', 'subject_id')) {
            Schema::table('activity_logs', function (Blueprint $table): void {
                $table->unsignedBigInteger('subject_id')->nullable()->after('subject_type');
            });
        }

        if (! Schema::hasColumn('activity_logs', 'ip_address')) {
            Schema::table('activity_logs', function (Blueprint $table): void {
                $table->string('ip_address')->nullable()->after('subject_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        /**
         * This is intentionally forward-only. Reversing it could rename or
         * remove columns that contain existing activity history.
         */
    }
};
