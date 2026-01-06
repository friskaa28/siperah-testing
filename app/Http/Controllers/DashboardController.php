<?php

namespace App\Http\Controllers;

use App\Models\ProduksiHarian;
use App\Models\BagiHasil;
use App\Models\Distribusi;
use App\Models\Notifikasi;
use App\Models\Peternak;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller as BaseController;

class DashboardController extends BaseController
{
    public function peternakDashboard()
    {
        $user = Auth::user();
        $peternak = $user->peternak;

        if (!$peternak) {
            return back()->withErrors(['error' => 'Profil peternak tidak ditemukan.']);
        }

        // Total pendapatan bulan ini (dari Bagi Hasil)
        $totalPendapatanBulanan = $peternak->getTotalPendapatanBulanan();

        // Target bulanan
        $targetBulanan = 50000000; // 50 juta
        $progressTarget = $totalPendapatanBulanan / $targetBulanan * 100;

        // Notifikasi (5 terbaru)
        $notifikasi = Notifikasi::where('iduser', $user->iduser)
            ->latest()
            ->limit(5)
            ->get();

        // Keuangan (10 transaksi terakhir)
        $keuangan = BagiHasil::whereHas('produksi', function ($q) use ($peternak) {
            $q->where('idpeternak', $peternak->idpeternak);
        })->latest()->limit(10)->get();

        // Chart data (4 minggu produksi)
        $produksiPerMinggu = $this->getProduksiPerMinggu($peternak->idpeternak);

        // Income history (last 6 months) for chart
        $incomeHistory = $this->getIncomeHistory($peternak->idpeternak);
        
        // Average income
        $averageIncome = count($incomeHistory) > 0 ? array_sum(array_column($incomeHistory, 'total')) / count($incomeHistory) : 0;

        return view('dashboard.peternak', [
            'totalPenjualanBulanIni' => $totalPendapatanBulanan,
            'progressTarget' => $progressTarget,
            'notifikasi' => $notifikasi,
            'keuangan' => $keuangan,
            'produksiPerMinggu' => $produksiPerMinggu,
            'incomeHistory' => $incomeHistory,
            'averageIncome' => $averageIncome,
            'peternak' => $peternak,
        ]);
    }

    public function pengelolaDashboard()
    {
        $user = Auth::user();

        if (!($user->isPengelola() || $user->isAdmin())) {
            return back()->withErrors(['error' => 'Anda tidak berhak mengakses halaman ini.']);
        }

        // KPI data
        $totalPeternak = Peternak::count();
        $totalProduksiBulanIni = ProduksiHarian::whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->sum('jumlah_susu_liter');
        $totalDistribusi = Distribusi::whereMonth('tanggal_kirim', now()->month)
            ->whereYear('tanggal_kirim', now()->year)
            ->count();
        $totalBagiHasil = BagiHasil::whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->sum('total_pendapatan');

        // Top 5 peternak
        $top5Peternak = Peternak::withCount('distribusi')
            ->orderBy('distribusi_count', 'desc')
            ->limit(5)
            ->get();

        // Bagi hasil breakdown (pie chart)
        $bagiHasilBreakdown = BagiHasil::whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->selectRaw('status, COUNT(*) as count, SUM(total_pendapatan) as total')
            ->groupBy('status')
            ->get();

        // Monthly Stats for Chart (last 12 months or current year)
        $monthlyStats = $this->getMonthlyComparison();

        return view('dashboard.pengelola', [
            'totalPeternak' => $totalPeternak,
            'totalProduksiBulanIni' => $totalProduksiBulanIni,
            'totalDistribusi' => $totalDistribusi,
            'totalBagiHasil' => $totalBagiHasil,
            'top5Peternak' => $top5Peternak,
            'bagiHasilBreakdown' => $bagiHasilBreakdown,
            'monthlyStats' => $monthlyStats,
        ]);
    }

    private function getMonthlyComparison()
    {
        $thisYear = now()->year;
        $lastYear = $thisYear - 1;
        $stats = [];
        
        for ($m = 1; $m <= 12; $m++) {
            $prodThis = ProduksiHarian::whereYear('tanggal', $thisYear)->whereMonth('tanggal', $m)->sum('jumlah_susu_liter');
            $prodLast = ProduksiHarian::whereYear('tanggal', $lastYear)->whereMonth('tanggal', $m)->sum('jumlah_susu_liter');
            
            $distThis = Distribusi::whereYear('tanggal_kirim', $thisYear)->whereMonth('tanggal_kirim', $m)->sum('volume');
            
            $stats[] = [
                'month' => date('M', mktime(0, 0, 0, $m, 1)),
                'produksi_this' => (float)$prodThis,
                'produksi_last' => (float)$prodLast,
                'distribusi' => (float)$distThis
            ];
        }
        return $stats;
    }

    private function getProduksiPerMinggu($idpeternak = null)
    {
        $data = [];
        for ($i = 3; $i >= 0; $i--) {
            $start = now()->subWeeks($i)->startOfWeek();
            $end = now()->subWeeks($i)->endOfWeek();

            $query = ProduksiHarian::whereBetween('tanggal', [$start, $end]);
            if ($idpeternak) {
                $query->where('idpeternak', $idpeternak);
            }

            $total = $query->sum('jumlah_susu_liter');
            $data[] = [
                'week' => "W" . $start->week,
                'total' => $total,
            ];
        }
        return $data;
    }

    private function getIncomeHistory($idpeternak)
    {
        $stats = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $month = $date->month;
            $year = $date->year;

            $total = BagiHasil::whereHas('produksi', function ($q) use ($idpeternak) {
                    $q->where('idpeternak', $idpeternak);
                })
                ->whereMonth('tanggal', $month)
                ->whereYear('tanggal', $year)
                ->sum('hasil_pemilik');

            $stats[] = [
                'month' => $date->translatedFormat('M'),
                'total' => (float)$total,
            ];
        }
        return $stats;
    }
}
