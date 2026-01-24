@extends('layouts.app')

@section('title', 'Laporan Data - SIPERAH')

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col-md-12 d-print-none">
        <h1 class="h3 mb-0 fw-bold"><i class="fas fa-file-invoice"></i> Laporan Data SIPERAH</h1>
        <p class="text-muted">Pusat laporan terpadu: Pusat, Sub-Penampung, dan Harian</p>
    </div>
</div>

<!-- Navigasi Tab (Segments) -->
<div class="card shadow-sm border-0 mb-4" style="border-radius: 12px; overflow: hidden;">
    <div class="card-header bg-white p-0 border-0">
        <ul class="nav nav-pills nav-fill" id="laporanTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link py-3 @if($tab == 'pusat') active @endif" id="pusat-tab" data-bs-toggle="pill" data-bs-target="#pusat" type="button" role="tab" style="border-radius: 0; font-weight: 600;">
                    <i class="fas fa-building"></i> Laporan Pusat
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link py-3 @if($tab == 'sub_penampung') active @endif" id="sub-penampung-tab" data-bs-toggle="pill" data-bs-target="#sub_penampung" type="button" role="tab" style="border-radius: 0; font-weight: 600;">
                    <i class="fas fa-users"></i> Laporan Sub-Penampung
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link py-3 @if($tab == 'harian') active @endif" id="harian-tab" data-bs-toggle="pill" data-bs-target="#harian" type="button" role="tab" style="border-radius: 0; font-weight: 600;">
                    <i class="fas fa-calendar-day"></i> Laporan Harian
                </button>
            </li>
        </ul>
    </div>

    <div class="card-body p-4 bg-light">
        <div class="tab-content" id="laporanTabContent">
            
            <!-- SEGMENT 1: LAPORAN PUSAT -->
            <div class="tab-pane fade @if($tab == 'pusat') show active @endif" id="pusat" role="tabpanel">
                <!-- Print Only Header -->
                <div class="report-header d-none">
                    <h2 class="text-primary d-print-none" style="font-weight: 800; font-size: 24pt;">REKAPITULASI SETORAN SUSU</h2>
                    <p style="font-size: 12pt; margin-top: 5px;" class="d-print-none">Periode: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</p>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold mb-0">REKAPITULASI PUSAT</h5>
                    <div class="d-flex gap-2">
                        @if($isPrinting)
                        <button class="btn btn-sm btn-danger no-print" onclick="window.close()">
                            <i class="fas fa-times"></i> Tutup Halaman
                        </button>
                        @endif
                        <a href="{{ request()->fullUrlWithQuery(['tab' => 'pusat', 'print' => 'all']) }}" target="_blank" class="btn btn-sm btn-outline-primary no-print">
                            <i class="fas fa-print"></i> Cetak Semua
                        </a>
                        <button class="btn btn-sm btn-success fw-bold no-print" onclick="window.location.href='{{ request()->fullUrlWithQuery(['export' => 'excel', 'tab' => 'pusat']) }}'">
                            <i class="fas fa-file-excel"></i> Export Excel
                        </button>
                        <button class="btn btn-sm btn-outline-primary no-print" onclick="window.print()">
                            <i class="fas fa-print"></i> Cetak Halaman
                        </button>
                    </div>
                </div>

                <form action="{{ route('laporan.data') }}" method="GET" class="row g-2 mb-4 bg-white p-3 shadow-sm" style="border-radius: 12px;">
                    <input type="hidden" name="tab" value="pusat">
                    <div class="col-md-4">
                        <label class="small fw-bold text-muted mb-1">Dari Tanggal</label>
                        <input type="date" name="start_date" class="form-control form-control-sm" value="{{ $startDate }}">
                    </div>
                    <div class="col-md-2">
                        <label class="small fw-bold text-muted mb-1">Sampai Tanggal</label>
                        <input type="date" name="end_date" class="form-control form-control-sm" value="{{ $endDate }}">
                    </div>
                    <div class="col-md-3">
                        <label class="small fw-bold text-muted mb-1">Cari POS / Lokasi</label>
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="Contoh: Krad" value="{{ $search }}">
                    </div>
                    <div class="col-md-2">
                        <label class="small fw-bold text-muted mb-1">Tampilkan</label>
                        <select name="per_page" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="5" {{ $perPage == 5 ? 'selected' : '' }}>5 baris</option>
                            <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10 baris</option>
                            <option value="15" {{ $perPage == 15 ? 'selected' : '' }}>15 baris</option>
                            <option value="20" {{ $perPage == 20 ? 'selected' : '' }}>20 baris</option>
                            <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25 baris</option>
                            <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50 baris</option>
                            <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100 baris</option>
                        </select>
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold">Filter</button>
                    </div>
                </form>

                <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th class="py-3 px-4 print-header-white" style="background: #0d6efd; color: white; font-weight: bold; text-align: left; border: none;">TANGGAL</th>
                                    <th class="py-3 text-center print-header-white" style="background: #0d6efd; color: white; font-weight: bold; border: none;">LITER PAGI</th>
                                    <th class="py-3 text-center print-header-white" style="background: #0d6efd; color: white; font-weight: bold; border: none;">LITER SORE</th>
                                    <th class="py-3 px-4 print-header-white" style="background: #0d6efd; color: white; font-weight: bold; text-align: right; border: none;">GRAND TOTAL (L)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Removed manual total calculation to allow pagination totals -->
                                @forelse($data['pusat'] as $row)
                                <tr style="page-break-inside: avoid;">
                                    <td class="py-3 fw-bold px-4" style="text-align: left;">{{ \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') }}</td>
                                    <td class="py-3 text-center text-primary fw-bold">{{ $row->pagi > 0 ? number_format($row->pagi, 1, ',', '.') : '-' }}</td>
                                    <td class="py-3 text-center text-primary fw-bold">{{ $row->sore > 0 ? number_format($row->sore, 1, ',', '.') : '-' }}</td>
                                    <td class="py-3 fw-bold text-primary px-4" style="text-align: right;">{{ number_format($row->total, 1, ',', '.') }} L</td>
                                </tr>
                                <!-- Summing total moved to controller -->
                                @empty
                                <tr><td colspan="5" class="py-5 text-muted text-center">Data tidak ditemukan untuk periode ini.</td></tr>
                                @endforelse
                                @if(isset($data['pusat']) && count($data['pusat']) > 0)
                                <tr class="bg-light fw-bold" style="page-break-inside: avoid; border-top: 2px solid #000;">
                                    <td colspan="3" class="text-end py-3 px-4">TOTAL KESELURUHAN (HALAMAN INI)</td>
                                    <td class="px-4" style="font-size: 1.1rem; text-align: right;">{{ number_format($data['pusat']->sum('total'), 1, ',', '.') }} L</td>
                                </tr>
                                @endif
                            </tbody>
                            <tfoot class="fw-bold">
                                <tr>
                                    <td colspan="3" class="text-end py-3 px-4">GRAND TOTAL</td>
                                    <td class="px-4" style="font-size: 1.2rem; text-align: right;">{{ number_format($gtPusat, 1, ',', '.') }} L</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <div class="signature-section" style="display: none; justify-content: space-between; margin-top: 50px; padding: 0 50px; page-break-inside: avoid;">
                    <div style="text-align: center; width: 250px;">
                        <p class="mb-5">Mengetahui,</p>
                        <div style="margin-top: 70px;">
                            <p class="fw-bold mb-0">( ____________________ )</p>
                            <p class="small text-muted mt-1">Tim Verifikasi</p>
                        </div>
                    </div>
                    <div style="text-align: center; width: 250px;">
                        <p class="mb-0">.........., .......... 20....</p>
                        <p class="mb-5">Pengelola SIPERAH,</p>
                        <div style="margin-top: 70px;">
                            <p class="fw-bold mb-0">( ____________________ )</p>
                            <p class="small text-muted mt-1">Nama Pengelola</p>
                        </div>
                    </div>
                </div>

                <!-- Pagination for Pusat -->
                @if(!$isPrinting && $data['pusat'] instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <div class="mt-4 no-print d-flex justify-content-between align-items-center mb-4 px-3">
                    <div class="small text-muted">Menampilkan {{ $data['pusat']->firstItem() ?? 0 }} - {{ $data['pusat']->lastItem() ?? 0 }} dari {{ $data['pusat']->total() }} data</div>
                    <div>{{ $data['pusat']->appends(['tab' => 'pusat'])->links() }}</div>
                </div>
                @endif

                <!-- RINGKASAN AKHIR (Final Calculation) -->
                <div class="mt-5 p-4 bg-white border shadow-sm no-print" style="border-radius: 12px; border: 2px solid var(--primary) !important;">
                    <h6 class="fw-bold mb-3 text-primary"><i class="fas fa-calculator"></i> RINGKASAN AKHIR (PER POS)</h6>
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <table class="table table-sm table-bordered mb-0">
                                <thead>
                                    <tr class="bg-light">
                                        <th>KATEGORI POS</th>
                                        <th class="text-end">TOTAL VOLUME (Liter)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Total POS KRAD (Peternak Lokal)</td>
                                        <td class="text-end fw-bold">{{ number_format($tkPusat, 1, ',', '.') }} L</td>
                                    </tr>
                                    <tr>
                                        <td>Total POS TR & P (Sub-Penampung)</td>
                                        <td class="text-end fw-bold">{{ number_format($ttPusat, 1, ',', '.') }} L</td>
                                    </tr>
                                </tbody>
                                <tfoot class="fw-bold">
                                    <tr>
                                        <th class="py-2">GRAND TOTAL KESELURUHAN</th>
                                        <th class="text-end py-2" style="font-size: 1.1rem;">{{ number_format($gtPusat, 1, ',', '.') }} L</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div class="col-md-4 text-center mt-3 mt-md-0">
                            <p class="small text-muted mb-4">Diverifikasi pada: <br><strong>{{ now()->format('d/m/Y H:i') }}</strong></p>
                            <div style="border-top: 1px solid #ddd; width: 150px; margin: 40px auto 0;"></div>
                            <p class="small fw-bold">Admin SIPERAH</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SEGMENT 2: LAPORAN SUB-PENAMPUNG -->
            <div class="tab-pane fade @if($tab == 'sub_penampung') show active @endif" id="sub_penampung" role="tabpanel">
                <!-- Print Only Header -->
                <div class="report-header d-none">
                    <h2 class="text-primary d-print-none" style="font-weight: 800; font-size: 24pt;">REKAPITULASI SETORAN SUSU</h2>
                    <p style="font-size: 12pt; margin-top: 5px;" class="d-print-none">Periode: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</p>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold mb-0">REKAPITULASI SUB-PENAMPUNG</h5>
                    <div class="d-flex gap-2">
                        @if($isPrinting)
                        <button class="btn btn-sm btn-danger no-print" onclick="window.close()">
                            <i class="fas fa-times"></i> Tutup Halaman
                        </button>
                        @endif
                        <button class="btn btn-sm btn-success fw-bold no-print" onclick="window.location.href='{{ request()->fullUrlWithQuery(['export' => 'excel', 'tab' => 'sub_penampung']) }}'">
                            <i class="fas fa-file-excel"></i> Export Excel
                        </button>
                        <a href="{{ request()->fullUrlWithQuery(['tab' => 'sub_penampung', 'print' => 'all']) }}" target="_blank" class="btn btn-sm btn-outline-primary no-print">
                            <i class="fas fa-print"></i> Cetak Semua
                        </a>
                        <button class="btn btn-sm btn-outline-primary no-print" onclick="window.print()">
                            <i class="fas fa-print"></i> Cetak Halaman
                        </button>
                    </div>
                </div>

                <form action="{{ route('laporan.data') }}" method="GET" class="row g-2 mb-4 bg-white p-3 shadow-sm" style="border-radius: 12px;">
                    <input type="hidden" name="tab" value="sub_penampung">
                    <div class="col-md-4">
                        <label class="small fw-bold text-muted mb-1">Dari Tanggal</label>
                        <input type="date" name="start_date" class="form-control form-control-sm" value="{{ $startDate }}">
                    </div>
                    <div class="col-md-2">
                        <label class="small fw-bold text-muted mb-1">Sampai Tanggal</label>
                        <input type="date" name="end_date" class="form-control form-control-sm" value="{{ $endDate }}">
                    </div>
                    <div class="col-md-3">
                        <label class="small fw-bold text-muted mb-1">Cari Nama / ID Mitra</label>
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="Contoh: Budi" value="{{ $search }}">
                    </div>
                    <div class="col-md-2">
                        <label class="small fw-bold text-muted mb-1">Kategori</label>
                        <select name="status_mitra" class="form-select form-select-sm">
                            <option value="">-- Semua --</option>
                            <option value="sub_penampung_tr" {{ request('status_mitra') == 'sub_penampung_tr' ? 'selected' : '' }}>Sub-TR</option>
                            <option value="sub_penampung_p" {{ request('status_mitra') == 'sub_penampung_p' ? 'selected' : '' }}>Sub-P</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="small fw-bold text-muted mb-1">Tampilkan</label>
                        <select name="per_page" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="5" {{ $perPage == 5 ? 'selected' : '' }}>5 kartu</option>
                            <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10 kartu</option>
                            <option value="15" {{ $perPage == 15 ? 'selected' : '' }}>15 kartu</option>
                            <option value="20" {{ $perPage == 20 ? 'selected' : '' }}>20 kartu</option>
                            <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25 kartu</option>
                            <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50 kartu</option>
                        </select>
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="submit" class="btn btn-success btn-sm w-100 fw-bold">Filter</button>
                    </div>
                </form>

                <div class="row no-print" style="max-height: 800px; overflow-y: auto;">
                    @forelse($data['sub_penampung'] as $row)
                    <div class="col-md-4 mb-4">
                        <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                        <i class="fas fa-user-tie fa-lg"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-0 text-truncate" style="max-width: 150px;">{{ $row->peternak->nama_peternak }}</h6>
                                        <small class="text-muted">{{ $row->peternak->no_peternak }}</small>
                                    </div>
                                </div>
                                <div class="row text-center mt-4">
                                    <div class="col-6 border-end">
                                        <p class="small text-muted mb-1">Pagi</p>
                                        <h6 class="fw-bold mb-0">{{ number_format($row->pagi, 1, ',', '.') }} L</h6>
                                    </div>
                                    <div class="col-6">
                                        <p class="small text-muted mb-1">Sore</p>
                                        <h6 class="fw-bold mb-0">{{ number_format($row->sore, 1, ',', '.') }} L</h6>
                                    </div>
                                </div>
                                <div class="mt-4 pt-3 border-top text-center">
                                    <p class="small text-muted mb-1">Total Produksi</p>
                                    <h4 class="fw-bold text-success mb-0">{{ number_format($row->total, 1, ',', '.') }} L</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-12 text-center py-5">
                        <p class="text-muted">Tidak ada data sub-penampung untuk periode ini.</p>
                    </div>
                    @endforelse
                </div>

                @if(!$isPrinting && $data['sub_penampung'] instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <div class="mt-4 no-print d-flex justify-content-between align-items-center">
                    <div class="small text-muted">Menampilkan {{ $data['sub_penampung']->firstItem() ?? 0 }} - {{ $data['sub_penampung']->lastItem() ?? 0 }} dari {{ $data['sub_penampung']->total() }} data</div>
                    <div>{{ $data['sub_penampung']->appends(['tab' => 'sub_penampung'])->links() }}</div>
                </div>
                @endif

                <!-- Printable Summary for Sub-Penampung -->
                <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th class="py-3 px-4 print-header-white" style="font-weight: bold; text-align: left; border: none;">TANGGAL</th>
                                    <th class="py-3 px-4 print-header-white" style="font-weight: bold; text-align: left; border: none;">NAMA</th>
                                    <th class="py-3 text-center print-header-white" style="font-weight: bold; border: none;">LITER PAGI</th>
                                    <th class="py-3 text-center print-header-white" style="font-weight: bold; border: none;">LITER SORE</th>
                                    <th class="py-3 px-4 print-header-white" style="font-weight: bold; text-align: right; border: none;">GRAND TOTAL (L)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data['sub_penampung'] as $row)
                                <tr style="page-break-inside: avoid;">
                                    <td class="py-3 px-4" style="text-align: left;">{{ \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') }}</td>
                                    <td class="py-3 px-4 fw-bold" style="text-align: left;">{{ $row->peternak->nama_peternak }}</td>
                                    <td class="py-3 text-center">{{ number_format($row->pagi, 1, ',', '.') }}</td>
                                    <td class="py-3 text-center">{{ number_format($row->sore, 1, ',', '.') }}</td>
                                    <td class="py-3 fw-bold text-primary px-4" style="text-align: right;">{{ number_format($row->total, 1, ',', '.') }} L</td>
                                </tr>
                                @empty
                                <tr><td colspan="7" class="py-5 text-muted text-center">Data tidak ditemukan untuk periode ini.</td></tr>
                                @endforelse
                                @if(isset($data['sub_penampung']) && count($data['sub_penampung']) > 0)
                                <tr class="bg-light fw-bold" style="page-break-inside: avoid; border-top: 2px solid #000;">
                                    <td colspan="4" class="text-end py-3 px-4">TOTAL KESELURUHAN (HALAMAN INI)</td>
                                    <td class="px-4" style="font-size: 1.1rem; text-align: right;">{{ number_format($data['sub_penampung']->sum('total'), 1, ',', '.') }} L</td>
                                </tr>
                                @endif
                            </tbody>
                            <tfoot class="fw-bold">
                                <tr>
                                <tr>
                                    <td colspan="4" class="text-end py-3 px-4">GRAND TOTAL</td>
                                    <td class="px-4" style="font-size: 1.2rem; text-align: right;">{{ number_format($gtSub, 1, ',', '.') }} L</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <div class="signature-section" style="display: none; justify-content: space-between; margin-top: 50px; padding: 0 50px; page-break-inside: avoid;">
                    <div style="text-align: center; width: 250px;">
                        <p class="mb-5">Mengetahui,</p>
                        <div style="margin-top: 70px;">
                            <p class="fw-bold mb-0">( ____________________ )</p>
                            <p class="small text-muted mt-1">Tim Verifikasi</p>
                        </div>
                    </div>
                    <div style="text-align: center; width: 250px;">
                        <p class="mb-0">.........., .......... 20....</p>
                        <p class="mb-5">Pengelola SIPERAH,</p>
                        <div style="margin-top: 70px;">
                            <p class="fw-bold mb-0">( ____________________ )</p>
                            <p class="small text-muted mt-1">Nama Pengelola</p>
                        </div>
                    </div>
                </div>
                <!-- Summary Per POS for Sub-Penampung -->
                <div class="mt-5 p-4 bg-white border shadow-sm no-print" style="border-radius: 12px; border: 2px solid #16a34a !important;">
                    <h6 class="fw-bold mb-3 text-success"><i class="fas fa-calculator"></i> RINGKASAN SUB-PENAMPUNG PER POS</h6>
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <table class="table table-sm table-bordered mb-0">
                                <thead>
                                    <tr class="bg-light">
                                        <th>KATEGORI POS SUB</th>
                                        <th class="text-end">VOLUME (Liter)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Total POS TR</td>
                                        <td class="text-end fw-bold">{{ number_format($totalTR, 1, ',', '.') }} L</td>
                                    </tr>
                                    <tr>
                                        <td>Total POS P</td>
                                        <td class="text-end fw-bold">{{ number_format($totalP, 1, ',', '.') }} L</td>
                                    </tr>
                                    @if($totalSubLain > 0)
                                    <tr>
                                        <td>Sub-Penampung Lainnya</td>
                                        <td class="text-end fw-bold">{{ number_format($totalSubLain, 1, ',', '.') }} L</td>
                                    </tr>
                                    @endif
                                </tbody>
                                <tfoot class="fw-bold">
                                    <tr>
                                        <th class="py-2">TOTAL SUB-PENAMPUNG</th>
                                        <th class="text-end py-2" style="font-size: 1.1rem;">{{ number_format($totalTR + $totalP + $totalSubLain, 1, ',', '.') }} L</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div class="col-md-6 text-center mt-3 mt-md-0">
                            <p class="small text-muted mb-4">Diverifikasi pada: <br><strong>{{ now()->format('d/m/Y H:i') }}</strong></p>
                            <div style="border-top: 1px solid #ddd; width: 150px; margin: 40px auto 0;"></div>
                            <p class="small fw-bold text-success">Admin SIPERAH</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SEGMENT 3: LAPORAN HARIAN -->
            <div class="tab-pane fade @if($tab == 'harian') show active @endif" id="harian" role="tabpanel">
                <!-- Print Only Header -->
                <div class="report-header d-none">
                    <h2 class="text-primary d-print-none" style="font-weight: 800; font-size: 24pt;">REKAPITULASI SETORAN SUSU</h2>
                    <p style="font-size: 12pt; margin-top: 5px;" class="d-print-none">Tanggal: {{ \Carbon\Carbon::parse($tanggal)->format('d/m/Y') }}</p>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold mb-0">Monitoring Real-time Setoran & Potongan</h5>
                    <div class="d-flex gap-2">
                        @if($isPrinting)
                        <button class="btn btn-sm btn-danger no-print" onclick="window.close()">
                            <i class="fas fa-times"></i> Tutup Halaman
                        </button>
                        @endif
                        <a href="{{ request()->fullUrlWithQuery(['tab' => 'harian', 'print' => 'all']) }}" target="_blank" class="btn btn-sm btn-outline-primary no-print">
                            <i class="fas fa-print"></i> Cetak Semua
                        </a>
                        <button class="btn btn-sm btn-success fw-bold no-print" onclick="window.location.href='{{ request()->fullUrlWithQuery(['export' => 'excel', 'tab' => 'pusat']) }}'">
                            <i class="fas fa-file-excel"></i> Export Excel
                        </button>
                        <button class="btn btn-sm btn-outline-primary no-print" onclick="window.print()">
                            <i class="fas fa-print"></i> Cetak Halaman
                        </button>
                        <button class="btn btn-sm btn-success fw-bold no-print" onclick="window.location.href='{{ request()->fullUrlWithQuery(['export' => 'excel', 'tab' => 'harian']) }}'">
                            <i class="fas fa-file-excel"></i> Export Excel
                        </button>
                        <button class="btn btn-sm btn-outline-info no-print" onclick="showRekapModal()">
                            <i class="fas fa-calendar-alt"></i> Rekap Bulanan
                        </button>
                    </div>
                </div>

                <form action="{{ route('laporan.data') }}" method="GET" class="row g-2 mb-4 bg-white p-3 shadow-sm" style="border-radius: 12px;">
                    <input type="hidden" name="tab" value="harian">
                    <div class="col-md-3">
                        <label class="small fw-bold text-muted mb-1">Pilih Tanggal</label>
                        <input type="date" name="tanggal" class="form-control form-control-sm" value="{{ $tanggal }}">
                    </div>
                    <div class="col-md-3">
                        <label class="small fw-bold text-muted mb-1">Kategori Mitra</label>
                        <select name="status_mitra" class="form-select form-select-sm">
                            <option value="">-- Semua Kategori --</option>
                            <option value="peternak" {{ request('status_mitra') == 'peternak' ? 'selected' : '' }}>Peternak Lokal</option>
                            <option value="sub_penampung_tr" {{ request('status_mitra') == 'sub_penampung_tr' ? 'selected' : '' }}>Sub-Penampung TR</option>
                            <option value="sub_penampung_p" {{ request('status_mitra') == 'sub_penampung_p' ? 'selected' : '' }}>Sub-Penampung P</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="small fw-bold text-muted mb-1">Cari Nama / ID</label>
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="Contoh: Heri" value="{{ $search }}">
                    </div>
                    <div class="col-md-1">
                        <label class="small fw-bold text-muted mb-1">Baris</label>
                        <select name="per_page" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                            <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold">Filter</button>
                    </div>
                </form>

                <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th class="py-3 px-4 print-header-white" style="font-weight: bold; text-align: left; border: none;">TANGGAL</th>
                                    <th class="py-3 px-4 print-header-white" style="font-weight: bold; text-align: left; border: none;">NAMA PETERNAK</th>
                                    <th class="py-3 text-center print-header-white" style="font-weight: bold; border: none;">KATEGORI</th>
                                    <th class="py-3 text-center print-header-white" style="font-weight: bold; border: none;">VOLUME (L)</th>
                                    <th class="py-3 text-center print-header-white" style="font-weight: bold; border: none;">POTONGAN</th>
                                    <th class="py-3 px-4 print-header-white" style="font-weight: bold; text-align: right; border: none;">TOTAL RUPIAH</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $pageTotalRupiah = 0; @endphp
                                @forelse($peternaks as $p)
                                @php 
                                    $prodGroup = $produksis->get($p->idpeternak, collect());
                                    $totalLiters = $prodGroup->sum('jumlah_susu_liter');
                                    
                                    // Calculate financials
                                    $grossIncome = $totalLiters * $currentPrice;
                                    
                                    // Get kasbon/potongan for this specific date
                                    $dailyKasbon = $kasbons->get($p->idpeternak, collect())->sum('total_rupiah');
                                    
                                    $netIncome = $grossIncome - $dailyKasbon;
                                    $pageTotalRupiah += $netIncome;
                                @endphp
                                <tr style="page-break-inside: avoid;">
                                    <td class="py-3 px-4" style="text-align: left;">{{ \Carbon\Carbon::parse($tanggal)->format('d/m/Y') }}</td>
                                    <td class="py-3 px-4 fw-bold" style="text-align: left;">{{ $p->nama_peternak }}</td>
                                    <td class="py-3 text-center">
                                        <span class="badge bg-light text-dark border">
                                            @php
                                                $kategori = [
                                                    'peternak' => 'Peternak',
                                                    'sub_penampung_tr' => 'Sub-TR',
                                                    'sub_penampung_p' => 'Sub-P',
                                                    'sub_penampung' => 'Sub-Penampung'
                                                ];
                                            @endphp
                                            {{ $kategori[$p->status_mitra] ?? ucfirst($p->status_mitra) }}
                                        </span>
                                    </td>
                                    <td class="py-3 text-center fw-bold">{{ number_format($totalLiters, 1, ',', '.') }} L</td>
                                    <td class="py-3 text-center text-danger">
                                        @if($dailyKasbon > 0)
                                            - Rp {{ number_format($dailyKasbon, 0, ',', '.') }}
                                        @else
                                            <span class="text-muted small">Tidak</span>
                                        @endif
                                    </td>
                                    <td class="py-3 fw-bold px-4 text-success" style="text-align: right;">Rp {{ number_format($netIncome, 0, ',', '.') }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="6" class="py-5 text-muted text-center">Data peternak tidak ditemukan.</td></tr>
                                @endforelse
                                @if(isset($peternaks) && count($peternaks) > 0)
                                <tr class="bg-light fw-bold" style="page-break-inside: avoid; border-top: 2px solid #000;">
                                    <td colspan="5" class="text-end py-3 px-4">TOTAL (HALAMAN INI)</td>
                                    <td class="px-4" style="font-size: 1.1rem; text-align: right; color: #0d6efd !important;">Rp {{ number_format($pageTotalRupiah, 0, ',', '.') }}</td>
                                </tr>
                                @endif
                            </tbody>
                            <tfoot class="fw-bold">
                                <tr>
                                    <td colspan="5" class="text-end py-3 px-4">GRAND TOTAL</td>
                                    <td class="px-4" style="font-size: 1.2rem; text-align: right;">Rp {{ number_format($gtHarRp, 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <div class="signature-section" style="display: none; justify-content: space-between; margin-top: 50px; padding: 0 50px; page-break-inside: avoid;">
                    <div style="text-align: center; width: 250px;">
                        <p class="mb-5">Mengetahui,</p>
                        <div style="margin-top: 70px;">
                            <p class="fw-bold mb-0">( ____________________ )</p>
                            <p class="small text-muted mt-1">Tim Verifikasi</p>
                        </div>
                    </div>
                    <div style="text-align: center; width: 250px;">
                        <p class="mb-0">.........., .......... 20....</p>
                        <p class="mb-5">Pengelola SIPERAH,</p>
                        <div style="margin-top: 70px;">
                            <p class="fw-bold mb-0">( ____________________ )</p>
                            <p class="small text-muted mt-1">Nama Pengelola</p>
                        </div>
                    </div>
                </div>

                @if(!$isPrinting && $peternaks instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <div class="mt-4 no-print d-flex justify-content-between align-items-center mb-4 px-3 border-top pt-3">
                    <div class="small text-muted">Menampilkan {{ $peternaks->firstItem() ?? 0 }} - {{ $peternaks->lastItem() ?? 0 }} dari {{ $peternaks->total() }} data</div>
                    <div>{{ $peternaks->appends(['tab' => 'harian'])->links() }}</div>
                </div>
                @endif

                <!-- Signature Block for Harian Print -->
                <div class="d-none d-print-block mt-4 text-center">
                    <div class="row">
                        <div class="col-8"></div>
                        <div class="col-4">
                            <p>Dicetak pada: {{ now()->format('d/m/Y H:i') }}</p>
                            <br><br><br>
                            <p><strong>( ____________________ )</strong><br>Admin SIPERAH</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Copy Modal Rekap Bulanan from previous turn -->
<div id="rekapModal" style="display:none; position:fixed; z-index:2001; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.5); overflow-y: auto;">
    <div class="card shadow-lg" style="max-width:900px; margin: 30px auto; padding: 0; border-radius: 12px; overflow: hidden;">
        <div style="background: var(--primary); color: white; padding: 1.5rem; display: flex; justify-content: space-between; align-items: center;" class="no-print">
            <h2 class="h5 mb-0" style="color: white;"><i class="fas fa-calendar-alt"></i> Rekap Bulanan Susu</h2>
            <button onclick="hideRekapModal()" style="background: none; border: none; color: white; font-size: 1.5rem; cursor: pointer;">&times;</button>
        </div>
        <div style="padding: 2rem;">
            <div class="no-print mb-4 p-3 bg-light" style="border-radius: 8px;">
                <form action="{{ route('laporan.data') }}" method="GET" class="row g-2 align-items-end">
                    <input type="hidden" name="tab" value="harian">
                    <input type="hidden" name="tanggal" value="{{ $tanggal }}">
                    <div class="col-md-5">
                        <label class="small fw-bold text-muted mb-1">Bulan</label>
                        <select name="bulan" class="form-select form-select-sm">
                            @php $mnans = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']; @endphp
                            @for($i=1; $i<=12; $i++) <option value="{{ $i }}" {{ $bulan == $i ? 'selected' : '' }}>{{ $mnans[$i-1] }}</option> @endfor
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="small fw-bold text-muted mb-1">Tahun</label>
                        <select name="tahun" class="form-select form-select-sm">
                            @for($i=now()->year; $i>=2024; $i--) <option value="{{ $i }}" {{ $tahun == $i ? 'selected' : '' }}>{{ $i }}</option> @endfor
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary btn-sm w-100">Tampilkan</button>
                    </div>
                </form>
            </div>
            <div id="printableRekap">
                <div class="text-center mb-4">
                    <h4 class="fw-bold mb-0">PENGIRIMAN SUSU BULANAN</h4>
                    <p class="text-muted">{{ $mnans[$bulan-1] }} {{ $tahun }}</p>
                </div>
                <table class="table table-bordered text-center" style="border: 2px solid #000;">
                    <thead class="bg-light">
                        <tr>
                            <th style="border: 2px solid #000;">Tgl</th><th style="border: 2px solid #000;">Liter</th>
                            <th style="border: 2px solid #000;">Tgl</th><th style="border: 2px solid #000;">Liter</th>
                            <th style="border: 2px solid #000;">Tgl</th><th style="border: 2px solid #000;">Liter</th>
                        </tr>
                    </thead>
                    <tbody>
                        @for($i = 1; $i <= 11; $i++)
                        <tr>
                            @foreach([$i, $i+11, $i+22] as $day)
                                @if($day <= $daysInMonth)
                                    <td style="border: 2px solid #000; font-weight: bold;">{{ $day }}</td>
                                    <td style="border: 2px solid #000;">{{ isset($dailyTotals[$day]) ? number_format($dailyTotals[$day], 1, ',', '.') : '-' }}</td>
                                @else
                                    <td style="border: 2px solid #000; background: #eee;"></td><td style="border: 2px solid #000; background: #eee;"></td>
                                @endif
                            @endforeach
                        </tr>
                        @endfor
                    </tbody>
                </table>
                <div class="mt-4 p-2 border fw-bold" style="border: 2px solid #000 !important; width: 220px;">
                    TOTAL: {{ number_format($monthlyTotal, 1, ',', '.') }} Ltr
                </div>
            </div>
            <div class="text-center mt-4 no-print">
                <button class="btn btn-primary px-5" onclick="printRekap()">Cetak Rekap Bulanan</button>
            </div>
        </div>
    </div>
</div>

<style>
    .nav-pills .nav-link { color: var(--text-muted); border-bottom: 2px solid transparent; }
    .nav-pills .nav-link.active { background: none; color: var(--primary); border-bottom-color: var(--primary); }
    .nav-pills .nav-link:hover { background: #f8fafc; }
    
    [contenteditable="true"]:hover {
        background-color: rgba(0,0,0,0.05);
        outline: 1px dashed var(--primary);
    }
    @media print {
        @page {
            size: portrait;
            margin: 1.5cm;
        }
        .no-print, .sidebar, .navbar, .footer, .btn, form, .nav-pills, .nav-tabs, .card-header .row, #rekapModal .modal-header, #rekapModal .modal-footer { display: none !important; }
        .content { padding: 0 !important; margin: 0 !important; width: 100% !important; overflow: visible !important; }
        .layout { display: block !important; min-height: 0 !important; }
        .card { border: none !important; box-shadow: none !important; padding: 0 !important; margin-bottom: 0 !important; background: transparent !important; border-radius: 0 !important; }
        .card-body { padding: 0 !important; }
        body { background: white !important; font-size: 11pt; color: black !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .table { width: 100% !important; border-collapse: collapse !important; margin-bottom: 20px !important; border: 1.5px solid #000 !important; }
        .table th, .table td { border: 1px solid #000 !important; padding: 8px !important; color: black !important; vertical-align: middle; }
        .table thead th { background-color: #f8fafc !important; color: black !important; font-weight: bold !important; text-align: center; text-transform: uppercase; font-size: 10pt; }
        .signature-section { display: flex !important; justify-content: space-between !important; margin-top: 50px !important; page-break-inside: avoid !important; }
        .signature-box { text-align: center; width: 200px; }
        tr { page-break-inside: avoid !important; }
        .badge { border: 1px solid #000 !important; color: black !important; background: transparent !important; padding: 2px 5px !important; font-size: 9pt !important; border-radius: 4px !important; }
        .text-primary, .text-success, .text-danger, .fw-bold { color: black !important; }
        
        /* Professional Report Header */
        .report-header {
            display: block !important;
            text-align: center;
            margin-bottom: 40px;
            padding-bottom: 10px;
            border-bottom: 2px solid #000;
        }
        .report-header h2 { margin: 0; font-size: 24pt; font-weight: 800; color: #0d6efd !important; -webkit-print-color-adjust: exact; }
        .report-header p { margin: 10px 0 0; font-size: 12pt; color: #333; font-weight: bold; }
        
        /* Overwrite backgrounds for print */
        /* Overwrite backgrounds for print */
        .print-header-white {
            background: white !important;
            color: black !important;
            border: 1px solid #000 !important;
            box-shadow: none !important;
        }
        
        /* Ensure all badges and text colors are black in print */
        .badge { border: 1px solid #000 !important; color: black !important; background: transparent !important; }
        .text-primary, .text-success, .text-danger, .fw-bold, td, th { color: black !important; background: white !important; }

        .report-date { display: none; }

        .tab-pane { display: none !important; }
        .tab-pane.active { display: block !important; }
    }
    }
</style>

@endsection

@section('scripts')
<script>
    const rekapModal = document.getElementById('rekapModal');
    function showRekapModal() { rekapModal.style.display = 'block'; document.body.style.overflow = 'hidden'; }
    function hideRekapModal() { rekapModal.style.display = 'none'; document.body.style.overflow = 'auto'; }
    
    function printRekap() {
        document.body.classList.add('printing-rekap');
        window.print();
        window.onafterprint = () => document.body.classList.remove('printing-rekap');
    }
    
    window.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        
        // Auto show rekap modal if params exist
        if (urlParams.has('bulan')) showRekapModal();
        
        // Auto trigger print if print=all exists
        if (urlParams.get('print') === 'all') {
            setTimeout(() => {
                window.print();
            }, 500);
        }
    });
</script>
@endsection
