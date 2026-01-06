<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotifikasiController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $kategori = $request->get('kategori', 'semua');

        $query = Notifikasi::where('iduser', $user->iduser);

        if ($kategori !== 'semua') {
            $query->where('kategori', $kategori);
        }

        $notifikasi = $query->latest()->paginate(15);

        return view('notifikasi.index', [
            'notifikasi' => $notifikasi,
            'kategoriAktif' => $kategori,
        ]);
    }

    public function markAsRead($idnotif)
    {
        $notifikasi = Notifikasi::find($idnotif);

        if (!$notifikasi) {
            return response()->json(['error' => 'Notifikasi tidak ditemukan'], 404);
        }

        $notifikasi->markAsRead();

        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        $user = Auth::user();

        Notifikasi::where('iduser', $user->iduser)
            ->where('status_baca', 'belum_baca')
            ->update(['status_baca' => 'sudah_baca']);

        return response()->json(['success' => true]);
    }

    public function countUnread()
    {
        $user = Auth::user();
        $count = Notifikasi::where('iduser', $user->iduser)
            ->where('status_baca', 'belum_baca')
            ->count();

        return response()->json(['count' => $count]);
    }

    public function delete($idnotif)
    {
        $notifikasi = Notifikasi::find($idnotif);

        if (!$notifikasi) {
            return back()->withErrors(['error' => 'Notifikasi tidak ditemukan.']);
        }

        $notifikasi->delete();

        return back()->with('success', 'Notifikasi berhasil dihapus!');
    }
}
