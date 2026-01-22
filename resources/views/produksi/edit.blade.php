@extends('layouts.app')

@section('title', 'Edit Riwayat Setor Susu - SIPERAH')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="d-flex align-items-center mb-4">
            <a href="{{ route('produksi.index') }}" class="btn btn-outline-secondary me-3" style="border-radius: 10px;">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="h3 fw-bold mb-0">Edit Setor Susu</h1>
                <p class="text-muted mb-0">Perbarui data setoran susu peternak</p>
            </div>
        </div>

        <div class="card shadow-sm border-0" style="border-radius: 15px;">
            <div class="card-body p-4">
                <form action="{{ route('produksi.update', $produksi->idproduksi) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Pilih Peternak</label>
                            <select name="idpeternak" class="form-select @error('idpeternak') is-invalid @enderror" required>
                                <option value="">-- Pilih Peternak --</option>
                                @foreach($peternaks as $p)
                                    <option value="{{ $p->idpeternak }}" {{ $produksi->idpeternak == $p->idpeternak ? 'selected' : '' }}>
                                        [{{ $p->no_peternak }}] {{ $p->nama_peternak }}
                                    </option>
                                @endforeach
                            </select>
                            @error('idpeternak')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Tanggal Setor</label>
                            <input type="date" name="tanggal" class="form-control @error('tanggal') is-invalid @enderror" value="{{ $produksi->tanggal->format('Y-m-d') }}" required>
                            @error('tanggal')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Waktu Setor</label>
                            <select name="waktu_setor" class="form-select @error('waktu_setor') is-invalid @enderror" required>
                                <option value="pagi" {{ $produksi->waktu_setor == 'pagi' ? 'selected' : '' }}>Pagi</option>
                                <option value="sore" {{ $produksi->waktu_setor == 'sore' ? 'selected' : '' }}>Sore</option>
                            </select>
                            @error('waktu_setor')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold">Jumlah Susu (Liter)</label>
                            <div class="input-group">
                                <input type="number" step="0.1" name="jumlah_susu_liter" class="form-control @error('jumlah_susu_liter') is-invalid @enderror" value="{{ $produksi->jumlah_susu_liter }}" required>
                                <span class="input-group-text">LITER</span>
                            </div>
                            @error('jumlah_susu_liter')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold">Catatan (Opsional)</label>
                            <textarea name="catatan" class="form-control @error('catatan') is-invalid @enderror" rows="3" placeholder="Tambahkan catatan jika diperlukan...">{{ $produksi->catatan }}</textarea>
                            @error('catatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 mt-4 pt-3 border-top">
                            <button type="submit" class="btn btn-primary w-100 py-2 fw-bold" style="border-radius: 10px;">
                                <i class="fas fa-save me-1"></i> Simpan Perubahan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
