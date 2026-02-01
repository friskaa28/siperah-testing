@extends('layouts.app')

@section('title', 'Dashboard Admin - SIPERAH')

@section('content')
<div class="row align-items-center mb-3">
    <div class="col-12">
        <h1 class="fw-bold mb-0">Dashboard Admin</h1>
        <p class="text-muted">Pantau operasional harian dan kelola data master.</p>
    </div>
</div>

<!-- DATE FILTER -->
<div class="card mb-4" style="border-radius: 12px; border: 1px solid #E5E7EB;">
    <div class="card-body p-3">
        <form action="{{ route('dashboard.pengelola') }}" method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="small fw-bold text-muted mb-1">Tanggal Mulai</label>
                <input type="date" name="start_date" class="form-control form-control-sm" value="{{ $startDateStr }}" required>
            </div>
            <div class="col-md-3">
                <label class="small fw-bold text-muted mb-1">Tanggal Akhir</label>
                <input type="date" name="end_date" class="form-control form-control-sm" value="{{ $endDateStr }}" required>
            </div>
            <div class="col-md-3">
                <label class="small fw-bold text-muted mb-1">Tahun (untuk chart)</label>
                <select name="year" class="form-select form-select-sm">
                    @foreach($availableYears as $year)
                        <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>{{ $year }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary btn-sm px-4">
                    <i class="fas fa-filter"></i> Filter
                </button>
                <a href="{{ route('dashboard.pengelola') }}" class="btn btn-outline-secondary btn-sm px-4">
                    <i class="fas fa-redo"></i> Reset
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Quick Access Menu (Widget Style) -->
<!-- Quick Access Menu (Widget Style) -->
<div class="row mb-4 widget-grid">
    <!-- Setor Susu Widget -->
    <div class="widget-item">
        <div class="small-box bg-success-custom">
            <div class="inner">
                <h3>{{ rtrim(rtrim(number_format($periodLiter, 2, ',', '.'), '0'), ',') }}<sup style="font-size: 0.5em;">L</sup></h3>
                <p>Setoran Periode Ini</p>
            </div>
            <div class="icon">
                <i class="fas fa-cow"></i>
            </div>
            <a href="/produksi/input" class="small-box-footer">
                Input Data <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <!-- Potongan Widget -->
    <div class="widget-item">
        <div class="small-box bg-danger-custom">
            <div class="inner">
                <h3>Rp {{ number_format($periodKasbon/1000, 0) }}<sup style="font-size: 0.5em;">k</sup></h3>
                <p>Potongan Periode Ini</p>
            </div>
            <div class="icon">
                <i class="fas fa-shopping-basket"></i>
            </div>
            <a href="{{ route('kasbon.index') }}" class="small-box-footer">
                Input Potongan <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <!-- Katalog Widget -->
    <div class="widget-item">
        <div class="small-box bg-warning-custom">
            <div class="inner">
                <h3>{{ $totalLogistik }}</h3>
                <p>Item Logistik</p>
            </div>
            <div class="icon">
                <i class="fas fa-clipboard-list"></i>
            </div>
            <a href="{{ route('logistik.index') }}" class="small-box-footer" style="color: #333 !important;">
                Atur Stok <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <!-- Harga Susu Widget -->
    <div class="widget-item">
        <div class="small-box bg-primary-custom">
            <div class="inner">
                <h3>{{ number_format($currentPrice/1000, 1) }}<sup style="font-size: 0.5em;">rb</sup></h3>
                <p>Harga Susu</p>
            </div>
            <div class="icon">
                <i class="fas fa-tags"></i>
            </div>
            <a href="{{ route('harga_susu.index') }}" class="small-box-footer">
                Update Harga <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
</div>

<style>
    /* Force Layout */
    .widget-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 1.5rem;
        margin-right: 0;
        margin-left: 0;
    }
    .widget-item {
        width: 100%;
    }

    /* Small Box Core Styles */
    .small-box {
        border-radius: 0.5rem;
        position: relative;
        display: block;
        margin-bottom: 20px;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
        overflow: hidden;
        color: #fff;
        transition: transform 0.3s;
    }
    .small-box:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 15px -3px rgba(0,0,0,0.2);
    }
    .small-box > .inner {
        padding: 20px;
        position: relative;
        z-index: 2;
    }
    .small-box h3 {
        font-size: 2.2rem;
        font-weight: 700;
        margin: 0 0 10px 0;
        white-space: nowrap;
        padding: 0;
        line-height: 1;
    }
    .small-box p {
        font-size: 1.1rem;
        margin-bottom: 0;
        font-weight: 500;
    }
    .small-box .icon {
        color: rgba(0,0,0,0.15);
        z-index: 0;
        position: absolute;
        right: 15px;
        top: 15px;
        font-size: 4.5rem;
        transition: transform 0.3s;
    }
    .small-box:hover .icon {
        transform: scale(1.1);
    }
    .small-box-footer {
        position: relative;
        text-align: center;
        padding: 7px 0;
        color: #fff;
        color: rgba(255,255,255,0.8);
        display: block;
        z-index: 10;
        background: rgba(0,0,0,0.1);
        text-decoration: none !important;
        font-weight: 600;
    }
    .small-box-footer:hover {
        color: #fff;
        background: rgba(0,0,0,0.15);
    }

    /* Custom Colors */
    .bg-success-custom { background-color: #10B981 !important; color: white !important; }
    .bg-danger-custom { background-color: #EF4444 !important; color: white !important; }
    .bg-warning-custom { background-color: #F59E0B !important; color: white !important; }
    .bg-primary-custom { background-color: #3B82F6 !important; color: white !important; }
    
    /* Ensure icon colors work on all backgrounds */
    .bg-warning-custom .icon { color: rgba(255,255,255,0.25) !important; }
    .bg-warning-custom .small-box-footer { color: rgba(255,255,255,0.9) !important; }

    .dashboard-grid-pengelola {
        display: grid;
        grid-template-columns: 1fr;
        align-items: start;
    }
    @media (min-width: 992px) {
        .dashboard-grid-pengelola {
            grid-template-columns: 1.5fr 1fr;
        }
    }
</style>

<div class="dashboard-grid-pengelola" style="gap: 1.5rem;">
    <!-- Announcement & Stats -->
    <div>
        <!-- Broadcast Form -->
        <div class="card mb-4" style="padding: 1.5rem;">
            <h3 class="fw-bold mb-3" style="font-size: 1.1rem;"><i class="fas fa-bullhorn"></i> Siarkan Pengumuman (Broadcast)</h3>
            <form action="{{ route('pengumuman.broadcast') }}" method="POST">
                @csrf
                <div class="form-group">
                    <textarea name="isi" class="form-control" placeholder="Ketik info untuk peternak di sini (Contoh: Libur hari raya, stok pakan masuk...)" required style="min-height: 100px; border-radius: 12px;"></textarea>
                </div>
                <div class="text-end mt-2">
                    <button type="submit" class="btn btn-primary px-4">Siarkan Info</button>
                </div>
            </form>
        </div>

        <!-- Monthly Stats Chart -->
        <div class="card" style="padding: 1.5rem;">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
                <h3 class="fw-bold mb-0" style="font-size: 1.1rem;"><i class="fas fa-chart-line"></i> Statistik Data Setor Susu</h3>
                <form action="{{ route('dashboard.pengelola') }}" method="GET" class="d-flex flex-column flex-sm-row gap-2">
                    @if(request('start_date'))
                        <input type="hidden" name="start_date" value="{{ request('start_date') }}">
                    @endif
                    @if(request('end_date'))
                        <input type="hidden" name="end_date" value="{{ request('end_date') }}">
                    @endif
                    <select name="month" class="form-select form-select-sm" onchange="this.form.submit()" style="border-radius: 8px; font-weight: 500;">
                        <option value="">Semua Bulan</option>
                        <option value="1" {{ request('month') == '1' ? 'selected' : '' }}>Januari</option>
                        <option value="2" {{ request('month') == '2' ? 'selected' : '' }}>Februari</option>
                        <option value="3" {{ request('month') == '3' ? 'selected' : '' }}>Maret</option>
                        <option value="4" {{ request('month') == '4' ? 'selected' : '' }}>April</option>
                        <option value="5" {{ request('month') == '5' ? 'selected' : '' }}>Mei</option>
                        <option value="6" {{ request('month') == '6' ? 'selected' : '' }}>Juni</option>
                        <option value="7" {{ request('month') == '7' ? 'selected' : '' }}>Juli</option>
                        <option value="8" {{ request('month') == '8' ? 'selected' : '' }}>Agustus</option>
                        <option value="9" {{ request('month') == '9' ? 'selected' : '' }}>September</option>
                        <option value="10" {{ request('month') == '10' ? 'selected' : '' }}>Oktober</option>
                        <option value="11" {{ request('month') == '11' ? 'selected' : '' }}>November</option>
                        <option value="12" {{ request('month') == '12' ? 'selected' : '' }}>Desember</option>
                    </select>
                    <select name="year" class="form-select form-select-sm" onchange="this.form.submit()" style="border-radius: 8px; font-weight: 500;">
                        @foreach($availableYears as $year)
                            <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>{{ $year }}</option>
                        @endforeach
                    </select>
                </form>
            </div>
            <div style="height: 350px;">
                <canvas id="mainChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Right Sidebar: KPI & Activity -->
    <div>
        <!-- Top 5 Kontributor Chart -->
        <div class="card mb-4" style="padding: 1.5rem; height: 100%;">
            <h3 class="fw-bold mb-3" style="font-size: 1.1rem;"><i class="fas fa-trophy text-warning"></i> Top 5 Kontributor</h3>
            <div style="height: 250px; position: relative;">
                <canvas id="topPeternakChart"></canvas>
            </div>
        </div>
        
        <!-- Quick Action: Gaji -->
        <div class="card bg-primary text-white" style="border: none; border-radius: 12px;">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="fw-bold mb-0">Manajemen Gaji</h5>
                    <small class="text-white-50">Rekap & Hitung Gaji Mitra</small>
                </div>
                <a href="/gaji" class="btn btn-light text-primary fw-bold rounded-pill px-4">
                    Buka <i class="fas fa-arrow-right list-inline-item ms-1"></i>
                </a>
            </div>
        </div>

        <!-- Notification / Activity Feed -->
        <div class="card mt-4" style="padding: 1.5rem;">
            <h3 class="fw-bold mb-3" style="font-size: 1.1rem;"><i class="fas fa-bell text-info"></i> Aktivitas Terkini</h3>
            <div class="list-group list-group-flush mb-3">
                @forelse($notifikasi->take(2) as $notif)
                    <div class="list-group-item px-0 py-3 border-bottom">
                        <div class="d-flex justify-content-between align-items-start">
                            <div style="flex: 1; margin-right: 10px;">
                                <h6 class="mb-1 fw-bold text-dark" style="font-size: 0.9rem;">{{ $notif->judul }}</h6>
                                <p class="mb-0 text-muted small" style="line-height: 1.3;">{{ Str::limit($notif->pesan, 40) }}</p>
                            </div>
                            <small class="text-secondary text-nowrap" style="font-size: 0.75rem;">
                                {{ $notif->created_at->diffForHumans() }}
                            </small>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4">
                        <i class="fas fa-check-circle text-muted mb-2" style="font-size: 2rem;"></i>
                        <p class="text-muted small mb-0">Tidak ada aktivitas baru</p>
                    </div>
                @endforelse
            </div>
            <a href="{{ route('activity-log.index') }}" class="btn btn-outline-primary w-100 rounded-pill btn-sm fw-bold">Lihat Selengkapnya</a>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    const ctxMain = document.getElementById('mainChart').getContext('2d');
    const monthlyStats = {!! json_encode($monthlyStats) !!};
    
    new Chart(ctxMain, {
        type: 'bar', // Default to bar, but we will mix types
        data: {
            labels: monthlyStats.map(s => s.month),
            datasets: [
                {
                    label: 'Volume Susu (Liter)',
                    data: monthlyStats.map(s => s.produksi),
                    backgroundColor: 'rgba(34, 197, 94, 0.7)',
                    borderColor: '#16a34a',
                    borderWidth: 1,
                    borderRadius: 4,
                    order: 2,
                    yAxisID: 'y'
                },
                {
                    label: 'Peternak Aktif',
                    data: monthlyStats.map(s => s.active_peternak),
                    type: 'line', // Mixed chart
                    borderColor: '#2563eb', // Blue
                    backgroundColor: 'rgba(37, 99, 235, 0.1)',
                    pointBackgroundColor: '#2563eb',
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    borderWidth: 3,
                    tension: 0.3, // Curve the line slightly
                    fill: false,
                    order: 1,
                    yAxisID: 'y1'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                }
            },
            interaction: {
                mode: 'nearest',
                axis: 'x',
                intersect: false
            },
            scales: {
                x: {
                    grid: { display: false }
                },
                y: {
                    beginAtZero: true,
                    title: { display: true, text: 'Volume (L)' },
                    grid: { color: '#f1f5f9' }
                },
                y1: {
                    beginAtZero: true,
                    position: 'right',
                    title: { display: true, text: 'Jml Peternak' },
                    grid: {
                        drawOnChartArea: false // Only draw grid lines for the left axis
                    },
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

    // --- Top 5 Peternak Chart (Doughnut) ---
    const ctxTop = document.getElementById('topPeternakChart').getContext('2d');
    // Prepare Data
    const peternakNames = @json($top5Peternak->pluck('nama_peternak'));
    const peternakVolumes = @json($top5Peternak->pluck('produksi_sum_jumlah_susu_liter'));

    new Chart(ctxTop, {
        type: 'doughnut',
        data: {
            labels: peternakNames,
            datasets: [{
                data: peternakVolumes,
                backgroundColor: [
                    '#3B82F6', // Blue
                    '#10B981', // Green
                    '#F59E0B', // Orange
                    '#EF4444', // Red
                    '#8B5CF6'  // Purple
                ],
                borderWidth: 0,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%', // Thinner ring
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        padding: 15,
                        boxWidth: 8,
                        font: { size: 11 }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let val = context.raw || 0;
                            let total = context.chart._metasets[context.datasetIndex].total;
                            let percentage = Math.round((val / total) * 100) + '%';
                            return context.label + ': ' + val.toLocaleString('id-ID') + ' L (' + percentage + ')';
                        }
                    }
                }
            }
        }
    });
</script>


    </div>
</div>

@endsection

@section('scripts')
<script>
    const ctxMain = document.getElementById('mainChart').getContext('2d');
    const monthlyStats = {!! json_encode($monthlyStats) !!};
    
    new Chart(ctxMain, {
        type: 'bar', // Default to bar, but we will mix types
        data: {
            labels: monthlyStats.map(s => s.month),
            datasets: [
                {
                    label: 'Volume Susu (Liter)',
                    data: monthlyStats.map(s => s.produksi),
                    backgroundColor: 'rgba(34, 197, 94, 0.7)',
                    borderColor: '#16a34a',
                    borderWidth: 1,
                    borderRadius: 4,
                    order: 2,
                    yAxisID: 'y'
                },
                {
                    label: 'Peternak Aktif',
                    data: monthlyStats.map(s => s.active_peternak),
                    type: 'line', // Mixed chart
                    borderColor: '#2563eb', // Blue
                    backgroundColor: 'rgba(37, 99, 235, 0.1)',
                    pointBackgroundColor: '#2563eb',
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    borderWidth: 3,
                    tension: 0.3, // Curve the line slightly
                    fill: false,
                    order: 1,
                    yAxisID: 'y1'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                }
            },
            interaction: {
                mode: 'nearest',
                axis: 'x',
                intersect: false
            },
            scales: {
                x: {
                    grid: { display: false }
                },
                y: {
                    beginAtZero: true,
                    title: { display: true, text: 'Volume (L)' },
                    grid: { color: '#f1f5f9' }
                },
                y1: {
                    beginAtZero: true,
                    position: 'right',
                    title: { display: true, text: 'Jml Peternak' },
                    grid: {
                        drawOnChartArea: false // Only draw grid lines for the left axis
                    },
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
</script>
@endsection
