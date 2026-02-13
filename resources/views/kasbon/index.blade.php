@extends('layouts.app')

@section('title', 'Manajemen Potongan - SIPERAH')

@section('content')
<div class="mb-4">
    <h1 class="fw-bold mb-0">Input Potongan / Logistik</h1>
    <p class="text-muted">Catat pengambilan pakan, vitamin, atau potongan tunai peternak.</p>
</div>

<div class="kasbon-grid" style="gap: 2rem;">
    <!-- Form Input Kasbon -->
    <div class="card" style="height: fit-content;">
        <h3 class="mb-4">Transaksi Baru</h3>
        <form action="{{ route('kasbon.store') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label class="form-label">Nama Mitra (Peternak)</label>
                <select name="idpeternak" id="idpeternak" class="form-select select2" required>
                    <option value="">-- Pilih Mitra --</option>
                    @foreach($peternaks as $p)
                        <option value="{{ $p->idpeternak }}" {{ old('idpeternak') == $p->idpeternak ? 'selected' : '' }}>{{ $p->no_peternak ? '['.$p->no_peternak.'] ' : '' }}{{ $p->nama_peternak }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Pilih Barang / Logistik</label>
                <select name="idlogistik" id="idlogistik" class="form-select select2" required onchange="updateSubtotal()">
                    <option value="">-- Pilih Barang --</option>
                    @foreach($items as $item)
                        <option value="{{ $item->id }}" data-price="{{ $item->harga_satuan }}">{{ $item->nama_barang }} (Rp {{ number_format($item->harga_satuan, 0, ',', '.') }})</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Jumlah (Qty)</label>
                <input type="number" step="0.01" name="qty" id="qty" class="form-control" required min="0.01" value="1" oninput="updateSubtotal()">
            </div>

            <div class="form-group">
                <label class="form-label">Tanggal Transaksi</label>
                <input type="date" name="tanggal" class="form-control" required value="{{ old('tanggal', date('Y-m-d')) }}">
            </div>

            <div class="p-3 mb-4 d-flex justify-content-between align-items-center" style="background: #f8fafc; border-radius: 12px; border: 1px solid var(--border);">
                <span class="fw-bold text-muted">Total Potongan</span>
                <span class="fw-bold text-primary" style="font-size: 1.25rem;" id="subtotalDisplay">Rp 0</span>
            </div>

            <button type="submit" class="btn btn-primary w-100">Catat Transaksi</button>
        </form>
    </div>

    <!-- Riwayat Potongan -->
    <div class="card">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="mb-0">Riwayat Potongan Terbaru</h3>
            <form action="{{ route('kasbon.index') }}" method="GET" class="d-flex gap-2 align-items-center flex-wrap">
                <div style="width: 250px;">
                    <select name="idpeternak" class="form-select select2" onchange="this.form.submit()">
                        <option value="">-- Semua Mitra --</option>
                        @foreach($peternaks as $p)
                            <option value="{{ $p->idpeternak }}" {{ request('idpeternak') == $p->idpeternak ? 'selected' : '' }}>
                                {{ $p->nama_peternak }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div style="width: 200px;">
                    <select name="jenis_inputan" class="form-select select2" onchange="this.form.submit()">
                        <option value="">-- Semua Jenis --</option>
                        @foreach($itemNames as $name)
                            <option value="{{ $name }}" {{ request('jenis_inputan') == $name ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <select name="per_page" class="form-select w-auto" onchange="this.form.submit()">
                    <option value="10" {{ $kasbons->perPage() == 10 ? 'selected' : '' }}>10 baris</option>
                    <option value="25" {{ $kasbons->perPage() == 25 ? 'selected' : '' }}>25 baris</option>
                    <option value="50" {{ $kasbons->perPage() == 50 ? 'selected' : '' }}>50 baris</option>
                </select>
                @if(request()->hasAny(['idpeternak', 'jenis_inputan']))
                    <a href="{{ route('kasbon.index') }}" class="btn btn-secondary btn-sm" title="Reset Filter"><i class="fas fa-times"></i></a>
                @endif
            </form>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Mitra</th>
                        <th>Barang</th>
                        <th>Total</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kasbons as $k)
                    <tr>
                        <td>{{ $k->tanggal->format('d/m/y') }}</td>
                        <td>
                            <div class="fw-bold">{{ $k->peternak->nama_peternak }}</div>
                            <div class="small text-muted">{{ $k->peternak->no_peternak }}</div>
                        </td>
                        <td>
                            <div>{{ $k->nama_item }}</div>
                            <div class="small text-muted">{{ $k->qty }} x Rp{{ number_format($k->harga_satuan, 0, ',', '.') }}</div>
                        </td>
                        <td class="fw-bold text-danger">Rp {{ number_format($k->total_rupiah, 0, ',', '.') }}</td>
                        <td class="text-center">
                            <a href="{{ route('kasbon.edit', $k->id) }}" class="action-btn edit me-1" title="Edit Data" style="background: #e0f2fe; color: #0284c7;">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('kasbon.destroy', $k->id) }}" method="POST" onsubmit="return confirm('Hapus data kasbon ini?')" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="action-btn delete" title="Hapus Data">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">Belum ada data potongan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $kasbons->links() }}
        </div>
    </div>
</div>

@endsection

@section('scripts')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<style>
    .select2-container--default .select2-selection--single {
        border-radius: 12px;
        height: 48px;
        padding-top: 10px;
        border-color: var(--border);
        background-color: #FAFAFA;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 46px;
    }

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
    
    .action-btn.delete {
        background: #fee2e2;
        color: #b91c1c;
    }
    .action-btn.delete:hover {
        background: #b91c1c;
        color: white;
    }

    .kasbon-grid {
        display: grid;
        grid-template-columns: 1fr;
    }
    @media (min-width: 992px) {
        .kasbon-grid {
            grid-template-columns: 1fr 2fr;
        }
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

        // Open select2 on focus
        $(document).on('focus', '.select2-selection.select2-selection--single', function (e) {
            $(this).closest(".select2-container").siblings('select:enabled').select2('open');
        });
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
