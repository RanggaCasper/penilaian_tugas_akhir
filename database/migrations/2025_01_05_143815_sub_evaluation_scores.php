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
        Schema::create('sub_evaluation_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('score_id')->constrained('evaluation_scores');
            $table->foreignId('sub_evaluation_criteria_id')->constrained('sub_evaluation_criterias');
            $table->double('score');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_evaluation_scores');
    }
};
