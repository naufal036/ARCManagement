<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_tasks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_assignment_id');

            $table->string('task_name');
            $table->text('task_description')->nullable();
            $table->date('due_date');
            $table->string('status')->default('pending');

            $table->timestamps();

            $table->foreign('project_assignment_id')
                ->references('id')
                ->on('project_assignments')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_tasks');
    }
};
