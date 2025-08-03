<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer le rôle admin_general s'il n'existe pas
        $adminRole = Role::firstOrCreate(
            ['nom' => 'admin_general'],
            [
                'libelle' => 'Administrateur Général',
                'description' => 'Accès complet à tous les niveaux, peut créer piliers et objectifs stratégiques',
                'permissions' => ['all'],
                'actif' => true,
            ]
        );

        // Créer l'utilisateur administrateur
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@pilotage-strategique.com'],
            [
                'name' => 'Administrateur Général',
                'password' => Hash::make('password123'),
                'role_id' => $adminRole->id,
            ]
        );

        $this->command->info('Utilisateur administrateur créé avec succès !');
        $this->command->info('Email: admin@pilotage-strategique.com');
        $this->command->info('Mot de passe: password123');
    }
}
