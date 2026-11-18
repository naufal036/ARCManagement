<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add the columns expected by ActivityLog without removing legacy data.
     */
    public function up(): void
    {
        if (! Schema::hasTable('activity_logs')) {
            throw new RuntimeException('The activity_logs table must exist before it can be aligned.');
        }

        $addActivity = ! Schema::hasColumn('activity_logs', 'activity');
        $addDescription = ! Schema::hasColumn('activity_logs', 'description');
        $addSubjectType = ! Schema::hasColumn('activity_logs', 'subject_type');
        $addSubjectId = ! Schema::hasColumn('activity_logs', 'subject_id');
        $addIpAddress = ! Schema::hasColumn('activity_logs', 'ip_address');

        if ($addActivity || $addDescription || $addSubjectType || $addSubjectId || $addIpAddress) {
            Schema::table('activity_logs', function (Blueprint $table) use (
                $addActivity,
                $addDescription,
                $addSubjectType,
                $addSubjectId,
                $addIpAddress
            ): void {
                if ($addActivity) {
                    $table->string('activity')->nullable();
                }

                if ($addDescription) {
                    $table->text('description')->nullable();
                }

                if ($addSubjectType) {
                    $table->string('subject_type')->nullable();
                }

                if ($addSubjectId) {
                    $table->unsignedBigInteger('subject_id')->nullable();
                }

                if ($addIpAddress) {
                    $table->string('ip_address')->nullable();
                }
            });
        }

        // Preserve and expose old log labels stored in the legacy `aktivitas` column.
        if (Schema::hasColumn('activity_logs', 'aktivitas')) {
            DB::table('activity_logs')
                ->whereNull('activity')
                ->update(['activity' => DB::raw('aktivitas')]);
        }
    }

    /**
     * Keep the additive columns so activity data written after deployment is not lost.
     */
    public function down(): void
    {
        // Intentionally left in place to avoid dropping audit log data.
    }
};
