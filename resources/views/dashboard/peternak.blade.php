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
<div style="background: #E0F2FE; color: #0369A1; padding: 12px 20px; border-radius: 12px; margin-bottom: 16px; border: 1px solid #BAE6FD; font-size: 0.9rem;">
    <i class="fas fa-calendar-alt"></i> Periode Gajian: <strong>{{ $startDate->format('d M Y') }}</strong> s/d <strong>{{ $endDate->format('d M Y') }}</strong>
</div>

<!-- DATE FILTER -->
<div class="card mb-4" style="border-radius: 12px; border: 1px solid #E5E7EB;">
    <div class="card-body p-3">
        <form action="{{ route('dashboard.peternak') }}" method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="small fw-bold text-muted mb-1">Tanggal Mulai</label>
                <input type="date" name="start_date" class="form-control form-control-sm" value="{{ $startDateStr }}" required>
            </div>
            <div class="col-md-3">
                <label class="small fw-bold text-muted mb-1">Tanggal Akhir</label>
                <input type="date" name="end_date" class="form-control form-control-sm" value="{{ $endDateStr }}" required>
            </div>
            <div class="col-md-6">
                <button type="submit" class="btn btn-primary btn-sm px-4">
                    <i class="fas fa-filter"></i> Filter
                </button>
                <a href="{{ route('dashboard.peternak') }}" class="btn btn-outline-secondary btn-sm px-4">
                    <i class="fas fa-redo"></i> Reset
                </a>
            </div>
        </form>
    </div>
</div>

<div class="dashboard-grid mb-4">
    <!-- Card 1: Setoran Susu -->
    <div class="card" style="border-left: 4px solid var(--primary); padding: 1.5rem;">
        <h3 style="text-transform: uppercase; font-size: 0.8rem; letter-spacing: 0.05em;">Total Setoran Susu</h3>
        <h2 style="color: var(--primary); font-size: 2.25rem;">{{ number_format($totalLiter, 1, ',', '.') }} <small style="font-size: 1rem;">Liter</small></h2>
        <p class="mb-0 mt-2">Setoran terkumpul di periode ini.</p>
    </div>

    <!-- Card 2: Potongan -->
    <div class="card" style="border-left: 4px solid var(--danger); padding: 1.5rem;">
        <h3 style="text-transform: uppercase; font-size: 0.8rem; letter-spacing: 0.05em;">Total Potongan</h3>
        <h2 style="color: var(--danger); font-size: 2.25rem;">Rp {{ number_format($totalKasbon, 0, ',', '.') }}</h2>
        <p class="mb-0 mt-2">Total potongan/hutang saat ini.</p>
    </div>

    <div class="card" style="border-left: 4px solid var(--success); padding: 1.5rem; background: #F0FDF4;">
        <h3 style="text-transform: uppercase; font-size: 0.8rem; letter-spacing: 0.05em;">Estimasi Gaji Bersih</h3>
        <h2 style="color: var(--success); font-size: 2.25rem;">Rp {{ number_format($estimasiGaji, 0, ',', '.') }}</h2>
        <p class="mb-0 mt-2"><strong>(Liter x Rp {{ number_format($currentPrice, 0, ',', '.') }}) - Potongan</strong></p>
        <div class="mt-2 pt-2 border-top" style="font-size: 0.75rem; color: var(--text-light);">
            <i class="fas fa-certificate"></i> Terverifikasi Digital SIPERAH
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-8">
        <div class="card p-3 h-100">
            <h4 class="mb-3">Grafik Produksi Harian ({{ $startDate->format('d M') }} - {{ $endDate->format('d M') }})</h4>
            <canvas id="dailyChart" style="max-height: 300px;"></canvas>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-3 h-100">
            <h4 class="mb-3">Performa Bulanan</h4>
            <canvas id="monthlyChart" style="max-height: 300px;"></canvas>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Daily Chart
    const ctxDaily = document.getElementById('dailyChart').getContext('2d');
    new Chart(ctxDaily, {
        type: 'bar',
        data: {
            labels: {!! json_encode($dailyProduction['labels'] ?? []) !!},
            datasets: [{
                label: 'Produksi (Liter)',
                data: {!! json_encode($dailyProduction['data'] ?? []) !!},
                backgroundColor: '#3b82f6',
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    // Monthly Chart
    const ctxMonthly = document.getElementById('monthlyChart').getContext('2d');
    new Chart(ctxMonthly, {
        type: 'line',
        data: {
            labels: {!! json_encode($monthlyProduction['labels'] ?? []) !!},
            datasets: [{
                label: 'Total Liter',
                data: {!! json_encode($monthlyProduction['data'] ?? []) !!},
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
</script>

<div class="dashboard-grid-bottom mb-4">
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

    <!-- Notification / Activity Widget -->
    <div class="card" style="padding: 1.5rem;">
        <h3 style="font-size: 1.1rem; margin-bottom: 1.5rem; font-weight: 700;"><i class="fas fa-bell text-warning"></i> Notifikasi Terkini</h3>
        <div class="list-group list-group-flush">
            @forelse($notifikasi as $notif)
                <div class="list-group-item px-0 py-3 border-bottom">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-1 fw-bold text-dark" style="font-size: 0.9rem;">{{ $notif->judul }}</h6>
                            <p class="mb-0 text-muted small" style="line-height: 1.3;">{{ Str::limit($notif->pesan, 40) }}</p>
                        </div>
                        <small class="text-secondary text-nowrap ms-2" style="font-size: 0.7rem;">
                            {{ $notif->created_at->diffForHumans() }}
                        </small>
                    </div>
                </div>
            @empty
                <div class="text-center py-4">
                    <i class="fas fa-check-circle text-muted mb-2" style="font-size: 2rem;"></i>
                    <p class="text-muted small mb-0">Tidak ada notifikasi baru</p>
                </div>
            @endforelse
        </div>
        <a href="{{ route('notifikasi.index') }}" class="btn btn-light w-100 mt-3 text-primary fw-bold" style="font-size: 0.85rem;">Lihat Semua</a>
    </div>
</div>

@section('styles')
<style>
    .dashboard-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    .dashboard-grid-bottom {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    @media (min-width: 992px) {
        .dashboard-grid {
            grid-template-columns: repeat(3, 1fr);
        }
        .dashboard-grid-bottom {
            grid-template-columns: 2fr 1fr;
        }
    }
</style>
@endsection
@endsection
