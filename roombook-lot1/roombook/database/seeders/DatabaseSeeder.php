<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * Lance avec : php artisan db:seed
     */
    public function run(): void
    {
        // Supprime les données existantes
        User::truncate();

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
    }
}
