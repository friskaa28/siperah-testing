<?php

namespace App\Http\Controllers;

use App\Models\Peternak;
use App\Models\User;
use Illuminate\Http\Request;

class PeternakController extends Controller
{
    public function index(Request $request)
    {
        $query = Peternak::with('user')->orderBy('nama_peternak', 'asc');

        if ($request->search) {
            $query->where('nama_peternak', 'like', '%' . $request->search . '%')
                  ->orWhere('lokasi', 'like', '%' . $request->search . '%')
                  ->orWhere('no_peternak', 'like', '%' . $request->search . '%');
        }

        if ($request->status_mitra) {
            $query->where('status_mitra', $request->status_mitra);
        }

        // Filter by koperasi_id if user is scoped (VILLAGE ADMIN ROLE)
        if (auth()->user()->koperasi_id) {
            $query->where('koperasi_id', auth()->user()->koperasi_id);
        }

        // Filter if sub-penampung
        if (auth()->user()->isSubPenampung()) {
            $query->where(function($q) {
                $q->where('id_sub_penampung', auth()->user()->peternak->idpeternak)
                  ->orWhere('idpeternak', auth()->user()->peternak->idpeternak);
            });
        }

        $perPage = $request->get('per_page', 5);
        $peternaks = $query->paginate($perPage)->withQueryString();

        $subPenampungs = Peternak::whereIn('status_mitra', ['sub_penampung', 'sub_penampung_tr', 'sub_penampung_p'])->get();

        return view('peternak.index', compact('peternaks', 'subPenampungs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'status_mitra' => 'required|in:peternak,sub_penampung,sub_penampung_tr,sub_penampung_p',
            'no_peternak' => 'nullable|string',
            'lokasi' => 'nullable|string',
            'kelompok' => 'nullable|string',
            'id_sub_penampung' => 'nullable|exists:peternak,idpeternak',
        ]);

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            $user = User::create([
                'nama' => $request->nama,
                'email' => $request->email,
                'password' => \Illuminate\Support\Facades\Hash::make($request->password),
                'role' => 'peternak',
            ]);

            Peternak::create([
                'iduser' => $user->iduser,
                'nama_peternak' => $request->nama,
                'no_peternak' => $request->no_peternak,
                'lokasi' => $request->lokasi,
                'kelompok' => $request->kelompok,
                'status_mitra' => $request->status_mitra,
                'id_sub_penampung' => $request->id_sub_penampung ?: (auth()->user()->isSubPenampung() ? auth()->user()->peternak->idpeternak : null),
                'koperasi_id' => auth()->user()->koperasi_id,
            ]);

            \Illuminate\Support\Facades\DB::commit();
            return back()->with('success', 'Peternak baru berhasil ditambahkan!');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->with('error', 'Gagal menambahkan peternak: ' . $e->getMessage());
        }
    }

    public function updateStatus(Request $request, $id)
    {
        $peternak = Peternak::findOrFail($id);
        
        $request->validate([
            'status_mitra' => 'required|in:peternak,sub_penampung,sub_penampung_tr,sub_penampung_p',
            'no_peternak' => 'nullable|string',
            'lokasi' => 'nullable|string',
            'kelompok' => 'nullable|string',
            'id_sub_penampung' => 'nullable|exists:peternak,idpeternak',
        ]);

        $peternak->update($request->all());

        return back()->with('success', 'Data peternak berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $peternak = Peternak::findOrFail($id);
        $user = $peternak->user;

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            $peternak->delete();
            if ($user) {
                $user->delete();
            }
            \Illuminate\Support\Facades\DB::commit();
            return back()->with('success', 'Data peternak berhasil dihapus!');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->with('error', 'Gagal menghapus peternak: ' . $e->getMessage());
        }
    }
}
