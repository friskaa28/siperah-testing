@extends('layouts.app')

@section('title', 'Edit Slip Gaji - SIPERAH')

@section('content')
<div class="mb-4">
    <a href="{{ route('gaji.index', ['bulan' => $slip->bulan, 'tahun' => $slip->tahun]) }}" class="text-primary" style="text-decoration: none;">&larr; Kembali ke Daftar</a>
    <h1 class="mt-2">Detail & Potongan Slip ({{ $slip->peternak->nama_peternak }})</h1>
</div>

<form action="{{ route('gaji.update', $slip->idslip) }}" method="POST">
    @csrf
    @method('PUT')
    
    <div class="grid" style="grid-template-columns: 1fr 1fr; gap: 2rem;">
        <!-- INFO DASAR -->
        <div class="card">
            <h3 class="mb-2">Informasi Pembayaran</h3>
            <div class="form-group">
                <label class="form-label">Jumlah Susu (Liter)</label>
                <input type="number" step="0.01" name="jumlah_susu" class="form-control" value="{{ $slip->jumlah_susu }}" required>
            </div>
            <div class="form-group">
                <label class="form-label">Harga @ Liter (Rp)</label>
                <input type="number" name="harga_satuan" class="form-control" value="{{ (int)$slip->harga_satuan }}" required>
            </div>
            <div class="form-group">
                <label class="form-label">Total Pembayaran (Kotor)</label>
                <input type="number" name="total_pembayaran" class="form-control" value="{{ (int)$slip->total_pembayaran }}" required>
            </div>
            <div class="form-group">
                <label class="form-label">Status</label>
                <select name="status" class="form-control">
                    <option value="pending" {{ $slip->status == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="dibayar" {{ $slip->status == 'dibayar' ? 'selected' : '' }}>Dibayar</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Tanggal Bayar</label>
                <input type="date" name="tanggal_bayar" class="form-control" value="{{ $slip->tanggal_bayar ? $slip->tanggal_bayar->format('Y-m-d') : '' }}">
            </div>
        </div>

        <!-- POTONGAN -->
        <div class="card">
            <h3 class="mb-2">Daftar Potongan (Rp)</h3>
            <div class="grid" style="grid-template-columns: 1fr 1fr; gap: 1rem;">
                @php
                    $potongans = [
                        'potongan_shr' => '1. SHR',
                        'potongan_hutang_bl_ll' => '2. HUT. BL LL',
                        'potongan_pakan_a' => '3. PAKAN A',
                        'potongan_pakan_b' => '4. PAKAN B',
                        'potongan_vitamix' => '5. VITAMIX',
                        'potongan_konsentrat' => '6. KONSENTRAT',
                        'potongan_skim' => '7. SKIM',
                        'potongan_ib_keswan' => '8. IB/KESWAN',
                        'potongan_susu_a' => '9. SUSU A',
                        'potongan_kas_bon' => '10. KAS BON',
                        'potongan_pakan_b_2' => '11. PAKAN B (2)',
                        'potongan_sp' => '12. SP',
                        'potongan_karpet' => '13. KARPET',
                        'potongan_vaksin' => '14. VAKSIN',
                        'potongan_lain_lain' => '15. LAIN-LAIN',
                    ];
                @endphp

                @foreach($potongans as $key => $label)
                <div class="form-group" style="margin-bottom: 0.5rem;">
                    <label class="form-label" style="font-size: 0.8rem;">{{ $label }}</label>
                    <input type="number" name="{{ $key }}" class="form-control" style="padding: 0.5rem;" value="{{ (int)$slip->$key }}">
                </div>
                @endforeach
            </div>

            <div class="mt-4 p-2" style="background: #F3F4F6; border-radius: 8px;">
                <div class="flex-between mb-1">
                    <span class="font-bold">Total Potongan:</span>
                    <span id="total_p_display" class="font-bold text-danger">Rp {{ number_format($slip->total_potongan, 0, ',', '.') }}</span>
                </div>
                <div class="flex-between">
                    <span class="font-bold" style="font-size: 1.2rem;">Sisa Pembayaran:</span>
                    <span id="sisa_p_display" class="font-bold text-primary" style="font-size: 1.2rem;">Rp {{ number_format($slip->sisa_pembayaran, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4 d-flex gap-2">
        <button type="submit" class="btn btn-primary" style="padding: 1rem 3rem;">Simpan Perubahan</button>
        <button type="button" class="btn btn-secondary" onclick="window.print()">Cetak Halaman Ini</button>
    </div>
</form>

<script>
    // Simple auto-calculate logic for display
    document.querySelectorAll('input[type="number"]').forEach(input => {
        input.addEventListener('input', calculate);
    });

    function calculate() {
        const totalBayar = parseFloat(document.getElementsByName('total_pembayaran')[0].value) || 0;
        let totalPotongan = 0;
        
        @foreach($potongans as $key => $label)
            totalPotongan += parseFloat(document.getElementsByName('{{ $key }}')[0].value) || 0;
        @endforeach

        const sisa = totalBayar - totalPotongan;

        document.getElementById('total_p_display').innerText = 'Rp ' + totalPotongan.toLocaleString('id-ID');
        document.getElementById('sisa_p_display').innerText = 'Rp ' + sisa.toLocaleString('id-ID');
    }
</script>
@endsection
