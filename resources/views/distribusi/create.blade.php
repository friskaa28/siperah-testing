@extends('layouts.app')

@section('title', 'Input Distribusi - SIPERAH')

@section('content')
<h1>Input Distribusi Susu</h1>

<div class="card" style="max-width: 600px; margin: 0 auto;">
    <form method="POST" action="{{ route('distribusi.store') }}" id="distribusiForm">
        @csrf

        <div class="form-group">
            <label class="form-label" for="tanggal_kirim">Tanggal Kirim</label>
            <input type="date" id="tanggal_kirim" name="tanggal_kirim" class="form-control" required value="{{ now()->format('Y-m-d') }}">
        </div>

        @if(isset($peternaks) && count($peternaks) > 0)
        <div class="form-group">
            <label class="form-label" for="idpeternak">Pilih Peternak</label>
            <select name="idpeternak" id="idpeternak" class="form-control" required onchange="window.location.href='?idpeternak=' + this.value">
                <option value="">-- Pilih Peternak --</option>
                @foreach($peternaks as $p)
                    <option value="{{ $p->idpeternak }}" {{ (request('idpeternak') == $p->idpeternak) ? 'selected' : '' }}>
                        {{ $p->nama_peternak }} ({{ $p->lokasi }})
                    </option>
                @endforeach
            </select>
        </div>
        @endif

        <div class="form-group">
            <label class="form-label" for="tujuan">Tujuan Distribusi</label>
            <input type="text" id="tujuan" name="tujuan" class="form-control" placeholder="Contoh: Koperasi Kradinan" required value="{{ old('tujuan', $lastDistribusi->tujuan ?? '') }}">
        </div>

        <div class="form-group" style="background: #ECFDF5; padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem; border: 1px solid #A7F3D0;">
            <label class="form-label" for="volume" style="color: #065F46; font-size: 1.1rem;">Volume Susu (Liter)</label>
            <input type="number" id="volume" name="volume" class="form-control" step="0.01" required autofocus style="font-size: 1.5rem; font-weight: 700; height: auto; padding: 1rem;" placeholder="0.00">
            <p style="margin-top: 0.5rem; font-size: 0.8rem; color: #065F46; opacity: 0.8;">* Masukkan volume susu yang dikirim.</p>
        </div>

        <div class="form-group">
            <label class="form-label" for="harga_per_liter">Harga Per Liter (Rp)</label>
            <input type="number" id="harga_per_liter" name="harga_per_liter" class="form-control" step="0.01" required value="{{ old('harga_per_liter', $lastDistribusi->harga_per_liter ?? 0) }}">
            <p style="margin-top: 0.5rem; font-size: 0.8rem; color: var(--text-light);">* Diambil secara otomatis dari data terakhir peternak ini.</p>
        </div>

        <div class="form-group">
            <label class="form-label" for="catatan">Catatan (Opsional)</label>
            <textarea id="catatan" name="catatan" class="form-control" rows="2"></textarea>
        </div>

        <div class="flex-between" style="margin-top: 2rem;">
            <a href="{{ auth()->user()->isPeternak() ? '/dashboard-peternak' : '/dashboard-pengelola' }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary" style="padding: 0.75rem 2.5rem; font-size: 1.1rem;">Simpan Distribusi</button>
        </div>
    </form>
</div>

@endsection