<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Roles;
use App\Services\SIONService;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        $sion = new SIONService();
        $response = $sion->getMahasiswa('20241', '40', '58302');

        foreach ($response as $mahasiswa) {
            User::create([
                'name' => $mahasiswa['nama'],
                'nim' => $mahasiswa['nim'],
                'email' => $mahasiswa['email'],
                'phone' => $mahasiswa['telepon'],
                'generation' => '20'.substr($mahasiswa['nim'], 0, 2),
                'role_id' => 4
            ]);
        }

        User::create([
            'nim' => '01',
            'name' => 'Super Admin',
            'password' => bcrypt('super123'),
            'phone' => '08312345678',
            'email' => 'super@example.com',
            'role_id' => Roles::where('name', 'Super')->first()->id
        ]);

        User::create([
            'nim' => '02',
            'name' => 'Admin',
            'password' => bcrypt('admin123'),
            'phone' => '08312345678',
            'email' => 'admin@example.com',
            'role_id' => Roles::where('name', 'Admin')->first()->id
        ]);

        User::create([
            'nim' => '03',
            'name' => 'Dosen',
            'password' => bcrypt('dosen123'),
            'phone' => '08312345678',
            'email' => 'dosen@example.com',
            'role_id' => Roles::where('name', 'Dosen')->first()->id
        ]);
    }
}
