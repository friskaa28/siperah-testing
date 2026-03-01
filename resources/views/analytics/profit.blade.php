@extends('layouts.app')

@section('title', 'Analisis Profit - SIPERAH Analytics')

@section('styles')
<style>
    .page-header { margin-bottom: 1.5rem; }
    .page-header h1 { font-size: 1.6rem; font-weight: 700; color: var(--dark); }
    .page-header p  { color: var(--text-light); font-size: 0.9rem; }

    .info-alert {
        background: #EFF6FF; border: 1px solid #BFDBFE; border-radius: 12px;
        padding: 1rem 1.5rem; margin-bottom: 1.5rem; color: #1D4ED8;
        display: flex; align-items: flex-start; gap: 0.75rem; font-size: 0.88rem;
    }

    .charts-2col { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem; }
    @media(max-width:800px) { .charts-2col { grid-template-columns: 1fr; } }

    .chart-card {
        background: white; border-radius: 14px; padding: 1.5rem;
        border: 1px solid var(--border);
    }
    .chart-card h3 { font-size: 1rem; font-weight: 600; margin-bottom: 1rem; color: var(--dark); }

    .profit-table { width: 100%; border-collapse: collapse; font-size: 0.88rem; }
    .profit-table th { padding: 0.75rem 1rem; text-align: right; color: var(--text-light); font-weight: 500; font-size: 0.82rem; border-bottom: 2px solid var(--border); background: #F9FAFB; }
    .profit-table th:first-child { text-align: left; }
    .profit-table td { padding: 0.85rem 1rem; border-bottom: 1px solid var(--border); text-align: right; }
    .profit-table td:first-child { text-align: left; font-weight: 500; }
    .profit-table tr:last-child td { border: none; }
    .profit-table tr:hover td { background: #F9FAFB; }

    .growth-badge {
        display: inline-flex; align-items: center; gap: 0.3rem;
        padding: 0.2rem 0.5rem; border-radius: 6px; font-size: 0.78rem; font-weight: 600;
    }
    .growth-pos { background: #ECFDF5; color: #16A34A; }
    .growth-neg { background: #FEF2F2; color: #DC2626; }
    .growth-neu { background: #F3F4F6; color: #6B7280; }

    .empty-state {
        text-align: center; padding: 4rem 2rem; color: var(--text-light);
    }
    .empty-state i { font-size: 3rem; margin-bottom: 1rem; display: block; color: #D1D5DB; }
</style>
@endsection

@section('content')
<div class="page-header">
    <h1><i class="fas fa-coins" style="color:#7C3AED; margin-right:0.5rem;"></i>Analisis Profit & Revenue</h1>
    <p>Perbandingan sebelum & sesudah penerapan sistem SIPERAH — KPI Aspek 4</p>
</div>

<div class="info-alert">
    <i class="fas fa-info-circle" style="margin-top:2px; flex-shrink:0;"></i>
    <div>
        <strong>Catatan:</strong> Data profit diisi oleh Admin/Pengelola melalui menu <strong>Input KPI Data</strong>.
        Data "sebelum sistem" merupakan data historis yang diinput secara manual untuk perbandingan.
    </div>
</div>

@if($profits->isEmpty())
    <div class="chart-card">
        <div class="empty-state">
            <i class="fas fa-chart-bar"></i>
            <h3 style="color:var(--dark); margin-bottom:0.5rem;">Belum Ada Data Profit</h3>
            <p>Admin/Pengelola perlu menginput data profit melalui menu <strong>Input KPI Data</strong> di sidebar.</p>
        </div>
    </div>
@else
    {{-- Latest Summary Cards --}}
    @if($latestProfit)
    @php
        $latestGrowth = $latestProfit->revenue_before > 0
            ? round((($latestProfit->revenue_after - $latestProfit->revenue_before) / $latestProfit->revenue_before) * 100, 1)
            : null;
        $profitGrowth = $latestProfit->profit_before > 0
            ? round((($latestProfit->profit_after - $latestProfit->profit_before) / $latestProfit->profit_before) * 100, 1)
            : null;
    @endphp
    <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:1rem; margin-bottom:1.5rem;">
        <div class="chart-card" style="padding:1.25rem; text-align:center;">
            <div style="font-size:0.78rem; color:var(--text-light); margin-bottom:0.5rem;">Revenue Terbaru Setelah Sistem ({{ $latestProfit->period }})</div>
            <div style="font-size:1.5rem; font-weight:700; color:var(--primary);">Rp {{ number_format($latestProfit->revenue_after ?? 0, 0, ',', '.') }}</div>
            @if($latestGrowth !== null)
            <div class="growth-badge {{ $latestGrowth >= 0 ? 'growth-pos' : 'growth-neg' }}" style="margin-top:0.5rem;">
                <i class="fas fa-arrow-{{ $latestGrowth >= 0 ? 'up' : 'down' }}"></i>
                {{ abs($latestGrowth) }}% vs sebelumnya
            </div>
            @endif
        </div>
        <div class="chart-card" style="padding:1.25rem; text-align:center;">
            <div style="font-size:0.78rem; color:var(--text-light); margin-bottom:0.5rem;">Profit Setelah Sistem ({{ $latestProfit->period }})</div>
            <div style="font-size:1.5rem; font-weight:700; color:#22C55E;">Rp {{ number_format($latestProfit->profit_after, 0, ',', '.') }}</div>
            @if($profitGrowth !== null)
            <div class="growth-badge {{ $profitGrowth >= 0 ? 'growth-pos' : 'growth-neg' }}" style="margin-top:0.5rem;">
                <i class="fas fa-arrow-{{ $profitGrowth >= 0 ? 'up' : 'down' }}"></i>
                {{ abs($profitGrowth) }}% pertumbuhan
            </div>
            @endif
        </div>
        <div class="chart-card" style="padding:1.25rem; text-align:center;">
            <div style="font-size:0.78rem; color:var(--text-light); margin-bottom:0.5rem;">Volume Susu Setelah Sistem ({{ $latestProfit->period }})</div>
            <div style="font-size:1.5rem; font-weight:700; color:#F97316;">{{ number_format($latestProfit->milk_volume_after ?? 0, 1, ',', '.') }} L</div>
            @if($latestProfit->milk_volume_before > 0)
            @php $milkGrowth = round((($latestProfit->milk_volume_after - $latestProfit->milk_volume_before) / $latestProfit->milk_volume_before) * 100, 1); @endphp
            <div class="growth-badge {{ $milkGrowth >= 0 ? 'growth-pos' : 'growth-neg' }}" style="margin-top:0.5rem;">
                <i class="fas fa-arrow-{{ $milkGrowth >= 0 ? 'up' : 'down' }}"></i>
                {{ abs($milkGrowth) }}% vs sebelumnya
            </div>
            @endif
        </div>
    </div>
    @endif

    {{-- Charts --}}
    <div class="charts-2col">
        <div class="chart-card">
            <h3><i class="fas fa-chart-bar" style="color:#7C3AED;margin-right:6px;"></i>Revenue: Sebelum vs Sesudah</h3>
            <canvas id="revenueChart" height="130"></canvas>
        </div>
        <div class="chart-card">
            <h3><i class="fas fa-chart-line" style="color:#22C55E;margin-right:6px;"></i>Profit Bersih per Periode</h3>
            <canvas id="profitChart" height="130"></canvas>
        </div>
    </div>

    @if($profits->whereNotNull('milk_volume_after')->count() > 0)
    <div class="chart-card" style="margin-bottom:1.5rem;">
        <h3><i class="fas fa-tint" style="color:#F97316;margin-right:6px;"></i>Volume Susu: Sebelum vs Sesudah</h3>
        <canvas id="milkChart" height="80"></canvas>
    </div>
    @endif

    {{-- Data Table --}}
    <div class="chart-card">
        <h3><i class="fas fa-table" style="color:#2180D3;"></i> Tabel Perbandingan Lengkap</h3>
        <div style="overflow-x:auto; margin-top:0.5rem;">
            <table class="profit-table">
                <thead>
                    <tr>
                        <th>Periode</th>
                        <th>Revenue Sebelum</th>
                        <th>Revenue Sesudah</th>
                        <th>Pertumbuhan</th>
                        <th>Profit Sebelum</th>
                        <th>Profit Sesudah</th>
                        <th>Pertumbuhan</th>
                        <th>Vol Susu (L)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($profits->sortByDesc('period') as $p)
                    @php
                        $revGrowth = $p->revenue_before > 0 ? round((($p->revenue_after - $p->revenue_before)/$p->revenue_before)*100,1) : null;
                        $pftGrowth = $p->profit_before > 0 ? round((($p->profit_after - $p->profit_before)/$p->profit_before)*100,1) : null;
                    @endphp
                    <tr>
                        <td><strong>{{ $p->period }}</strong></td>
                        <td>Rp {{ number_format($p->revenue_before ?? 0, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($p->revenue_after  ?? 0, 0, ',', '.') }}</td>
                        <td>
                            @if($revGrowth !== null)
                                <span class="growth-badge {{ $revGrowth >= 0 ? 'growth-pos' : 'growth-neg' }}">
                                    <i class="fas fa-arrow-{{ $revGrowth >= 0 ? 'up' : 'down' }}"></i>
                                    {{ abs($revGrowth) }}%
                                </span>
                            @else <span class="growth-badge growth-neu">—</span>
                            @endif
                        </td>
                        <td>Rp {{ number_format($p->profit_before, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($p->profit_after,  0, ',', '.') }}</td>
                        <td>
                            @if($pftGrowth !== null)
                                <span class="growth-badge {{ $pftGrowth >= 0 ? 'growth-pos' : 'growth-neg' }}">
                                    <i class="fas fa-arrow-{{ $pftGrowth >= 0 ? 'up' : 'down' }}"></i>
                                    {{ abs($pftGrowth) }}%
                                </span>
                            @else <span class="growth-badge growth-neu">—</span>
                            @endif
                        </td>
                        <td>{{ number_format($p->milk_volume_before ?? 0, 1, ',', '.') }} → {{ number_format($p->milk_volume_after ?? 0, 1, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif
@endsection

@section('scripts')
@if(!$profits->isEmpty())
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const labels       = {!! $labels !!};
const revBefore    = {!! $revenueBefore !!};
const revAfter     = {!! $revenueAfter !!};
const profitBefore = {!! $profitBefore !!};
const profitAfter  = {!! $profitAfter !!};

new Chart(document.getElementById('revenueChart'), {
    type: 'bar',
    data: {
        labels,
        datasets: [
            { label: 'Sebelum Sistem', data: revBefore, backgroundColor: 'rgba(156,163,175,0.7)', borderRadius: 5 },
            { label: 'Sesudah Sistem', data: revAfter,  backgroundColor: 'rgba(124,58,237,0.7)',  borderRadius: 5 },
        ]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'top' } },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { callback: v => 'Rp ' + (v/1000000).toFixed(1) + 'jt' }
            }
        }
    }
});

new Chart(document.getElementById('profitChart'), {
    type: 'line',
    data: {
        labels,
        datasets: [
            {
                label: 'Profit Sebelum', data: profitBefore,
                borderColor: '#9CA3AF', backgroundColor: 'rgba(156,163,175,0.1)',
                fill: true, tension: 0.4,
            },
            {
                label: 'Profit Sesudah', data: profitAfter,
                borderColor: '#22C55E', backgroundColor: 'rgba(34,197,94,0.1)',
                fill: true, tension: 0.4,
            }
        ]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'top' } },
        scales: {
            y: {
                ticks: { callback: v => 'Rp ' + (v/1000000).toFixed(1) + 'jt' }
            }
        }
    }
});

@if($profits->whereNotNull('milk_volume_after')->count() > 0)
new Chart(document.getElementById('milkChart'), {
    type: 'bar',
    data: {
        labels,
        datasets: [
            { label: 'Sebelum (L)', data: {!! $milkBefore !!}, backgroundColor: 'rgba(249,115,22,0.4)', borderRadius: 5 },
            { label: 'Sesudah (L)', data: {!! $milkAfter !!},  backgroundColor: 'rgba(249,115,22,0.85)', borderRadius: 5 },
        ]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'top' } },
        scales: { y: { beginAtZero: true, ticks: { callback: v => v + ' L' } } }
    }
});
@endif
</script>
@endif
@endsection
