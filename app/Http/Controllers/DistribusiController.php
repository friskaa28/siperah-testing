<?php

namespace App\Http\Controllers;

use App\Models\Distribusi;
use App\Models\BagiHasil;
use App\Models\Peternak;
use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;

class DistribusiController extends Controller
{
    public function create(Request $request)
    {
        $user = Auth::user();
        $peternaks = [];
        $lastDistribusi = null;

        if ($user->isAdmin() || $user->isPengelola()) {
            $peternaks = Peternak::all();
            if ($request->idpeternak) {
                $lastDistribusi = Distribusi::where('idpeternak', $request->idpeternak)
                    ->orderBy('iddistribusi', 'desc')
                    ->first();
            }
        } else {
            $peternak = $user->peternak;
            if ($peternak) {
                $lastDistribusi = Distribusi::where('idpeternak', $peternak->idpeternak)
                    ->orderBy('iddistribusi', 'desc')
                    ->first();
            }
        }

        return view('distribusi.create', compact('peternaks', 'lastDistribusi'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $isAdmin = $user->isAdmin() || $user->isPengelola();

        $rules = [
            'tujuan' => 'required|string|max:100',
            'volume' => 'required|numeric|min:0.1',
            'harga_per_liter' => 'required|numeric|min:0',
            'tanggal_kirim' => 'required|date',
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

        $distribusi = Distribusi::create(array_merge($validated, [
            'idpeternak' => $idpeternak,
            'status' => 'pending',
        ]));

        // Auto-trigger bagi hasil calculation
        // Find peternak object to get productions
        $peternakObj = Peternak::find($idpeternak);
        
        $produksi = $peternakObj->produksi()
            ->whereDate('tanggal', $validated['tanggal_kirim'])
            ->first();

        if ($produksi) {
            BagiHasil::hitungBagiHasil($produksi, 60, 40);
        }

        // Send notification
        Notifikasi::create([
            'iduser' => $isAdmin ? $peternakObj->iduser : $user->iduser, // Notify peternak if admin input? Or notify user themselves? Logic: If admin input, notify peternak.
            'judul' => 'Distribusi Tercatat',
            'pesan' => "Distribusi ke {$validated['tujuan']} sebesar {$validated['volume']} liter telah tercatat.",
            'tipe' => 'info',
            'kategori' => 'jadwal',
            'status_baca' => 'belum_baca',
        ]);

        if ($isAdmin) {
             return redirect('/manajemen-distribusi')->with('success', 'Distribusi berhasil dicatat!');
        }

        return redirect('/dashboard-peternak')->with('success', 'Distribusi berhasil dicatat!');
    }

    public function recap()
    {
        $user = Auth::user();

        if ($user->isPeternak()) {
            $peternak = $user->peternak;
            
            if (!$peternak) {
                return back()->withErrors(['error' => 'Profil peternak tidak ditemukan.']);
            }

            $distribusi = Distribusi::where('idpeternak', $peternak->idpeternak)
                ->orderBy('tanggal_kirim', 'desc')
                ->paginate(15);
        } else {
            $distribusi = Distribusi::orderBy('tanggal_kirim', 'desc')
                ->paginate(15);
        }

        return view('distribusi.recap', ['distribusi' => $distribusi]);
    }

    public function show($iddistribusi)
    {
        $distribusi = Distribusi::find($iddistribusi);

        if (!$distribusi) {
            return back()->withErrors(['error' => 'Distribusi tidak ditemukan.']);
        }

        return view('distribusi.show', ['distribusi' => $distribusi]);
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user->isAdmin()) {
            abort(403, 'Akses khusus Admin Kantor.');
        }

        $bulan = $request->get('bulan', now()->month);
        $tahun = $request->get('tahun', now()->year);
        $idpeternak = $request->get('idpeternak');

        $peternaks = Peternak::all();

        $query = Distribusi::with('peternak')
            ->whereMonth('tanggal_kirim', $bulan)
            ->whereYear('tanggal_kirim', $tahun);

        if ($idpeternak) {
            $query->where('idpeternak', $idpeternak);
        }

        $distribusi = $query->orderBy('tanggal_kirim', 'desc')->get();

        return view('distribusi.index', compact('distribusi', 'peternaks', 'bulan', 'tahun', 'idpeternak'));
    }

    public function exportPdf(Request $request)
    {
        $user = Auth::user();
        if (!$user->isAdmin()) abort(403);

        $bulan = $request->get('bulan', now()->month);
        $tahun = $request->get('tahun', now()->year);
        $idpeternak = $request->get('idpeternak');

        $query = Distribusi::with('peternak')
            ->whereMonth('tanggal_kirim', $bulan)
            ->whereYear('tanggal_kirim', $tahun);

        if ($idpeternak) $query->where('idpeternak', $idpeternak);
        
        $distribusi = $query->orderBy('tanggal_kirim', 'desc')->get();
        $namaBulan = \Carbon\Carbon::create()->month($bulan)->translatedFormat('F');
        $totalVolume = $distribusi->sum('volume');

        $pdf = Pdf::loadView('distribusi.pdf', [
            'distribusi' => $distribusi,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'namaBulan' => $namaBulan,
            'totalVolume' => $totalVolume
        ]);

        return $pdf->download("Rekap_Distribusi_{$bulan}_{$tahun}.pdf");
    }

    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template_import_distribusi.csv"',
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            // Header: No. Peternak, Nama Peternak, Tujuan, Volume (L), Harga (Rp), Tanggal (YYYY-MM-DD), Catatan
            fputcsv($file, ['No. Peternak', 'Nama Peternak', 'Tujuan', 'Volume (Liter)', 'Harga Per Liter', 'Tanggal (YYYY-MM-DD)', 'Catatan']);
            // Contoh data
            fputcsv($file, ['P001', 'Budi Santoso', 'IPS Lembang', '150.5', '7000', date('Y-m-d'), 'Susu pagi']);
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file_csv' => 'required|file|mimes:csv,txt',
        ]);

