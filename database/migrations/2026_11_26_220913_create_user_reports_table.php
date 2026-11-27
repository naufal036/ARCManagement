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
        Schema::create('user_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('proyek_id')->nullable()->constrained('proyek')->nullOnDelete();
            $table->string('title');
            $table->string('status')->default('In Progress');
            $table->string('priority')->default('Prioritas II');
            $table->date('due_date')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_completed')->default(false);
            $table->timestamps();
            $table->index(['user_id', 'status', 'due_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_reports');
    }
};
