<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contract_addendums', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('contract_id');

            $table->string('addendum_number')->unique();
            $table->date('addendum_date');
            $table->text('description')->nullable();
            $table->decimal('additional_value', 15, 2)->default(0);

            $table->timestamps();

            $table->foreign('contract_id')
                  ->references('id')
                  ->on('contracts')
                  ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contract_addendums');
    }
};