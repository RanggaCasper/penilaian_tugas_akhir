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
        Schema::create('sub_evaluation_criterias', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->double('score');
            $table->foreignId('evaluation_criteria_id')->constrained('evaluation_criterias');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_evaluation_criteria');
    }
};
