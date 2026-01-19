@extends('layouts.app')

@section('title', 'Monitoring Harian - SIPERAH')

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col-md-6">
        <h1 class="h3 mb-0"><i class="fas fa-desktop"></i> Monitoring Harian</h1>
        <p class="text-muted">Monitoring real-time setoran susu dan kasbon peternak</p>
    </div>
    <div class="col-md-6 text-md-end">
        <form action="{{ route('monitoring.index') }}" method="GET" class="d-inline-flex gap-2">
            <input type="date" name="tanggal" class="form-control" value="{{ $tanggal }}" onchange="this.form.submit()">
        </form>
    </div>
</div>

<div class="card shadow-sm border-0" style="border-radius: 12px;">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="px-4 py-3 text-center" style="width: 50px;">No</th>
                    <th class="py-3">Nama Peternak</th>
                    <th class="py-3 text-center">Status Setor</th>
                    <th class="py-3 text-end">Volume (L)</th>
                    <th class="py-3 text-center">Status Kasbon</th>
                    <th class="py-3 text-end px-4">Total Kasbon</th>
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
                    <td class="py-3 text-center">
                        @if($prod)
                            <span class="badge bg-success"><i class="fas fa-check"></i> Sudah</span>
                        @else
                            <span class="badge bg-secondary text-light opacity-50"><i class="fas fa-minus"></i> Belum</span>
                        @endif
                    </td>
                    <td class="py-3 text-end fw-bold">
                        {{ $prod ? number_format($prod->jumlah_susu_liter, 2) : '-' }}
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
                    <td colspan="6" class="text-center py-4 text-muted">Data peternak tidak ditemukan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
