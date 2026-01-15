<?php

namespace App\Http\Controllers;

use App\Models\Pengumuman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengumumanController extends Controller
{
    public function broadcast(Request $request)
    {
        $validated = $request->validate([
            'isi' => 'required|string',
        ]);

        Pengumuman::create([
            'isi' => $validated['isi'],
            'id_admin' => Auth::id(),
        ]);

        return back()->with('success', 'Pengumuman berhasil disiarkan!');
    }
}
