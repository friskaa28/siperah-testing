@extends('layouts.app')

@section('title', 'Dashboard Peternak - SIPERAH')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-md-6">
        <h1 class="fw-bold mb-0">Dashboard Peternak</h1>
        <p class="text-muted">Selamat datang kembali, <strong>{{ $peternak->nama_peternak }}</strong></p>
    </div>
    <div class="col-md-6 text-end">
        <a href="/laporan" class="btn btn-primary px-4 py-2" style="border-radius: 8px; font-weight: 600;">
            📄 Laporan Pendapatan
        </a>
    </div>
</div>

<div class="grid" style="grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 1.5rem;">
    <!-- KPI Card 1: Pendapatan -->
    <div class="card" style="border-left: 4px solid var(--primary); padding: 1rem;">
        <h3 style="color: var(--text-light); font-size: 0.75rem; margin-bottom: 2px; text-transform: uppercase;">Pendapatan (Bln Ini)</h3>
        <h2 style="color: var(--primary); margin: 0; font-size: 1.5rem;">Rp {{ number_format($totalPenjualanBulanIni, 0, ',', '.') }}</h2>
        <div style="background: #f1f5f9; border-radius: 4px; height: 6px; margin-top: 8px; overflow: hidden;">
            <div style="background: var(--primary); height: 100%; width: {{ min($progressTarget, 100) }}%;"></div>
        </div>
    </div>

    <!-- KPI Card 2: Average Income -->
    <div class="card" style="border-left: 4px solid var(--success); padding: 1rem;">
        <h3 style="color: var(--text-light); font-size: 0.75rem; margin-bottom: 2px; text-transform: uppercase;">Rata-rata Pendapatan</h3>
        <h2 style="color: var(--success); margin: 0; font-size: 1.5rem;">Rp {{ number_format($averageIncome, 0, ',', '.') }}</h2>
        <p style="color: var(--text-light); font-size: 0.65rem; margin: 0;">6 bulan terakhir</p>
    </div>

    <!-- KPI Card 3: Target -->
    <div class="card" style="border-left: 4px solid var(--warning); padding: 1rem;">
        <h3 style="color: var(--text-light); font-size: 0.75rem; margin-bottom: 2px; text-transform: uppercase;">Pencapaian Target</h3>
        <h2 style="color: var(--warning); margin: 0; font-size: 1.5rem;">{{ round($progressTarget, 1) }}%</h2>
        <p style="color: var(--text-light); font-size: 0.65rem; margin: 0;">Dari target Rp 50jt</p>
    </div>

    <!-- KPI Card 4: Sapi -->
    <div class="card" style="border-left: 4px solid var(--danger); padding: 1rem;">
        <h3 style="color: var(--text-light); font-size: 0.75rem; margin-bottom: 2px; text-transform: uppercase;">Total Sapi</h3>
        <h2 style="color: var(--danger); margin: 0; font-size: 1.5rem;">{{ $peternak->jumlah_sapi }}</h2>
        <p style="color: var(--text-light); font-size: 0.65rem; margin: 0;">Ekor terdaftar</p>
    </div>
</div>

<div class="grid" style="grid-template-columns: 2fr 1fr; gap: 1rem; margin-top: 1.5rem;">
    <!-- Income Chart -->
    <div class="card" style="padding: 1.5rem;">
        <h3 style="font-size: 1rem; margin-bottom: 1rem; font-weight: 700;">Tren Pendapatan (6 Bulan Terakhir)</h3>
        <div style="height: 280px;">
            <canvas id="incomeChart"></canvas>
        </div>
    </div>

    <!-- Recent Notifications -->
    <div class="card" style="padding: 1.5rem;">
        <h3 style="font-size: 1rem; margin-bottom: 1rem; font-weight: 700;">🔔 Notifikasi Terbaru</h3>
        <div class="notif-list">
            @forelse($notifikasi as $n)
                <div style="padding: 10px; border-bottom: 1px solid #f1f5f9; transition: all 0.2s;" onmouseover="this.style.backgroundColor='#f8fafc'" onmouseout="this.style.backgroundColor='transparent'">
                    <p style="font-weight: 600; font-size: 0.85rem; margin-bottom: 2px; color: var(--text);">{{ $n->judul }}</p>
                    <p style="font-size: 0.75rem; color: var(--text-light); margin: 0;">{{ $n->pesan }}</p>
                </div>
            @empty
                <div class="text-center py-5">
                    <p class="text-muted small">Tidak ada notifikasi baru</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    const ctx = document.getElementById('incomeChart').getContext('2d');
    const incomeData = {!! json_encode($incomeHistory) !!};

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: incomeData.map(d => d.month),
            datasets: [{
                label: 'Pendapatan (Rp)',
                data: incomeData.map(d => d.total),
                borderColor: '#2180D3',
                backgroundColor: 'rgba(33, 128, 211, 0.1)',
                borderWidth: 3,
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#2180D3',
                pointBorderWidth: 2,
                pointRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#f1f5f9' },
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + value.toLocaleString('id-ID');
                        },
                        font: { size: 10 }
                    }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 10 } }
                }
            }
        }
    });
</script>
@endsection
