<?php

namespace App\Http\Controllers;

use App\Models\Peternak;
use App\Models\ProduksiHarian;
use App\Models\SlipPembayaran;
use App\Models\HargaSusuHistory;
use App\Models\Kasbon;
use App\Models\ActivityLog;
use App\Exports\ProduksiTemplateExport;
use App\Imports\ProduksiImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GajiController extends Controller
{
    public function index(Request $request)
    {
        $bulan = $request->get('bulan', now()->month);
        $tahun = $request->get('tahun', now()->year);

        if (!SlipPembayaran::where('bulan', $bulan)->where('tahun', $tahun)->exists() && !$request->has('bulan')) {
            $lastSlip = SlipPembayaran::orderBy('tahun', 'desc')->orderBy('bulan', 'desc')->first();
            if ($lastSlip) {
                $bulan = $lastSlip->bulan;
                $tahun = $lastSlip->tahun;
            }
        }

        $slips = SlipPembayaran::with('peternak')
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->get();

        return view('gaji.index', compact('slips', 'bulan', 'tahun'));
    }

    public function downloadTemplate()
    {
        return Excel::download(new ProduksiTemplateExport, 'template_siperah.xlsx');
    }

    private function generateForMonth($bulan, $tahun)
    {
        $peternaks = Peternak::all();
        
        // Defined period: 14th of previous month to 13th of chosen month
        $endDate = Carbon::createFromDate($tahun, $bulan, 13)->endOfDay();
        $startDate = $endDate->copy()->subMonth()->day(14)->startOfDay();

        foreach ($peternaks as $p) {
            $totalLiters = ProduksiHarian::where('idpeternak', $p->idpeternak)
                ->whereBetween('tanggal', [$startDate, $endDate])
                ->sum('jumlah_susu_liter');

            if ($totalLiters <= 0) continue;

            $harga = HargaSusuHistory::getHargaAktif($endDate);
            $totalKasbon = Kasbon::where('idpeternak', $p->idpeternak)
                ->whereBetween('tanggal', [$startDate, $endDate])
                ->sum('total_rupiah');

            $totalGross = $totalLiters * $harga;
            $netSalary = $totalGross - $totalKasbon;

            SlipPembayaran::updateOrCreate(
                ['idpeternak' => $p->idpeternak, 'bulan' => $bulan, 'tahun' => $tahun],
                [
                    'jumlah_susu' => $totalLiters, 
                    'harga_satuan' => $harga, 
                    'potongan_pakan' => $totalKasbon, // Reusing existing column for kasbon total
                    'total_pembayaran' => $netSalary
                ]
            );
        }
    }

    public function generate(Request $request)
    {
        $this->generateForMonth($request->bulan, $request->tahun);
        return back()->with('success', "Slip pembayaran berhasil diperbarui.");
    }

    public function edit($idslip)
    {
        $slip = SlipPembayaran::with('peternak')->findOrFail($idslip);
        return view('gaji.edit', compact('slip'));
    }

    public function update(Request $request, $idslip)
    {
        $slip = SlipPembayaran::findOrFail($idslip);
        $data = $request->all();
        unset($data['total_potongan']);
        unset($data['sisa_pembayaran']);
        $slip->update($data);
        return redirect()->route('gaji.index', ['bulan' => $slip->bulan, 'tahun' => $slip->tahun])->with('success', 'Slip pembayaran berhasil diupdate.');
    }

    public function print($idslip)
    {
        $slip = SlipPembayaran::with('peternak')->findOrFail($idslip);
        $peternakId = $slip->peternak->no_peternak ?: 'MTR-' . str_pad($slip->peternak->idpeternak, 3, '0', STR_PAD_LEFT);
        $qrBase64 = $this->generateQrBase64(url()->full());
        
        // Log print activity
        ActivityLog::log(
            'PRINT_SALARY_SLIP',
            'Mencetak slip gaji untuk ' . $slip->peternak->nama_peternak . ' periode ' . date('F Y', mktime(0, 0, 0, $slip->bulan, 1, $slip->tahun)),
            $slip
        );
        
        return view('gaji.slip_print', compact('slip', 'peternakId', 'qrBase64'));
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

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,xls']);
        
        $import = new ProduksiImport;
        Excel::import($import, $request->file('file'));

        foreach ($import->monthsToRegen as $info) {
            $this->generateForMonth($info['m'], $info['y']);
        }

        if ($import->imported > 0) {
            $msg = "✅ Berhasil! {$import->imported} data masuk.";
            if (count($import->failedNames) > 0) {
                $msg .= " Namun, nama ini gagal: " . implode(', ', array_unique($import->failedNames));
            }
            $last = collect($import->monthsToRegen)->last();
            return redirect()->route('gaji.index', ['bulan' => $last['m'], 'tahun' => $last['y']])->with('success', $msg);
        }

        $error = "❌ Gagal! 0 data yang cocok.";
        if (count($import->failedNames) > 0) {
            $error .= " Nama-nama berikut tidak terdaftar di sistem: " . implode(', ', array_unique($import->failedNames));
        }
        if (count($import->unrecognizedDates) > 0) {
            $error .= " Tanggal bermasalah: " . implode(', ', array_unique($import->unrecognizedDates));
        }
        
        return back()->with('error', $error);
    }

    public function sign($idslip)
    {
        $slip = SlipPembayaran::findOrFail($idslip);
        
        if ($slip->isSigned()) {
            return back()->with('error', 'Slip ini sudah ditandatangani.');
        }

        // Generate a simple unique token for digital signature verification
        $token = hash('sha256', $slip->idslip . '|' . auth()->id() . '|' . now());

        $slip->update([
            'signed_by' => auth()->id(),
            'signed_at' => now(),
            'signature_token' => $token,
            'status' => 'dibayar' // Auto mark as paid when signed
        ]);

        // Audit Logging
        ActivityLog::log(
            'SIGN_SALARY_SLIP', 
            "Admin " . auth()->user()->nama . " menandatangani slip gaji Mitra: " . $slip->peternak->nama_peternak . " (ID: $slip->idslip)",
            $slip
        );

        return back()->with('success', 'Slip gaji berhasil ditandatangani secara digital.');
    }
}
