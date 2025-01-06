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
        Schema::create('sub_scores', function (Blueprint $table) {
            $table->id();
            $table->double('score');
            $table->foreignId('score_id')->constrained('scores')->onDelete('cascade');
            $table->foreignId('sub_criteria_id')->constrained('sub_criterias');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_scores');
    }
};
