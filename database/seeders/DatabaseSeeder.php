<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Role;
use App\Models\Generation;
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
        // $sion = new SIONService();
        // $response = $sion->getMahasiswa('20241', '40', '58302');

        // foreach ($response as $mahasiswa) {  
        //     $gen = '20' . substr($mahasiswa['identity'], 0, 2);  

        //     $generation = Generation::firstOrCreate(  
        //         ['name' => $gen],
        //     );

        //     User::create([  
        //         'name' => $mahasiswa['nama'],  
        //         'identity' => $mahasiswa['identity'],  
        //         'email' => $mahasiswa['email'],  
        //         'phone' => $mahasiswa['telepon'],  
        //         'generation_id' => $generation->id,
        //         'role_id' => 4,
        //         'password' => bcrypt($mahasiswa['email']),
        //     ]);  
        // }  

        Generation::create([
            'name' => '2022',
        ]);

        User::create([
            'identity' => '01',
            'name' => 'Super Admin',
            'password' => bcrypt('super123'),
            'phone' => '08312345678',
            'email' => 'super@example.com',
            'role_id' => Role::where('name', 'Super')->first()->id
        ]);

        User::create([
            'identity' => '02',
            'name' => 'Admin',
            'password' => bcrypt('admin123'),
            'phone' => '08312345678',
            'email' => 'admin@example.com',
            'role_id' => Role::where('name', 'Admin')->first()->id
        ]);

        User::create([
            'identity' => '03',
            'name' => 'Dosen',
            'password' => bcrypt('dosen123'),
            'phone' => '08312345678',
            'email' => 'dosen@example.com',
            'role_id' => Role::where('name', 'Lecturer')->first()->id
        ]);

        User::create([
            'identity' => '04',
            'name' => 'student',
            'password' => bcrypt('student123'),
            'phone' => '08312345678',
            'email' => 'student@example.com',
            'generation_id' =>  Generation::where('name', '2022')->first()->id,
            'role_id' => Role::where('name', 'Student')->first()->id
        ]);
    }
}
