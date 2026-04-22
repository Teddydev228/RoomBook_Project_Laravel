<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $password = bcrypt('password');

        User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => $password,
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Marie Martin',
            'email' => 'responsable@test.com',
            'password' => $password,
            'role' => 'responsable',
        ]);

        User::create([
            'name' => 'Jean Dupont',
            'email' => 'enseignant@test.com',
            'password' => $password,
            'role' => 'enseignant',
        ]);
    }
}
