<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer un compte admin temporaire sans vérification d'email
        User::create([
            'name' => 'Admin Temporaire',
            'email' => 'admin@temp.com', // Email temporaire
            'password' => Hash::make('password123'), // Mot de passe temporaire
            'role' => 'admin',
            'numero_telephone' => '0600000000',
            'store_name' => 'Admin Store',
            'rib' => 'TEMP123456789',
            'email_verified_at' => now(), // Marquer comme vérifié
            'is_active' => true,
        ]);

        $this->command->info('Compte admin temporaire créé avec succès !');
        $this->command->info('Email: admin@temp.com');
        $this->command->info('Mot de passe: password123');
        $this->command->info('⚠️  ATTENTION: Ce compte est temporaire et doit être supprimé en production !');
    }
}
