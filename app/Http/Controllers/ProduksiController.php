<?php

namespace App\Http\Controllers;

use App\Models\ProduksiHarian;
use App\Models\BagiHasil;
use App\Models\Peternak;
use App\Models\Notifikasi;
use App\Exports\ProduksiTemplateExport;
use App\Exports\SubPenampungReportExport;
use App\Imports\ProduksiImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProduksiController extends Controller
{
    public function create(Request $request)
    {
        $user = Auth::user();
        $peternaks = [];
        $lastProduksi = null;
        $katalog = \App\Models\KatalogLogistik::all();

        if ($user->isAdmin() || $user->isPengelola()) {
            $peternaks = Peternak::all();
            if ($request->idpeternak) {
                $lastProduksi = ProduksiHarian::where('idpeternak', $request->idpeternak)
                    ->orderBy('idproduksi', 'desc')
                    ->first();
            }
        } else {
            $peternak = $user->peternak;
            if ($peternak) {
                $lastProduksi = ProduksiHarian::where('idpeternak', $peternak->idpeternak)
                    ->orderBy('idproduksi', 'desc')
                    ->first();
            }
        }

        return view('produksi.create', compact('peternaks', 'lastProduksi', 'katalog'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $isAdmin = $user->isAdmin() || $user->isPengelola();

        $rules = [
            'tanggal' => 'required|date',
            'waktu_setor' => 'required|in:pagi,sore',
            'jumlah_susu_liter' => 'required|numeric|min:1',
            'catatan' => 'nullable|string',
        ];

        if ($isAdmin) {
            $rules['idpeternak'] = 'required|exists:peternak,idpeternak';
        }

        $validated = $request->validate($rules);

        if ($isAdmin) {
             $idpeternak = $validated['idpeternak'];
        } else {
            $peternak = $user->peternak;
            if (!$peternak) {
                return back()->withErrors(['error' => 'Profil peternak tidak ditemukan.']);
            }
            $idpeternak = $peternak->idpeternak;
        }

        // Check if record already exists
        $existingProduksi = ProduksiHarian::where('tanggal', $validated['tanggal'])
            ->where('idpeternak', $idpeternak)
            ->where('waktu_setor', $validated['waktu_setor'])
            ->first();

        if ($existingProduksi) {
            // Update existing record (Add to it)
            $existingProduksi->increment('jumlah_susu_liter', $validated['jumlah_susu_liter']);
            // Append note if exists
            if (!empty($validated['catatan'])) {
                $existingProduksi->catatan = ($existingProduksi->catatan ? $existingProduksi->catatan . '; ' : '') . $validated['catatan'];
                $existingProduksi->save();
            }
            $actionMsg = "ditambahkan ke data yang sudah ada (Total: {$existingProduksi->jumlah_susu_liter}L)";
        } else {
            // Create new record
            ProduksiHarian::create(array_merge($validated, [
                'idpeternak' => $idpeternak,
                'biaya_pakan' => 0,
                'biaya_tenaga' => 0,
                'biaya_operasional' => 0,
            ]));
            $actionMsg = "berhasil dicatat";
        }

        // Get target user for notification
        $targetPeternak = Peternak::find($idpeternak);
        $targetUser = $targetPeternak ? $targetPeternak->iduser : $user->iduser;

        // Send notification
        Notifikasi::create([
            'iduser' => $targetUser,
            'judul' => 'Setor Susu Tercatat',
            'pesan' => "Setor Susu Anda sebesar {$validated['jumlah_susu_liter']} liter ({$validated['waktu_setor']}) telah {$actionMsg}.",
            'tipe' => 'success',
            'kategori' => 'jadwal',
            'status_baca' => 'belum_baca',
        ]);

        if ($isAdmin) {
            return redirect()->route('produksi.create', [
                'tanggal' => $validated['tanggal'],
                'idpeternak' => $idpeternak,
                'waktu_setor' => $validated['waktu_setor']
            ])->with('success', "Setor Susu {$actionMsg}!"); 
        }

        return redirect()->route('produksi.create', [
            'tanggal' => $validated['tanggal'],
            'waktu_setor' => $validated['waktu_setor']
        ])->with('success', "Setor Susu {$actionMsg}!");
    }


    public function listPeternak(Request $request)
    {
        $user = Auth::user();
        $isAdmin = $user->isAdmin() || $user->isPengelola();
        $now = now();

        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $idpeternak = $request->get('idpeternak');

        $query = ProduksiHarian::query();

        if ($isAdmin) {
            $peternaks = Peternak::all();
            if ($idpeternak) {
                $query->where('idpeternak', $idpeternak);
            }
        } else {
            $peternak = $user->peternak;
            if (!$peternak) {
                return back()->withErrors(['error' => 'Profil peternak tidak ditemukan.']);
            }
            $query->where('idpeternak', $peternak->idpeternak);
            $peternaks = collect([$peternak]);
        }

        if ($startDate) {
            $query->where('tanggal', '>=', $startDate);
        }
        if ($endDate) {
            $query->where('tanggal', '<=', $endDate);
        }

        $statusSetor = $request->get('status_setor');
        if ($statusSetor === 'pagi' || $statusSetor === 'sore') {
            $query->where('waktu_setor', $statusSetor);
        }

        $perPage = $request->get('per_page', 15);
        
        // Calculate totals if filtering by specific farmer (BEFORE grouping the main query)
        $summary = null;
        if ($idpeternak) {
            $summaryQuery = clone $query;
            $summary = $summaryQuery->selectRaw('
                SUM(CASE WHEN waktu_setor = "pagi" THEN jumlah_susu_liter ELSE 0 END) as total_pagi,
                SUM(CASE WHEN waktu_setor = "sore" THEN jumlah_susu_liter ELSE 0 END) as total_sore,
                SUM(jumlah_susu_liter) as grand_total
            ')->first();
        }

        // Group by date and peternak to show Pagi/Sore/Total
        $produksi = $query->with('peternak')
            ->selectRaw('tanggal, idpeternak, 
                MAX(CASE WHEN waktu_setor = "pagi" THEN idproduksi END) as idpagi,
                MAX(CASE WHEN waktu_setor = "sore" THEN idproduksi END) as idsore,
                SUM(CASE WHEN waktu_setor = "pagi" THEN jumlah_susu_liter ELSE 0 END) as pagi,
                SUM(CASE WHEN waktu_setor = "sore" THEN jumlah_susu_liter ELSE 0 END) as sore,
                SUM(jumlah_susu_liter) as total,
            MAX(created_at) as input_time,
            MAX(updated_at) as last_update')
            ->groupBy('tanggal', 'idpeternak')
            ->orderBy('last_update', 'desc');

        if ($statusSetor === 'lengkap') {
            $produksi->havingRaw('pagi > 0 AND sore > 0');
        }

        if ($request->get('export') === 'excel') {
            return Excel::download(new SubPenampungReportExport($produksi->get()), 'Riwayat_Setoran_'.now()->format('YmdHis').'.xlsx');
        }

        $produksi = $produksi->paginate($perPage)
            ->withQueryString();

        return view('produksi.list_peternak', compact('produksi', 'peternaks', 'isAdmin', 'perPage', 'startDate', 'endDate', 'idpeternak', 'summary'));
    }


    public function printRiwayat(Request $request)
    {
        $user = Auth::user();
        $isAdmin = $user->isAdmin() || $user->isPengelola();
        
        // --- 1. Filter Logic (Same as listPeternak) ---
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $idpeternak = $request->get('idpeternak');

        $query = ProduksiHarian::query();

        if ($isAdmin) {
            $peternaks = Peternak::all(); // Need this if we want to show filter info or name lookup
            if ($idpeternak) {
                $query->where('idpeternak', $idpeternak);
            }
        } else {
            $peternak = $user->peternak;
            if (!$peternak) {
                return back()->withErrors(['error' => 'Profil peternak tidak ditemukan.']);
            }
            $query->where('idpeternak', $peternak->idpeternak);
            // $peternaks variable not strictly needed for print unless for header
        }

        if ($startDate) {
            $query->where('tanggal', '>=', $startDate);
        }
        if ($endDate) {
            $query->where('tanggal', '<=', $endDate);
        }

        // --- 2. Data Fetching (No Pagination) ---
        // Group by date and peternak to show Pagi/Sore/Total
        $data = $query->with('peternak')
            ->selectRaw('tanggal, idpeternak, 
                SUM(CASE WHEN waktu_setor = "pagi" THEN jumlah_susu_liter ELSE 0 END) as pagi,
                SUM(CASE WHEN waktu_setor = "sore" THEN jumlah_susu_liter ELSE 0 END) as sore,
                SUM(jumlah_susu_liter) as total')
            ->groupBy('tanggal', 'idpeternak')
            ->orderBy('tanggal', 'asc') // Chronological order for report
            ->get();

        // --- 3. Grouping by Month & Peternak ---
        // We want a report style: 
        // Header: Peternak A
        //   Sub-Header: Januari 2026
        //     Table Rows...
        //     Total Januari
        // But if multiple peternaks are mixed (no filter), maybe group by Peternak first?
        // User asked: "filter peternak A yang diterapkan otomatis pas cetak juga peternak itu aja"
        // So if specific peternak is selected, we just show that.
        // If ALL peternaks are selected, we should probably group by Peternak then Month, or just Month if it's a global report.
        // Let's assume Group by Peternak -> Group by Month.

        $groupedData = $data->groupBy(function($item) {
            return $item->peternak->nama_peternak;
        })->map(function($peternakGroup) {
            return $peternakGroup->groupBy(function($item) {
                return \Carbon\Carbon::parse($item->tanggal)->format('F Y');
            });
        });

        return view('produksi.print_riwayat', compact('groupedData', 'startDate', 'endDate', 'isAdmin', 'idpeternak'));
    }

    public function detailPerhitungan($idproduksi)
    {
        $produksi = ProduksiHarian::find($idproduksi);

        if (!$produksi) {
            return back()->withErrors(['error' => 'Produksi tidak ditemukan.']);
        }

        // Check authorization
        $user = Auth::user();
        if ($user->isPeternak() && $produksi->peternak->iduser !== $user->iduser) {
            return back()->withErrors(['error' => 'Anda tidak berhak mengakses data ini.']);
        }

        return view('produksi.detail_perhitungan', [
            'produksi' => $produksi,
        ]);
    }

    public function edit($idproduksi)
    {
        $produksi = ProduksiHarian::findOrFail($idproduksi);
        
        // Sum duplicates to show the REAL total in the edit form
        $totalLiter = ProduksiHarian::where('tanggal', $produksi->tanggal)
            ->where('idpeternak', $produksi->idpeternak)
            ->where('waktu_setor', $produksi->waktu_setor)
            ->sum('jumlah_susu_liter');
            
        // Override the instance value for the view
        $produksi->jumlah_susu_liter = $totalLiter;

        $peternaks = Peternak::all();
        return view('produksi.edit', compact('produksi', 'peternaks'));
    }

    public function update(Request $request, $idproduksi)
    {
        $produksi = ProduksiHarian::findOrFail($idproduksi);
        
        // Find ALL duplicates based on the ORIGINAL data before update
        // We will delete these after updating the main record
        $duplicates = ProduksiHarian::where('tanggal', $produksi->tanggal)
            ->where('idpeternak', $produksi->idpeternak)
            ->where('waktu_setor', $produksi->waktu_setor)
            ->where('idproduksi', '!=', $idproduksi)
            ->get();
        
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'waktu_setor' => 'required|in:pagi,sore',
            'jumlah_susu_liter' => 'required|numeric|min:0.1',
            'idpeternak' => 'required|exists:peternak,idpeternak',
            'catatan' => 'nullable|string',
        ]);

        $produksi->update($validated);

        // Delete the merged duplicates
        foreach($duplicates as $dup) {
            $dup->delete();
        }

        $redirectTo = $request->input('redirect_to');
        if ($redirectTo) {
            return redirect($redirectTo)->with('success', 'Data produksi berhasil diperbarui dan duplikat telah digabungkan.');
        }

        return redirect()->route('produksi.index')->with('success', 'Data produksi berhasil diperbarui dan duplikat telah digabungkan.');
    }


    public function destroy($idproduksi)
    {
        $produksi = ProduksiHarian::findOrFail($idproduksi);
        
        // Delete ALL records that belong to this specific session (duplicates included)
        ProduksiHarian::where('tanggal', $produksi->tanggal)
            ->where('idpeternak', $produksi->idpeternak)
            ->where('waktu_setor', $produksi->waktu_setor)
            ->delete();

        if (request()->has('redirect_to')) {
            return redirect(request('redirect_to'))->with('success', 'Data produksi (dan duplikatnya) berhasil dihapus.');
        }

        return redirect()->route('produksi.index')->with('success', 'Data produksi (dan duplikatnya) berhasil dihapus.');
    }


    public function downloadTemplate(Request $request)
    {
        if ($request->format == 'matrix') {
            return Excel::download(new \App\Exports\ProduksiMatrixTemplateExport, 'template_produksi_matriks.xlsx');
        }
        return Excel::download(new \App\Exports\ProduksiListTemplateExport, 'template_produksi_list.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
            'bulan' => 'nullable|integer|between:1,12',
            'tahun' => 'nullable|integer|min:2020',
        ]);
        
        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');

        $import = new ProduksiImport($bulan, $tahun);
        Excel::import($import, $request->file('file'));

        if ($import->imported > 0) {
            $msg = "✅ Berhasil! {$import->imported} data produksi berhasil diimport.";
            
            if (count($import->failedNames) > 0) {
                $msg .= " Namun, nama/ID ini tidak ditemukan: " . implode(', ', array_unique($import->failedNames));
            }
            if (count($import->unrecognizedDates) > 0) {
                $msg .= " Tanggal bermasalah: " . implode(', ', array_unique($import->unrecognizedDates));
            }
            if (count($import->invalidWaktu) > 0) {
                $msg .= " Waktu setor tidak valid (default ke pagi): " . implode(', ', array_unique($import->invalidWaktu));
            }
            
            return back()->with('success', $msg);
        }

        $error = "❌ Gagal! 0 data yang berhasil diimport.";
        if (count($import->failedNames) > 0) {
            $error .= " Nama/ID tidak terdaftar: " . implode(', ', array_unique($import->failedNames));
        }
        if (count($import->unrecognizedDates) > 0) {
            $error .= " Tanggal bermasalah: " . implode(', ', array_unique($import->unrecognizedDates));
        }
        
        return back()->with('error', $error);
    }
}
