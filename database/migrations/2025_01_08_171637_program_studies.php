<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('program_studies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
        
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('program_study_id')->nullable()->constrained('program_studies');
        });

        Schema::table('periods', function (Blueprint $table) {
            $table->foreignId('program_study_id')->nullable()->constrained('program_studies');
        });

        Schema::table('rubrics', function (Blueprint $table) {
            $table->foreignId('program_study_id')->nullable()->constrained('program_studies');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('program_studies');
    }
};
