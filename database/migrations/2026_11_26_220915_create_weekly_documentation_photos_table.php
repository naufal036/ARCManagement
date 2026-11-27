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
        Schema::create('weekly_documentation_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('weekly_documentation_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('slot_index')->nullable();
            $table->string('description')->nullable();
            $table->string('path')->nullable();
            $table->boolean('is_mandatory')->default(false);
            $table->timestamps();
            $table->index(['weekly_documentation_id', 'is_mandatory']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weekly_documentation_photos');
    }
};
