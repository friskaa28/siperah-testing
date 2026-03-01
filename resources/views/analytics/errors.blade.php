@extends('layouts.app')

@section('title', 'Error Rate - SIPERAH Analytics')

@section('styles')
<style>
    .page-header { margin-bottom: 1.5rem; }
    .page-header h1 { font-size: 1.6rem; font-weight: 700; color: var(--dark); }
    .page-header p  { color: var(--text-light); font-size: 0.9rem; }

    .filter-bar {
        background: white; border-radius: 12px; padding: 1.25rem 1.5rem;
        border: 1px solid var(--border); margin-bottom: 1.5rem;
        display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;
    }
    .filter-bar .form-group { margin: 0; min-width: 160px; }

    .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 1.5rem; }
    @media(max-width:900px) { .stats-grid { grid-template-columns: repeat(2,1fr); } }

    .stat-card {
        background: white; border-radius: 12px; padding: 1.25rem;
        border: 1px solid var(--border); text-align: center;
    }
    .stat-card .num { font-size: 1.8rem; font-weight: 700; }
    .stat-card .lbl { font-size: 0.8rem; color: var(--text-light); margin-top: 0.2rem; }
    .num-red    { color: #EF4444; }
    .num-green  { color: #22C55E; }
    .num-orange { color: #F97316; }
    .num-blue   { color: #2180D3; }

    .charts-2col { display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem; }
    @media(max-width:900px) { .charts-2col { grid-template-columns: 1fr; } }

    .chart-card {
        background: white; border-radius: 14px; padding: 1.5rem;
        border: 1px solid var(--border);
    }
    .chart-card h3 { font-size: 1rem; font-weight: 600; margin-bottom: 1rem; color: var(--dark); }

    .error-table { width: 100%; border-collapse: collapse; font-size: 0.88rem; }
    .error-table th { padding: 0.75rem 1rem; text-align: left; color: var(--text-light); font-weight: 500; font-size: 0.82rem; border-bottom: 2px solid var(--border); background: #F9FAFB; }
    .error-table td { padding: 0.85rem 1rem; border-bottom: 1px solid var(--border); vertical-align: middle; }
    .error-table tr:last-child td { border: none; }
    .error-table tr:hover td { background: #F9FAFB; }

    .badge {
        display: inline-flex; align-items: center; gap: 0.3rem;
        padding: 0.25rem 0.75rem; border-radius: 99px; font-size: 0.78rem; font-weight: 500;
    }
    .badge-danger  { background: #FEF2F2; color: #DC2626; }
    .badge-success { background: #ECFDF5; color: #16A34A; }
    .badge-warning { background: #FFFBEB; color: #D97706; }
    .badge-info    { background: #EFF6FF; color: #2563EB; }

    .period-table { width: 100%; border-collapse: collapse; font-size: 0.85rem; }
    .period-table th { padding: 0.6rem 0.75rem; text-align: left; color: var(--text-light); font-weight: 500; border-bottom: 1px solid var(--border); background: #F9FAFB; }
    .period-table td { padding: 0.6rem 0.75rem; border-bottom: 1px solid var(--border); }
    .rate-bar {
        display: inline-block; height: 6px; border-radius: 3px;
        background: #22C55E; vertical-align: middle; margin-left: 6px;
    }
</style>
@endsection

@section('content')
<div class="page-header">
    <h1><i class="fas fa-exclamation-triangle" style="color:#EF4444; margin-right:0.5rem;"></i>Analitik Error Rate</h1>
    <p>Kesalahan pencatatan data & perhitungan pembayaran — KPI Aspek 1</p>
</div>

{{-- Filters --}}
<form method="GET" class="filter-bar">
    <div class="form-group">
        <label class="form-label">Periode (YYYY-MM)</label>
        <input type="month" name="period" class="form-control" value="{{ $period }}">
    </div>
    <div class="form-group">
        <label class="form-label">Tipe Error</label>
        <select name="error_type" class="form-select">
            <option value="">Semua Tipe</option>
            <option value="salary_calc" @selected($errorType === 'salary_calc')>Perhitungan Pembayaran</option>
            <option value="data_entry"  @selected($errorType === 'data_entry')>Pencatatan Data</option>
            <option value="other"       @selected($errorType === 'other')>Lainnya</option>
        </select>
    </div>
    <div class="form-group">
        <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Filter</button>
        <a href="{{ route('analytics.errors') }}" class="btn btn-secondary" style="margin-left:0.5rem;">Reset</a>
    </div>
</form>

{{-- Summary Stats --}}
<div class="stats-grid">
    <div class="stat-card">
        <div class="num num-blue">{{ $totalErrors }}</div>
        <div class="lbl">Total Error Tercatat</div>
    </div>
    <div class="stat-card">
        <div class="num num-red">{{ $unresolved }}</div>
        <div class="lbl">Belum Diselesaikan</div>
    </div>
    <div class="stat-card">
        <div class="num num-green">{{ $resolved }}</div>
        <div class="lbl">Sudah Diselesaikan</div>
    </div>
    <div class="stat-card">
        <div class="num num-orange">{{ $resolutionRate }}%</div>
        <div class="lbl">Resolution Rate</div>
    </div>
</div>

{{-- Human Error Rate Card --}}
<div class="chart-card" style="margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: space-between; border-left: 5px solid #F97316;">
    <div>
        <h3 style="margin-bottom:0.2rem;"><i class="fas fa-user-slash" style="color:#F97316;"></i> Human Error Rate (Deletion Rate)</h3>
        <p style="font-size:0.85rem; color:var(--text-light); margin:0;">Persentase data yang dihapus ulang karena kesalahan input dibandingkan total entri.</p>
    </div>
    <div style="text-align:right;">
        <div style="font-size: 2rem; font-weight: 800; color: #F97316;">{{ $humanErrorRate }}%</div>
        <div style="font-size: 0.75rem; color: var(--text-light);">{{ $totalDeletions }} Data Terhapus</div>
    </div>
</div>

{{-- Charts --}}
<div class="charts-2col">
    <div class="chart-card">
        <h3><i class="fas fa-chart-bar" style="color:#EF4444;margin-right:6px;"></i>Tren Error per Periode</h3>
        <canvas id="errorTrendChart" height="120"></canvas>
    </div>
    <div class="chart-card">
        <h3><i class="fas fa-chart-pie" style="color:#7C3AED;margin-right:6px;"></i>Distribusi Tipe Error</h3>
        <canvas id="errorTypeChart" height="200"></canvas>
    </div>
</div>

{{-- Period Summary Table --}}
<div class="chart-card" style="margin-bottom:1.5rem;">
    <h3><i class="fas fa-table" style="color:#2180D3;"></i> Ringkasan per Periode</h3>
    @if($summary->isEmpty())
        <p style="color:var(--text-light); text-align:center; padding:2rem;">Belum ada data error tercatat.</p>
    @else
    <div style="overflow-x:auto;">
        <table class="period-table">
            <thead>
                <tr>
                    <th>Periode</th>
                    <th>Total Error</th>
                    <th>Terselesaikan</th>
                    <th>Belum Selesai</th>
                    <th>Resolution Rate</th>
                </tr>
            </thead>
            <tbody>
                @foreach($summary as $s)
                <tr>
                    <td><strong>{{ $s->period }}</strong></td>
                    <td>{{ $s->total }}</td>
                    <td>{{ $s->resolved_count }}</td>
                    <td>
                        @if($s->unresolved_count > 0)
                            <span class="badge badge-danger">{{ $s->unresolved_count }}</span>
                        @else
                            <span class="badge badge-success">0</span>
                        @endif
                    </td>
                    <td>
                        {{ $s->resolution_rate }}%
                        <div class="rate-bar" style="width:{{ min(80, $s->resolution_rate) }}px;"></div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

{{-- Error Log Table --}}
<div class="chart-card">
    <h3><i class="fas fa-list-alt" style="color:#EF4444;"></i> Log Error Detail</h3>
    @if($errorLogs->isEmpty())
        <div style="text-align:center; color:var(--text-light); padding:3rem 0;">
            <i class="fas fa-check-circle" style="font-size:2.5rem; color:#22C55E; display:block; margin-bottom:0.5rem;"></i>
            Tidak ada error untuk filter yang dipilih.
        </div>
    @else
    <div style="overflow-x:auto; margin-top:0.5rem;">
        <table class="error-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tipe</th>
                    <th>Deskripsi</th>
                    <th>Periode</th>
                    <th>Dilaporkan Oleh</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($errorLogs as $err)
                <tr>
                    <td style="color:var(--text-light);">{{ $err->id }}</td>
                    <td>
                        <span class="badge @if($err->error_type === 'salary_calc') badge-warning @elseif($err->error_type === 'data_entry') badge-info @else badge-danger @endif">
                            {{ $err->error_type_label }}
                        </span>
                    </td>
                    <td style="max-width:300px;">{{ Str::limit($err->description, 80) }}</td>
                    <td>{{ $err->period ?? '—' }}</td>
                    <td>{{ $err->reporter->nama ?? '—' }}</td>
                    <td style="color:var(--text-light); font-size:0.82rem;">
                        {{ \Carbon\Carbon::parse($err->created_at)->format('d M Y, H:i') }}
                    </td>
                    <td>
                        @if($err->resolved)
                            <span class="badge badge-success"><i class="fas fa-check"></i> Selesai</span>
                        @else
                            <span class="badge badge-danger"><i class="fas fa-clock"></i> Pending</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="pagination">{{ $errorLogs->links() }}</div>
    @endif
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const trendData = {!! json_encode($errorTrend) !!};
// Build arrays
const trendLabels     = Object.keys(trendData);
const salaryErrors    = trendLabels.map(p => (trendData[p].find(d => d.error_type === 'salary_calc')?.count || 0));
const dataEntryErrors = trendLabels.map(p => (trendData[p].find(d => d.error_type === 'data_entry')?.count || 0));
const otherErrors     = trendLabels.map(p => (trendData[p].find(d => d.error_type === 'other')?.count || 0));

new Chart(document.getElementById('errorTrendChart'), {
    type: 'bar',
    data: {
        labels: trendLabels,
        datasets: [
            { label: 'Perhitungan Pembayaran', data: salaryErrors, backgroundColor: '#FCD34D', borderRadius: 4 },
            { label: 'Pencatatan Data',  data: dataEntryErrors, backgroundColor: '#60A5FA', borderRadius: 4 },
            { label: 'Lainnya',          data: otherErrors, backgroundColor: '#F87171', borderRadius: 4 },
        ]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'top' } },
        scales: { x: { stacked: true }, y: { stacked: true, beginAtZero: true, ticks: { stepSize: 1 } } }
    }
});

// Donut chart for type distribution
new Chart(document.getElementById('errorTypeChart'), {
    type: 'doughnut',
    data: {
        labels: ['Perhitungan Pembayaran', 'Pencatatan Data', 'Lainnya'],
        datasets: [{
            data: [
                {!! \App\Models\KpiError::where('error_type','salary_calc')->count() !!},
                {!! \App\Models\KpiError::where('error_type','data_entry')->count() !!},
                {!! \App\Models\KpiError::where('error_type','other')->count() !!}
            ],
            backgroundColor: ['#FCD34D', '#60A5FA', '#F87171'],
            borderWidth: 2, borderColor: '#fff'
        }]
    },
    options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
});
</script>
@endsection
