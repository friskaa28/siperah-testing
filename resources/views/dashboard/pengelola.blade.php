@extends('layouts.app')

@section('title', 'Dashboard Admin - SIPERAH')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-md-6">
        <h1 class="fw-bold mb-0">Dashboard Admin</h1>
        <p class="text-muted">Pantau operasional harian dan kelola data master.</p>
    </div>
    <div class="col-md-6 text-end">
        <div class="p-2 px-3 d-inline-block" style="background: #f0f7ff; border-radius: 12px; border: 1px solid #dbeafe;">
            <p class="mb-0 text-muted small">Harga Susu Aktif</p>
            <h4 class="fw-bold text-primary mb-0">Rp {{ number_format($currentPrice, 0, ',', '.') }}/L</h4>
        </div>
    </div>
</div>

<!-- Quick Access Menu -->
<div class="grid" style="grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 2rem;">
    <a href="/produksi/input" class="card text-center text-decoration-none p-4" style="border-bottom: 4px solid var(--success); transition: transform 0.2s;">
        <span style="font-size: 2rem; display: block; margin-bottom: 10px;"><i class="fas fa-cow"></i></span>
        <h4 class="fw-bold mb-1" style="color: var(--dark);">Setoran Susu</h4>
        <p class="text-muted small mb-0">Input Pagi/Sore</p>
    </a>
    <a href="{{ route('kasbon.index') }}" class="card text-center text-decoration-none p-4" style="border-bottom: 4px solid var(--danger); transition: transform 0.2s;">
        <span style="font-size: 2rem; display: block; margin-bottom: 10px;"><i class="fas fa-shopping-bag"></i></span>
        <h4 class="fw-bold mb-1" style="color: var(--dark);">Input Kasbon</h4>
        <p class="text-muted small mb-0">Pakan & Logistik</p>
    </a>
    <a href="{{ route('logistik.index') }}" class="card text-center text-decoration-none p-4" style="border-bottom: 4px solid var(--warning); transition: transform 0.2s;">
        <span style="font-size: 2rem; display: block; margin-bottom: 10px;"><i class="fas fa-clipboard-list"></i></span>
        <h4 class="fw-bold mb-1" style="color: var(--dark);">Katalog Barang</h4>
        <p class="text-muted small mb-0">Atur Harga Pakan</p>
    </a>
    <a href="{{ route('harga_susu.index') }}" class="card text-center text-decoration-none p-4" style="border-bottom: 4px solid var(--primary); transition: transform 0.2s;">
        <span style="font-size: 2rem; display: block; margin-bottom: 10px;"><i class="fas fa-tags"></i></span>
        <h4 class="fw-bold mb-1" style="color: var(--dark);">Harga Susu</h4>
        <p class="text-muted small mb-0">Update Harga Beli</p>
    </a>
</div>

<div class="grid" style="grid-template-columns: 2fr 1.2fr; gap: 1.5rem;">
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
            <h3 class="fw-bold mb-4" style="font-size: 1.1rem;"><i class="fas fa-chart-line"></i> Statistik Produksi (Time Series)</h3>
            <div style="height: 300px;">
                <canvas id="mainChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Right Sidebar: KPI & Activity -->
    <div>
        <div class="card mb-4" style="padding: 1.5rem;">
            <h3 class="fw-bold mb-3" style="font-size: 1.1rem;"><i class="fas fa-chart-pie"></i> Ringkasan Sistem</h3>
            <div class="d-flex justify-content-between py-2 border-bottom">
                <span class="text-muted">Total Mitra</span>
                <span class="fw-bold">{{ $totalPeternak }}</span>
            </div>
            <div class="d-flex justify-content-between py-2 border-bottom">
                <span class="text-muted">Produksi Bulan Ini</span>
                <span class="fw-bold">{{ number_format($totalProduksiBulanIni, 1) }} L</span>
            </div>
            <div class="d-flex justify-content-between py-2 mt-2">
                <a href="/gaji" class="btn btn-secondary w-100 py-2">Rekapitulasi Gaji &raquo;</a>
            </div>
        </div>


    </div>
</div>

@endsection

@section('scripts')
<script>
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
                    borderRadius: 4
                },
                {
                    label: 'Produksi {{ date('Y') - 1 }}',
                    data: monthlyStats.map(s => s.produksi_last),
                    backgroundColor: 'rgba(100, 116, 139, 0.5)',
                    borderRadius: 4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' }
            },
            scales: {
                y: { beginAtZero: true, grid: { color: '#f1f5f9' } },
                x: { grid: { display: false } }
            }
        }
    });
</script>
@endsection
