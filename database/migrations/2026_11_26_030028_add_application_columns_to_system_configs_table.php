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
        if (! Schema::hasColumn('system_configs', 'config_key')) {
            Schema::table('system_configs', function (Blueprint $table): void {
                $table->string('config_key')->nullable()->unique()->after('id');
            });
        }

        if (! Schema::hasColumn('system_configs', 'config_value')) {
            Schema::table('system_configs', function (Blueprint $table): void {
                $table->text('config_value')->nullable()->after('config_key');
            });
        }

        if (! Schema::hasColumn('system_configs', 'description')) {
            Schema::table('system_configs', function (Blueprint $table): void {
                $table->text('description')->nullable()->after('config_value');
            });
        }

        if (! Schema::hasColumn('system_configs', 'updated_by')) {
            Schema::table('system_configs', function (Blueprint $table): void {
                $table->foreignId('updated_by')
                    ->nullable()
                    ->after('description')
                    ->constrained('users')
                    ->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        /**
         * This is intentionally forward-only. Dropping these columns could
         * destroy application configuration or data migrated into them.
         */
    }
};
