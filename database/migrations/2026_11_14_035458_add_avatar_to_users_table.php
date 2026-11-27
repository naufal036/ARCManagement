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
        if (Schema::hasColumn('users', 'avatar')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $column = $table->string('avatar')->nullable();

            if (Schema::hasColumn('users', 'status')) {
                $column->after('status');
            } elseif (Schema::hasColumn('users', 'phone')) {
                $column->after('phone');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('avatar');
        });
    }
};
