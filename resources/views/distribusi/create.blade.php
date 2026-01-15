@extends('layouts.app')

@section('title', 'Input Distribusi - SIPERAH')

@section('content')
<h1>Input Distribusi Susu</h1>

<div class="card" style="max-width: 650px; margin: 0 auto; border-radius: 16px;">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('distribusi.store') }}" id="distribusiForm">
            @csrf

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label small fw-bold" for="tanggal_kirim">Tanggal Kirim</label>
                    <input type="date" id="tanggal_kirim" name="tanggal_kirim" class="form-control form-control-sm" required value="{{ now()->format('Y-m-d') }}">
                </div>

                @if(isset($peternaks) && count($peternaks) > 0)
                <div class="col-md-6">
                    <label class="form-label small fw-bold" for="idpeternak">Pilih Peternak</label>
                    <select name="idpeternak" id="idpeternak" class="form-select border-2 form-select-sm" required onchange="window.location.href='?idpeternak=' + this.value">
                        <option value="">-- Pilih --</option>
                        @foreach($peternaks as $p)
                            <option value="{{ $p->idpeternak }}" {{ (request('idpeternak') == $p->idpeternak) ? 'selected' : '' }}>
                                {{ $p->nama_peternak }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif
            </div>

            <div class="mb-3">
                <label class="form-label small fw-bold" for="tujuan">Tujuan Distribusi</label>
                <input type="text" id="tujuan" name="tujuan" class="form-control form-control-sm" placeholder="Contoh: Koperasi Kradinan" required value="{{ old('tujuan', $lastDistribusi->tujuan ?? '') }}">
            </div>

            <div class="mb-4" style="background: #ECFDF5; padding: 1.25rem; border-radius: 12px; border: 1px solid #A7F3D0;">
                <label class="form-label fw-bold" for="volume" style="color: #065F46;">Volume Susu (Liter)</label>
                <div class="input-group">
                    <input type="number" id="volume" name="volume" class="form-control border-form" step="0.01" required autofocus style="font-size: 1.25rem; font-weight: 700; background: white;" placeholder="0.00">
                    <span class="input-group-text bg-white border-form fw-bold text-success">LITER</span>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label small fw-bold" for="harga_per_liter">Harga Per Liter (Rp)</label>
                    <input type="number" id="harga_per_liter" name="harga_per_liter" class="form-control form-control-sm" required value="{{ old('harga_per_liter', $lastDistribusi->harga_per_liter ?? 0) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-bold" for="catatan">Catatan</label>
                    <input type="text" id="catatan" name="catatan" class="form-control form-control-sm" placeholder="Opsional">
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center pt-2">
                <a href="{{ auth()->user()->isPeternak() ? '/dashboard-peternak' : '/dashboard-pengelola' }}" class="text-muted text-decoration-none small fw-bold">← Kembali</a>
                <button type="submit" class="btn btn-success px-5 fw-bold" style="border-radius: 10px; color: white;">Simpan Distribusi</button>
            </div>
        </form>
    </div>
</div>

@endsection