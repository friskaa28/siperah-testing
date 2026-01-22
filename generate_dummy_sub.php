<?php

use App\Models\Peternak;
use App\Models\ProduksiHarian;
use Carbon\Carbon;

// Bootstrap Laravel if running from CLI
if (!defined('LARAVEL_START')) {
    require __DIR__ . '/vendor/autoload.php';
    $app = require_once __DIR__ . '/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
}

echo "Generating dummy production data for Sub-Penampungs...\n";

$subPenampungs = Peternak::where('status_mitra', 'sub_penampung')->get();

if ($subPenampungs->isEmpty()) {
    echo "No Sub-Penampungs found. Please create some first.\n";
    exit;
}

$now = now();
$startDate = $now->copy()->subMonth()->day(14);
$endDate = $now->copy();

$count = 0;
for ($date = $startDate->copy(); $date <= $endDate; $date->addDay()) {
    foreach ($subPenampungs as $sp) {
        // Morning deposit
        ProduksiHarian::updateOrCreate(
            ['idpeternak' => $sp->idpeternak, 'tanggal' => $date->format('Y-m-d'), 'waktu_setor' => 'pagi'],
            ['jumlah_susu_liter' => rand(50, 200) + (rand(0, 99) / 100)]
        );
        
        // Afternoon deposit
        ProduksiHarian::updateOrCreate(
            ['idpeternak' => $sp->idpeternak, 'tanggal' => $date->format('Y-m-d'), 'waktu_setor' => 'sore'],
            ['jumlah_susu_liter' => rand(40, 180) + (rand(0, 99) / 100)]
        );
        
        $count += 2;
    }
}

echo "Done! Generated $count production records for " . $subPenampungs->count() . " sub-penampungs.\n";
