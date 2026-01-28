@extends('layouts.app')

@section('title', 'Laporan Harian - SIPERAH')

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col-md-12 mb-3">
        <h1 class="h3 mb-0 fw-bold"><i class="fas fa-calendar-day"></i> Laporan Harian</h1>
        <p class="text-muted">Monitoring real-time setoran susu dan potongan peternak</p>
    </div>
    <div class="col-md-12">
        <div class="card border-0 shadow-sm p-3" style="border-radius: 12px;">
            <form action="{{ route('monitoring.index') }}" method="GET" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="small fw-bold text-muted mb-1">Pilih Tanggal</label>
                    <input type="date" name="tanggal" class="form-control" value="{{ $tanggal }}">
                </div>
                <div class="col-md-3">
                    <label class="small fw-bold text-muted mb-1">Kategori Mitra</label>
                    <select name="status_mitra" class="form-select">
                        <option value="">-- Semua Kategori --</option>
                        <option value="peternak" {{ request('status_mitra') == 'peternak' ? 'selected' : '' }}>Peternak Lokal</option>
                        <option value="sub_penampung_tr" {{ request('status_mitra') == 'sub_penampung_tr' ? 'selected' : '' }}>Sub-Penampung TR</option>
                        <option value="sub_penampung_p" {{ request('status_mitra') == 'sub_penampung_p' ? 'selected' : '' }}>Sub-Penampung P</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="small fw-bold text-muted mb-1">Cari Nama / ID</label>
                    <input type="text" name="search" class="form-control" placeholder="Contoh: Heri" value="{{ request('search') }}">
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary fw-bold flex-grow-1">Filter</button>
                    <button type="button" class="btn btn-outline-primary" onclick="showRekapModal()" title="Rekap Bulanan">
                        <i class="fas fa-table"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0" style="border-radius: 12px;">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="px-4 py-3 text-center" style="width: 50px;">No</th>
                    <th class="py-3">Nama</th>
                    <th class="py-3">Kategori</th>
                    <th class="py-3 text-center">Status Setor</th>
                    <th class="py-3 text-end">Volume (L)</th>
                    <th class="py-3 text-center">Status Potongan</th>
                    <th class="py-3 text-end px-4">Total Potongan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($peternaks as $index => $p)
                @php
                    $prod = $produksis->get($p->idpeternak);
                    $kasbonList = $kasbons->get($p->idpeternak);
                    $hasKasbon = $kasbonList && $kasbonList->count() > 0;
                    $totalKasbon = $hasKasbon ? $kasbonList->sum('total_rupiah') : 0;
                @endphp
                <tr>
                    <td class="px-4 py-3 text-center text-muted">{{ $index + 1 }}</td>
                    <td class="py-3 fw-medium">
                        {{ $p->nama_peternak }}
                        <small class="d-block text-muted">{{ $p->no_peternak }}</small>
                    </td>
                    <td class="py-3">
                        @php
                            $kategori = [
                                'peternak' => 'Peternak',
                                'sub_penampung_tr' => 'Sub-TR',
                                'sub_penampung_p' => 'Sub-P'
                            ];
                        @endphp
                        <span class="badge bg-light text-dark border">
                            {{ $kategori[$p->status_mitra] ?? ucfirst($p->status_mitra) }}
                        </span>
                    </td>
                    <td class="py-3 text-center">
                        @if($prod)
                            <span class="badge bg-success"><i class="fas fa-check"></i> Sudah</span>
                        @else
                            <span class="badge bg-secondary text-light opacity-50"><i class="fas fa-minus"></i> Belum</span>
                        @endif
                    </td>
                    <td class="py-3 text-end fw-bold">
                        {{ $prod ? number_format($prod->jumlah_susu_liter, 2, ',', '.') : '-' }}
                    </td>
                    <td class="py-3 text-center">
                        @if($hasKasbon)
                            <span class="badge bg-danger"><i class="fas fa-shopping-basket"></i> Ada</span>
                        @else
                            <span class="badge bg-light text-muted border">Tidak</span>
                        @endif
                    </td>
                    <td class="py-3 text-end px-4 fw-bold text-danger">
                        {{ $totalKasbon > 0 ? 'Rp ' . number_format($totalKasbon, 0, ',', '.') : '-' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-4 text-muted">Data peternak tidak ditemukan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Rekap Bulanan -->
<div id="rekapModal" style="display:none; position:fixed; z-index:1001; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.5); overflow-y: auto;">
    <div class="card shadow-lg" style="max-width:900px; margin: 30px auto; padding: 0; border-radius: 12px; overflow: hidden;">
        <div style="background: var(--primary); color: white; padding: 1.5rem; display: flex; justify-content: space-between; align-items: center;" class="no-print">
            <h2 class="h5 mb-0" style="color: white;"><i class="fas fa-calendar-alt"></i> Rekap Bulanan Pengiriman Susu</h2>
            <button onclick="hideRekapModal()" style="background: none; border: none; color: white; font-size: 1.5rem; cursor: pointer;">&times;</button>
        </div>
        
        <div style="padding: 2rem;">
            <!-- Filter di dalam modal -->
            <div class="no-print mb-4 p-3 bg-light" style="border-radius: 8px;">
                <form action="{{ route('monitoring.index') }}" method="GET" class="row g-2 align-items-end">
                    <input type="hidden" name="tanggal" value="{{ $tanggal }}">
                    <div class="col-md-5">
                        <label class="small fw-bold text-muted mb-1">Bulan</label>
                        <select name="bulan" class="form-select form-select-sm">
                            @for($i=1; $i<=12; $i++)
                                <option value="{{ $i }}" {{ $bulan == $i ? 'selected' : '' }}>
                                    @php
                                        $monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                                    @endphp
                                    {{ $monthNames[$i-1] }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="small fw-bold text-muted mb-1">Tahun</label>
                        <select name="tahun" class="form-select form-select-sm">
                            @for($i=now()->year; $i>=2024; $i--)
                                <option value="{{ $i }}" {{ $tahun == $i ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary btn-sm w-100">Tampilkan</button>
                    </div>
                </form>
            </div>

            <div id="printableRekap">
                <div class="text-center mb-4">
                    <h2 class="fw-bold mb-1" style="color: #000;">PENGIRIMAN SUSU</h2>
                    <p class="mb-0 text-muted">Bulan: {{ $monthNames[$bulan-1] }} {{ $tahun }}</p>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered text-center" style="border: 2px solid #000 !important;">
                        <thead style="background: #f1f5f9;">
                            <tr>
                                <th style="width: 10%; border: 2px solid #000 !important;">Tgl</th>
                                <th style="width: 23%; border: 2px solid #000 !important;">Jumlah (L)</th>
                                <th style="width: 10%; border: 2px solid #000 !important;">Tgl</th>
                                <th style="width: 23%; border: 2px solid #000 !important;">Jumlah (L)</th>
                                <th style="width: 10%; border: 2px solid #000 !important;">Tgl</th>
                                <th style="width: 24%; border: 2px solid #000 !important;">Jumlah (L)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @for($i = 1; $i <= 11; $i++)
                            <tr>
                                @php $cols = [$i, $i+11, $i+22]; @endphp
                                @foreach($cols as $day)
                                    @if($day <= $daysInMonth)
                                        <td style="border: 2px solid #000 !important; font-weight: bold; background: #fafafa;">{{ $day }}</td>
                                        <td style="border: 2px solid #000 !important;">
                                            {{ isset($dailyTotals[$day]) ? number_format($dailyTotals[$day], 2, ',', '.') : '-' }}
                                        </td>
                                    @else
                                        <td style="border: 2px solid #000 !important; background: #eee;"></td>
                                        <td style="border: 2px solid #000 !important; background: #eee;"></td>
                                    @endif
                                @endforeach
                            </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 p-3 border" style="border: 2px solid #000 !important; max-width: 250px;">
                    <div class="d-flex justify-content-between fw-bold" style="font-size: 1.1rem;">
                        <span>TOTAL:</span>
                        <span>{{ number_format($monthlyTotal, 2, ',', '.') }} Ltr</span>
                    </div>
                </div>

                <div class="signature-section mt-5" style="display: none; justify-content: space-between;">
                    <div style="text-align: center; width: 200px;">
                        <p class="mb-5">Mengetahui,</p>
                        <div style="margin-top: 50px;">
                            <p class="fw-bold mb-0">( ____________________ )</p>
                            <p class="small text-muted mt-1">Tim Verifikasi</p>
                        </div>
                    </div>
                    <div style="text-align: center; width: 200px;">
                        <p class="mb-0">{{ date('d') }} {{ $monthNames[date('n')-1] }} {{ date('Y') }}</p>
                        <p class="mb-5">Pengelola SIPERAH,</p>
                        <div style="margin-top: 50px;">
                            <p class="fw-bold mb-0">( ____________________ )</p>
                            <p class="small text-muted mt-1">Nama Pengelola</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center mt-4 no-print">
                <button class="btn btn-secondary px-4 me-2" onclick="hideRekapModal()">Tutup</button>
                <button class="btn btn-primary px-4" onclick="printMonthlyReport()">
                    <i class="fas fa-print"></i> Cetak Laporan Bulanan
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        body { visibility: hidden; background: white !important; }
        .no-print { display: none !important; }
        #rekapModal { 
            visibility: visible !important;
            display: block !important; 
            position: absolute !important; 
            left: 0 !important;
            top: 0 !important;
            width: 100% !important;
            background: white !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        #rekapModal .card { 
            box-shadow: none !important; 
            max-width: 100% !important; 
            margin: 0 !important;
            border: none !important;
            visibility: visible !important;
        }
        #rekapModal * { visibility: visible !important; }
        .signature-section { display: flex !important; }
    }
</style>

@endsection

@section('scripts')
<script>
    const rekapModal = document.getElementById('rekapModal');
    
    // Jika ada parameter bulan/tahun di URL, otomatis buka modal
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('bulan') || urlParams.has('tahun')) {
            showRekapModal();
        }
    });

    function showRekapModal() {
        rekapModal.style.display = 'block';
        document.body.style.overflow = 'hidden';
    }

    function hideRekapModal() {
        rekapModal.style.display = 'none';
        document.body.style.overflow = 'auto';
        // Reset URL if closed after filter
        if (window.location.search.includes('bulan')) {
            const url = new URL(window.location);
            url.searchParams.delete('bulan');
            url.searchParams.delete('tahun');
            window.history.replaceState({}, '', url);
        }
    }

    function printMonthlyReport() {
        window.print();
    }

    window.onclick = function(event) {
        if (event.target == rekapModal) hideRekapModal();
    }
</script>
@endsection
