<?php

namespace App\Http\Controllers;

use App\Models\ProduksiHarian;
use App\Models\BagiHasil;
use App\Models\Distribusi;
use App\Models\Notifikasi;
use App\Models\Peternak;
use App\Models\HargaSusuHistory;
use App\Models\Kasbon;
use App\Models\Pengumuman;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller as BaseController;
use Carbon\Carbon;

class DashboardController extends BaseController
{
    public function peternakDashboard()
    {
        $user = Auth::user();
        $peternak = $user->peternak;

        if (!$peternak) {
            return back()->withErrors(['error' => 'Profil peternak tidak ditemukan.']);
        }

        // Defined period: 14th of last month to 13th of this month (if today <= 13)
        // Or 14th of this month to 13th of next month (if today > 13)
        $now = now();
        if ($now->day <= 13) {
            $startDate = $now->copy()->subMonth()->day(14)->startOfDay();
            $endDate = $now->copy()->day(13)->endOfDay();
        } else {
            $startDate = $now->copy()->day(14)->startOfDay();
            $endDate = $now->copy()->addMonth()->day(13)->endOfDay();
        }

        // Total Liter in period
        $totalLiter = ProduksiHarian::where('idpeternak', $peternak->idpeternak)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->sum('jumlah_susu_liter');

        // Current Milk Price
        $currentPrice = HargaSusuHistory::getHargaAktif();

        // Total Kasbon in period
        $totalKasbon = Kasbon::where('idpeternak', $peternak->idpeternak)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->sum('total_rupiah');

        // Estimasi Gaji Bersih
        $estimasiGaji = ($totalLiter * $currentPrice) - $totalKasbon;

        // Announcements
        $pengumuman = Pengumuman::latest()->limit(3)->get();

        // Notifikasi (5 terbaru)
        $notifikasi = Notifikasi::where('iduser', $user->iduser)
            ->latest()
            ->limit(5)
            ->get();

        // Income history (last 6 months) for chart - keeping existing logic but maybe refactor later
        $incomeHistory = $this->getIncomeHistory($peternak->idpeternak);
        
        // Average income
        $averageIncome = count($incomeHistory) > 0 ? array_sum(array_column($incomeHistory, 'total')) / count($incomeHistory) : 0;

        return view('dashboard.peternak', [
            'totalLiter' => $totalLiter,
            'estimasiGaji' => $estimasiGaji,
            'totalKasbon' => $totalKasbon,
            'pengumuman' => $pengumuman,
            'currentPrice' => $currentPrice,
            'notifikasi' => $notifikasi,
            'incomeHistory' => $incomeHistory,
            'averageIncome' => $averageIncome,
            'peternak' => $peternak,
            'startDate' => $startDate,
            'endDate' => $endDate,
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

        // Latest Notifications
        $notifikasi = Notifikasi::where('iduser', $user->iduser)
            ->latest()
            ->limit(5)
            ->get();

        // Active Milk Price
        $currentPrice = HargaSusuHistory::getHargaAktif();

        return view('dashboard.pengelola', [
            'totalPeternak' => $totalPeternak,
            'totalProduksiBulanIni' => $totalProduksiBulanIni,
            'totalDistribusi' => $totalDistribusi,
            'totalBagiHasil' => $totalBagiHasil,
            'top5Peternak' => $top5Peternak,
            'bagiHasilBreakdown' => $bagiHasilBreakdown,
            'monthlyStats' => $monthlyStats,
            'notifikasi' => $notifikasi,
            'currentPrice' => $currentPrice,
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
            
            $stats[] = [
                'month' => date('M', mktime(0, 0, 0, $m, 1)),
                'produksi_this' => (float)$prodThis,
                'produksi_last' => (float)$prodLast,
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
