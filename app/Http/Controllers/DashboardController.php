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

        // --- NEW: Data for Dashboard Charts ---

        // 1. Daily Production (Day by Day) in current period
        $dailyProduction = [];
        $periodRange = \Carbon\CarbonPeriod::create($startDate, $endDate);
        
        // Pre-fetch production data to avoid N+1
        $productionData = ProduksiHarian::where('idpeternak', $peternak->idpeternak)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->get()
            ->groupBy(function($date) {
                return \Carbon\Carbon::parse($date->tanggal)->format('Y-m-d');
            });

        foreach ($periodRange as $date) {
            $dateKey = $date->format('Y-m-d');
            $dayLabel = $date->format('d M');
            
            $totalDay = 0;
            if (isset($productionData[$dateKey])) {
                $totalDay = $productionData[$dateKey]->sum('jumlah_susu_liter');
            }

            $dailyProduction['labels'][] = $dayLabel;
            $dailyProduction['data'][] = $totalDay;
        }

        // 2. Monthly Production (Month to Month) in current year
        $monthlyProduction = [];
        $currentYear = now()->year;
        
        for ($m = 1; $m <= 12; $m++) {
            $monthStart = Carbon::create($currentYear, $m, 1)->startOfMonth();
            $monthEnd = Carbon::create($currentYear, $m, 1)->endOfMonth();
            
            // Adjust query logic to match how 'monthly' is defined (simple sum per calendar month)
            $monthTotal = ProduksiHarian::where('idpeternak', $peternak->idpeternak)
                ->whereMonth('tanggal', $m)
                ->whereYear('tanggal', $currentYear)
                ->sum('jumlah_susu_liter');

            $monthlyProduction['labels'][] = $monthStart->translatedFormat('M');
            $monthlyProduction['data'][] = (float)$monthTotal;
        }

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
            'dailyProduction' => $dailyProduction,
            'monthlyProduction' => $monthlyProduction,
        ]);
    }

    public function pengelolaDashboard(\Illuminate\Http\Request $request)
    {
        $user = Auth::user();

        if (!($user->isPengelola() || $user->isAdmin())) {
            return back()->withErrors(['error' => 'Anda tidak berhak mengakses halaman ini.']);
        }

        // Year Filter
        $selectedYear = $request->input('year', now()->year);
        $availableYears = range(now()->year, now()->year - 4);

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
        // Top 5 Peternak (by Volume Susu in Selected Year)
        $top5Peternak = Peternak::withSum(['produksi' => function($q) use ($selectedYear) {
            $q->whereYear('tanggal', $selectedYear);
        }], 'jumlah_susu_liter')
        ->orderByDesc('produksi_sum_jumlah_susu_liter')
        ->take(5)
        ->get();

        // Bagi hasil breakdown (pie chart)
        $bagiHasilBreakdown = BagiHasil::whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->selectRaw('status, COUNT(*) as count, SUM(total_pendapatan) as total')
            ->groupBy('status')
            ->get();

        // Monthly Stats for Chart (Selected Year)
        $monthlyStats = $this->getMonthlyStats($selectedYear);

        // Latest Notifications
        $notifikasi = Notifikasi::where('iduser', $user->iduser)
            ->latest()
            ->limit(5)
            ->get();

        // Active Milk Price
        $currentPrice = HargaSusuHistory::getHargaAktif();

        // Widget Metrics (Today's Data)
        $todayLiter = ProduksiHarian::whereDate('tanggal', now())->sum('jumlah_susu_liter');
        $todayKasbon = Kasbon::whereDate('tanggal', now())->sum('total_rupiah');
        $totalLogistik = \App\Models\KatalogLogistik::count();

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
            'selectedYear' => $selectedYear,
            'availableYears' => $availableYears,
            'todayLiter' => $todayLiter,
            'todayKasbon' => $todayKasbon,
            'totalLogistik' => $totalLogistik,
        ]);
    }

    private function getMonthlyStats($year)
    {
        $stats = [];
        
        for ($m = 1; $m <= 12; $m++) {
            $query = ProduksiHarian::whereYear('tanggal', $year)->whereMonth('tanggal', $m);
            
            $prodTotal = $query->sum('jumlah_susu_liter');
            $activePeternak = $query->distinct('idpeternak')->count('idpeternak');
            
            $stats[] = [
                'month' => date('M', mktime(0, 0, 0, $m, 1)),
                'produksi' => (float)$prodTotal,
                'active_peternak' => $activePeternak,
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
