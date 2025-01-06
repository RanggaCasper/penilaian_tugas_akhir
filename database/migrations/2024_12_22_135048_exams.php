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
        Schema::create('exams', function (Blueprint $table) {  
            $table->id();
            $table->date('exam_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('room');
            $table->boolean('is_editable')->default(false);
            $table->enum('type', ['proposal', 'final_project']);
            $table->foreignId('student_id')->constrained('users');
            $table->foreignId('primary_examiner_id')->constrained('users');
            $table->foreignId('secondary_examiner_id')->constrained('users');
            $table->foreignId('tertiary_examiner_id')->constrained('users');
            $table->foreignId('rubric_id')->constrained('rubrics');
            $table->timestamps();  
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};
