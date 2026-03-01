<?php

namespace App\Http\Controllers;

use App\Models\Peternak;
use App\Models\ProduksiHarian;
use App\Models\SlipPembayaran;
use App\Models\HargaSusuHistory;
use App\Models\Kasbon;
use App\Models\ActivityLog;
use App\Exports\GajiTemplateExport;
use App\Imports\GajiImport;
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

        $perPage = $request->get('per_page', 10);
        $slipsQuery = SlipPembayaran::with('peternak')
            ->where('bulan', $bulan)
            ->where('tahun', $tahun);

        if (auth()->user()->isSubPenampung()) {
            $slipsQuery->whereHas('peternak', function($q) {
                $q->where('id_sub_penampung', auth()->user()->peternak->idpeternak)
                  ->orWhere('idpeternak', auth()->user()->peternak->idpeternak);
            });
        }

        if (auth()->user()->koperasi_id) {
            $slipsQuery->whereHas('peternak', function($q) {
                $q->where('koperasi_id', auth()->user()->koperasi_id);
            });
        }

        $slips = $slipsQuery->paginate($perPage)->withQueryString();

        return view('gaji.index', compact('slips', 'bulan', 'tahun', 'perPage'));
    }

    public function downloadTemplate()
    {
        return Excel::download(new GajiTemplateExport, 'template_manajemen_pembayaran.xlsx');
    }

    private function generateForMonth($bulan, $tahun)
    {
        $user = auth()->user();
        $peternaksQuery = Peternak::query();

        if ($user->isSubPenampung()) {
            $peternaksQuery->where(function($q) use ($user) {
                $q->where('id_sub_penampung', $user->peternak->idpeternak)
                  ->orWhere('idpeternak', $user->peternak->idpeternak);
            });
        }

        if ($user->koperasi_id) {
            $peternaksQuery->where('koperasi_id', $user->koperasi_id);
        }

        $peternaks = $peternaksQuery->get();
        
        // Defined period: Full Calendar Month (1st to End of Month)
        // Matches "Riwayat Setor Susu" logic
        $startDate = Carbon::createFromDate($tahun, $bulan, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth();

        foreach ($peternaks as $p) {
            $totalLiters = ProduksiHarian::where('idpeternak', $p->idpeternak)
                ->whereBetween('tanggal', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                ->sum('jumlah_susu_liter');

            if ($totalLiters <= 0) continue;

            $harga = HargaSusuHistory::getHargaAktif($endDate);
            
            // Initial deduction from Kasbon (only total for reference if needed, 
            // but we'll use syncPotongan for detailed breakdown)
            $totalKasbon = Kasbon::where('idpeternak', $p->idpeternak)
                ->whereBetween('tanggal', [$startDate, $endDate])
                ->sum('total_rupiah');

            $totalGross = $totalLiters * $harga;

            SlipPembayaran::updateOrCreate(
                ['idpeternak' => $p->idpeternak, 'bulan' => $bulan, 'tahun' => $tahun],
                [
                    'jumlah_susu' => $totalLiters, 
                    'harga_satuan' => $harga, 
                    'total_pembayaran' => $totalGross,
                    // We don't overwrite detailed deductions here if they already exist
                ]
            );

            // Trigger sync for each generated slip
            $slip = SlipPembayaran::where(['idpeternak' => $p->idpeternak, 'bulan' => $bulan, 'tahun' => $tahun])->first();
            if ($slip) $this->syncPotongan($slip->idslip);
        }
    }

    public function syncPotongan($idslip)
    {
        $slip = SlipPembayaran::findOrFail($idslip);
        // Defined period: Full Calendar Month (1st to End of Month)
        // Matches "Riwayat Setor Susu" logic
        $startDate = Carbon::createFromDate($slip->tahun, $slip->bulan, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($slip->tahun, $slip->bulan, 1)->endOfMonth();

        $kasbons = Kasbon::where('idpeternak', $slip->idpeternak)
            ->whereBetween('tanggal', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get();

        $mapping = [
            'potongan_shr' => '/shr/i',
            'potongan_hutang_bl_ll' => '/hut.*bl.*ll/i',
            'potongan_pakan_a' => '/pakan\s*a/i',
            'potongan_pakan_b' => '/pakan\s*b(?!\s*\(2\))/i',
            'potongan_vitamix' => '/(vitami|vetami)/i', // Match Vitamin or Vitamix or Vetamix
            'potongan_konsentrat' => '/konsentrat/i',
            'potongan_skim' => '/skim/i',
            'potongan_ib_keswan' => '/(ib|keswan)/i',
            'potongan_susu_a' => '/susu\s*a/i',
            'potongan_kas_bon' => '/kas\s*bon/i',
            'potongan_pakan_b_2' => '/pakan\s*b\s*\(2\)/i',
            'potongan_sp' => '/\bsp\b/i',
            'potongan_karpet' => '/karpet/i',
            'potongan_vaksin' => '/vaksin/i',
        ];

        $updates = [];
        foreach ($mapping as $column => $regex) {
            $sum = $kasbons->filter(function($k) use ($regex) {
                return preg_match($regex, $k->nama_item);
            })->sum('total_rupiah');
            $updates[$column] = $sum;
        }

        // Special case for Lain-Lain: everything not matched above
        $matchedIds = [];
        foreach ($mapping as $column => $regex) {
            $ids = $kasbons->filter(function($k) use ($regex) {
                return preg_match($regex, $k->nama_item);
            })->pluck('id')->toArray();
            $matchedIds = array_merge($matchedIds, $ids);
        }
        $otherSum = $kasbons->whereNotIn('id', array_unique($matchedIds))->sum('total_rupiah');
        $updates['potongan_lain_lain'] = $otherSum;

        $slip->update($updates);
        return $slip;
    }

    public function generate(Request $request)
    {
        $this->generateForMonth($request->bulan, $request->tahun);
        return back()->with('success', "Slip pembayaran berhasil diperbarui dan potongan disinkronkan.");
    }

    public function edit($idslip)
    {
        $slip = SlipPembayaran::with('peternak')->findOrFail($idslip);
        $currentHarga = HargaSusuHistory::getHargaAktif($slip->tanggal_bayar ?? now());
        return view('gaji.edit', compact('slip', 'currentHarga'));
    }

    public function update(Request $request, $idslip)
    {
        $slip = SlipPembayaran::findOrFail($idslip);
        $data = $request->except(['_token', '_method', 'total_potongan', 'sisa_pembayaran']);
        
        $slip->update($data);

        // Sync back to Kasbon history (Riwayat Potongan)
        $this->syncDeductionsToKasbon($slip->idpeternak, $data, $slip->tanggal_bayar ?? now(), $slip->idslip);

        return redirect()->route('gaji.index', ['bulan' => $slip->bulan, 'tahun' => $slip->tahun])->with('success', 'Data slip pembayaran berhasil disimpan dan disinkronkan ke riwayat potongan.');
    }

    public function print($idslip)
    {
        $slip = SlipPembayaran::with('peternak')->findOrFail($idslip);
        $peternakId = $slip->peternak->no_peternak ?: 'MTR-' . str_pad($slip->peternak->idpeternak, 3, '0', STR_PAD_LEFT);
        $qrBase64 = $this->generateQrBase64(url()->full());
        
        // Fetch detailed deductions for dynamic printing
        $startDate = Carbon::createFromDate($slip->tahun, $slip->bulan, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($slip->tahun, $slip->bulan, 1)->endOfMonth();

        $kasbons = Kasbon::where('idpeternak', $slip->idpeternak)
            ->whereBetween('tanggal', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get();

        // Group by item name and sum the amount
        $deductions = $kasbons->groupBy(function($item) {
            return strtoupper($item->nama_item);
        })->map(function ($row) {
            return $row->sum('total_rupiah');
        })->sortKeys();

        // Log print activity
        ActivityLog::log(
            'PRINT_SALARY_SLIP',
            'Mencetak slip pembayaran untuk ' . $slip->peternak->nama_peternak . ' periode ' . date('F Y', mktime(0, 0, 0, $slip->bulan, 1, $slip->tahun)),
            $slip
        );
        
        return view('gaji.slip_print', compact('slip', 'peternakId', 'qrBase64', 'deductions'));
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
        
        $import = new GajiImport();
        Excel::import($import, $request->file('file'));

        if (count($import->data) > 0) {
            session(['import_preview' => $import->data]);
            return back()->with('import_preview_ready', true);
        }

        return back()->with('error', "❌ Gagal! Tidak ada data yang valid untuk diimport.");
    }

    public function confirmImport(Request $request)
    {
        if ($request->has('cancel')) {
            session()->forget('import_preview');
            return back()->with('success', 'Import dibatalkan.');
        }

        $selectedIndices = $request->input('selected_rows', []);
        $allData = session('import_preview', []);
        
        if (empty($selectedIndices)) {
            return back()->with('error', '❌ Tidak ada data yang dipilih untuk diimport.');
        }

        if (empty($allData)) {
            return back()->with('error', '❌ Sesi import telah berakhir atau data tidak ditemukan.');
        }

        $importedCount = 0;
        foreach ($selectedIndices as $index) {
            if (isset($allData[$index])) {
                $row = $allData[$index];
                
                $idPeternak = $row['idpeternak'];

                // 0. Auto-register new Peternak if doesn't exist
                if (empty($idPeternak)) {
                    $newPeternak = Peternak::create([
                        'nama_peternak' => $row['nama_peternak'],
                        'status_mitra' => $row['status_mitra'] ?? 'peternak',
                        // other fields will be handled by model booted() or default values
                    ]);
                    $idPeternak = $newPeternak->idpeternak;
                }

                // 1. Update/Create Slip Pembayaran
                $slip = SlipPembayaran::updateOrCreate(
                    [
                        'idpeternak' => $idPeternak,
                        'bulan' => $row['bulan'],
                        'tahun' => $row['tahun']
                    ],
                    array_merge($row['potongan'], [
                        'jumlah_susu' => $row['jumlah_susu'],
                        'harga_satuan' => $row['harga_satuan'],
                        'total_pembayaran' => $row['total_pembayaran'],
                        'tanggal_bayar' => $this->parseDate($row['tanggal_input']),
                    ])
                );

                // 2. Sync deductions to Kasbon history (Riwayat Potongan)
                $this->syncDeductionsToKasbon($idPeternak, $row['potongan'], $slip->tanggal_bayar ?? now(), $slip->idslip);

                // 3. Update/Create Produksi Harian (Riwayat Setor)
                if ($row['jumlah_susu'] > 0 && !empty($row['tanggal_input'])) {
                    $tanggalProduksi = $this->parseDate($row['tanggal_input']);
                    if ($tanggalProduksi) {
                        ProduksiHarian::updateOrCreate(
                            [
                                'idpeternak' => $idPeternak,
                                'tanggal' => $tanggalProduksi,
                                'waktu_setor' => 'pagi' // Default to pagi for bulk import
                            ],
                            [
                                'jumlah_susu_liter' => $row['jumlah_susu'],
                                'catatan' => 'Import via Manajemen Pembayaran'
                            ]
                        );
                    }
                }

                $importedCount++;
            }
        }

        session()->forget('import_preview');
        return redirect()->route('gaji.index')->with('success', "✅ Berhasil mengimport $importedCount data slip pembayaran dan mensinkronisasi potongan ke riwayat.");
    }

    private function syncDeductionsToKasbon($idPeternak, $potongan, $tanggal, $idslip = null)
    {
        \Illuminate\Support\Facades\Log::info("Syncing deductions for peternak $idPeternak on date $tanggal. idslip: $idslip", ['potongan' => $potongan]);

        if ($idslip) {
            // Remove old synced deductions for this specific slip to prevent duplicates
            \App\Models\Kasbon::where('idslip', $idslip)->delete();
        }

        $mapping = [
            'potongan_shr' => 'SHR',
            'potongan_hutang_bl_ll' => 'Hutang BL/LL',
            'potongan_pakan_a' => 'Pakan A',
            'potongan_pakan_b' => 'Pakan B',
            'potongan_vitamix' => 'Vitamin', // Aligned with DB
            'potongan_konsentrat' => 'Konsentrat',
            'potongan_skim' => 'Skim',
            'potongan_ib_keswan' => 'IB Keswan',
            'potongan_susu_a' => 'Susu A',
            'potongan_kas_bon' => 'Kasbon', // Aligned with common naming
            'potongan_pakan_b_2' => 'Pakan B 2',
            'potongan_sp' => 'SP',
            'potongan_karpet' => 'Karpet',
            'potongan_vaksin' => 'Vaksin',
            'potongan_lain_lain' => 'Lain-lain',
        ];

        foreach ($potongan as $key => $value) {
            if ($value > 0 && isset($mapping[$key])) {
                $itemName = $mapping[$key];
                
                // Find or create logistics item to link
                $item = \App\Models\KatalogLogistik::firstOrCreate(
                    ['nama_barang' => $itemName],
                    ['harga_satuan' => $value] // Default to current value if new
                );

                // Record to Kasbon
                \App\Models\Kasbon::create([
                    'idpeternak' => $idPeternak,
                    'idslip' => $idslip,
                    'idlogistik' => $item->id,
                    'nama_item' => $item->nama_barang,
                    'qty' => 1,
                    'harga_satuan' => $value,
                    'total_rupiah' => $value,
                    'tanggal' => $tanggal,
                ]);
            }
        }
    }

    public function sign($idslip)
    {
        $slip = SlipPembayaran::findOrFail($idslip);
        
        if ($slip->isSigned()) {
            return back()->with('error', 'Slip ini sudah ditandatangani.');
        }

        $token = hash('sha256', $slip->idslip . '|' . auth()->id() . '|' . now());

        $slip->update([
            'signed_by' => auth()->id(),
            'signed_at' => now(),
            'signature_token' => $token,
            'status' => 'dibayar' 
        ]);

        ActivityLog::log(
            'SIGN_SALARY_SLIP', 
            "Admin " . auth()->user()->nama . " menandatangani slip pembayaran Mitra: " . $slip->peternak->nama_peternak . " (ID: $slip->idslip)",
            $slip
        );

        return back()->with('success', 'Slip pembayaran berhasil ditandatangani secara digital.');
    }

    public function undoSign($idslip)
    {
        $slip = SlipPembayaran::findOrFail($idslip);
        
        $updated = $slip->update([
            'signed_by' => null,
            'signed_at' => null,
            'signature_token' => null,
            'status' => 'pending'
        ]);

        if ($updated) {
            ActivityLog::log(
                'UNDO_SIGN_SALARY_SLIP', 
                "Admin " . auth()->user()->nama . " MEMBATALKAN tanda tangan slip pembayaran Mitra: " . $slip->peternak->nama_peternak . " (ID: $slip->idslip)",
                $slip
            );
            return true;
        }

        \Illuminate\Support\Facades\Log::error("Failed to update slip status for ID: $idslip");
        return false;
    }

    private function parseDate($date)
    {
        if (empty($date)) return null;
        
        // If it's already a Y-m-d string (from some Excel processors)
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) return $date;

        try {
            // Try d/m/Y first (standard Indonesia)
            return \Carbon\Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d');
        } catch (\Exception $e) {
            try {
                // Try d-m-Y
                return \Carbon\Carbon::createFromFormat('d-m-Y', $date)->format('Y-m-d');
            } catch (\Exception $e2) {
                try {
                    // Fallback to generic parsing
                    return \Carbon\Carbon::parse($date)->format('Y-m-d');
                } catch (\Exception $e3) {
                    \Illuminate\Support\Facades\Log::error("Failed to parse date: " . $date);
                    return null;
                }
            }
        }
    }
}
