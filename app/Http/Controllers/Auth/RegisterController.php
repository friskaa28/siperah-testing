<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Peternak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        // 1. Validate Basic User Data
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|in:peternak,pengelola,admin',
            // Peternak specific validation (conditional)
            'jumlah_sapi' => 'required_if:role,peternak|nullable|integer|min:0',
            'lokasi' => 'required_if:role,peternak|nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            // 2. Create User
            $user = User::create([
                'nama' => $validated['nama'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => $validated['role'],
            ]);

            // 3. Create Peternak Profile if Role is Peternak
            if ($validated['role'] === 'peternak') {
                Peternak::create([
                    'iduser' => $user->iduser,
                    'nama_peternak' => $validated['nama'], // Use user name as default
                    'jumlah_sapi' => $validated['jumlah_sapi'],
                    'lokasi' => $validated['lokasi'],
                    'koperasi_id' => 1, // Defaulting to 1 for now, user didn't specify logic
                ]);
            }

            DB::commit();

            // 4. Auto Login
            Auth::login($user);

            // 5. Redirect based on role
            if ($user->isPeternak()) {
                return redirect()->route('dashboard.peternak');
            } else {
                return redirect()->route('dashboard.pengelola');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal mendaftar: ' . $e->getMessage()])->withInput();
        }
    }
}
