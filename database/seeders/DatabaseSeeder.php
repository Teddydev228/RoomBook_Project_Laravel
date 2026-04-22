<?php

namespace Database\Seeders;

use App\Models\Equipment;
use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * Lance avec : php artisan db:seed
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        Booking::truncate();
        DB::table('booking_equipment')->truncate();
        Equipment::truncate();
        Room::truncate();
        User::truncate();
        Schema::enableForeignKeyConstraints();

        // ── Administrateur ────────────────────────────────────────
        User::create([
            'name'     => 'Admin Principal',
            'email'    => 'admin@roombook.fr',
            'password' => Hash::make('password'),
            'role'     => 'admin',
        ]);

        // ── Responsables ─────────────────────────────────────────
        User::create([
            'name'     => 'Dupont Marie',
            'email'    => 'responsable@roombook.fr',
            'password' => Hash::make('password'),
            'role'     => 'responsable',
        ]);

        User::create([
            'name'     => 'Martin Pierre',
            'email'    => 'martin.pierre@roombook.fr',
            'password' => Hash::make('password'),
            'role'     => 'responsable',
        ]);

        // ── Enseignants ──────────────────────────────────────────
        User::create([
            'name'     => 'Bernard Sophie',
            'email'    => 'enseignant@roombook.fr',
            'password' => Hash::make('password'),
            'role'     => 'enseignant',
        ]);

        User::create([
            'name'     => 'Leroy Thomas',
            'email'    => 'leroy.thomas@roombook.fr',
            'password' => Hash::make('password'),
            'role'     => 'enseignant',
        ]);

        User::create([
            'name'     => 'Moreau Julie',
            'email'    => 'moreau.julie@roombook.fr',
            'password' => Hash::make('password'),
            'role'     => 'enseignant',
        ]);

        Room::insert([
            [
                'name' => 'Salle A101',
                'capacity' => 35,
                'building' => 'Batiment A',
                'is_available' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Salle B204',
                'capacity' => 50,
                'building' => 'Batiment B',
                'is_available' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        Equipment::insert([
            [
                'name' => 'Videoprojecteur',
                'quantity' => 6,
                'is_available' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Micro sans fil',
                'quantity' => 20,
                'is_available' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $this->command->info('✅ Utilisateurs créés avec succès !');
        $this->command->table(
            ['Rôle', 'Email', 'Mot de passe'],
            [
                ['Admin',       'admin@roombook.fr',          'password'],
                ['Responsable', 'responsable@roombook.fr',    'password'],
                ['Responsable', 'martin.pierre@roombook.fr',  'password'],
                ['Enseignant',  'enseignant@roombook.fr',     'password'],
                ['Enseignant',  'leroy.thomas@roombook.fr',   'password'],
                ['Enseignant',  'moreau.julie@roombook.fr',   'password'],
            ]
        );

        $this->command->info('✅ Salles et materiels de base crees.');
    }
}
