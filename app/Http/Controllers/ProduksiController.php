<?php

namespace App\Http\Controllers;

use App\Models\ProduksiHarian;
use App\Models\BagiHasil;
use App\Models\Peternak;
use App\Models\Notifikasi;
use App\Exports\ProduksiTemplateExport;
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

        // Create produksi record
        $produksi = ProduksiHarian::create(array_merge($validated, [
            'idpeternak' => $idpeternak,
            'biaya_pakan' => 0,
            'biaya_tenaga' => 0,
            'biaya_operasional' => 0,
        ]));

        // Send notification
        Notifikasi::create([
            'iduser' => $user->iduser,
            'judul' => 'Setor Susu Tercatat',
            'pesan' => "Setor Susu Anda sebesar {$validated['jumlah_susu_liter']} liter telah tercatat.",
            'tipe' => 'success',
            'kategori' => 'jadwal',
            'status_baca' => 'belum_baca',
        ]);

        if ($isAdmin) {
            return redirect()->route('produksi.create')->with('success', 'Setor Susu berhasil dicatat!'); 
        }

        return redirect()->route('produksi.create')->with('success', 'Setor Susu berhasil dicatat!');
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

        $perPage = $request->get('per_page', 15);
        
        // Group by date and peternak to show Pagi/Sore/Total
        $produksi = $query->with('peternak')
            ->selectRaw('tanggal, idpeternak, 
                MAX(CASE WHEN waktu_setor = "pagi" THEN idproduksi END) as idpagi,
                MAX(CASE WHEN waktu_setor = "sore" THEN idproduksi END) as idsore,
                SUM(CASE WHEN waktu_setor = "pagi" THEN jumlah_susu_liter ELSE 0 END) as pagi,
                SUM(CASE WHEN waktu_setor = "sore" THEN jumlah_susu_liter ELSE 0 END) as sore,
                SUM(jumlah_susu_liter) as total')
            ->groupBy('tanggal', 'idpeternak')
            ->groupBy('tanggal', 'idpeternak')
            ->orderBy('tanggal', 'desc');

        if ($request->get('status_setor') === 'pagi') {
            $produksi->havingRaw('pagi > 0 AND sore = 0');
        } elseif ($request->get('status_setor') === 'sore') {
            $produksi->havingRaw('sore > 0 AND pagi = 0');
        } elseif ($request->get('status_setor') === 'lengkap') {
            $produksi->havingRaw('pagi > 0 AND sore > 0');
        }

        $produksi = $produksi->paginate($perPage)
            ->withQueryString();

        if ($request->get('export') === 'excel') {
            return Excel::download(new SubPenampungReportExport($produksi), 'Riwayat_Setoran_'.now()->format('YmdHis').'.xlsx');
        }

        return view('produksi.list_peternak', compact('produksi', 'peternaks', 'isAdmin', 'perPage', 'startDate', 'endDate', 'idpeternak'));
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
        $peternaks = Peternak::all();
        return view('produksi.edit', compact('produksi', 'peternaks'));
    }

    public function update(Request $request, $idproduksi)
    {
        $produksi = ProduksiHarian::findOrFail($idproduksi);
        
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'waktu_setor' => 'required|in:pagi,sore',
            'jumlah_susu_liter' => 'required|numeric|min:0.1',
            'idpeternak' => 'required|exists:peternak,idpeternak',
            'catatan' => 'nullable|string',
        ]);

        $produksi->update($validated);

        return redirect()->route('produksi.index')->with('success', 'Data produksi berhasil diperbarui.');
    }

    public function destroy($idproduksi)
    {
        $produksi = ProduksiHarian::findOrFail($idproduksi);
        $produksi->delete();

        return redirect()->route('produksi.index')->with('success', 'Data produksi berhasil dihapus.');
    }

    public function downloadTemplate()
    {
        return Excel::download(new ProduksiTemplateExport, 'template_produksi_harian.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,xls']);
        
        $import = new ProduksiImport;
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
