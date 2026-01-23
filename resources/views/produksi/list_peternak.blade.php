@extends('layouts.app')

@section('title', 'Riwayat Setor Susu - SIPERAH')

@section('content')
<div id="printableHeader" class="d-none">
    <h2>KOPERASI PRODUSEN SI PENGOLAHAN RAHAYU</h2>
    <p class="mb-0">Riwayat Setoran Susu Peternak</p>
    <div class="text-end small fw-bold">Periode: {{ $startDate ?? '-' }} s/d {{ $endDate ?? '-' }}</div>
</div>

<div class="row mb-4 align-items-center">
    <div class="col-md-6">
        <h1 class="h3 mb-0"><i class="fas fa-history"></i> Riwayat Setor Susu</h1>
        <p class="text-muted">Kelola dan pantau data pengumpulan susu harian</p>
    </div>
    @if(isset($isAdmin) && $isAdmin)
    <div class="col-md-6">
        <form action="{{ route('produksi.index') }}" method="GET" class="d-flex flex-wrap gap-2 justify-content-end align-items-end">
            @if(request('idpeternak')) <input type="hidden" name="idpeternak" value="{{ request('idpeternak') }}"> @endif
            <div class="filter-group">
                <label class="small fw-bold text-muted mb-1">Mulai</label>
                <input type="date" name="start_date" class="form-control form-control-sm border-2" value="{{ $startDate }}">
            </div>
            <div class="filter-group">
                <label class="small fw-bold text-muted mb-1">Hingga</label>
                <input type="date" name="end_date" class="form-control form-control-sm border-2" value="{{ $endDate }}">
            </div>
            <div class="filter-group">
                <label class="small fw-bold text-muted mb-1">Baris</label>
                <select name="per_page" class="form-select form-select-sm border-2" style="max-width: 100px;" onchange="this.form.submit()">
                    <option value="15" {{ $perPage == 15 ? 'selected' : '' }}>15</option>
                    <option value="30" {{ $perPage == 30 ? 'selected' : '' }}>30</option>
                    <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                </select>
            </div>

            <div class="filter-group">
                <label class="small fw-bold text-muted mb-1">Status</label>
                <select name="status_setor" class="form-select form-select-sm border-2" style="max-width: 150px;" onchange="this.form.submit()">
                    <option value="">-- Semua --</option>
                    <option value="pagi" {{ request('status_setor') == 'pagi' ? 'selected' : '' }}>Pagi</option>
                    <option value="sore" {{ request('status_setor') == 'sore' ? 'selected' : '' }}>Sore</option>
                    <option value="lengkap" {{ request('status_setor') == 'lengkap' ? 'selected' : '' }}>Lengkap</option>
                </select>
            </div>
            @if(isset($isAdmin) && $isAdmin)
            <div class="filter-group">
                <label class="small fw-bold text-muted mb-1">Peternak</label>
                <select name="idpeternak" class="form-select form-select-sm border-2" style="max-width: 200px;" onchange="this.form.submit()">
                    <option value="">-- Semua --</option>
                    @foreach($peternaks as $p)
                        <option value="{{ $p->idpeternak }}" {{ $idpeternak == $p->idpeternak ? 'selected' : '' }}>
                            {{ $p->nama_peternak }}
                        </option>
                    @endforeach
                </select>
            </div>
            @endif
            <button type="submit" class="btn btn-primary btn-sm fw-bold px-3">Filter</button>
            <button type="button" class="btn btn-success btn-sm fw-bold px-3" onclick="window.location.href='{{ request()->fullUrlWithQuery(['export' => 'excel']) }}'">
                <i class="fas fa-file-excel"></i> Export
            </button>
            <button type="button" class="btn btn-info btn-sm fw-bold px-3" onclick="window.print()">
                <i class="fas fa-print"></i> Cetak
            </button>
        </form>
    </div>
    @endif
</div>

