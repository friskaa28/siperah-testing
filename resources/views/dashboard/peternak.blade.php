@extends('layouts.app')

@section('title', 'Dashboard Peternak - SIPERAH')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-md-6">
        <h1 class="fw-bold mb-0">Dashboard Peternak</h1>
        <p class="text-muted">Selamat datang kembali, <strong>{{ $peternak->nama_peternak }}</strong></p>
    </div>
    <div class="col-md-6 text-end">
        <a href="{{ route('peternak.laporan.index') }}" class="btn btn-secondary px-4 py-2" style="border-radius: 8px; font-weight: 600;">
            <i class="fas fa-file-invoice-dollar"></i> Slip Gaji & Laporan
        </a>
    </div>
</div>

<!-- INFO PERIODE -->
<div style="background: #E0F2FE; color: #0369A1; padding: 12px 20px; border-radius: 12px; margin-bottom: 24px; border: 1px solid #BAE6FD; font-size: 0.9rem;">
    <i class="fas fa-calendar-alt"></i> Periode Gajian: <strong>{{ $startDate->format('d M Y') }}</strong> s/d <strong>{{ $endDate->format('d M Y') }}</strong>
</div>

<div class="grid" style="grid-template-columns: repeat(3, 1fr); gap: 1.5rem; margin-bottom: 2rem;">
    <!-- Card 1: Setoran Susu -->
    <div class="card" style="border-left: 4px solid var(--primary); padding: 1.5rem;">
        <h3 style="text-transform: uppercase; font-size: 0.8rem; letter-spacing: 0.05em;">Total Setoran Susu</h3>
        <h2 style="color: var(--primary); font-size: 2.25rem;">{{ number_format($totalLiter, 1, ',', '.') }} <small style="font-size: 1rem;">Liter</small></h2>
        <p class="mb-0 mt-2">Setoran terkumpul di periode ini.</p>
    </div>

    <!-- Card 2: Kasbon/Potongan -->
    <div class="card" style="border-left: 4px solid var(--danger); padding: 1.5rem;">
        <h3 style="text-transform: uppercase; font-size: 0.8rem; letter-spacing: 0.05em;">Total Kasbon/Pakan</h3>
        <h2 style="color: var(--danger); font-size: 2.25rem;">Rp {{ number_format($totalKasbon, 0, ',', '.') }}</h2>
        <p class="mb-0 mt-2">Total hutang barang/pakan saat ini.</p>
    </div>

    <!-- Card 3: Estimasi Gaji -->
    <div class="card" style="border-left: 4px solid var(--success); padding: 1.5rem; background: #F0FDF4;">
        <h3 style="text-transform: uppercase; font-size: 0.8rem; letter-spacing: 0.05em;">Estimasi Gaji Bersih</h3>
        <h2 style="color: var(--success); font-size: 2.25rem;">Rp {{ number_format($estimasiGaji, 0, ',', '.') }}</h2>
        <p class="mb-0 mt-2"><strong>(Liter x Rp {{ number_format($currentPrice, 0, ',', '.') }}) - Kasbon</strong></p>
        <div class="mt-2 pt-2 border-top" style="font-size: 0.75rem; color: var(--text-light);">
            <i class="fas fa-certificate"></i> Terverifikasi Digital SIPERAH
        </div>
    </div>
</div>

<div class="grid" style="grid-template-columns: 2fr 1fr; gap: 1.5rem;">
    <!-- Announcement Area -->
    <div class="card" style="padding: 2rem;">
        <h3 style="font-size: 1.25rem; margin-bottom: 1.5rem; font-weight: 700;"><i class="fas fa-bullhorn"></i> Pengumuman Terbaru</h3>
        <div class="announcement-list">
            @forelse($pengumuman as $p)
                <div style="background: #F8FAF9; padding: 20px; border-radius: 12px; margin-bottom: 15px; border-left: 5px solid #CBD5E1;">
                    <p style="margin-bottom: 8px; line-height: 1.6;">{{ $p->isi }}</p>
                    <small style="color: var(--text-light);">Disiarkan pada {{ $p->created_at->format('d M Y, H:i') }}</small>
                </div>
            @empty
                <div class="text-center py-5">
                    <p class="text-muted">Belum ada pengumuman hari ini.</p>
                </div>
            @endforelse
        </div>
    </div>


</div>

@endsection
