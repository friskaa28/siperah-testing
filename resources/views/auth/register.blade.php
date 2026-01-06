@extends('layouts.app')

@section('title', 'Daftar - SIPERAH')

@section('content')
<div style="display: flex; justify-content: center; align-items: center; min-height: 100vh; padding: 2rem 0;">
    <div class="card" style="width: 100%; max-width: 500px;">
        <div style="text-align: center; margin-bottom: 24px;">
            <img src="{{ asset('img/logo-siperah.png') }}" alt="SIPERAH Logo" style="height: 60px; object-fit: contain;">
        </div>
        <h2 style="text-align: center; margin-bottom: 24px;">Pendaftaran Akun Baru</h2>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            @if ($errors->any())
                <div class="alert alert-danger" style="background-color: #f8d7da; color: #721c24; padding: 12px; margin-bottom: 20px; border: 1px solid #f5c6cb; border-radius: 4px;">
                    <ul style="margin: 0; padding-left: 20px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="form-group">
                <label class="form-label">Peran (Role)</label>
                <select name="role" id="role" class="form-control" onchange="togglePeternakFields()">
                    <option value="peternak" {{ old('role') == 'peternak' ? 'selected' : '' }}>Peternak</option>
                    <option value="pengelola" {{ old('role') == 'pengelola' ? 'selected' : '' }}>Pengelola / Pengepul</option>
                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                </select>
                <small style="color: grey; display: block; margin-top: 4px;">Pilih peran anda dalam sistem.</small>
            </div>

            <div class="form-group">
                <label class="form-label" for="nama">Nama Lengkap</label>
                <input type="text" id="nama" name="nama" class="form-control" required value="{{ old('nama') }}">
            </div>

            <div class="form-group">
                <label class="form-label" for="email">Email</label>
                <input type="email" id="email" name="email" class="form-control" required value="{{ old('email') }}">
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="password_confirmation">Konfirmasi Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required>
            </div>

            <!-- Bagian Khusus Peternak -->
            <div id="peternak-fields" style="border-top: 1px solid var(--border); padding-top: 16px; margin-top: 16px;">
                <h3 style="font-size: 1rem; margin-bottom: 12px; color: var(--primary);">Informasi Peternakan</h3>
                
                <div class="form-group">
                    <label class="form-label" for="jumlah_sapi">Jumlah Sapi</label>
                    <input type="number" id="jumlah_sapi" name="jumlah_sapi" class="form-control" value="{{ old('jumlah_sapi') }}" min="0">
                </div>

                <div class="form-group">
                    <label class="form-label" for="lokasi">Lokasi Kandang / Alamat</label>
                    <input type="text" id="lokasi" name="lokasi" class="form-control" value="{{ old('lokasi') }}">
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 16px;">Daftar Sekarang</button>
            
            <div style="text-align: center; margin-top: 16px; font-size: 0.9rem;">
                Sudah punya akun? <a href="{{ route('login') }}" style="color: var(--primary); text-decoration: none; font-weight: 500;">Login disini</a>
            </div>
        </form>
    </div>
</div>

<script>
    function togglePeternakFields() {
        const role = document.getElementById('role').value;
        const fields = document.getElementById('peternak-fields');
        const jumlahSapi = document.getElementById('jumlah_sapi');
        const lokasi = document.getElementById('lokasi');

        if (role === 'peternak') {
            fields.style.display = 'block';
            jumlahSapi.setAttribute('required', 'required');
            lokasi.setAttribute('required', 'required');
        } else {
            fields.style.display = 'none';
            jumlahSapi.removeAttribute('required');
            lokasi.removeAttribute('required');
        }
    }

    // Run on load to set initial state (e.g. if old input exists)
    document.addEventListener('DOMContentLoaded', togglePeternakFields);
</script>
@endsection
