<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Peternak;
use App\Models\ProduksiHarian;
use App\Models\Kasbon;
use Carbon\Carbon;

class RekapController extends Controller
{
    public function index(Request $request)
    {
        $tanggal = $request->input('tanggal', Carbon::today()->format('Y-m-d'));
        
        // Fetch all active peternaks
        $peternaks = Peternak::orderBy('nama_peternak')->get();
        
        // Fetch production for the date
        $produksis = ProduksiHarian::whereDate('tanggal', $tanggal)->get()->keyBy('idpeternak');
        
        // Fetch kasbon for the date
        $kasbons = Kasbon::whereDate('tanggal', $tanggal)->get()->groupBy('idpeternak');

        return view('rekap.index', compact('peternaks', 'produksis', 'kasbons', 'tanggal'));
    }
}
