<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$email = 'admin@siperah.com';
$password = 'password123';

echo "Checking user: $email\n";
$user = User::where('email', $email)->first();

if (!$user) {
    echo "User NOT FOUND.\n";
    exit(1);
}

echo "User found. ID: " . $user->iduser . "\n";
echo "Current Hash: " . $user->password . "\n";

if (Hash::check($password, $user->password)) {
    echo "SUCCESS: Password matches '$password'.\n";
} else {
    echo "FAIL: Password does NOT match '$password'.\n";
    echo "Resetting password to '$password'...\n";
    
    $user->password = Hash::make($password);
    $user->save();
    
    echo "Password RESET successful.\n";
}
