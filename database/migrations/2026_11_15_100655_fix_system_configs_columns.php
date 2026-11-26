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
        // Fix system_configs table - add missing columns
        Schema::table('system_configs', function (Blueprint $table) {
            if (!Schema::hasColumn('system_configs', 'config_key')) {
                $table->string('config_key')->unique()->after('id');
            }
            if (!Schema::hasColumn('system_configs', 'config_value')) {
                $table->text('config_value')->nullable()->after('config_key');
            }
            if (!Schema::hasColumn('system_configs', 'description')) {
                $table->text('description')->nullable()->after('config_value');
            }
            if (!Schema::hasColumn('system_configs', 'updated_by')) {
                $table->foreignId('updated_by')->nullable()->constrained('users')->after('description');
            }
        });

        // Fix users table - add missing columns
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('email');
            }
            if (!Schema::hasColumn('users', 'status')) {
                $table->enum('status', ['active', 'inactive'])->default('active')->after('phone');
            }
            if (!Schema::hasColumn('users', 'avatar')) {
                $table->string('avatar')->nullable()->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('system_configs', function (Blueprint $table) {
            $table->dropForeign(['updated_by']);
            $table->dropColumn(['config_key', 'config_value', 'description', 'updated_by']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'status', 'avatar']);
        });
    }
};
