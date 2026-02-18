<?php
// Create test users directly
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

$users = [
    ['email' => 'admin@example.com', 'name' => 'Admin User'],
    ['email' => 'gestionnaire@example.com', 'name' => 'Gestionnaire User'],
    ['email' => 'observateur@example.com', 'name' => 'Observateur User'],
];

foreach ($users as $user) {
    User::updateOrCreate(
        ['email' => $user['email']],
        array_merge($user, [
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ])
    );
    echo "✓ Created/Updated: {$user['email']}\n";
}

echo "\nDone! Test users are ready.\n";