<div class="card shadow-sm border-0" style="border-radius: 12px;">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="px-4 py-3">Tanggal</th>
                    @if(isset($isAdmin) && $isAdmin)
                        <th class="py-3">Nama</th>
                        <th class="py-3 text-center">Status Setor Susu</th>
                    @endif
                    <th class="py-3 text-center">Pagi (L)</th>
                    <th class="py-3 text-center">Sore (L)</th>
                    <th class="py-3 text-end">Total (L)</th>
                    <th class="py-3 text-center px-4">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($produksi as $p)
                @php
                    // Legacy total just for checking if we strictly need to show something? 
                    // But user asked to conform to input which has NO cost now.
                @endphp
                    <tr>
                        <td class="px-4 py-3 fw-bold">{{ \Carbon\Carbon::parse($p->tanggal)->format('d/m/Y') }}</td>
                        @if(isset($isAdmin) && $isAdmin)
                            <td class="py-3">{{ $p->peternak->nama_peternak }}</td>
                            <td class="py-3 text-center">
                                @if($p->pagi > 0 && $p->sore > 0)
                                    <span class="badge bg-success">Lengkap</span>
                                @elseif($p->pagi > 0)
                                    <span class="badge bg-primary">Pagi</span>
                                @elseif($p->sore > 0)
                                    <span class="badge bg-warning text-dark">Sore</span>
                                @else
                                    <span class="badge bg-secondary">Belum</span>
                                @endif
                            </td>
                        @endif
                        <td class="py-3 text-center text-primary fw-bold">{{ $p->pagi > 0 ? number_format($p->pagi, 1, ',', '.') : '-' }}</td>
                        <td class="py-3 text-center text-primary fw-bold">{{ $p->sore > 0 ? number_format($p->sore, 1, ',', '.') : '-' }}</td>
                        <td class="py-3 text-end fw-bold">{{ number_format($p->total, 1, ',', '.') }} L</td>
                        <td class="py-3 px-4">
                            <div class="d-flex justify-content-center gap-3">
                                @php
                                    $actions = [
                                        'pagi' => ['id' => $p->idpagi, 'icon' => 'fa-sun', 'title' => 'Pagi'],
                                        'sore' => ['id' => $p->idsore, 'icon' => 'fa-moon', 'title' => 'Sore']
                                    ];
                                @endphp
                                @foreach($actions as $type => $act)
                                    @if($act['id'])
                                    <div class="d-flex flex-column align-items-center gap-1">
                                        <span class="small text-muted fw-bold" style="font-size: 0.7rem;">{{ strtoupper($type) }}</span>
                                        <div class="d-flex gap-1 border rounded p-1">
                                            <a href="{{ route('produksi.edit', $act['id']) }}" class="text-warning p-1" title="Edit {{ $act['title'] }}">
                                                <i class="fas fa-edit fa-xs"></i>
                                            </a>
                                            <form action="{{ route('produksi.destroy', $act['id']) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data {{ $act['title'] }} ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-danger border-0 bg-transparent p-1" title="Hapus {{ $act['title'] }}">
                                                    <i class="fas fa-trash fa-xs"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                    @endif
                                @endforeach
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ isset($isAdmin) && $isAdmin ? 7 : 5 }}" class="text-center py-5 text-muted">
                            <div class="mb-2" style="font-size: 2rem;"><i class="fas fa-inbox"></i></div>
                            Belum ada data setor susu tercatat.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($produksi->hasPages())
        <div class="card-footer bg-white border-0 p-4">
            {{ $produksi->appends(request()->query())->links() }}
        </div>
    @endif
</div>
<style>
    .action-btn {
        width: 34px;
        height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        text-decoration: none;
        border: none;
        font-size: 0.9rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    
    .action-btn.detail {
        background: #e0f2fe;
        color: #0369a1;
    }
    .action-btn.detail:hover {
        background: #0369a1;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(3, 105, 161, 0.2);
    }

    .action-btn.edit {
        background: #fef9c3;
        color: #a16207;
    }
    .action-btn.edit:hover {
        background: #a16207;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(161, 98, 7, 0.2);
    }

    .action-btn.delete {
        background: #fee2e2;
        color: #b91c1c;
    }
    .action-btn.delete:hover {
        background: #b91c1c;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(185, 28, 28, 0.2);
    }
</style>
@section('styles')
<style>
    @media print {
        @page { size: portrait; margin: 1.5cm; }
        .no-print, .sidebar, .navbar, .footer, .btn, form, .pagination, .action-btn, .gap-3 .d-flex.flex-column { display: none !important; }
        #printableHeader { display: block !important; text-align: center; margin-bottom: 30px; border-bottom: 3px double #000; padding-bottom: 15px; }
        #printableHeader h2 { margin: 0; font-size: 18pt; font-weight: bold; }
        body { background: white !important; font-size: 11pt; }
        .card { border: none !important; box-shadow: none !important; }
        .table { border: 1.5px solid #000 !important; }
        .table th, .table td { border: 1px solid #000 !important; padding: 8px !important; color: black !important; }
        .table thead th { background: #f8fafc !important; text-transform: uppercase; font-size: 10pt; }
        .text-primary, .fw-bold { color: black !important; }
    }
</style>
@endsection

@endsection
