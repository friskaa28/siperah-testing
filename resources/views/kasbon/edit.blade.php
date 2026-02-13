@extends('layouts.app')

@section('title', 'Edit Potongan - SIPERAH')

@section('content')
<div class="mb-4">
    <a href="{{ route('kasbon.index') }}" class="text-decoration-none text-muted"><i class="fas fa-arrow-left"></i> Kembali ke Riwayat</a>
    <h1 class="fw-bold mb-0 mt-2">Edit Transaksi Potongan</h1>
</div>

<div class="card" style="max-width: 600px;">
    <div class="card-body">
        <form action="{{ route('kasbon.update', $kasbon->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="form-group mb-3">
                <label class="form-label">Nama Mitra (Peternak)</label>
                <select name="idpeternak" id="idpeternak" class="form-select select2" required>
                    <option value="">-- Pilih Mitra --</option>
                    @foreach($peternaks as $p)
                        <option value="{{ $p->idpeternak }}" {{ (old('idpeternak', $kasbon->idpeternak) == $p->idpeternak) ? 'selected' : '' }}>
                            {{ $p->no_peternak ? '['.$p->no_peternak.'] ' : '' }}{{ $p->nama_peternak }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group mb-3">
                <label class="form-label">Pilih Barang / Logistik</label>
                <select name="idlogistik" id="idlogistik" class="form-select select2" required onchange="updateSubtotal()">
                    <option value="">-- Pilih Barang --</option>
                    @foreach($items as $item)
                        <option value="{{ $item->id }}" data-price="{{ $item->harga_satuan }}" 
                            {{ (old('idlogistik', $kasbon->idlogistik) == $item->id) ? 'selected' : '' }}>
                            {{ $item->nama_barang }} (Rp {{ number_format($item->harga_satuan, 0, ',', '.') }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group mb-3">
                <label class="form-label">Jumlah (Qty)</label>
                <input type="number" step="0.01" name="qty" id="qty" class="form-control" required min="0.01" 
                    value="{{ old('qty', $kasbon->qty) }}" oninput="updateSubtotal()">
            </div>

            <div class="form-group mb-4">
                <label class="form-label">Tanggal Transaksi</label>
                <input type="date" name="tanggal" class="form-control" required 
                    value="{{ old('tanggal', $kasbon->tanggal->format('Y-m-d')) }}">
            </div>

            <div class="p-3 mb-4 d-flex justify-content-between align-items-center" style="background: #f8fafc; border-radius: 12px; border: 1px solid var(--border);">
                <span class="fw-bold text-muted">Total Potongan</span>
                <span class="fw-bold text-primary" style="font-size: 1.25rem;" id="subtotalDisplay">
                    Rp {{ number_format($kasbon->total_rupiah, 0, ',', '.') }}
                </span>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1">Simpan Perubahan</button>
                <a href="{{ route('kasbon.index') }}" class="btn btn-light" style="border: 1px solid #ddd;">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<style>
    .select2-container--default .select2-selection--single {
        border-radius: 8px;
        height: 48px;
        padding-top: 10px;
        border-color: #ced4da;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 46px;
    }
</style>

<script>
    $(document).ready(function() {
        $('.select2').each(function() {
            $(this).select2({
                placeholder: $(this).find('option:first').text(),
                allowClear: true,
                width: '100%'
            });
        });
        
        updateSubtotal();
    });

    function updateSubtotal() {
        const select = document.getElementById('idlogistik');
        const qty = document.getElementById('qty').value;
        const display = document.getElementById('subtotalDisplay');
        
        const option = select.options[select.selectedIndex];
        const price = option.dataset.price || 0;
        
        const total = price * qty;
        display.innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(total);
    }
</script>
@endsection
