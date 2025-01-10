<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\ApiKey;
use App\Models\Generation;
use Illuminate\Support\Str;
use App\Models\ProgramStudy;
use App\Services\SIONService;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->firstOrCreate();
        $sion = new SIONService();

        $response = $sion->getProdi('40');
        foreach ($response as $prodi) {
            ProgramStudy::firstOrCreate(
                ['id' => (int) $prodi['kodeProdi']],
                ['name' => $prodi['namaProdi']]
            );
        }
    
        Generation::firstOrCreate(
            ['name' => '2022']
        );

        User::firstOrCreate(
            ['identity' => '01'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('super123'),
                'phone' => '08312345678',
                'email' => 'super@example.com',
                'role_id' => Role::where('name', 'Super')->first()->id
            ]
        );

        User::firstOrCreate(
            ['identity' => '02'],
            [
                'name' => 'Admin',
                'password' => bcrypt('admin123'),
                'phone' => '08312345678',
                'email' => 'admin@example.com',
                'role_id' => Role::where('name', 'Admin')->first()->id,
                'program_study_id' => ProgramStudy::where('name', 'Teknologi Rekayasa Perangkat Lunak')->first()->id
            ]
        );

        User::firstOrCreate(
            ['identity' => '03'],
            [
                'name' => 'Dosen',
                'password' => bcrypt('dosen123'),
                'phone' => '08312345678',
                'email' => 'dosen@example.com',
                'role_id' => Role::where('name', 'Lecturer')->first()->id,
                'program_study_id' => ProgramStudy::where('name', 'Teknologi Rekayasa Perangkat Lunak')->first()->id
            ]
        );

        User::firstOrCreate(
            ['identity' => '04'],
            [
                'name' => 'student',
                'password' => bcrypt('student123'),
                'phone' => '08312345678',
                'email' => 'student@example.com',
                'generation_id' => Generation::where('name', '2022')->first()->id,
                'role_id' => Role::where('name', 'Student')->first()->id,
                'program_study_id' => ProgramStudy::where('name', 'Teknologi Rekayasa Perangkat Lunak')->first()->id
            ]
        );

        User::firstOrCreate(
            ['identity' => '05'],
            [
                'name' => 'special',
                'password' => bcrypt('special123'),
                'phone' => '08312345678',
                'email' => 'special@example.com',
                'role_id' => Role::where('name', 'Special')->first()->id
            ]
        );

        ApiKey::firstOrCreate(
            ['user_id' => User::where('identity', '05')->first()->id],
            [
                'api_id' => Str::random(8),
                'api_key' => Str::random(64)
            ]
        );
    }
}
