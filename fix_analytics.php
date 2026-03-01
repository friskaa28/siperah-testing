<?php
// Script ini digunakan untuk memperbaiki role akun analytics di server
// Letakkan file ini di root directory Laravel (sejajar dengan artisan)
// Jalankan via browser: domainanda.com/fix_analytics.php
// Atau via terminal: php fix_analytics.php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\DB;

echo "<pre>";
echo "--- Diagnostic & Fix Account Analytics ---\n\n";

// 1. Cek User
$email = 'analytics@siperah.com';
$user = User::where('email', $email)->first();

if (!$user) {
    echo "[ERROR] User dengan email $email tidak ditemukan di database.\n";
    echo "Saran: Jalankan seeder dengan command: php artisan db:seed --class=DatabaseSyncSeeder\n";
} else {
    echo "[INFO] User ditemukan: " . $user->nama . "\n";
    echo "[INFO] Role saat ini di database: '" . $user->role . "'\n";

    // 2. Cek apakah role 'tim_analytics' sudah diizinkan di ENUM
    try {
        echo "[INFO] Mencoba update role ke 'tim_analytics'...\n";
        
        // Kita coba update langsung via query untuk memastikan melewati model validation jika ada
        DB::table('users')->where('iduser', $user->iduser)->update(['role' => 'tim_analytics']);
        
        $updatedUser = User::find($user->iduser);
        echo "[SUCCESS] Role sekarang adalah: '" . $updatedUser->role . "'\n";
        echo "[INFO] Harusnya sekarang akun sudah bisa login.\n";
        
    } catch (\Exception $e) {
        echo "[ERROR] Gagal update role: " . $e->getMessage() . "\n";
        echo "[ADVICE] Kemungkinan database belum menjalankan migration terbaru.\n";
        echo "[ADVICE] Jalankan command ini di terminal server: php artisan migrate\n";
    }
}

echo "\n--- Selesai ---\n";
echo "</pre>";
?>
