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

        Schema::table('activity_logs', function (Blueprint $table): void {
            if (! Schema::hasColumn('activity_logs', 'description')) {
                $table->text('description')->nullable();
            }

            if (! Schema::hasColumn('activity_logs', 'subject_type')) {
                $table->string('subject_type')->nullable();
            }

            if (! Schema::hasColumn('activity_logs', 'subject_id')) {
                $table->unsignedBigInteger('subject_id')->nullable();
            }

            if (! Schema::hasColumn('activity_logs', 'ip_address')) {
                $table->string('ip_address')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $columns = ['description', 'subject_type', 'subject_id', 'ip_address'];
        $columns = array_values(array_filter(
            $columns,
            fn (string $column): bool => Schema::hasColumn('activity_logs', $column)
        ));

        if ($columns !== []) {
            Schema::table('activity_logs', function (Blueprint $table) use ($columns): void {
                $table->dropColumn($columns);
            });
        }

        if (Schema::hasColumn('activity_logs', 'activity') && ! Schema::hasColumn('activity_logs', 'aktivitas')) {
            Schema::table('activity_logs', function (Blueprint $table): void {
                $table->renameColumn('activity', 'aktivitas');
            });
        }
    }
};
