<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
        });

        Schema::table('contract_addendums', function (Blueprint $table) {
            $table->decimal('value_before', 15, 2)->nullable();
            $table->decimal('value_after', 15, 2)->nullable();
            $table->date('new_end_date')->nullable();
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
        });

        Schema::table('project_assignments', function (Blueprint $table) {
            $table->string('status')->default('active');
        });

        Schema::table('project_tasks', function (Blueprint $table) {
            $table->date('start_date')->nullable();
            $table->string('priority')->default('medium');
        });
    }

    public function down(): void
    {
        Schema::table('project_tasks', function (Blueprint $table) {
            $table->dropColumn(['start_date', 'priority']);
        });

        Schema::table('project_assignments', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('contract_addendums', function (Blueprint $table) {
            $table->dropColumn([
                'value_before',
                'value_after',
                'new_end_date',
                'file_path',
                'file_name',
            ]);
        });

        Schema::table('contracts', function (Blueprint $table) {
            $table->dropColumn(['file_path', 'file_name']);
        });
    }
};
