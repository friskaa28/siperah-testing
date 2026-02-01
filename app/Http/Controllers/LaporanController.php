<?php

namespace App\Http\Controllers;

use App\Models\Peternak;
use App\Models\ProduksiHarian;
use App\Models\HargaSusuHistory;
use App\Models\Kasbon;
use App\Exports\PusatReportExport;
use App\Exports\SubPenampungReportExport;
use App\Exports\HarianReportExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LaporanController extends Controller
{
    public function pusatReport(Request $request)
    {
        $now = now();
        $bulan = $request->get('bulan', $now->month);
        $tahun = $request->get('tahun', $now->year);
        
        $currentMonth = Carbon::createFromDate($tahun, $bulan, 1);
        $prevMonth = $currentMonth->copy()->subMonth();

        $startDate = $request->get('start_date', $prevMonth->endOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', $currentMonth->endOfMonth()->format('Y-m-d'));

        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        // Aggregate by date and POS (Location) for all mitras
        $reportData = ProduksiHarian::where(function($q) use ($startDate, $endDate) {
            $q->where(function($sq) use ($startDate) {
                $sq->whereDate('tanggal', $startDate)->where('waktu_setor', 'sore');
            })->orWhere(function($sq) use ($startDate, $endDate) {
                $sq->whereDate('tanggal', '>', $startDate)->whereDate('tanggal', '<', $endDate);
            })->orWhere(function($sq) use ($endDate) {
                $sq->whereDate('tanggal', $endDate)->where('waktu_setor', 'pagi');
            });
        })
            ->join('peternak', 'produksi_harian.idpeternak', '=', 'peternak.idpeternak')
            ->selectRaw('tanggal, peternak.lokasi as pos, 
                SUM(jumlah_susu_liter) as total')
            ->groupBy('tanggal', 'peternak.lokasi')
            ->orderBy('tanggal', 'asc')
            ->orderBy('peternak.lokasi', 'asc')
            ->get();

        if ($request->get('export') === 'excel') {
            return Excel::download(new PusatReportExport($reportData), 'Laporan_Pusat_'.now()->format('YmdHis').'.xlsx');
        }

        return view('laporan.pusat', compact('reportData', 'startDate', 'endDate'));
    }

    public function rekapHarian(Request $request)
    {
        $bulan = $request->get('bulan', now()->month);
        $tahun = $request->get('tahun', now()->year);

        $daysInMonth = Carbon::createFromDate($tahun, $bulan, 1)->daysInMonth;
        
        $dailyTotals = ProduksiHarian::selectRaw('DAY(tanggal) as day, SUM(jumlah_susu_liter) as total')
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->groupBy('day')
            ->pluck('total', 'day')
            ->toArray();

        $monthlyTotal = array_sum($dailyTotals);

        return view('laporan.rekap_harian', compact('dailyTotals', 'monthlyTotal', 'bulan', 'tahun', 'daysInMonth'));
    }

    public function subPenampungReport(Request $request)
    {
        $now = now();
        $bulan = $request->get('bulan', $now->month);
        $tahun = $request->get('tahun', $now->year);
        
        $currentMonth = Carbon::createFromDate($tahun, $bulan, 1);
        $prevMonth = $currentMonth->copy()->subMonth();

        $startDate = $request->get('start_date', $prevMonth->endOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', $currentMonth->endOfMonth()->format('Y-m-d'));

        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        // Get production data grouped by sub-penampung
        $reportData = ProduksiHarian::where(function($q) use ($startDate, $endDate) {
            $q->where(function($sq) use ($startDate) {
                $sq->whereDate('tanggal', $startDate)->where('waktu_setor', 'sore');
            })->orWhere(function($sq) use ($startDate, $endDate) {
                $sq->whereDate('tanggal', '>', $startDate)->whereDate('tanggal', '<', $endDate);
            })->orWhere(function($sq) use ($endDate) {
                $sq->whereDate('tanggal', $endDate)->where('waktu_setor', 'pagi');
            });
        })
            ->whereHas('peternak', function($q) {
                $q->whereIn('status_mitra', ['sub_penampung', 'sub_penampung_tr', 'sub_penampung_p']);
            })
            ->with('peternak')
            ->selectRaw('idpeternak, 
                SUM(CASE WHEN waktu_setor = "pagi" THEN jumlah_susu_liter ELSE 0 END) as pagi,
                SUM(CASE WHEN waktu_setor = "sore" THEN jumlah_susu_liter ELSE 0 END) as sore,
                SUM(jumlah_susu_liter) as total')
            ->groupBy('idpeternak')
            ->get();

        return view('laporan.sub_penampung', compact('reportData', 'startDate', 'endDate'));
    }

    public function laporanData(Request $request)
    {
        $tab = $request->get('tab', 'pusat');
        $search = $request->get('search');
        $perPage = $request->get('per_page', 10);
        $now = now();
        
        // Default filters for Pusat & Sub-Penampung
    $startDate = $request->get('start_date');
    $endDate = $request->get('end_date');

    // Default filters for Harian
    $tanggal = $request->get('tanggal', $now->format('Y-m-d'));
    $bulan = $request->get('bulan', $now->month);
    $tahun = $request->get('tahun', $now->year);

    if (!$startDate || !$endDate) {
        $currentMonth = Carbon::createFromDate($tahun, $bulan, 1);
        $prevMonth = $currentMonth->copy()->subMonth();
        
        $startDate = $prevMonth->endOfMonth()->format('Y-m-d');
        $endDate = $currentMonth->endOfMonth()->format('Y-m-d');
    }

    $start = Carbon::parse($startDate)->startOfDay();
    $end = Carbon::parse($endDate)->endOfDay();

        $isPrinting = $request->get('print') === 'all';
        
        // 1. Laporan Pusat Data (Split by POS/Location - Inclusive)
        $pusatQuery = ProduksiHarian::where(function($q) use ($startDate, $endDate) {
            $q->where(function($sq) use ($startDate) {
                $sq->where('tanggal', $startDate)->where('waktu_setor', 'sore');
            })->orWhere(function($sq) use ($startDate, $endDate) {
                $sq->where('tanggal', '>', $startDate)->where('tanggal', '<', $endDate);
            })->orWhere(function($sq) use ($endDate) {
                $sq->where('tanggal', $endDate)->where('waktu_setor', 'pagi');
            });
        })
            ->join('peternak', 'produksi_harian.idpeternak', '=', 'peternak.idpeternak')
            ->when($search, function($q) use ($search) {
                return $q->where('peternak.lokasi', 'like', "%$search%");
            })
            ->selectRaw('tanggal, peternak.lokasi as pos, peternak.status_mitra,
                SUM(CASE WHEN waktu_setor = "pagi" THEN jumlah_susu_liter ELSE 0 END) as pagi,
                SUM(CASE WHEN waktu_setor = "sore" THEN jumlah_susu_liter ELSE 0 END) as sore,
                SUM(jumlah_susu_liter) as total')
            ->groupBy('tanggal', 'peternak.lokasi', 'peternak.status_mitra')
            ->orderBy('tanggal', 'asc')
            ->orderBy('peternak.lokasi', 'asc');

        // Calculate totals from ALL matching records before pagination
        $pusatAll = $pusatQuery->get();
        $gtPusat = $pusatAll->sum('total');
        $tkPusat = $pusatAll->where('status_mitra', 'peternak')->sum('total');
        $ttPusat = $pusatAll->whereIn('status_mitra', ['sub_penampung', 'sub_penampung_tr', 'sub_penampung_p'])->sum('total');

        $data['pusat'] = $isPrinting ? $pusatAll : $pusatQuery->paginate($perPage, ['*'], 'pusat_page')->withQueryString();

        // 2. Laporan Sub-Penampung Data
        $subQuery = ProduksiHarian::where(function($q) use ($startDate, $endDate) {
            $q->where(function($sq) use ($startDate) {
                $sq->where('tanggal', $startDate)->where('waktu_setor', 'sore');
            })->orWhere(function($sq) use ($startDate, $endDate) {
                $sq->where('tanggal', '>', $startDate)->where('tanggal', '<', $endDate);
            })->orWhere(function($sq) use ($endDate) {
                $sq->where('tanggal', $endDate)->where('waktu_setor', 'pagi');
            });
        })
            ->whereHas('peternak', function($q) use ($search, $request) {
                if ($request->status_mitra) {
                    $q->where('status_mitra', $request->status_mitra);
                } else {
                    $q->whereIn('status_mitra', ['sub_penampung', 'sub_penampung_tr', 'sub_penampung_p']);
                }
                if ($search) {
                    $q->where(function($sq) use ($search) {
                        $sq->where('nama_peternak', 'like', "%$search%")
                           ->orWhere('no_peternak', 'like', "%$search%");
                    });
                }
            })
            ->with('peternak')
            ->selectRaw('tanggal, idpeternak,
                SUM(CASE WHEN waktu_setor = "pagi" THEN jumlah_susu_liter ELSE 0 END) as pagi,
                SUM(CASE WHEN waktu_setor = "sore" THEN jumlah_susu_liter ELSE 0 END) as sore,
                SUM(jumlah_susu_liter) as total')
            ->groupBy('tanggal', 'idpeternak')
            ->orderBy('tanggal', 'desc');

        // Calculate specialized totals from ALL matching records before pagination
        $subAll = $subQuery->get();
        $gtSub = $subAll->sum('total');
        $totalTR = $subAll->where('peternak.status_mitra', 'sub_penampung_tr')->sum('total');
        $totalP = $subAll->where('peternak.status_mitra', 'sub_penampung_p')->sum('total');
        $totalSubLain = $subAll->where('peternak.status_mitra', 'sub_penampung')->sum('total');

        $data['sub_penampung'] = $isPrinting ? $subAll : $subQuery->paginate($perPage, ['*'], 'sub_page')->withQueryString();

        // 3. Laporan Harian (Real-time) Data
        $peternaksQuery = Peternak::orderBy('nama_peternak');
        if ($tab === 'harian') {
            if ($search) {
                $peternaksQuery->where(function($q) use ($search) {
                    $q->where('nama_peternak', 'like', "%$search%")
                      ->orWhere('no_peternak', 'like', "%$search%");
                });
            }
            if ($request->status_mitra) {
                $peternaksQuery->where('status_mitra', $request->status_mitra);
            }
        }
        $peternaks = $isPrinting ? $peternaksQuery->get() : $peternaksQuery->paginate($perPage, ['*'], 'harian_page')->withQueryString();
        
        // Eager load peternak to calculate grand total easily
    $produksisHarian = ProduksiHarian::with('peternak')->whereDate('tanggal', $tanggal)->get();
    $produksis = $produksisHarian->groupBy('idpeternak');
    $kasbons = Kasbon::whereDate('tanggal', $tanggal)->get()->groupBy('idpeternak');
        
        $currentPrice = HargaSusuHistory::getHargaAktif($tanggal);
        $gtHarRp = $produksisHarian->sum(function($p) use ($currentPrice) {
            return $p->jumlah_susu_liter * $currentPrice;
        }) - $kasbons->flatten()->sum('total_rupiah');

        // Extra for Rekap Bulanan Modal in Harian tab
        $daysInMonth = Carbon::createFromDate($tahun, $bulan, 1)->daysInMonth;
        $dailyTotals = ProduksiHarian::selectRaw('DAY(tanggal) as day, SUM(jumlah_susu_liter) as total')
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->groupBy('day')
            ->pluck('total', 'day')
            ->toArray();
        $monthlyTotal = array_sum($dailyTotals);

        if ($request->get('export') === 'excel') {
            if ($tab === 'pusat') {
                return Excel::download(new PusatReportExport($data['pusat']), 'Laporan_Pusat_'.now()->format('YmdHis').'.xlsx');
            } elseif ($tab === 'sub_penampung') {
                return Excel::download(new SubPenampungReportExport($data['sub_penampung']), 'Laporan_Sub_Penampung_'.now()->format('YmdHis').'.xlsx');
            } elseif ($tab === 'harian') {
                return Excel::download(new HarianReportExport($peternaks, $produksis, $tanggal), 'Monitoring_Harian_'.now()->format('YmdHis').'.xlsx');
            }
        }

        return view('laporan.data', compact(
            'tab', 'data', 'startDate', 'endDate', 'tanggal', 
            'peternaks', 'produksis', 'produksisHarian', 'kasbons', 'bulan', 'tahun', 
            'daysInMonth', 'dailyTotals', 'monthlyTotal', 'tkPusat', 'ttPusat', 'gtPusat',
            'gtSub', 'gtHarRp', 'currentPrice', 'isPrinting', 'totalTR', 'totalP', 'totalSubLain', 'search', 'perPage'
        ));
    }
}
