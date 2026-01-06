@extends('layouts.app')

@section('title', 'Input Produksi - SIPERAH')

@section('content')
<h1>Input Produksi Harian</h1>

<div class="card" style="max-width: 600px; margin: 0 auto;">
    <form method="POST" action="{{ route('produksi.store') }}" enctype="multipart/form-data" id="produksiForm">
        @csrf

        <div class="grid" style="grid-template-columns: 1fr 1fr; gap: 1rem;">
            <div class="form-group">
                <label class="form-label" for="tanggal">Tanggal</label>
                <input type="date" id="tanggal" name="tanggal" class="form-control" required value="{{ now()->format('Y-m-d') }}">
            </div>

            <div class="form-group">
                <label class="form-label" for="waktu_setor">Waktu Setor</label>
                <select name="waktu_setor" id="waktu_setor" class="form-control" required>
                    <option value="pagi" {{ old('waktu_setor') == 'pagi' ? 'selected' : '' }}>Pagi</option>
                    <option value="sore" {{ old('waktu_setor') == 'sore' ? 'selected' : '' }}>Sore</option>
                </select>
            </div>
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

        <div class="form-group" style="background: #F0F9FF; padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem; border: 1px solid #BAE6FD;">
            <label class="form-label" for="jumlah_susu_liter" style="color: #0369A1; font-size: 1.1rem;">Jumlah Susu (Liter)</label>
            <input type="number" id="jumlah_susu_liter" name="jumlah_susu_liter" class="form-control" step="0.01" required autofocus style="font-size: 1.5rem; font-weight: 700; height: auto; padding: 1rem;" placeholder="0.00">
            <p style="margin-top: 0.5rem; font-size: 0.8rem; color: #0369A1; opacity: 0.8;">* Masukkan jumlah susu yang disetorkan hari ini.</p>
        </div>

        <div style="background: #F9FAFB; padding: 1.5rem; border-radius: 12px; border: 1px solid var(--border); margin-bottom: 2rem;">
            <h3 style="font-size: 1rem; margin-bottom: 1rem; color: var(--dark);">Biaya Operasional (Otomatis dari data terakhir)</h3>
            <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="biaya_pakan">Biaya Pakan (Rp)</label>
                    <input type="number" id="biaya_pakan" name="biaya_pakan" class="form-control" step="0.01" required value="{{ old('biaya_pakan', $lastProduksi->biaya_pakan ?? 0) }}">
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="biaya_tenaga">Biaya Tenaga (Rp)</label>
                    <input type="number" id="biaya_tenaga" name="biaya_tenaga" class="form-control" step="0.01" required value="{{ old('biaya_tenaga', $lastProduksi->biaya_tenaga ?? 0) }}">
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="biaya_operasional">Biaya Operasional (Rp)</label>
                    <input type="number" id="biaya_operasional" name="biaya_operasional" class="form-control" step="0.01" required value="{{ old('biaya_operasional', $lastProduksi->biaya_operasional ?? 0) }}">
                </div>
            </div>
        </div>

        <div class="grid" style="grid-template-columns: 1fr 1fr; gap: 1rem;">
            <div class="form-group">
                <label class="form-label" for="foto_bukti">Foto Bukti (Opsional)</label>
                <input type="file" id="foto_bukti" name="foto_bukti" class="form-control" accept="image/*">
            </div>

            <div class="form-group">
                <label class="form-label" for="catatan">Catatan</label>
                <textarea id="catatan" name="catatan" class="form-control" rows="1" style="min-height: 45px;"></textarea>
            </div>
        </div>

        <div class="flex-between" style="margin-top: 1rem;">
            <a href="{{ auth()->user()->isPeternak() ? '/dashboard-peternak' : '/dashboard-pengelola' }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary" style="padding: 0.75rem 2.5rem; font-size: 1.1rem;">Simpan Data</button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-detect waktu setor
        const now = new Date();
        const hour = now.getHours();
        const waktuSelect = document.getElementById('waktu_setor');
        
        // Hanya ganti jika user belum memilih manual (old value check could be added if needed)
        if (!{{ old('waktu_setor') ? 'true' : 'false' }}) {
            if (hour >= 5 && hour < 12) {
                waktuSelect.value = 'pagi';
            } else if (hour >= 13 && hour <= 23) {
                waktuSelect.value = 'sore';
            }
        }

        // Shortcut Enter untuk submit jika di Liter (tapi biar lebih safety, manual click is fine or keydown handler)
        const literInput = document.getElementById('jumlah_susu_liter');
        literInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                // Biarkan default behavior yaitu submit form
            }
        });
    });
</script>

@endsection
