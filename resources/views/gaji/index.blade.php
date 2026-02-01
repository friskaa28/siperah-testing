@extends('layouts.app')

@section('title', 'Manajemen Gaji - SIPERAH')

@section('content')
@if(session('error'))
    <div style="background: #FEE2E2; color: #991B1B; padding: 1rem; border-radius: 8px; border: 1px solid #FECACA; margin-bottom: 1.5rem;">
        <i class="fas fa-times-circle"></i> {{ session('error') }}
    </div>
@endif
@if(session('success'))
    <div style="background: #DCFCE7; color: #166534; padding: 1rem; border-radius: 8px; border: 1px solid #BBF7D0; margin-bottom: 1.5rem;">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
@endif
<div class="row mb-4 align-items-center">
    <div class="col-12 col-md-6 mb-3 mb-md-0">
        <h1 class="mb-0">Slip Gaji & Pembayaran Susu</h1>
    </div>
</div>

<div class="row mb-4">
    <!-- Existing Monthly Upload -->
    <div class="col-12 col-lg-6 mb-3 mb-lg-0">
        <div class="card shadow-sm border-0 h-100" style="border-radius: 12px;">
            <div class="card-header bg-white border-0 pt-3 pb-0">
                <h6 class="fw-bold text-muted mb-0"><i class="fas fa-file-invoice-dollar"></i> Unggah Gaji & Potongan (Bulanan)</h6>
            </div>
            <div class="card-body p-3">
                <form action="{{ route('gaji.import') }}" method="POST" enctype="multipart/form-data" class="row g-2 align-items-end">
                    @csrf
                    <div class="col-12">
                        <input type="file" name="file" class="form-control form-control-sm" style="border-radius: 8px;" required accept=".xlsx, .xls">
                    </div>
                    <div class="col-6">
                        <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold" style="border-radius: 8px;">
                            <i class="fas fa-upload"></i> Unggah Bulanan
                        </button>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('gaji.template') }}" class="btn btn-light btn-sm w-100 fw-bold border" style="border-radius: 8px; color: #64748b;">
                            <i class="fas fa-download"></i> Template
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- New Daily Upload (Matrix Support) -->
    <div class="col-12 col-lg-6">
        <div class="card shadow-sm border-0 h-100" style="border-radius: 12px;">
            <div class="card-header bg-white border-0 pt-3 pb-0">
                <h6 class="fw-bold text-muted mb-0"><i class="fas fa-cow"></i> Unggah Setor Susu (Harian)</h6>
            </div>
            <div class="card-body p-3">
                <form action="{{ route('produksi.import') }}" method="POST" enctype="multipart/form-data" class="row g-2 align-items-end">
                    @csrf
                    <!-- Date Context for Matrix Import -->
                    <div class="col-6">
                        <select name="bulan" class="form-control form-control-sm" style="border-radius: 8px;">
                            @for($i=1; $i<=12; $i++)
                                <option value="{{ $i }}" {{ date('n') == $i ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $i, 1)) }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-6">
                        <select name="tahun" class="form-control form-control-sm" style="border-radius: 8px;">
                            @for($i=now()->year; $i>=now()->year-1; $i--)
                                <option value="{{ $i }}" {{ now()->year == $i ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    
                    <div class="col-12">
                        <input type="file" name="file" class="form-control form-control-sm" style="border-radius: 8px;" required accept=".xlsx, .xls">
                    </div>
                    <div class="col-6">
                        <button type="submit" class="btn btn-success btn-sm w-100 fw-bold" style="border-radius: 8px;">
                            <i class="fas fa-upload"></i> Unggah Harian
                        </button>
                    </div>
                    <div class="col-6">
                        <div class="btn-group w-100">
                            <button type="button" class="btn btn-light btn-sm fw-bold border dropdown-toggle w-100" data-bs-toggle="dropdown" aria-expanded="false" style="border-radius: 8px; color: #64748b;">
                                <i class="fas fa-download"></i> Template
                            </button>
                            <ul class="dropdown-menu shadow border-0" style="border-radius: 12px;">
                                <li>
                                    <a class="dropdown-item py-2 small" href="{{ route('produksi.template', ['format' => 'matrix']) }}">
                                        <i class="fas fa-th me-2 text-primary"></i> <span class="fw-bold">Format Matriks (Manual)</span><br>
                                        <span class="text-muted" style="font-size: 0.75rem;">Sesuai buku catatan Anda</span>
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item py-2 small" href="{{ route('produksi.template') }}">
                                        <i class="fas fa-list me-2 text-secondary"></i> Format List (Standar)<br>
                                        <span class="text-muted" style="font-size: 0.75rem;">Kolom Tanggal, Waktu, Liter</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <!-- LEFT: MAIN TABLE -->
    <div class="col-12 col-lg-8">
        <div class="card">
            <div class="card-body p-3">
                <div class="row g-2 mb-3">
                    <div class="col-12 col-md-9">
                        <form action="{{ route('gaji.index') }}" method="GET" class="row g-2">
                            <div class="col-6 col-sm-4">
                                <label class="small fw-bold mb-1">Bulan</label>
                                <select name="bulan" class="form-control form-control-sm">
                                    @for($i=1; $i<=12; $i++)
                                        <option value="{{ $i }}" {{ $bulan == $i ? 'selected' : '' }}>{{ date('M', mktime(0, 0, 0, $i, 1)) }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-6 col-sm-3">
                                <label class="small fw-bold mb-1">Tahun</label>
                                <select name="tahun" class="form-control form-control-sm">
                                    @for($i=now()->year; $i>=now()->year-1; $i--)
                                        <option value="{{ $i }}" {{ $tahun == $i ? 'selected' : '' }}>{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-6 col-sm-3">
                                <label class="small fw-bold mb-1">Tampilkan</label>
                                <select name="per_page" class="form-control form-control-sm" onchange="this.form.submit()">
                                    <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                                    <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                                    <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                                </select>
                            </div>
                            <div class="col-6 col-sm-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-secondary btn-sm w-100">Lihat</button>
                            </div>
                        </form>
                    </div>
                    <div class="col-12 col-md-3 d-flex align-items-end">
                        <form action="{{ route('gaji.generate') }}" method="POST" class="w-100">
                            @csrf
                            <input type="hidden" name="bulan" value="{{ $bulan }}">
                            <input type="hidden" name="tahun" value="{{ $tahun }}">
                            <button type="submit" class="btn btn-success btn-sm w-100 fw-bold" title="Update data liter & sinkron potongan harian" style="height: 31px;">
                                <i class="fas fa-sync"></i> Refresh & Sinkron
                            </button>
                        </form>
                    </div>
                </div>

        @if($slips->count() > 0)
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 30%;">Peternak</th>
                        <th style="width: 15%; text-align: center;">Total Susu</th>
                        <th style="width: 40%; text-align: right;">Sisa Bayar (Netto)</th>
                        <th style="width: 15%; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($slips as $s)
                    <tr>
                        <td>
                            <div style="font-weight: 600;">{{ $s->peternak->nama_peternak }}</div>
                            <div style="font-size: 0.75rem; color: #666;">No: {{ $s->peternak->no_peternak ?: '-' }}</div>
                        </td>
                        <td class="text-center">{{ rtrim(rtrim(number_format($s->jumlah_susu, 2, ',', '.'), '0'), ',') }} L</td>
                        <td style="font-weight: 700; color: #166534; text-align: right; font-size: 1.1rem;">
                            Rp {{ number_format($s->sisa_pembayaran, 0, ',', '.') }}
                            @if($s->isSigned())
                                <div style="font-size: 0.65rem; color: var(--primary); margin-top: 4px;">
                                    <i class="fas fa-check-circle"></i> Terbayar & Digital Signed
                                </div>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex justify-content-center gap-2">
                                <a href="{{ route('gaji.edit', $s->idslip) }}" class="action-btn detail" title="Pratinjau & Edit Potongan">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('gaji.print', $s->idslip) }}" target="_blank" class="action-btn print" title="Cetak Slip Gaji">
                                    <i class="fas fa-print"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($slips->hasPages())
        <div class="mt-4 no-print d-flex justify-content-between align-items-center mb-2 px-3">
            <div class="small text-muted">Menampilkan {{ $slips->firstItem() ?? 0 }} - {{ $slips->lastItem() ?? 0 }} dari {{ $slips->total() }} data</div>
            <div>{{ $slips->links() }}</div>
        </div>
        @endif
        @else
        <div class="text-center p-4">
            <p class="text-light">Belum ada data pembayaran untuk bulan ini.</p>
            <p style="font-size: 0.85rem; color: #999;">Silakan upload Excel atau klik "Refresh & Sinkron Data".</p>
        </div>
        @endif
            </div>
        </div>
    </div>

    <!-- RIGHT: HELPER -->
    <div class="col-12 col-lg-4">
        <div class="card" style="background: #F9FAFB;">
            <div class="card-body p-3">
                <h3 class="mb-2" style="font-size: 1.1rem;">Panduan Gaji</h3>
                <ol style="font-size: 0.85rem; padding-left: 1.2rem; color: #4B5553; line-height: 1.6;">
                    <li><strong>Unggah Excel</strong>: Gunakan template untuk mengunggah liter & potongan sekaligus.</li>
                    <li><strong>Segarkan Data</strong>: Mengambil liter dari setoran harian & sinkronisasi otomatis potongan kasbon.</li>
                    <li><strong>Pratinjau</strong>: Cek detail slip, edit manual jika perlu, dan lakukan Tanda Tangan Digital.</li>
                </ol>

                <div class="mt-3 p-3 border rounded shadow-sm" style="background: #E0F2FE; border-color: #BAE6FD !important;">
                    <h4 class="fw-bold mb-2" style="font-size: 0.95rem; color: #0369A1;"><i class="fas fa-info-circle"></i> Sistem Periode</h4>
                    <p class="small mb-0" style="color: #075985;">Perhitungan dihitung per 1 bulan berjalan dengan pembagian:</p>
                    <ul class="small mt-1 mb-0 ps-3" style="color: #0c4a6e;">
                        <li><strong>Awal:</strong> Tanggal terakhir bulan lalu (Hanya SORE).</li>
                        <li><strong>Tengah:</strong> Tanggal 1 s/d H-1 bulan ini (FULL 24 jam).</li>
                        <li><strong>Akhir:</strong> Tanggal terakhir bulan ini (Hanya PAGI).</li>
                    </ul>
                </div>

                <h3 class="mt-3 mb-2" style="font-size: 1.1rem;">Bulan Tersedia:</h3>
                <div class="d-flex flex-wrap gap-1 mb-3">
                    @php
                        $availableMonths = \App\Models\ProduksiHarian::selectRaw('MONTH(tanggal) as m, YEAR(tanggal) as y')
                            ->distinct()->orderBy('y', 'desc')->orderBy('m', 'desc')->take(6)->get();
                    @endphp
                    @foreach($availableMonths as $am)
                        <a href="{{ route('gaji.index', ['bulan' => $am->m, 'tahun' => $am->y]) }}" class="btn btn-secondary btn-sm" style="font-size: 0.75rem;">
                            {{ date('M Y', mktime(0, 0, 0, $am->m, 1, $am->y)) }}
                        </a>
                    @endforeach
                </div>

                <h3 class="mt-3 mb-2" style="font-size: 1.1rem;">Daftar Peternak Aktif:</h3>
                <div style="max-height: 300px; overflow-y: auto; font-size: 0.8rem; border: 1px solid #E5E7EB; border-radius: 6px; padding: 5px; background: #fff;">
                    @foreach(\App\Models\Peternak::all() as $p)
                        <div style="padding: 5px; border-bottom: 1px solid #F3F4F6;">
                            <strong>{{ $p->no_peternak ?: '??' }}</strong> - {{ $p->nama_peternak }}
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
<style>
    .action-btn {
        width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        transition: all 0.2s;
        text-decoration: none;
        border: none;
        font-size: 0.85rem;
    }
    
    .action-btn.detail {
        background: #e0f2fe;
        color: #0369a1;
    }
    .action-btn.detail:hover {
        background: #0369a1;
        color: white;
    }

    .action-btn.print {
        background: #f0fdf4;
        color: #166534;
    }
    .action-btn.print:hover {
        background: #166534;
        color: white;
    }
</style>

@if(session('import_preview'))
<div class="card shadow-sm border-0 mb-4" style="border-radius: 12px; background: #FFFBEB; border: 1px solid #FEF3C7 !important;">
    <div class="card-body p-4">
        <div class="flex-between mb-3">
            <h2 class="h5 mb-0 text-warning-emphasis"><i class="fas fa-file-check me-2"></i> Konfirmasi Data Import</h2>
            <div class="d-flex gap-2">
                <form action="{{ route('gaji.confirm-import') }}" method="POST">
                    @csrf
                    <div id="hidden_inputs_container"></div>
                    <button type="submit" class="btn btn-success fw-bold shadow-sm px-4" id="btn_submit_import">
                        <i class="fas fa-save me-1"></i> SIMPAN SEMUA DATA TERPILIH
                    </button>
                </form>
                <form action="{{ route('gaji.confirm-import') }}" method="POST">
                    @csrf
                    <input type="hidden" name="cancel" value="1">
                    <button type="submit" class="btn btn-outline-secondary btn-sm">Batal</button>
                </form>
            </div>
        </div>
        
        <div class="alert alert-info py-2" style="font-size: 0.85rem;">
            <i class="fas fa-info-circle me-1"></i> Berikut adalah pratinjau data dari file Excel. Silakan periksa kembali sebelum menyimpan.
        </div>

        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
            <table class="table table-sm table-hover mb-0" id="previewTable">
                <thead class="bg-light sticky-top">
                    <tr>
                        <th class="text-center" style="width: 50px;">
                            <input class="form-check-input" type="checkbox" id="selectAll" checked>
                        </th>
                        <th>Mitra</th>
                        <th>Kategori</th>
                        <th class="text-center">Periode</th>
                        <th class="text-end">Liter</th>
                        <th class="text-end">Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(session('import_preview', []) as $idx => $row)
                    <tr class="{{ $row['is_new'] ? 'table-warning' : '' }}">
                        <td class="text-center">
                            <input class="form-check-input row-checkbox" type="checkbox" name="selected_rows[]" value="{{ $idx }}" checked>
                        </td>
                        <td>
                            <div class="fw-bold">{{ $row['nama_peternak'] }}</div>
                            @if($row['is_new'])
                                <small class="text-warning fw-bold">Baru</small>
                            @else
                                <small class="text-muted">{{ $row['no_peternak'] }}</small>
                            @endif
                        </td>
                        <td>
                            @php
                                $catName = [
                                    'peternak' => 'Peternak',
                                    'sub_penampung' => 'Sub Penampung',
                                    'sub_penampung_tr' => 'Sub-P TR',
                                    'sub_penampung_p' => 'Sub-P P',
                                ][$row['status_mitra'] ?? 'peternak'] ?? 'Peternak';
                            @endphp
                            <span class="badge bg-white text-dark border">{{ $catName }}</span>
                        </td>
                        <td class="text-center">{{ date('M', mktime(0, 0, 0, $row['bulan'], 1)) }} {{ $row['tahun'] }}</td>
                        <td class="text-end">{{ number_format($row['jumlah_susu'], 2) }}</td>
                        <td class="text-end fw-bold text-success">Rp {{ number_format($row['total_pembayaran'], 0, ',', '.') }}</td>
                        <td>
                            @if($row['is_new'])
                                <span class="badge bg-warning text-dark">Daftar Baru</span>
                            @else
                                <span class="badge bg-success-subtle text-success">Siap</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.row-checkbox');
        const hiddenContainer = document.getElementById('hidden_inputs_container');
        const btnSubmit = document.getElementById('btn_submit_import');

        function updateHiddenInputs() {
            if (!hiddenContainer) return;
            hiddenContainer.innerHTML = '';
            let count = 0;
            checkboxes.forEach(cb => {
                if (cb.checked) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'selected_rows[]';
                    input.value = cb.value;
                    hiddenContainer.appendChild(input);
                    count++;
                }
            });
            if (btnSubmit) {
                btnSubmit.disabled = count === 0;
                btnSubmit.innerHTML = `<i class="fas fa-save me-1"></i> SIMPAN ${count} DATA TERPILIH`;
            }
        }
        
        if (selectAll) {
            selectAll.addEventListener('change', function() {
                checkboxes.forEach(cb => {
                    cb.checked = selectAll.checked;
                });
                updateHiddenInputs();
            });

            checkboxes.forEach(cb => {
                cb.addEventListener('change', function() {
                    const allChecked = Array.from(checkboxes).every(c => c.checked);
                    const someChecked = Array.from(checkboxes).some(c => c.checked);
                    selectAll.checked = allChecked;
                    selectAll.indeterminate = someChecked && !allChecked;
                    updateHiddenInputs();
                });
            });
            
            // Initial run
            updateHiddenInputs();

            // Auto scroll to preview
            const previewSection = document.querySelector('.card-body.p-4').closest('.card');
            if (previewSection) {
                previewSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }
    });
</script>
<style>
    .bg-success-subtle { background-color: #dcfce7; }
    .bg-danger-subtle { background-color: #fee2e2; }
    #previewTable thead th {
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
        border-bottom: 2px solid #e2e8f0;
    }
    #previewTable tbody td {
        vertical-align: middle;
        font-size: 0.9rem;
    }
</style>
