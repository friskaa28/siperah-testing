<?php

namespace App\Http\Controllers;

use App\Models\ProduksiHarian;
use App\Models\HargaSusuHistory;
use App\Models\Kasbon;
use App\Models\Peternak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PeternakLaporanController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $peternak = $user->peternak;

        if (!$peternak) {
            abort(403, 'Akses khusus peternak.');
        }

        $range = $request->get('range', '1');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        if ($range !== 'custom') {
            $now = now();
            if ($now->day <= 13) {
                $startDate = $now->copy()->subMonth()->day(14)->startOfDay();
                $endDate = $now->copy()->day(13)->endOfDay();
            } else {
                $startDate = $now->copy()->day(14)->startOfDay();
                $endDate = $now->copy()->addMonth()->day(13)->endOfDay();
            }
        } else {
            $startDate = \Carbon\Carbon::parse($startDate)->startOfDay();
            $endDate = \Carbon\Carbon::parse($endDate)->endOfDay();
        }

        // 1. Get Production Data
        $produksi = ProduksiHarian::where('idpeternak', $peternak->idpeternak)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->orderBy('tanggal', 'asc')
            ->get();

        // 2. Get Kasbon Data
        $kasbonHistory = Kasbon::where('idpeternak', $peternak->idpeternak)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->orderBy('tanggal', 'asc')
            ->get();

        // 3. Current Price for calculation
        $currentPrice = HargaSusuHistory::getHargaAktif();

        // 4. Summaries
        $totalLiter = $produksi->sum('jumlah_susu_liter');
        $totalKasbon = $kasbonHistory->sum('total_rupiah');
        $totalGross = $totalLiter * $currentPrice;
        $netSalary = $totalGross - $totalKasbon;

        // 5. QR and ID Fallback
        $peternakId = $peternak->no_peternak ?: 'MTR-' . str_pad($peternak->idpeternak, 3, '0', STR_PAD_LEFT);
        $qrBase64 = $this->generateQrBase64('SIPERAH-E-STATEMENT-' . $peternak->idpeternak . '-' . $startDate->format('Ymd') . '-' . $endDate->format('Ymd'));

        return view('laporan.pendapatan', compact('produksi', 'kasbonHistory', 'startDate', 'endDate', 'totalLiter', 'totalKasbon', 'totalGross', 'netSalary', 'currentPrice', 'range', 'peternakId', 'qrBase64'));
    }

    public function exportPdf(Request $request)
    {
        $user = Auth::user();
        $peternak = $user->peternak;

        if (!$peternak) abort(403);

        $range = $request->get('range', '1');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        if ($range !== 'custom') {
            $now = now();
            if ($now->day <= 13) {
                $startDate = $now->copy()->subMonth()->day(14)->startOfDay();
                $endDate = $now->copy()->day(13)->endOfDay();
            } else {
                $startDate = $now->copy()->day(14)->startOfDay();
                $endDate = $now->copy()->addMonth()->day(13)->endOfDay();
            }
        } else {
            $startDate = \Carbon\Carbon::parse($startDate)->startOfDay();
            $endDate = \Carbon\Carbon::parse($endDate)->endOfDay();
        }

        $produksi = ProduksiHarian::where('idpeternak', $peternak->idpeternak)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->orderBy('tanggal', 'asc')
            ->get();

        $kasbonHistory = Kasbon::where('idpeternak', $peternak->idpeternak)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->orderBy('tanggal', 'asc')
            ->get();

        $currentPrice = HargaSusuHistory::getHargaAktif();

        $totalLiter = $produksi->sum('jumlah_susu_liter');
        $totalKasbon = $kasbonHistory->sum('total_rupiah');
        $totalGross = $totalLiter * $currentPrice;
        $netSalary = $totalGross - $totalKasbon;

        $peternakId = $peternak->no_peternak ?: 'MTR-' . str_pad($peternak->idpeternak, 3, '0', STR_PAD_LEFT);
        $qrBase64 = $this->generateQrBase64('SIPERAH-E-STATEMENT-' . $peternak->idpeternak . '-' . $startDate->format('Ymd') . '-' . $endDate->format('Ymd'));

        // Log PDF export activity
        ActivityLog::log(
            'EXPORT_E_STATEMENT',
            'Export E-Statement PDF periode ' . $startDate->format('d/m/Y') . ' - ' . $endDate->format('d/m/Y'),
            $peternak
        );

        $pdf = app('dompdf.wrapper')->loadView('laporan.pendapatan_pdf', compact('produksi', 'kasbonHistory', 'startDate', 'endDate', 'peternak', 'totalLiter', 'totalKasbon', 'totalGross', 'netSalary', 'currentPrice', 'range', 'qrBase64', 'peternakId'));
        
        $filename = "E-Statement_" . str_replace(' ', '_', $peternak->nama_peternak) . "_" . $startDate->format('Ymd') . "-" . $endDate->format('Ymd') . ".pdf";
        return $pdf->download($filename);
    }

    private function generateQrBase64($data)
    {
        try {
            $url = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . urlencode($data);
            $imageData = file_get_contents($url);
            if ($imageData === false) return null;
            return 'data:image/png;base64,' . base64_encode($imageData);
        } catch (\Exception $e) {
            return null;
        }
    }
}
