<?php

namespace App\Http\Controllers;

use App\Models\BagiHasil;
use App\Models\SlipPembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class PeternakLaporanController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $peternak = $user->peternak;

        if (!$peternak) {
            abort(403, 'Akses khusus peternak.');
        }

        $bulan = $request->get('bulan', now()->month);
        $tahun = $request->get('tahun', now()->year);

        // Get Bagi Hasil history for the filtered month
        $bagiHasil = BagiHasil::whereHas('produksi', function ($q) use ($peternak) {
                $q->where('idpeternak', $peternak->idpeternak);
            })
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->orderBy('tanggal', 'desc')
            ->get();

        // Get Salary Slip (from Admin) if exists
        $slip = SlipPembayaran::where('idpeternak', $peternak->idpeternak)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->first();

        return view('laporan.pendapatan', compact('bagiHasil', 'slip', 'bulan', 'tahun'));
    }

    public function exportPdf(Request $request)
    {
        $user = Auth::user();
        $peternak = $user->peternak;

        if (!$peternak) abort(403);

        $bulan = $request->get('bulan', now()->month);
        $tahun = $request->get('tahun', now()->year);

        $bagiHasil = BagiHasil::whereHas('produksi', function ($q) use ($peternak) {
                $q->where('idpeternak', $peternak->idpeternak);
            })
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->orderBy('tanggal', 'asc')
            ->get();

        $slip = SlipPembayaran::where('idpeternak', $peternak->idpeternak)
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->first();

        $namaBulan = \Carbon\Carbon::create()->month($bulan)->translatedFormat('F');

        $pdf = Pdf::loadView('laporan.pendapatan_pdf', compact('bagiHasil', 'slip', 'bulan', 'tahun', 'peternak', 'namaBulan'));
        
        return $pdf->download("Slip_Gaji_{$peternak->no_peternak}_{$namaBulan}_{$tahun}.pdf");
    }
}
