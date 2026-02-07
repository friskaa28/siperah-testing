@extends('layouts.app')

@section('title', 'Edit Riwayat Setor Susu - SIPERAH')

@section('content')
<div class="container-fluid px-2">
    <div class="row mb-3 align-items-center">
        <div class="col-md-8">
            <h3 class="fw-bold mb-0"><i class="fas fa-edit"></i> Edit Setor Susu</h3>
            <p class="text-muted small mb-0">Perbarui data setoran susu peternak</p>
        </div>
        <div class="col-md-4 text-md-end mt-2 mt-md-0">
            <a href="{{ route('produksi.index') }}" class="btn btn-outline-secondary btn-sm px-3">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

<div class="row justify-content-center">
    <div class="col-lg-12">
        <div class="card shadow-sm border-0" style="border-radius: 15px;">
            <div class="card-body p-3 p-md-4">
                <form action="{{ route('produksi.update', $produksi->idproduksi) }}" method="POST" id="editProduksiForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="redirect_to" value="{{ request('redirect_to', old('redirect_to')) }}">

                    <div class="row g-3">

                        <!-- Left Column -->
                        <div class="col-md-7 border-end-md pe-md-4">
                            <div class="d-flex align-items-center mb-3">
                                <h5 class="fw-bold mb-0 text-primary"><i class="fas fa-box"></i> Data Setor Susu</h5>
                            </div>

                            <div class="row g-2 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label x-small fw-bold">Tanggal Setor</label>
                                    <input type="date" name="tanggal" class="form-control form-control-sm @error('tanggal') is-invalid @enderror" value="{{ $produksi->tanggal->format('Y-m-d') }}" required>
                                    @error('tanggal') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label x-small fw-bold">Waktu Setor</label>
                                    <select name="waktu_setor" class="form-select form-select-sm @error('waktu_setor') is-invalid @enderror" required>
                                        <option value="pagi" {{ $produksi->waktu_setor == 'pagi' ? 'selected' : '' }}>Pagi</option>
                                        <option value="sore" {{ $produksi->waktu_setor == 'sore' ? 'selected' : '' }}>Sore</option>
                                    </select>
                                    @error('waktu_setor') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label x-small fw-bold">Mitra (Peternak / Sub-Penampung)</label>
                                <select name="idpeternak" class="form-select select2 @error('idpeternak') is-invalid @enderror" required>
                                    <option value="">-- Pilih Mitra --</option>
                                    @foreach($peternaks as $p)
                                        <option value="{{ $p->idpeternak }}" {{ $produksi->idpeternak == $p->idpeternak ? 'selected' : '' }}>
                                            {{ $p->no_peternak ? '['.$p->no_peternak.'] ' : '' }}{{ $p->nama_peternak }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('idpeternak') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label x-small fw-bold text-primary">Jumlah Susu (Liter)</label>
                                <div class="input-group">
                                    <input type="number" step="any" name="jumlah_susu_liter" class="form-control @error('jumlah_susu_liter') is-invalid @enderror" value="{{ $produksi->jumlah_susu_liter }}" required style="font-size: 1.25rem; font-weight: 700; height: 50px;">
                                    <span class="input-group-text bg-light px-3 fw-bold">LTR</span>
                                </div>
                                @error('jumlah_susu_liter') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="col-md-5 ps-md-4">
                            <div class="mb-3">
                                <label class="form-label x-small fw-bold">Catatan (Opsional)</label>
                                <textarea name="catatan" class="form-control @error('catatan') is-invalid @enderror" rows="4" placeholder="Tambahkan catatan jika diperlukan...">{{ $produksi->catatan }}</textarea>
                                @error('catatan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 text-center border-top pt-4">
                        <button type="submit" class="btn btn-primary px-5 py-2 fw-bold shadow" style="border-radius: 10px; min-width: 200px;">
                            <i class="fas fa-save me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
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
</div>
@endsection
@section('scripts')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: "-- Cari Mitra --",
            allowClear: true,
            width: '100%',
            dropdownParent: $('#editProduksiForm')
        });

        // Open select2 on focus
        $(document).on('focus', '.select2-selection.select2-selection--single', function (e) {
            $(this).closest(".select2-container").siblings('select:enabled').select2('open');
        });
    });
</script>
@endsection
