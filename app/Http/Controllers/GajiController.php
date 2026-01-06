<?php

namespace App\Http\Controllers;

use App\Models\Peternak;
use App\Models\ProduksiHarian;
use App\Models\SlipPembayaran;
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
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=template_siperah.csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $peternaks = Peternak::all();
        $callback = function() use($peternaks) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Timestamp', 'Nama/No Peternak', 'Tanggal (YYYY-MM-DD)', 'Liter']);
            foreach ($peternaks as $p) {
                fputcsv($file, [now()->format('Y-m-d H:i:s'), $p->no_peternak ?: $p->nama_peternak, now()->format('Y-m-d'), '10.5']);
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }

    private function generateForMonth($bulan, $tahun)
    {
        $peternaks = Peternak::all();
        foreach ($peternaks as $p) {
            $totalLiters = ProduksiHarian::where('idpeternak', $p->idpeternak)
                ->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun)
                ->sum('jumlah_susu_liter');

            if ($totalLiters <= 0) continue;

            $lastDist = $p->distribusi()->orderBy('iddistribusi', 'desc')->first();
            $harga = $lastDist ? $lastDist->harga_per_liter : 7000;

            SlipPembayaran::updateOrCreate(
                ['idpeternak' => $p->idpeternak, 'bulan' => $bulan, 'tahun' => $tahun],
                ['jumlah_susu' => $totalLiters, 'harga_satuan' => $harga, 'total_pembayaran' => $totalLiters * $harga]
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
        return view('gaji.slip_print', compact('slip'));
    }

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|mimes:csv,txt']);
        $file = $request->file('file');
        $filePath = $file->getRealPath();
        
        $fileContent = file_get_contents($filePath);
        // Handle BOM (Byte Order Mark) from Excel CSVs
        $fileContent = str_replace("\xEF\xBB\xBF", '', $fileContent);
        file_put_contents($filePath, $fileContent);

        $firstLine = explode("\n", $fileContent)[0] ?? '';
        $delimiter = (strpos($firstLine, ';') !== false) ? ';' : ',';
        
        $handle = fopen($filePath, "r");
        fgetcsv($handle, 1000, $delimiter); // Skip header
        
        $imported = 0;
        $failedNames = [];
        $unrecognizedDates = [];
        $row = 1;
        $monthsToRegen = [];

        while (($data = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {
            $row++;
            if (!array_filter($data) || count($data) < 4) continue;
            
            $peternakID = trim($data[1] ?? '');
            $tanggalLabel = trim($data[2] ?? '');
            $liter = str_replace(',', '.', trim($data[3] ?? 0));

            if (empty($peternakID) || empty($tanggalLabel)) continue;

            $idTrim = strtolower(trim($peternakID));
            $peternak = Peternak::where('no_peternak', $peternakID)
                ->orWhere(DB::raw('LOWER(nama_peternak)'), $idTrim)
                ->orWhere(DB::raw('LOWER(nama_peternak)'), 'LIKE', '%' . $idTrim . '%')
                ->first();

            if ($peternak) {
                try {
                    $dt = Carbon::parse($tanggalLabel);
                    ProduksiHarian::updateOrCreate(
                        ['idpeternak' => $peternak->idpeternak, 'tanggal' => $dt->format('Y-m-d'), 'waktu_setor' => 'pagi'],
                        ['jumlah_susu_liter' => (float)$liter, 'biaya_pakan' => 0, 'biaya_tenaga' => 0, 'biaya_operasional' => 0]
                    );
                    $monthsToRegen[$dt->format('Y-m')] = ['m' => $dt->month, 'y' => $dt->year];
                    $imported++;
                } catch (\Exception $e) {
                    $unrecognizedDates[] = $tanggalLabel . " (Error: " . $e->getMessage() . ")";
                }
            } else {
                $failedNames[] = $peternakID;
            }
        }
        fclose($handle);

        foreach ($monthsToRegen as $info) {
            $this->generateForMonth($info['m'], $info['y']);
        }

        if ($imported > 0) {
            $msg = "✅ Berhasil! $imported data masuk.";
            if (count($failedNames) > 0) {
                $msg .= " Namun, nama ini gagal: " . implode(', ', array_unique($failedNames));
            }
            $last = collect($monthsToRegen)->last();
            return redirect()->route('gaji.index', ['bulan' => $last['m'], 'tahun' => $last['y']])->with('success', $msg);
        }

        $error = "❌ Gagal! 0 data yang cocok.";
        if (count($failedNames) > 0) {
            $error .= " Nama-nama berikut tidak terdaftar di sistem: " . implode(', ', array_unique($failedNames));
        }
        if (count($unrecognizedDates) > 0) {
            $error .= " Tanggal bermasalah: " . implode(', ', array_unique($unrecognizedDates));
        }
        
        return back()->with('error', $error);
    }
}
