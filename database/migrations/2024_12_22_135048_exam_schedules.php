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
        Schema::create('exam_schedules', function (Blueprint $table) {  
            $table->id();
            $table->date('exam_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('room');
            $table->boolean('status')->default(false);
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('primary_examiner_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('secondary_examiner_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('tertiary_examiner_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();  
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_schedule');
    }
};
