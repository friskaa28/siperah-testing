@extends('layouts.app')

@section('title', 'Manajemen Gaji - SIPERAH')

@section('content')
@if(session('error'))
    <div style="background: #FEE2E2; color: #991B1B; padding: 1rem; border-radius: 8px; border: 1px solid #FECACA; margin-bottom: 1.5rem;">
        ❌ {{ session('error') }}
    </div>
@endif
@if(session('success'))
    <div style="background: #DCFCE7; color: #166534; padding: 1rem; border-radius: 8px; border: 1px solid #BBF7D0; margin-bottom: 1.5rem;">
        ✅ {{ session('success') }}
    </div>
@endif
<div class="flex-between mb-4">
    <h1>Slip Pembayaran Susu <small style="font-size: 0.5em; opacity: 0.5;">v2.1</small></h1>
    <div class="d-flex gap-2">
        @if($errors->any())
            <div style="background: #FEE2E2; color: #991B1B; padding: 0.5rem; border-radius: 6px; font-size: 0.8rem; border: 1px solid #FECACA;">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="card shadow-sm border-0" style="border-radius: 12px; margin-bottom: 0;">
            <div class="card-body p-2">
                <form action="{{ route('gaji.import') }}" method="POST" enctype="multipart/form-data" class="d-flex flex-wrap align-items-center gap-2">
                    @csrf
                    <label class="small fw-bold text-muted mb-0 text-nowrap">Upload Excel Produksi:</label>
                    <input type="file" name="file" class="form-control form-control-sm w-auto" style="border-radius: 20px; min-width: 200px;" required accept=".xlsx, .xls">
                    <button type="submit" class="btn btn-primary btn-sm px-3 fw-bold shadow-sm text-nowrap" style="border-radius: 8px;">
                        📥 Import Excel
                    </button>
                    <a href="{{ route('gaji.template') }}" class="btn btn-light btn-sm px-3 fw-bold border shadow-sm text-nowrap" style="border-radius: 8px; color: #64748b;">
                        📊 Download Template
                    </a>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="grid" style="grid-template-columns: 2fr 1fr; gap: 1.5rem; align-items: start;">
    <!-- LEFT: MAIN TABLE -->
    <div class="card">
        <div class="flex-between mb-4">
            <form action="{{ route('gaji.index') }}" method="GET" class="d-flex gap-2 align-center">
                <select name="bulan" class="form-control" style="width: 130px;">
                    @for($i=1; $i<=12; $i++)
                        <option value="{{ $i }}" {{ $bulan == $i ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $i, 1)) }}</option>
                    @endfor
                </select>
                <select name="tahun" class="form-control" style="width: 100px;">
                    @for($i=now()->year; $i>=now()->year-1; $i--)
                        <option value="{{ $i }}" {{ $tahun == $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
                <button type="submit" class="btn btn-secondary">Lihat</button>
            </form>
            
            <form action="{{ route('gaji.generate') }}" method="POST">
                @csrf
                <input type="hidden" name="bulan" value="{{ $bulan }}">
                <input type="hidden" name="tahun" value="{{ $tahun }}">
                <button type="submit" class="btn btn-success" title="Update data liter dari rekaman harian">Refresh Total Liter</button>
            </form>
        </div>

        @if($slips->count() > 0)
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Peternak</th>
                        <th>Total Susu</th>
                        <th>Sisa Bayar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($slips as $s)
                    <tr>
                        <td>
                            <div style="font-weight: 600;">{{ $s->peternak->nama_peternak }}</div>
                            <div style="font-size: 0.75rem; color: #666;">No: {{ $s->peternak->no_peternak ?: '-' }} | {{ $s->peternak->kelompok ?: '-' }}</div>
                        </td>
                        <td>{{ number_format($s->jumlah_susu, 2) }} L</td>
                        <td style="font-weight: 600; color: #166534;">
                            Rp {{ number_format($s->sisa_pembayaran, 0, ',', '.') }}
                            @if($s->isSigned())
                                <div style="font-size: 0.65rem; color: var(--primary); margin-top: 4px;">
                                    <i class="fas fa-check-circle"></i> Signed Digitally
                                </div>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('gaji.edit', $s->idslip) }}" class="btn btn-secondary p-1" title="Input Potongan">⚙️ Potongan</a>
                                
                                @if(!$s->isSigned())
                                    <form action="{{ route('gaji.sign', $s->idslip) }}" method="POST" onsubmit="return confirm('Tanda tangani slip ini secara digital? Tindakan ini akan mengunci slip.')">
                                        @csrf
                                        <button type="submit" class="btn btn-success p-1" title="Tanda Tangani Digital">✍️ Sign</button>
                                    </form>
                                @endif

                                <a href="{{ route('gaji.print', $s->idslip) }}" target="_blank" class="btn btn-primary p-1">🖨️ Cetak</a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center p-4">
            <p class="text-light">Belum ada data pembayaran untuk bulan ini.</p>
            <p style="font-size: 0.85rem; color: #999;">Silakan upload CSV atau klik "Refresh Total Liter" jika data harian sudah ada.</p>
        </div>
        @endif
    </div>

    <!-- RIGHT: HELPER -->
    <div class="card" style="background: #F9FAFB;">
        <h3 class="mb-2">Panduan Cepat</h3>
        <ol style="font-size: 0.85rem; padding-left: 1.2rem; color: #4B5563; line-height: 1.6;">
            <li><strong>Upload CSV GForm</strong>: Pilih file dan klik "Proses Data". Slip gaji akan langsung terbuat otomatis.</li>
            <li><strong>Cek Nama</strong>: Pastikan nama di Excel sama dengan daftar di bawah.</li>
            <li><strong>Isi Potongan</strong>: Klik tombol ⚙️ untuk memasukkan biaya pakan, kas bon, dll.</li>
            <li><strong>Cetak</strong>: Klik 🖨️ untuk print slip fisik.</li>
        </ol>

        <h3 class="mt-4 mb-2">Bulan yang Tersedia:</h3>
        <div class="d-flex flex-wrap gap-1 mb-4">
            @php
                $availableMonths = \App\Models\ProduksiHarian::selectRaw('MONTH(tanggal) as m, YEAR(tanggal) as y')
                    ->distinct()->orderBy('y', 'desc')->orderBy('m', 'desc')->take(6)->get();
            @endphp
            @foreach($availableMonths as $am)
                <a href="{{ route('gaji.index', ['bulan' => $am->m, 'tahun' => $am->y]) }}" class="btn btn-secondary p-1" style="font-size: 0.75rem;">
                    {{ date('M Y', mktime(0, 0, 0, $am->m, 1, $am->y)) }}
                </a>
            @endforeach
        </div>

        <h3 class="mt-2 mb-2">Daftar Peternak Aktif:</h3>
        <div style="max-height: 300px; overflow-y: auto; font-size: 0.8rem; border: 1px solid #E5E7EB; border-radius: 6px; padding: 5px; background: #fff;">
            @foreach(\App\Models\Peternak::all() as $p)
                <div style="padding: 5px; border-bottom: 1px solid #F3F4F6;">
                    <strong>{{ $p->no_peternak ?: '??' }}</strong> - {{ $p->nama_peternak }}
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
