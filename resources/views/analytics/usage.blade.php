@extends('layouts.app')

@section('title', 'Analitik Penggunaan - SIPERAH')

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
    @media(max-width:1000px) { .stats-grid { grid-template-columns: repeat(2,1fr); } }
    @media(max-width:550px)  { .stats-grid { grid-template-columns: 1fr; } }

    .stat-card {
        background: white; border-radius: 12px; padding: 1.25rem;
        border: 1px solid var(--border); text-align: center;
    }
    .stat-card .num   { font-size: 1.8rem; font-weight: 700; color: var(--primary); }
    .stat-card .lbl   { font-size: 0.8rem; color: var(--text-light); margin-top: 0.2rem; }

    .charts-2col { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem; }
    @media(max-width:800px) { .charts-2col { grid-template-columns: 1fr; } }

    .chart-card {
        background: white; border-radius: 14px; padding: 1.5rem;
        border: 1px solid var(--border);
    }
    .chart-card h3 { font-size: 1rem; font-weight: 600; margin-bottom: 1rem; color: var(--dark); }

    .user-table { width: 100%; border-collapse: collapse; font-size: 0.88rem; }
    .user-table th { padding: 0.75rem 1rem; text-align: left; color: var(--text-light); font-weight: 500; font-size: 0.82rem; border-bottom: 2px solid var(--border); background: #F9FAFB; }
    .user-table td { padding: 0.85rem 1rem; border-bottom: 1px solid var(--border); }
    .user-table tr:last-child td { border: none; }
    .user-table tr:hover td { background: #F9FAFB; }

    .duration-bar {
        display: inline-block; height: 8px; border-radius: 4px;
        background: var(--primary); transition: width 0.3s;
    }

    .role-badge {
        display: inline-block; padding: 0.2rem 0.6rem; border-radius: 99px;
        font-size: 0.75rem; font-weight: 500;
    }
    .role-pengelola { background: #EFF6FF; color: #2180D3; }
    .role-peternak  { background: #ECFDF5; color: #16A34A; }
    .role-admin     { background: #F5F3FF; color: #7C3AED; }
</style>
@endsection

@section('content')
<div class="page-header">
    <h1><i class="fas fa-users" style="color:#2180D3; margin-right:0.5rem;"></i>Analitik Penggunaan Sistem</h1>
    <p>Durasi sesi, pola akses, dan aktivitas per pengguna</p>
</div>

{{-- Filters --}}
<form method="GET" class="filter-bar">
    <div class="form-group">
        <label class="form-label">Dari Tanggal</label>
        <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}">
    </div>
    <div class="form-group">
        <label class="form-label">Sampai Tanggal</label>
        <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}">
    </div>
    <div class="form-group">
        <label class="form-label">Pengguna</label>
        <select name="user_id" class="form-select">
            <option value="">Semua Pengguna</option>
            @foreach($users as $u)
                <option value="{{ $u->iduser }}" @selected($userId == $u->iduser)>{{ $u->nama }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
        <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Filter</button>
        <a href="{{ route('analytics.usage') }}" class="btn btn-secondary" style="margin-left:0.5rem;">Reset</a>
    </div>
</form>

<div class="mb-4 d-flex gap-2 flex-wrap">
    <a href="{{ route('analytics.usage', ['preset' => '7d']) }}" class="btn btn-sm {{ request('preset') == '7d' ? 'btn-primary' : 'btn-outline-primary' }}">7 Hari Terakhir</a>
    <a href="{{ route('analytics.usage', ['preset' => '30d']) }}" class="btn btn-sm {{ request('preset') == '30d' ? 'btn-primary' : 'btn-outline-primary' }}">30 Hari Terakhir</a>
    <a href="{{ route('analytics.usage', ['preset' => 'this_month']) }}" class="btn btn-sm {{ request('preset') == 'this_month' ? 'btn-primary' : 'btn-outline-primary' }}">Bulan Ini</a>
    <a href="{{ route('analytics.usage', ['preset' => 'last_month']) }}" class="btn btn-sm {{ request('preset') == 'last_month' ? 'btn-primary' : 'btn-outline-primary' }}">Bulan Lalu</a>
</div>

{{-- Summary Stats --}}
@php
    $totalSessions   = $rawSessions->count();
    $completedSess   = $rawSessions->whereNotNull('duration_seconds');
    $avgDurMin       = round($completedSess->avg('duration_seconds') / 60 ?? 0, 1);
    $totalTimeMin    = round($completedSess->sum('duration_seconds') / 60, 0);
    $uniqueUserCount = $rawSessions->unique('user_id')->count();
@endphp
<div class="stats-grid">
    <div class="stat-card">
        <div class="num">{{ $totalSessions }}</div>
        <div class="lbl">Total Sesi</div>
    </div>
    <div class="stat-card">
        <div class="num">{{ $uniqueUserCount }}</div>
        <div class="lbl">Pengguna Unik</div>
    </div>
    <div class="stat-card">
        <div class="num">{{ $avgDurMin }} <small style="font-size:1rem;">min</small></div>
        <div class="lbl">Rata-rata Durasi</div>
    </div>
    <div class="stat-card">
        <div class="num">{{ number_format($totalTimeMin) }} <small style="font-size:1rem;">min</small></div>
        <div class="lbl">Total Waktu Penggunaan</div>
    </div>
</div>

{{-- Charts --}}
<div class="charts-2col">
    <div class="chart-card">
        <h3><i class="fas fa-calendar-alt" style="color:#2180D3; margin-right:6px;"></i>Sesi Harian</h3>
        <canvas id="dailyChart" height="150"></canvas>
    </div>
    <div class="chart-card">
        <h3><i class="fas fa-clock" style="color:#F97316; margin-right:6px;"></i>Pola Akses per Jam</h3>
        <canvas id="hourlyChart" height="150"></canvas>
    </div>
</div>

<div class="charts-2col">
    <div class="chart-card">
        <h3><i class="fas fa-stopwatch" style="color:#7C3AED; margin-right:6px;"></i>Distribusi Durasi Sesi</h3>
        <canvas id="durationChart" height="180"></canvas>
    </div>
    <div class="chart-card" style="display:flex; flex-direction:column; justify-content:center;">
        <h3><i class="fas fa-info-circle" style="color:#22C55E; margin-right:6px;"></i>Periode Filter</h3>
        <p style="color:var(--text-light); font-size:0.88rem;">
            Menampilkan data dari <strong>{{ $dateFrom }}</strong> s.d. <strong>{{ $dateTo }}</strong>
        </p>
        <p style="color:var(--text-light); font-size:0.88rem; margin-top:0.5rem;">
            Total sesi terekam: <strong>{{ $totalSessions }}</strong> sesi<br>
            Sesi selesai (ada durasi): <strong>{{ $completedSess->count() }}</strong>
        </p>
        @if($completedSess->count() > 0)
        <p style="color:var(--text-light); font-size:0.88rem; margin-top:0.5rem;">
            Durasi terpendek: <strong>{{ round(($completedSess->min('duration_seconds') ?? 0)/60, 1) }} min</strong><br>
            Durasi terpanjang: <strong>{{ round(($completedSess->max('duration_seconds') ?? 0)/60, 1) }} min</strong>
        </p>
        @endif
    </div>
</div>

{{-- Per User Table --}}
<div class="chart-card" style="margin-top:1.5rem;">
    <h3><i class="fas fa-table" style="color:#2180D3;"></i> Ringkasan per Pengguna</h3>
    @if($perUser->isEmpty())
        <div style="text-align:center; color:var(--text-light); padding:3rem 0;">
            <i class="fas fa-search" style="font-size:2rem; display:block; margin-bottom:0.5rem;"></i>
            Belum ada data sesi untuk filter yang dipilih.
        </div>
    @else
    @php $maxTime = $perUser->max('total_time') ?: 1; @endphp
    <div style="overflow-x:auto; margin-top:0.5rem;">
        <table class="user-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama Pengguna</th>
                    <th>Role</th>
                    <th>Total Sesi</th>
                    <th>Rata-rata Durasi</th>
                    <th>Total Waktu</th>
                    <th>Akses Terakhir</th>
                </tr>
            </thead>
            <tbody>
                @foreach($perUser as $i => $data)
                <tr>
                    <td style="color:var(--text-light);">{{ $i + 1 }}</td>
                    <td>
                        <strong>{{ $data['user']->nama ?? '(user dihapus)' }}</strong><br>
                        <small style="color:var(--text-light);">{{ $data['user']->email ?? '' }}</small>
                    </td>
                    <td>
                        @if($data['user'])
                            @if($data['user']->isSubPenampung())
                                <span class="role-badge" style="background: #FEF3C7; color: #92400E;">Sub-Penampung</span>
                            @else
                                <span class="role-badge role-{{ $data['user']->role }}">{{ ucfirst($data['user']->role) }}</span>
                            @endif
                        @endif
                    </td>
                    <td><strong>{{ $data['total_sessions'] }}</strong></td>
                    <td>{{ $data['avg_duration'] }} min</td>
                    <td>
                        <div style="display:flex; align-items:center; gap:0.5rem;">
                            <span style="min-width:55px;">{{ $data['total_time'] }} min</span>
                            <div class="duration-bar" style="width:{{ min(100, round($data['total_time']/$maxTime*100)) }}px;"></div>
                        </div>
                    </td>
                    <td style="color:var(--text-light);">
                        @if($data['last_seen'])
                            {{ \Carbon\Carbon::parse($data['last_seen'])->diffForHumans() }}
                        @else —
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Daily chart
const dailyData = {!! json_encode($dailyChart) !!};
new Chart(document.getElementById('dailyChart'), {
    type: 'bar',
    data: {
        labels: Object.keys(dailyData),
        datasets: [{ label: 'Sesi', data: Object.values(dailyData), backgroundColor: 'rgba(33,128,211,0.7)', borderRadius: 5 }]
    },
    options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
});

// Hourly chart
const hourlyData = {!! json_encode($hourlyPattern) !!};
const hours = Array.from({length: 24}, (_, i) => `${String(i).padStart(2,'0')}:00`);
new Chart(document.getElementById('hourlyChart'), {
    type: 'line',
    data: {
        labels: hours,
        datasets: [{
            label: 'Akses', data: hours.map((_, i) => hourlyData[String(i).padStart(2,'0')] || 0),
            borderColor: '#F97316', backgroundColor: 'rgba(249,115,22,0.1)',
            fill: true, tension: 0.4, pointRadius: 3,
        }]
    },
    options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
});

// Duration distribution
const durationData = {!! json_encode($durationBuckets) !!};
new Chart(document.getElementById('durationChart'), {
    type: 'doughnut',
    data: {
        labels: Object.keys(durationData),
        datasets: [{
            data: Object.values(durationData),
            backgroundColor: ['#BFDBFE','#93C5FD','#60A5FA','#3B82F6','#1D4ED8'],
            borderWidth: 2, borderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'bottom' } }
    }
});
</script>
@endsection
