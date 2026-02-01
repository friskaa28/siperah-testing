<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Kasbon;
use Carbon\Carbon;

echo "Current Time: " . now() . "\n";

// Replicate Controller Logic
$currentMonth = now()->startOfMonth();
$prevMonth = $currentMonth->copy()->subMonth();
$startDate = $prevMonth->endOfMonth()->startOfDay();
$endDate = $currentMonth->endOfMonth()->startOfDay();

echo "Range Search: " . $startDate->toDateTimeString() . " TO " . $endDate->toDateTimeString() . "\n";

$count = Kasbon::whereBetween('tanggal', [$startDate, $endDate])->count();
$sum = Kasbon::whereBetween('tanggal', [$startDate, $endDate])->sum('total_rupiah');

echo "Found in Range: $count records. Total: $sum\n";

echo "\n--- Recent 10 Kasbon Records ---\n";
$recent = Kasbon::latest('tanggal')->take(10)->get();
foreach ($recent as $k) {
    echo "ID: {$k->idkasbon} | Tgl: {$k->tanggal->format('Y-m-d')} | Total: {$k->total_rupiah} | Item: {$k->nama_item}\n";
    
    // Check if inside range manually
    $isInside = $k->tanggal >= $startDate && $k->tanggal <= $endDate;
    echo "   -> Inside Range? " . ($isInside ? "YES" : "NO") . "\n";
}
