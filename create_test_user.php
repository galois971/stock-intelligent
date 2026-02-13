<?php
require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

// Vérifier si l'utilisateur existe déjà
$existingUser = User::where('email', 'test@example.com')->first();

if ($existingUser) {
    echo "Utilisateur test existe déjà!\n";
    echo "Email: test@example.com\n";
    echo "Mot de passe: password123\n";
} else {
    // Créer le nouvel utilisateur
    User::create([
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => Hash::make('password123'),
        'email_verified_at' => now(),
    ]);

    echo "✓ Utilisateur de test créé avec succès!\n\n";
    echo "Identifiants de connexion:\n";
    echo "Email: test@example.com\n";
    echo "Mot de passe: password123\n";
}
