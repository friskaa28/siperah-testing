<?php

namespace App\Http\Controllers;

use App\Models\HargaSusuHistory;
use Illuminate\Http\Request;

class HargaSusuController extends Controller
{
    public function index()
    {
        $history = HargaSusuHistory::orderBy('tanggal_berlaku', 'desc')->get();
        $currentPrice = HargaSusuHistory::getHargaAktif();
        return view('harga_susu.index', compact('history', 'currentPrice'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'harga' => 'required|numeric|min:0',
            'tanggal_berlaku' => 'required|date',
        ]);

        HargaSusuHistory::create($validated);

        return back()->with('success', 'Harga susu baru berhasil ditetapkan!');
    }

    public function destroy($id)
    {
        $history = HargaSusuHistory::findOrFail($id);
        $history->delete();

        return back()->with('success', 'Riwayat harga berhasil dihapus!');
    }
}
