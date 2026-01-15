<?php

namespace App\Http\Controllers;

use App\Models\Peternak;
use App\Models\ProduksiHarian;
use App\Models\HargaSusuHistory;
use App\Models\Kasbon;
use App\Exports\PusatReportExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LaporanController extends Controller
{
    public function pusatReport(Request $request)
    {
        $now = now();
        $startDate = $request->get('start_date', $now->copy()->subMonth()->day(14)->format('Y-m-d'));
        $endDate = $request->get('end_date', $now->copy()->day(13)->format('Y-m-d'));

        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        // Aggregate by date
        $reportData = ProduksiHarian::selectRaw('tanggal, 
                SUM(CASE WHEN waktu_setor = "pagi" THEN jumlah_susu_liter ELSE 0 END) as pagi,
                SUM(CASE WHEN waktu_setor = "sore" THEN jumlah_susu_liter ELSE 0 END) as sore,
                SUM(jumlah_susu_liter) as total')
            ->whereBetween('tanggal', [$start, $end])
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'asc')
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
}
