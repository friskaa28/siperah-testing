<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $c1 = new \App\Http\Controllers\DistribusiController();
    echo "DistribusiController instantiable.\n";
} catch (\Throwable $e) {
    echo "DistribusiController FAIL: " . $e->getMessage() . "\n";
}

try {
    $c2 = new \App\Http\Controllers\ProduksiController();
    echo "ProduksiController instantiable.\n";
} catch (\Throwable $e) {
    echo "ProduksiController FAIL: " . $e->getMessage() . "\n";
}

try {
    $c3 = new \App\Http\Controllers\DashboardController();
    echo "DashboardController instantiable.\n";
} catch (\Throwable $e) {
    echo "DashboardController FAIL: " . $e->getMessage() . "\n";
}
