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
    public function peternakDashboard(\Illuminate\Http\Request $request)
    {
        $user = Auth::user();
        $peternak = $user->peternak;

        if (!$peternak) {
            return back()->withErrors(['error' => 'Profil peternak tidak ditemukan.']);
        }

        // Check if user provided custom date range
        $customStartDate = $request->input('start_date');
        $customEndDate = $request->input('end_date');

        if ($customStartDate && $customEndDate) {
            // Use custom dates from filter
            $startDate = Carbon::parse($customStartDate)->startOfDay();
            $endDate = Carbon::parse($customEndDate)->startOfDay();
        } else {
            // Default: Session-based current month
            $currentMonth = now()->startOfMonth();
            $prevMonth = $currentMonth->copy()->subMonth();
            $startDate = $prevMonth->endOfMonth()->startOfDay();
            $endDate = $currentMonth->endOfMonth()->startOfDay();
        }

        // Total Liter in period (Session-based inclusive logic)
        $totalLiter = ProduksiHarian::where('idpeternak', $peternak->idpeternak)
            ->where(function($q) use ($startDate, $endDate) {
                $q->where(function($sq) use ($startDate) {
                    $sq->whereDate('tanggal', $startDate)->where('waktu_setor', 'sore');
                })->orWhere(function($sq) use ($startDate, $endDate) {
                    $sq->whereDate('tanggal', '>', $startDate)->whereDate('tanggal', '<', $endDate);
                })->orWhere(function($sq) use ($endDate) {
                    $sq->whereDate('tanggal', $endDate)->where('waktu_setor', 'pagi');
                });
            })
            ->sum('jumlah_susu_liter');

        // Current Milk Price
        $currentPrice = HargaSusuHistory::getHargaAktif();

        // Total Potongan in period
        $totalKasbon = Kasbon::where('idpeternak', $peternak->idpeternak)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->sum('total_rupiah');

        // Estimasi Gaji Bersih
        $estimasiGaji = ($totalLiter * $currentPrice) - $totalKasbon;

        // Announcements
        $pengumuman = Pengumuman::latest()->limit(3)->get();

        // Notifikasi (5 terbaru, skip bagi_hasil)
        $notifikasi = Notifikasi::where('iduser', $user->iduser)
            ->where('kategori', '!=', 'bagi_hasil')
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

        // Add a flag to view to show this is a Group view if sub-penampung
        $isGroupView = $user->isSubPenampung();

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
            'startDateStr' => $startDate->format('Y-m-d'),
            'endDateStr' => $endDate->format('Y-m-d'),
            'dailyProduction' => $dailyProduction,
            'monthlyProduction' => $monthlyProduction,
            'isGroupView' => $isGroupView,
        ]);
    }

    public function pengelolaDashboard(\Illuminate\Http\Request $request)
    {
        $user = Auth::user();

        if (!($user->isPengelola() || $user->isAdmin())) {
            return back()->withErrors(['error' => 'Anda tidak berhak mengakses halaman ini.']);
        }

        // Date Range Filter for Widgets
        // Date Range Filter for Widgets
        $customStartDate = $request->input('start_date');
        $customEndDate = $request->input('end_date');
        
        $reqYear = $request->input('year');
        $reqMonth = $request->input('month');

        if ($customStartDate && $customEndDate) {
            $startDate = Carbon::parse($customStartDate)->startOfDay();
            $endDate = Carbon::parse($customEndDate)->startOfDay();
        } elseif ($reqYear && $reqMonth) {
            // If Month/Year selected, use Session Logic for that month
            $currentMonth = Carbon::createFromDate($reqYear, $reqMonth, 1)->startOfMonth();
            $prevMonth = $currentMonth->copy()->subMonth();
            $startDate = $prevMonth->endOfMonth()->startOfDay(); // e.g. Jan 31
            $endDate = $currentMonth->endOfMonth()->startOfDay(); // e.g. Feb 28
        } else {
            // Default: 1 Jan 2026 to End of Current Month (User Request)
            $startDate = Carbon::create(2026, 01, 01)->startOfDay();
            $endDate = now()->endOfMonth();
        }

        // Year Filter (for charts)
        // Year Filter (for charts)
        $selectedYear = $request->input('year', now()->year);
        $selectedMonth = $request->input('month'); 
        $availableYears = range(now()->year, now()->year - 4);

        // --- SCOPING LOGIC ---
        $isSub = $user->isSubPenampung();
        $subId = $isSub ? $user->peternak->idpeternak : null;
        
        $peternakQuery = Peternak::query();
        $produksiQuery = ProduksiHarian::query();
        $kasbonQuery = Kasbon::query();
        $distribusiQuery = Distribusi::query();
        $bagiHasilQuery = BagiHasil::query();

        if ($isSub) {
            $peternakQuery->where(function($q) use ($subId) {
                $q->where('id_sub_penampung', $subId)
                  ->orWhere('idpeternak', $subId);
            });
            $produksiQuery->whereIn('idpeternak', function($q) use ($subId) {
                $q->select('idpeternak')->from('peternak')->where('id_sub_penampung', $subId)->orWhere('idpeternak', $subId);
            });
            $kasbonQuery->whereIn('idpeternak', function($q) use ($subId) {
                $q->select('idpeternak')->from('peternak')->where('id_sub_penampung', $subId)->orWhere('idpeternak', $subId);
            });
            // For distributions andbagi hasil, scoping might be more complex if they aren't directly linked to individual farmers in a way that's easy to query
            // Skipping Distribusi scoping for now as it's usually factory-level, but BagiHasil should be scoped
            $bagiHasilQuery->whereHas('produksi', function($q) use ($subId) {
                $q->where('idpeternak', $subId)->orWhereIn('idpeternak', function($sq) use ($subId) {
                    $sq->select('idpeternak')->from('peternak')->where('id_sub_penampung', $subId);
                });
            });
        } elseif ($user->koperasi_id) {
            $peternakQuery->where('koperasi_id', $user->koperasi_id);
            $produksiQuery->whereHas('peternak', function($q) use ($user) {
                $q->where('koperasi_id', $user->koperasi_id);
            });
            $kasbonQuery->whereHas('peternak', function($q) use ($user) {
                $q->where('koperasi_id', $user->koperasi_id);
            });
            $bagiHasilQuery->whereHas('produksi.peternak', function($q) use ($user) {
                $q->where('koperasi_id', $user->koperasi_id);
            });
        }

        $totalPeternak = $peternakQuery->count();
        $totalProduksiBulanIni = $produksiQuery->whereBetween('tanggal', [$startDate, $endDate])->sum('jumlah_susu_liter');
        $totalDistribusi = $distribusiQuery->whereBetween('tanggal_kirim', [$startDate, $endDate])->count();
        $totalBagiHasil = $bagiHasilQuery->whereBetween('tanggal', [$startDate, $endDate])->sum('total_pendapatan');

        // Top 5 peternak
        $top5Peternak = $peternakQuery->withSum(['produksi' => function($q) use ($selectedYear) {
            $q->whereYear('tanggal', $selectedYear);
        }], 'jumlah_susu_liter')
        ->orderByDesc('produksi_sum_jumlah_susu_liter')
        ->take(5)
        ->get();

        $monthlyStats = $this->getMonthlyStats($selectedYear, $selectedMonth, $subId);

        // Widget Metrics
        $periodLiter = $totalProduksiBulanIni;
        $periodKasbon = $kasbonQuery->whereBetween('tanggal', [$startDate, $endDate])->sum('total_rupiah');
        $totalLogistik = \App\Models\KatalogLogistik::count();

        // Latest Notifications
        $notifikasi = Notifikasi::where('iduser', $user->iduser)
            ->where('kategori', '!=', 'bagi_hasil')
            ->latest()
            ->limit(5)
            ->get();

        $currentPrice = HargaSusuHistory::getHargaAktif();

        return view('dashboard.pengelola', [
            'totalPeternak' => $totalPeternak,
            'totalProduksiBulanIni' => $totalProduksiBulanIni,
            'totalDistribusi' => $totalDistribusi,
            'totalBagiHasil' => $totalBagiHasil,
            'top5Peternak' => $top5Peternak,
            'bagiHasilBreakdown' => collect(), // Placeholder for now
            'monthlyStats' => $monthlyStats,
            'notifikasi' => $notifikasi,
            'currentPrice' => $currentPrice,
            'selectedYear' => $selectedYear,
            'availableYears' => $availableYears,
            'periodLiter' => $periodLiter,
            'periodKasbon' => $periodKasbon,
            'totalLogistik' => $totalLogistik,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'startDateStr' => $startDate->format('Y-m-d'),
            'endDateStr' => $endDate->format('Y-m-d'),
            'selectedMonth' => $selectedMonth,
        ]);
    }

    private function getMonthlyStats($year, $month = null, $subId = null)
    {
        $stats = [];
        
        if ($month) {
            // Year-over-year comparison for selected month
            $years = range($year, $year - 4);
            foreach ($years as $y) {
                $query = ProduksiHarian::whereYear('tanggal', $y)->whereMonth('tanggal', $month);
                
                if ($subId) {
                    $query->whereIn('idpeternak', function($q) use ($subId) {
                        $q->select('idpeternak')->from('peternak')->where('id_sub_penampung', $subId)->orWhere('idpeternak', $subId);
                    });
                }

                $prodTotal = $query->sum('jumlah_susu_liter');
                $activePeternak = $query->distinct('idpeternak')->count('idpeternak');
                
                $stats[] = [
                    'month' => (string)$y, // Use year as label
                    'produksi' => (float)$prodTotal,
                    'active_peternak' => $activePeternak,
                ];
            }
        } else {
            // All months for selected year
            for ($m = 1; $m <= 12; $m++) {
                $query = ProduksiHarian::whereYear('tanggal', $year)->whereMonth('tanggal', $m);
                
                if ($subId) {
                    $query->whereIn('idpeternak', function($q) use ($subId) {
                        $q->select('idpeternak')->from('peternak')->where('id_sub_penampung', $subId)->orWhere('idpeternak', $subId);
                    });
                }

                $prodTotal = $query->sum('jumlah_susu_liter');
                $activePeternak = $query->distinct('idpeternak')->count('idpeternak');
                
                $stats[] = [
                    'month' => date('M', mktime(0, 0, 0, $m, 1)),
                    'produksi' => (float)$prodTotal,
                    'active_peternak' => $activePeternak,
                ];
            }
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
