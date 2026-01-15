@extends('layouts.app')

@section('title', 'Input Produksi - SIPERAH')

@section('content')
<div class="container-fluid px-2">
    <div class="row mb-3 align-items-center">
        <div class="col-md-8">
            <h3 class="fw-bold mb-0">🥛 Input Produksi Harian</h3>
            <p class="text-muted small mb-0">Catat hasil produksi susu dan biaya operasional</p>
        </div>
        <div class="col-md-4 text-md-end mt-2 mt-md-0">
            <a href="{{ auth()->user()->isPeternak() ? '/dashboard-peternak' : '/dashboard-pengelola' }}" class="btn btn-outline-secondary btn-sm px-3">
                ← Kembali
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-12">
            <!-- Import Section -->
            @if(auth()->user()->isAdmin() || auth()->user()->isPengelola())
            <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
                <div class="card-body p-3">
                    <form action="{{ route('produksi.import') }}" method="POST" enctype="multipart/form-data" class="d-flex flex-wrap align-items-center gap-3">
                        @csrf
                        <div class="d-flex flex-column flex-md-row align-items-md-center gap-2 flex-grow-1">
                            <label for="file" class="form-label small fw-bold text-muted mb-0 text-nowrap">Upload Excel Produksi:</label>
                            <input type="file" class="form-control form-control-sm" name="file" id="file" accept=".xlsx, .xls" required style="border-radius: 20px;">
                        </div>
                        
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-sm px-3 fw-bold shadow-sm" style="border-radius: 8px; white-space: nowrap;">
                                📥 Import Excel
                            </button>
                            <a href="{{ route('produksi.template') }}" class="btn btn-light btn-sm px-3 fw-bold border shadow-sm" title="Download Template" style="border-radius: 8px; white-space: nowrap; color: #64748b;">
                                📊 Download Template
                            </a>
                        </div>
                    </form>
                </div>
            </div>
            @endif

            <div class="card shadow-sm border-0" style="border-radius: 15px;">
                <div class="card-body p-3 p-md-4">
                    <form method="POST" action="{{ route('produksi.store') }}" enctype="multipart/form-data" id="produksiForm">
                        @csrf

                        <div class="row g-3">
                            <!-- Left Column: Data Produksi -->
                            <div class="col-md-7 border-end-md pe-md-4">
                                <div class="d-flex align-items-center mb-3">
                                    <h5 class="fw-bold mb-0 text-primary">📦 Data Produksi</h5>
                                </div>
                                
                                <div class="row g-2 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label x-small fw-bold" for="tanggal">Tanggal</label>
                                        <input type="date" id="tanggal" name="tanggal" class="form-control form-control-sm" required value="{{ now()->format('Y-m-d') }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label x-small fw-bold" for="waktu_setor">Waktu Setor</label>
                                        <select name="waktu_setor" id="waktu_setor" class="form-select form-select-sm" required>
                                            <option value="pagi" {{ old('waktu_setor') == 'pagi' ? 'selected' : '' }}>Pagi</option>
                                            <option value="sore" {{ old('waktu_setor') == 'sore' ? 'selected' : '' }}>Sore</option>
                                        </select>
                                    </div>
                                </div>

                                @if(isset($peternaks) && count($peternaks) > 0)
                                <div class="mb-3">
                                    <label class="form-label x-small fw-bold" for="idpeternak">Mitra (Peternak / Sub-Penampung)</label>
                                    <select name="idpeternak" id="idpeternak" class="form-select select2" required>
                                        <option value="">-- Pilih Mitra --</option>
                                        @foreach($peternaks as $p)
                                            <option value="{{ $p->idpeternak }}" data-status="{{ $p->status_mitra }}" {{ (request('idpeternak') == $p->idpeternak) ? 'selected' : '' }}>
                                                {{ $p->no_peternak ? '['.$p->no_peternak.'] ' : '' }}{{ $p->nama_peternak }} ({{ ucfirst($p->status_mitra) }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @endif

                                <div class="mb-3">
                                    <label class="form-label x-small fw-bold text-primary" for="jumlah_susu_liter">Total Liter</label>
                                    <div class="input-group">
                                        <input type="number" id="jumlah_susu_liter" name="jumlah_susu_liter" class="form-control" step="0.01" required autofocus style="font-size: 1.25rem; font-weight: 700; height: 50px;" placeholder="0.00">
                                        <span class="input-group-text bg-light px-3 fw-bold">LTR</span>
                                    </div>
                                </div>

                                <!-- Kalkulator Jurigen (Sub-Penampung) -->
                                <div id="calculator-section" style="display:none; background: #fffbeb; border: 1px solid #fef08a; padding: 12px; border-radius: 10px; margin-top: -5px;">
                                    <label class="form-label x-small fw-bold text-warning mb-1">Kalkulator Jurigen</label>
                                    <div class="input-group input-group-sm">
                                        <input type="text" id="jurigen_input" class="form-control" placeholder="10.5+15+3">
                                        <button type="button" class="btn btn-warning btn-sm" onclick="calculateJurigen()">Hitung</button>
                                    </div>
                                    <span class="x-small text-muted mt-1 d-block">Gunakan (+) untuk menjumlahkan.</span>
                                </div>
                            </div>

                            <!-- Right Column: Notes -->
                            <div class="col-md-5 ps-md-4">
                                <div class="mb-3">
                                    <label class="form-label x-small fw-bold" for="catatan">Catatan / Keterangan</label>
                                    <textarea id="catatan" name="catatan" class="form-control" rows="4" placeholder="Contoh: Susu grade A, atau catatan lainnya..."></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 text-center">
                            <button type="submit" class="btn btn-primary px-5 py-2 fw-bold shadow" style="border-radius: 10px; min-width: 200px;">
                                Simpan Data Produksi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .x-small { font-size: 0.7rem; }
    .select2-container--default .select2-selection--single {
        height: 31px !important;
        padding: 2px 5px !important;
        font-size: 0.875rem !important;
        border-radius: 12px !important;
        border: 1px solid #E5E7EB !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 25px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 31px !important;
    }
    @media (min-width: 768px) {
        .border-end-md { border-right: 1px solid #e2e8f0; }
    }
</style>
@endsection

@section('scripts')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: "-- Pilih Mitra --",
            allowClear: true
        });

        $('#idpeternak').on('change', function() {
            const status = $(this).find(':selected').data('status');
            const calcSection = $('#calculator-section');
            if (status === 'sub_penampung') {
                calcSection.show();
            } else {
                calcSection.hide();
            }
        });

        $('#idpeternak').trigger('change');
    });

    function calculateJurigen() {
        const input = document.getElementById('jurigen_input').value;
        const totalInput = document.getElementById('jumlah_susu_liter');
        
        try {
            const sanitized = input.replace(/[^0-9.+]/g, '');
            const parts = sanitized.split('+');
            let total = 0;
            parts.forEach(p => {
                if (p.trim() !== '') {
                    total += parseFloat(p);
                }
            });
            
            totalInput.value = total.toFixed(2);
        } catch (e) {
            alert('Format input tidak valid.');
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const now = new Date();
        const hour = now.getHours();
        const waktuSelect = document.getElementById('waktu_setor');
        
        if (!{{ old('waktu_setor') ? 'true' : 'false' }}) {
            if (hour >= 5 && hour < 12) {
                waktuSelect.value = 'pagi';
            } else if (hour >= 13 && hour <= 23) {
                waktuSelect.value = 'sore';
            }
        }
    });
</script>
@endsection
