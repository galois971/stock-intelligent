<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ResetAdminPasswordSeeder extends Seeder
{
    /**
     * Reset admin password for testing.
     *
     * Uses env RESET_ADMIN_EMAIL and RESET_ADMIN_PASSWORD if set.
     */
    public function run(): void
    {
        $email = env('RESET_ADMIN_EMAIL', 'admin@example.com');
        $password = env('RESET_ADMIN_PASSWORD', 'NouveauMotDePasse123!');

        $user = User::where('email', $email)->first();

        if ($user) {
            $user->password = Hash::make($password);
            $user->save();
            $this->command->info("Mot de passe pour {$email} réinitialisé.");
            $this->command->info("Valeur utilisée: RESET_ADMIN_PASSWORD ou 'NouveauMotDePasse123!'");
        } else {
            $this->command->warn("Utilisateur {$email} introuvable. Vérifie la base de données et les migrations.");
        }
    }
}
