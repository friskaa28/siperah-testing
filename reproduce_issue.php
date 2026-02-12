<?php
// reproduce_issue.php
use App\Models\Peternak;
use App\Models\ProduksiHarian;
use App\Models\SlipPembayaran;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// 1. Find Udin
$udin = Peternak::where('nama_peternak', 'like', '%Udin%')->first();
if (!$udin) {
    echo "Peternak Udin not found.\n";
    exit;
}

echo "Found Peternak: {$udin->nama_peternak} (ID: {$udin->idpeternak})\n";

// 2. Check current Gaji period
$slip = SlipPembayaran::where('idpeternak', $udin->idpeternak)->orderBy('tahun', 'desc')->orderBy('bulan', 'desc')->first();

if (!$slip) {
    echo "No slip found for Udin.\n";
    $bulan = now()->month;
    $tahun = now()->year;
} else {
    $bulan = $slip->bulan;
    $tahun = $slip->tahun;
    echo "Checking Slip for Bulan: $bulan, Tahun: $tahun\n";
    echo "Slip Total Susu: {$slip->jumlah_susu}\n";
}

// 3. Calculate using OLD Gaji logic (Shifted)
// Note: In controller logic:
// $endDate = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth()->startOfDay();
// $startDate = Carbon::createFromDate($tahun, $bulan, 1)->subMonth()->endOfMonth()->startOfDay();

$endDate = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth()->startOfDay();
$startDate = Carbon::createFromDate($tahun, $bulan, 1)->subMonth()->endOfMonth()->startOfDay();

echo "Gaji Logic Period: " . $startDate->format('Y-m-d H:i:s') . " to " . $endDate->format('Y-m-d H:i:s') . "\n";

$gajiTotal = ProduksiHarian::where('idpeternak', $udin->idpeternak)
    ->where(function($q) use ($startDate, $endDate) {
        $q->where(function($sq) use ($startDate) {
            $sq->whereDate('tanggal', $startDate)->where('waktu_setor', 'sore');
        })->orWhere(function($sq) use ($startDate, $endDate) {
            // Note: whereDate acts on the date part. if > startDate (Jan 31), it starts Feb 1.
            $sq->whereDate('tanggal', '>', $startDate)->whereDate('tanggal', '<', $endDate);
        })->orWhere(function($sq) use ($endDate) {
            $sq->whereDate('tanggal', $endDate)->where('waktu_setor', 'pagi');
        });
    })
    ->sum('jumlah_susu_liter');

echo "Calculated Gaji Total (Shifted): $gajiTotal\n";

// 4. Calculate using CALENDAR MONTH logic
$calendarStart = Carbon::createFromDate($tahun, $bulan, 1)->startOfDay();
$calendarEnd = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth()->endOfDay(); // Ensure end of day coverage

echo "Calendar Logic Period: " . $calendarStart->format('Y-m-d H:i:s') . " to " . $calendarEnd->format('Y-m-d H:i:s') . "\n";

$calendarTotal = ProduksiHarian::where('idpeternak', $udin->idpeternak)
    ->whereBetween('tanggal', [$calendarStart, $calendarEnd])
    ->sum('jumlah_susu_liter');

echo "Calculated Calendar Total: $calendarTotal\n";

// 5. Difference Analysis
$prevSore = ProduksiHarian::where('idpeternak', $udin->idpeternak)
    ->whereDate('tanggal', $startDate)->where('waktu_setor', 'sore')->sum('jumlah_susu_liter');

$currSore = ProduksiHarian::where('idpeternak', $udin->idpeternak)
    ->whereDate('tanggal', $endDate)->where('waktu_setor', 'sore')->sum('jumlah_susu_liter');

echo "Prev Month Last Day (Sore): $prevSore\n";
echo "Curr Month Last Day (Sore): $currSore\n";
