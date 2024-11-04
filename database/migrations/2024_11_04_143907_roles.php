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
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        // Data default untuk roles
        DB::table('roles')->insert([
            ['name' => 'Super', 'created_at' => now()],
            ['name' => 'Admin', 'created_at' => now()],
            ['name' => 'Dosen', 'created_at' => now()],
            ['name' => 'Mahasiswa', 'created_at' => now()],
        ]);
        
        // Tambah relasi ke table users
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
