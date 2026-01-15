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

        return view('produksi.create', compact('peternaks', 'lastProduksi'));
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
            'judul' => 'Produksi Tercatat',
            'pesan' => "Produksi Anda sebesar {$validated['jumlah_susu_liter']} liter telah tercatat.",
            'tipe' => 'success',
            'kategori' => 'jadwal',
            'status_baca' => 'belum_baca',
        ]);

        if ($isAdmin) {
            return redirect('/dashboard-pengelola')->with('success', 'Produksi berhasil dicatat!'); 
        }

        return redirect('/dashboard-peternak')->with('success', 'Produksi berhasil dicatat!');
    }

    public function listPeternak(Request $request)
    {
        $user = Auth::user();
        $isAdmin = $user->isAdmin() || $user->isPengelola();

        if ($isAdmin) {
            $query = ProduksiHarian::with('peternak')->orderBy('tanggal', 'desc');
            if ($request->idpeternak) {
                $query->where('idpeternak', $request->idpeternak);
            }
            $produksi = $query->paginate(15);
            $peternaks = Peternak::all();
            return view('produksi.list_peternak', compact('produksi', 'peternaks', 'isAdmin'));
        } else {
            $peternak = $user->peternak;
            if (!$peternak) {
                return back()->withErrors(['error' => 'Profil peternak tidak ditemukan.']);
            }
            $produksi = ProduksiHarian::where('idpeternak', $peternak->idpeternak)
                ->orderBy('tanggal', 'desc')
                ->paginate(15);
            return view('produksi.list_peternak', compact('produksi'));
        }
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
