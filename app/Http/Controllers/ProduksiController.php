<?php

namespace App\Http\Controllers;

use App\Models\ProduksiHarian;
use App\Models\BagiHasil;
use App\Models\Peternak;
use App\Models\Notifikasi;
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
            'biaya_pakan' => 'required|numeric|min:0',
            'biaya_tenaga' => 'required|numeric|min:0',
            'biaya_operational' => 'required|numeric|min:0',
            'photo_bukti' => 'nullable|image|max:2048',
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

        // Handle file upload
        if ($request->hasFile('photo_bukti')) {
            $file = $request->file('photo_bukti');
            $path = $file->store('produksi', 'public');
            $validated['photo_bukti'] = $path;
        }

        // Create produksi record
        $produksi = ProduksiHarian::create(array_merge($validated, [
            'idpeternak' => $idpeternak,
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

    public function listPeternak()
    {
        $user = Auth::user();
        $peternak = $user->peternak;

        if (!$peternak) {
            return back()->withErrors(['error' => 'Profil peternak tidak ditemukan.']);
        }

        $produksi = ProduksiHarian::where('idpeternak', $peternak->idpeternak)
            ->orderBy('tanggal', 'desc')
            ->paginate(15);

        return view('produksi.list_peternak', ['produksi' => $produksi]);
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

        $bagiHasil = $produksi->bagiHasil;

        return view('produksi.detail_perhitungan', [
            'produksi' => $produksi,
            'bagiHasil' => $bagiHasil,
        ]);
    }
}
