<?php
// FILE: public/debug_server.php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<head><title>Debug Server SIPERAH</title><style>body{font-family:monospace;padding:2rem;background:#f0f0f0;}</style></head><body>";
echo "<div style='background:white;padding:2rem;border-radius:10px;box-shadow:0 2px 10px rgba(0,0,0,0.1);max-width:800px;margin:0 auto;'>";
echo "<h2 style='border-bottom:2px solid #333;padding-bottom:10px;'>🕵️ ANALISA DEBUG SERVER</h2>";

// 1. BOOTSTRAP LARAVEL
echo "<b>[1/4] Booting Laravel Framework...</b><br>";
try {
    require __DIR__ . '/../vendor/autoload.php';
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $response = $kernel->handle(
        $request = Illuminate\Http\Request::capture()
    );
    echo "<span style='color:green'>✅ Success! Laravel v" . app()->version() . "</span><br><br>";
} catch (\Exception $e) {
    die("<span style='color:red'>❌ Failed to boot Laravel: " . $e->getMessage() . "</span>");
}

use App\Models\Peternak;
use App\Models\ProduksiHarian;
use App\Models\HargaSusuHistory;
use App\Models\SlipPembayaran;
use Carbon\Carbon;

// 2. PARAMETERS
$bulan = 1; // Januari
$tahun = 2026;
$cutoffDate = Carbon::createFromDate($tahun, $bulan, 13)->endOfDay();
$startDate = $cutoffDate->copy()->subMonth()->day(14)->startOfDay();

echo "<b>[2/4] Parameter Waktu:</b><br>";
echo "<ul>";
echo "<li>Bulan/Tahun Gaji: <b>Januari 2026</b></li>";
echo "<li>Cutoff Periode: {$cutoffDate->toDateTimeString()}</li>";
echo "<li>Start Periode: {$startDate->toDateTimeString()}</li>";
echo "</ul><br>";

// 3. CHECK HARGA
echo "<b>[3/4] Cek Master Harga Susu:</b><br>";
$hargaAktif = HargaSusuHistory::getHargaAktif($cutoffDate);
$latestHarga = HargaSusuHistory::orderBy('tanggal_berlaku', 'desc')->first();
$allHarga = HargaSusuHistory::orderBy('tanggal_berlaku', 'desc')->get();

echo "<div style='background:#eef;padding:10px;border-left:4px solid blue;'>";
echo "Harga yang dipakai sistem (getHargaAktif): <b style='font-size:1.2em'>" . ($hargaAktif > 0 ? "Rp " . number_format($hargaAktif) : "<span style='color:red'>Rp 0 (MASALAH DISINI!)</span>") . "</b><br>";
echo "Ref: Harga entry terakhir di DB: Rp " . ($latestHarga ? number_format($latestHarga->harga) . " (Tgl: {$latestHarga->tanggal_berlaku->format('d M Y')})" : "TIDAK ADA") . "<br>";
echo "</div>";

if ($hargaAktif == 0) {
    echo "<br><b style='color:red'>⚠️ DIAGNOSA: Harga 0 karena tidak ada harga yang tanggal berlakunya <= {$cutoffDate->format('d M Y')}</b><br>";
    echo "Daftar semua harga di database:<br>";
    if ($allHarga->count() > 0) {
        echo "<ul>";
        foreach ($allHarga as $h) {
            $isValid = $h->tanggal_berlaku <= $cutoffDate;
            echo "<li style='" . ($isValid ? 'color:green' : 'color:red') . "'>";
            echo "Tgl: {$h->tanggal_berlaku->format('d M Y')} - Rp " . number_format($h->harga);
            echo $isValid ? " (Valid)" : " (❌ Lewat Cutoff)";
            echo "</li>";
        }
        echo "</ul>";
    } else {
        echo "<i>(Tabel harga_susu_history KOSONG)</i>";
    }
}

// 4. CHECK PERHITUNGAN
echo "<br><b>[4/4] Simulasi Hitung Gaji (Sample 1 Peternak):</b><br>";
$peternak = Peternak::first();
if ($peternak) {
    echo "Peternak: <b>{$peternak->nama_peternak}</b><br>";
    
    $produksi = ProduksiHarian::where('idpeternak', $peternak->idpeternak)
        ->whereBetween('tanggal', [$startDate, $cutoffDate])
        ->get();
        
    $totalLiters = $produksi->sum('jumlah_susu_liter');
    $totalRupiah = $totalLiters * $hargaAktif;
    
    echo "Total Liter di periode ini: <b>{$totalLiters} Liter</b><br>";
    echo "Perhitungan: $totalLiters x $hargaAktif = <b>Rp " . number_format($totalRupiah) . "</b><br>";
    
    // Check existing Slip
    $slip = SlipPembayaran::where(['idpeternak' => $peternak->idpeternak, 'bulan' => $bulan, 'tahun' => $tahun])->first();
    if ($slip) {
        echo "<br>Status Slip di Database saat ini:<br>";
        echo "Total Susu: {$slip->jumlah_susu}<br>";
        echo "Harga Satuan: {$slip->harga_satuan}<br>";
        echo "Total Pembayaran: Rp " . number_format($slip->total_pembayaran) . "<br>";
        echo "Sisa Pembayaran: <b>Rp " . number_format($slip->sisa_pembayaran) . "</b><br>";
    } else {
        echo "<br>Belum ada data Slip Gaji tersimpan untuk periode ini.<br>";
    }
} else {
    echo "Tidak ada data Peternak untuk dites.";
}

echo "</div></body>";
