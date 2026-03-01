@extends('layouts.app')

@section('title', 'KPI Analytics Dashboard - SIPERAH')

@section('styles')
<style>
    .kpi-header {
        background: linear-gradient(135deg, #1a3a5c 0%, #2180D3 60%, #38bdf8 100%);
        border-radius: 16px;
        padding: 2rem 2.5rem;
        color: white;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
    }
    .kpi-header::after {
        content: '';
        position: absolute;
        top: -40px; right: -40px;
        width: 200px; height: 200px;
        background: rgba(255,255,255,0.07);
        border-radius: 50%;
    }
    .kpi-header h1 { color: white; font-size: 1.8rem; margin: 0; font-weight: 700; }
    .kpi-header p  { color: rgba(255,255,255,0.8); font-size: 0.95rem; margin: 0.3rem 0 0; }

    .kpi-cards { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.25rem; margin-bottom: 2rem; }
    @media(max-width: 1100px) { .kpi-cards { grid-template-columns: repeat(2, 1fr); } }
    @media(max-width: 600px)  { .kpi-cards { grid-template-columns: 1fr; } }

    .kpi-card {
        background: white;
        border-radius: 14px;
        padding: 1.5rem;
        border: 1px solid var(--border);
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        position: relative;
        overflow: hidden;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .kpi-card:hover { transform: translateY(-3px); box-shadow: 0 6px 20px rgba(0,0,0,0.1); }
    .kpi-card .icon {
        width: 50px; height: 50px;
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.4rem; margin-bottom: 1rem;
    }
    .kpi-card .value { font-size: 2rem; font-weight: 700; color: var(--dark); line-height: 1; }
    .kpi-card .label { font-size: 0.82rem; color: var(--text-light); margin-top: 0.3rem; font-weight: 500; }
    .kpi-card .sub   { font-size: 0.8rem; margin-top: 0.5rem; }

    .icon-blue   { background: #EFF6FF; color: #2180D3; }
    .icon-green  { background: #ECFDF5; color: #22C55E; }
    .icon-orange { background: #FFF7ED; color: #F97316; }
    .icon-purple { background: #F5F3FF; color: #7C3AED; }
    .icon-red    { background: #FEF2F2; color: #EF4444; }

    .charts-row { display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; margin-bottom: 2rem; }
    @media(max-width: 900px) { .charts-row { grid-template-columns: 1fr; } }

    .chart-card {
        background: white; border-radius: 14px; padding: 1.5rem;
        border: 1px solid var(--border); box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    }
    .chart-card h3 { font-size: 1rem; font-weight: 600; color: var(--dark); margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem; }

    .period-tabs { display: flex; gap: 0.5rem; margin-bottom: 1.5rem; }
    .period-tab {
        padding: 0.4rem 1rem; border-radius: 99px; font-size: 0.85rem;
        font-weight: 500; text-decoration: none; color: var(--text-light);
        background: #F3F4F6; border: none; cursor: pointer; transition: all 0.2s;
    }
    .period-tab.active { background: var(--primary); color: white; }

    .error-badge {
        display: inline-flex; align-items: center; gap: 0.3rem;
        padding: 0.25rem 0.75rem; border-radius: 99px; font-size: 0.78rem; font-weight: 500;
    }
    .badge-danger  { background: #FEF2F2; color: #DC2626; }
    .badge-success { background: #ECFDF5; color: #16A34A; }
    .badge-warning { background: #FFFBEB; color: #D97706; }

    .trend-up   { color: #22C55E; font-size: 0.8rem; }
    .trend-down { color: #EF4444; font-size: 0.8rem; }

    .recent-table { width: 100%; border-collapse: collapse; font-size: 0.88rem; }
    .recent-table th { padding: 0.6rem 0.75rem; text-align: left; color: var(--text-light); font-weight: 500; font-size: 0.82rem; border-bottom: 1px solid var(--border); }
    .recent-table td { padding: 0.75rem 0.75rem; border-bottom: 1px solid #F9FAFB; }
    .recent-table tr:last-child td { border: none; }
</style>
@endsection

@section('content')
<div class="kpi-header">
    <h1><i class="fas fa-chart-bar" style="margin-right:0.5rem;"></i>KPI Analytics Dashboard</h1>
    <p>Sistem Informasi Peternakan Rahmah &bull; Analitik Kuantitatif &bull; {{ now()->format('d F Y') }}</p>
</div>

{{-- Period Filter --}}
<div class="period-tabs">
    <a href="?period=weekly"  class="period-tab @if($period === 'weekly') active @endif">Mingguan</a>
    <a href="?period=monthly" class="period-tab @if($period === 'monthly') active @endif">Bulanan</a>
    <a href="?period=yearly"  class="period-tab @if($period === 'yearly') active @endif">Tahunan</a>
</div>

{{-- KPI Cards --}}
<div class="kpi-cards">
    {{-- Card 1: Avg Session Duration --}}
    <div class="kpi-card">
        <div class="icon icon-blue"><i class="fas fa-clock"></i></div>
        <div class="value">{{ $avgDurationMin }} <small style="font-size:1rem;">min</small></div>
        <div class="label">Rata-rata Durasi Penggunaan</div>
        <div class="sub" style="color:var(--text-light);">
            <i class="fas fa-users" style="margin-right:2px;"></i>
            {{ $sessionStats['unique_users'] }} pengguna aktif &bull; {{ $totalSessions }} sesi total
        </div>
    </div>

    {{-- Card 2: Error Rate --}}
    <div class="kpi-card">
        <div class="icon icon-red"><i class="fas fa-exclamation-triangle"></i></div>
        <div class="value">{{ $errorRate }}%</div>
        <div class="label">Error Rate (Unresolved)</div>
        <div class="sub">
            <span class="error-badge badge-danger">{{ $totalErrors - $resolvedErrors }} unresolved</span>
            <span class="error-badge badge-success" style="margin-left:4px;">{{ $resolvedErrors }} solved</span>
        </div>
    </div>

    {{-- Card 3: Active Users --}}
    <div class="kpi-card">
        <div class="icon icon-green"><i class="fas fa-user-check"></i></div>
        <div class="value">{{ $activeUsers }}</div>
        <div class="label">User Aktif (30 Hari Terakhir)</div>
        <div class="sub" style="color:var(--text-light);">
            dari {{ $totalUsers }} total user terdaftar
        </div>
    </div>

    {{-- Card 4: Profit Growth --}}
    <div class="kpi-card">
        <div class="icon icon-purple"><i class="fas fa-chart-line"></i></div>
        @if($latestProfit)
            @php
                $growth = $latestProfit->revenue_before > 0
                    ? round((($latestProfit->revenue_after - $latestProfit->revenue_before) / $latestProfit->revenue_before) * 100, 1)
                    : 0;
            @endphp
            <div class="value">{{ $growth }}%</div>
            <div class="label">Pertumbuhan Revenue ({{ $latestProfit->period }})</div>
            <div class="sub">
                <span class="{{ $growth >= 0 ? 'trend-up' : 'trend-down' }}">
                    <i class="fas fa-arrow-{{ $growth >= 0 ? 'up' : 'down' }}"></i>
                    Setelah penerapan sistem
                </span>
            </div>
        @else
            <div class="value">—</div>
            <div class="label">Data Profit Belum Tersedia</div>
            <div class="sub" style="color:var(--text-light);">Input melalui menu Admin KPI</div>
        @endif
    </div>
</div>

{{-- Charts Row --}}
<div class="charts-row">
    {{-- Usage Chart --}}
    <div class="chart-card">
        <h3><i class="fas fa-chart-area" style="color:#2180D3;"></i> Tren Penggunaan Sistem</h3>
        <canvas id="usageChart" height="100"></canvas>
    </div>

    {{-- Peak Hours --}}
    <div class="chart-card">
        <h3><i class="fas fa-clock" style="color:#F97316;"></i> Jam Penggunaan Tertinggi</h3>
        <canvas id="hourChart" height="200"></canvas>
    </div>
</div>

{{-- Second Charts Row --}}
<div class="charts-row">
    {{-- Error Trend --}}
    <div class="chart-card">
        <h3><i class="fas fa-bug" style="color:#EF4444;"></i> Tren Error per Periode</h3>
        <canvas id="errorChart" height="100"></canvas>
    </div>

    {{-- Recent Errors --}}
    <div class="chart-card">
        <h3><i class="fas fa-list" style="color:#7C3AED;"></i> Error Terbaru</h3>
        @if($recentErrors->isEmpty())
            <div style="text-align:center; color:var(--text-light); padding:2rem 0;">
                <i class="fas fa-check-circle" style="font-size:2rem; color:#22C55E; display:block; margin-bottom:0.5rem;"></i>
                Tidak ada error yang tercatat
            </div>
        @else
            <table class="recent-table">
                <thead>
                    <tr>
                        <th>Tipe</th>
                        <th>Periode</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentErrors as $err)
                    <tr>
                        <td>{{ $err->error_type_label }}</td>
                        <td>{{ $err->period ?? '—' }}</td>
                        <td>
                            @if($err->resolved)
                                <span class="error-badge badge-success"><i class="fas fa-check"></i> Selesai</span>
                            @else
                                <span class="error-badge badge-danger"><i class="fas fa-times"></i> Belum</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>

{{-- Quick Navigation --}}
<div class="kpi-cards" style="margin-top:0.5rem;">
    <a href="{{ route('analytics.usage') }}" style="text-decoration:none;">
        <div class="kpi-card" style="border-left:4px solid #2180D3; cursor:pointer;">
            <div class="icon icon-blue"><i class="fas fa-users"></i></div>
            <div class="label" style="font-size:0.9rem; font-weight:600; color:var(--dark);">Analitik Penggunaan</div>
            <div class="sub" style="color:var(--text-light);">Durasi sesi, pola akses per user →</div>
        </div>
    </a>
    <a href="{{ route('analytics.errors') }}" style="text-decoration:none;">
        <div class="kpi-card" style="border-left:4px solid #EF4444; cursor:pointer;">
            <div class="icon icon-red"><i class="fas fa-exclamation-circle"></i></div>
            <div class="label" style="font-size:0.9rem; font-weight:600; color:var(--dark);">Error Rate Detail</div>
            <div class="sub" style="color:var(--text-light);">Tabel error, tren resolusi →</div>
        </div>
    </a>
    <a href="{{ route('analytics.profit') }}" style="text-decoration:none;">
        <div class="kpi-card" style="border-left:4px solid #7C3AED; cursor:pointer;">
            <div class="icon icon-purple"><i class="fas fa-coins"></i></div>
            <div class="label" style="font-size:0.9rem; font-weight:600; color:var(--dark);">Analisis Profit</div>
            <div class="sub" style="color:var(--text-light);">Sebelum & sesudah penerapan →</div>
        </div>
    </a>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Usage Chart
const usageCtx = document.getElementById('usageChart').getContext('2d');
new Chart(usageCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($usageChart['labels']) !!},
        datasets: [{
            label: 'Jumlah Sesi',
            data: {!! json_encode($usageChart['data']) !!},
            backgroundColor: 'rgba(33,128,211,0.7)',
            borderColor: '#2180D3',
            borderWidth: 2,
            borderRadius: 6,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
    }
});

// Hourly Chart
const hourCtx = document.getElementById('hourChart').getContext('2d');
const hourLabels = Array.from({length: 24}, (_, i) => `${String(i).padStart(2,'0')}:00`);
const hourData   = hourLabels.map((_, i) => {{ json_encode($activityByHour) }}[i] || 0);
new Chart(hourCtx, {
    type: 'bar',
    data: {
        labels: hourLabels,
        datasets: [{
            label: 'Aktivitas',
            data: hourData,
            backgroundColor: function(ctx) {
                const v = ctx.raw || 0;
                const max = Math.max(...hourData, 1);
                const ratio = v / max;
                return `rgba(249,115,22,${0.3 + ratio * 0.7})`;
            },
            borderRadius: 4,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } }, x: { ticks: { font: { size: 9 } } } }
    }
});

// Error Trend Chart
const errorCtx = document.getElementById('errorChart').getContext('2d');
const errorData = {!! json_encode($errorTrend) !!};
new Chart(errorCtx, {
    type: 'line',
    data: {
        labels: errorData.labels,
        datasets: [
            {
                label: 'Total Error',
                data: errorData.total,
                borderColor: '#EF4444',
                backgroundColor: 'rgba(239,68,68,0.1)',
                fill: true, tension: 0.4, borderWidth: 2, pointRadius: 4,
            },
            {
                label: 'Terselesaikan',
                data: errorData.resolved,
                borderColor: '#22C55E',
                backgroundColor: 'rgba(34,197,94,0.08)',
                fill: true, tension: 0.4, borderWidth: 2, pointRadius: 4,
            }
        ]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'top' } },
        scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
    }
});
</script>
@endsection
