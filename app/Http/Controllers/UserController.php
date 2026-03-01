<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::orderBy('nama', 'asc');

        if ($request->search) {
            $query->where('nama', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
        }

        if ($request->role) {
            $query->where('role', $request->role);
        }

        if (auth()->user()->koperasi_id) {
            $query->where('koperasi_id', auth()->user()->koperasi_id);
        }

        $perPage = $request->get('per_page', 20);
        $users = $query->paginate($perPage)->withQueryString();

        return view('users.index', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,pengelola,peternak,tim_analytics',
            'koperasi_id' => 'nullable|string',
        ]);

        User::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'koperasi_id' => $request->koperasi_id,
        ]);

        return back()->with('success', 'Pengguna berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id . ',iduser',
            'role' => 'required|in:admin,pengelola,peternak,tim_analytics',
            'koperasi_id' => 'nullable|string',
        ]);

        $user->nama = $request->nama;
        $user->email = $request->email;
        $user->role = $request->role;
        $user->koperasi_id = $request->koperasi_id;

        if ($request->password) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return back()->with('success', 'Data pengguna berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        // Prevent deleting self
        if ($user->iduser == auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri!');
        }

        $user->delete();

        return back()->with('success', 'Pengguna berhasil dihapus!');
    }
}
