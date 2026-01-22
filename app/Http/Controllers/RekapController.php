<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Peternak;
use App\Models\ProduksiHarian;
use App\Models\Kasbon;
use Carbon\Carbon;

class RekapController extends Controller
{
    public function index(Request $request)
    {
        $tanggal = $request->input('tanggal', Carbon::today()->format('Y-m-d'));
        $bulan = $request->input('bulan', Carbon::parse($tanggal)->month);
        $tahun = $request->input('tahun', Carbon::parse($tanggal)->year);

        // Fetch peternaks with filters
        $peternaksQuery = Peternak::orderBy('nama_peternak');

        if ($request->status_mitra) {
            $peternaksQuery->where('status_mitra', $request->status_mitra);
        }

        if ($request->search) {
            $peternaksQuery->where(function($q) use ($request) {
                $q->where('nama_peternak', 'like', "%{$request->search}%")
                  ->orWhere('no_peternak', 'like', "%{$request->search}%");
            });
        }

        $peternaks = $peternaksQuery->get();
        
        // Fetch production for the date
        $produksis = ProduksiHarian::whereDate('tanggal', $tanggal)->get()->keyBy('idpeternak');
        
        // Fetch kasbon for the date
        $kasbons = Kasbon::whereDate('tanggal', $tanggal)->get()->groupBy('idpeternak');

        // Monthly Rekap Data for Modal
        $daysInMonth = Carbon::createFromDate($tahun, $bulan, 1)->daysInMonth;
        $dailyTotals = ProduksiHarian::selectRaw('DAY(tanggal) as day, SUM(jumlah_susu_liter) as total')
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->groupBy('day')
            ->pluck('total', 'day')
            ->toArray();
        $monthlyTotal = array_sum($dailyTotals);

        return view('rekap.index', compact('peternaks', 'produksis', 'kasbons', 'tanggal', 'bulan', 'tahun', 'daysInMonth', 'dailyTotals', 'monthlyTotal'));
    }
}