        $file = $request->file('file_csv');
        $handle = fopen($file->getRealPath(), 'r');
        
        // Skip header
        fgetcsv($handle);

        $successCount = 0;
        $errors = [];
        $row = 1;

        while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
            $row++;
            if (count($data) < 6) continue;

            $noPeternak = trim($data[0]);
            $namaPeternak = trim($data[1]);
            $tujuan = trim($data[2]);
            $volume = floatval($data[3]);
            $harga = intval($data[4]);
            $tanggal = trim($data[5]);
            $catatan = isset($data[6]) ? trim($data[6]) : null;

            // Find Peternak by No or Name
            $peternak = \App\Models\Peternak::where('no_peternak', $noPeternak)
                ->orWhere('nama_peternak', 'LIKE', "%$namaPeternak%")
                ->first();

            if (!$peternak) {
                $errors[] = "Baris $row: Peternak '$namaPeternak' ($noPeternak) tidak ditemukan.";
                continue;
            }

            try {
                $distribusi = Distribusi::create([
                    'idpeternak' => $peternak->idpeternak,
                    'tujuan' => $tujuan,
                    'volume' => $volume,
                    'harga_per_liter' => $harga,
                    'tanggal_kirim' => $tanggal,
                    'catatan' => $catatan,
                    'status' => 'terkirim', // Default imported to terkirim
                ]);

                // Auto-trigger bagi hasil calculation
                $produksi = $peternak->produksi()
                    ->whereDate('tanggal', $tanggal)
                    ->first();

                if ($produksi) {
                    \App\Models\BagiHasil::hitungBagiHasil($produksi, 60, 40);
                }

                $successCount++;
            } catch (\Exception $e) {
                $errors[] = "Baris $row: " . $e->getMessage();
            }
        }

        fclose($handle);

        if (count($errors) > 0) {
            return redirect()->route('distribusi.index')
                ->with('success', "Berhasil mengimpor $successCount data.")
                ->withErrors($errors);
        }

        return redirect()->route('distribusi.index')->with('success', "Berhasil mengimpor $successCount data distribusi.");
    }

    public function updateStatus(Request $request, $iddistribusi)
    {
        $user = Auth::user();
        
        if (!($user->isPengelola() || $user->isAdmin())) {
            return back()->withErrors(['error' => 'Anda tidak berhak mengubah status ini.']);
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,terkirim,diterima,ditolak',
        ]);

        $distribusi = Distribusi::find($iddistribusi);
        $distribusi->update($validated);

        // Send notification to peternak
        $peternak = $distribusi->peternak;

        if ($peternak) {
            $statusLabel = [
                'pending' => 'Menunggu pengiriman',
                'terkirim' => 'Dalam perjalanan',
                'diterima' => 'Telah diterima',
                'ditolak' => 'Ditolak',
            ];
    
            Notifikasi::create([
                'iduser' => $peternak->iduser,
                'judul' => 'Status Distribusi Berubah',
                'pesan' => "Status distribusi Anda berubah menjadi: {$statusLabel[$validated['status']]}",
                'tipe' => 'info',
                'kategori' => 'jadwal',
                'status_baca' => 'belum_baca',
            ]);
        }

        return back()->with('success', 'Status distribusi berhasil diubah!');
    }
}
