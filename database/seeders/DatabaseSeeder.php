<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Roles;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'username' => 'Admin',
            'password' => bcrypt('admin123'),
            'phone' => '08312345678',
            'email' => 'admin@example.com',
            'role_id' => Roles::where('name', 'Admin')->first()->id
        ]);
    }
}
