@extends('layouts.app')

@section('title', 'Dashboard Pengelola - SIPERAH')

@section('content')
<h1>Dashboard Pengelola</h1>

<div class="grid" style="grid-template-columns: repeat(4, 1fr); gap: 0.75rem; margin-bottom: 1rem;">
    <!-- KPI Card 1: Total Peternak -->
    <div class="card" style="border-left: 4px solid var(--primary); padding: 0.75rem 1rem;">
        <h3 style="color: var(--text-light); font-size: 0.75rem; margin-bottom: 2px; text-transform: uppercase; letter-spacing: 0.5px;">Peternak</h3>
        <h2 style="color: var(--primary); margin: 0; font-size: 1.5rem;">{{ $totalPeternak }}</h2>
        <p style="color: var(--text-light); font-size: 0.65rem; margin: 0;">Terdaftar</p>
    </div>

    <!-- KPI Card 2: Total Produksi -->
    <div class="card" style="border-left: 4px solid var(--success); padding: 0.75rem 1rem;">
        <h3 style="color: var(--text-light); font-size: 0.75rem; margin-bottom: 2px; text-transform: uppercase; letter-spacing: 0.5px;">Produksi</h3>
        <h2 style="color: var(--success); margin: 0; font-size: 1.5rem;">{{ number_format($totalProduksiBulanIni, 1) }} L</h2>
        <p style="color: var(--text-light); font-size: 0.65rem; margin: 0;">Bulan ini</p>
    </div>

    <!-- KPI Card 3: Total Distribusi -->
    <div class="card" style="border-left: 4px solid var(--warning); padding: 0.75rem 1rem;">
        <h3 style="color: var(--text-light); font-size: 0.75rem; margin-bottom: 2px; text-transform: uppercase; letter-spacing: 0.5px;">Distribusi</h3>
        <h2 style="color: var(--warning); margin: 0; font-size: 1.5rem;">{{ $totalDistribusi }}</h2>
        <p style="color: var(--text-light); font-size: 0.65rem; margin: 0;">Pengiriman</p>
    </div>

    <!-- KPI Card 4: Total Pendapatan -->
    <div class="card" style="border-left: 4px solid var(--danger); padding: 0.75rem 1rem;">
        <h3 style="color: var(--text-light); font-size: 0.75rem; margin-bottom: 2px; text-transform: uppercase; letter-spacing: 0.5px;">Revenue</h3>
        <h2 style="color: var(--danger); margin: 0; font-size: 1.5rem;">Rp {{ number_format($totalBagiHasil, 0, ',', '.') }}</h2>
        <p style="color: var(--text-light); font-size: 0.65rem; margin: 0;">Bagi hasil</p>
    </div>
</div>

<div class="grid" style="grid-template-columns: 2fr 1fr; gap: 1rem; margin-top: 1rem;">
    <!-- Main Chart: Produksi vs Distribusi -->
    <div class="card" style="padding: 1rem;">
        <h3 style="font-size: 1rem; margin-bottom: 0.75rem;">Statistik Bulanan ({{ date('Y') }})</h3>
        <div style="height: 240px;">
            <canvas id="mainChart"></canvas>
        </div>
    </div>

    <!-- Secondary Chart: Bagi Hasil Breakdown -->
    <div class="card" style="padding: 1rem;">
        <h3 style="font-size: 1rem; margin-bottom: 0.75rem;">Status Pembayaran</h3>
        <div style="height: 240px; display: flex; justify-content: center;">
            <canvas id="pieChart"></canvas>
        </div>
    </div>
</div>

<div class="grid" style="grid-template-columns: 1fr 1fr; gap: 1rem; margin-top: 1rem;">
    <!-- Top 5 Peternak -->
    <div class="card" style="padding: 1rem;">
        <h3 style="font-size: 1rem; margin-bottom: 0.75rem;">🏆 Top 5 Peternak (Bulan Ini)</h3>
        <table class="table" style="font-size: 0.8rem; margin-bottom: 0;">
            <thead>
                <tr>
                    <th>Peternak</th>
                    <th>No. Peternak</th>
                    <th>Jumlah Distribusi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($top5Peternak as $p)
                <tr>
                    <td style="font-weight: 500;">{{ $p->nama_peternak }}</td>
                    <td>{{ $p->no_peternak ?: '-' }}</td>
                    <td style="text-align: right; color: var(--primary); font-weight: 600;">{{ $p->distribusi_count }} x</td>
                </tr>
                @empty
                <tr><td colspan="3" class="text-center">Belum ada data</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Quick Actions -->
    <div class="card" style="padding: 1rem;">
        <h3 style="font-size: 1rem; margin-bottom: 0.75rem;">⚡ Akses Cepat</h3>
        <div class="grid" style="grid-template-columns: 1fr 1fr; gap: 0.75rem;">
            <a href="/produksi/input" class="btn btn-success text-center p-3" style="height: auto; flex-direction: column; font-size: 0.85rem;">
                <span style="font-size: 1.2rem; display: block; margin-bottom: 3px;">➕</span>
                Input Produksi
            </a>
            <a href="/manajemen-distribusi" class="btn btn-warning text-center p-3" style="height: auto; flex-direction: column; font-size: 0.85rem;">
                <span style="font-size: 1.2rem; display: block; margin-bottom: 3px;">🚚</span>
                Manajemen Distribusi
            </a>
            <a href="/gaji" class="btn btn-primary text-center p-3" style="height: auto; flex-direction: column; font-size: 0.85rem;">
                <span style="font-size: 1.2rem; display: block; margin-bottom: 3px;">💵</span>
                Manajemen Gaji
            </a>

        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // Bar Chart Configuration
    const ctxMain = document.getElementById('mainChart').getContext('2d');
    const monthlyStats = {!! json_encode($monthlyStats) !!};
    
    new Chart(ctxMain, {
        type: 'bar',
        data: {
            labels: monthlyStats.map(s => s.month),
            datasets: [
                {
                    label: 'Produksi {{ date('Y') }}',
                    data: monthlyStats.map(s => s.produksi_this),
                    backgroundColor: 'rgba(34, 197, 94, 0.8)',
                    borderColor: '#22C55E',
                    borderWidth: 1,
                    borderRadius: 5
                },
                {
                    label: 'Produksi {{ date('Y') - 1 }}',
                    data: monthlyStats.map(s => s.produksi_last),
                    backgroundColor: 'rgba(156, 163, 175, 0.3)',
                    borderColor: '#9CA3AF',
                    borderWidth: 1,
                    borderRadius: 5
                },
                {
                    label: 'Distribusi {{ date('Y') }}',
                    data: monthlyStats.map(s => s.distribusi),
                    backgroundColor: 'rgba(33, 128, 211, 0.8)',
                    borderColor: '#2180D3',
                    borderWidth: 1,
                    borderRadius: 5
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true, grid: { color: '#f3f4f6' } },
                x: { grid: { display: false } }
            },
            plugins: {
                legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 10 } } }
            }
        }
    });

    // Pie Chart Configuration
    const ctxPie = document.getElementById('pieChart').getContext('2d');
    const breakdown = {!! json_encode($bagiHasilBreakdown) !!};
    
    new Chart(ctxPie, {
        type: 'doughnut',
        data: {
            labels: breakdown.map(b => b.status === 'dibayar' ? 'Selesai' : 'Pending'),
            datasets: [{
                data: breakdown.map(b => b.total),
                backgroundColor: ['#22C55E', '#F97316', '#EF4444'],
                hoverOffset: 4,
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 10 }, usePointStyle: true } }
            }
        }
    });
</script>
@endsection
