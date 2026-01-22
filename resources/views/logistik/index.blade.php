@extends('layouts.app')

@section('title', 'Katalog Logistik - SIP-SUSU')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="fw-bold mb-0">Katalog Logistik</h1>
        <p class="text-muted">Kelola daftar pakan, vitamin, dan obat-obatan.</p>
    </div>
    <div class="d-flex gap-2">
        <form action="{{ route('logistik.index') }}" method="GET" class="d-flex gap-2 align-items-center">
            <select name="per_page" class="form-select form-select-sm" style="width: 120px;" onchange="this.form.submit()">
                <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10 baris</option>
                <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25 baris</option>
                <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50 baris</option>
            </select>
        </form>
        <button class="btn btn-primary" onclick="showAddModal()">
            <i class="fas fa-plus"></i> Tambah Barang
        </button>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Nama Barang</th>
                    <th>Harga Satuan</th>
                    <th>Tanggal Update</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                <tr>
                    <td class="fw-bold">{{ $item->nama_barang }}</td>
                    <td>Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                    <td>{{ $item->updated_at->format('d M Y') }}</td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center gap-2">
                            <button class="action-btn edit" onclick="showEditModal({{ $item }})" title="Edit Barang">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="{{ route('logistik.destroy', $item->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus barang ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="action-btn delete" title="Hapus Barang">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center py-4 text-muted">Belum ada data barang.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($items->hasPages())
    <div class="card-footer bg-white border-0 p-4 d-flex justify-content-between align-items-center">
        <div class="small text-muted">Menampilkan {{ $items->firstItem() ?? 0 }} - {{ $items->lastItem() ?? 0 }} dari {{ $items->total() }} barang</div>
        <div>{{ $items->links() }}</div>
    </div>
    @endif
</div>

<!-- Modal Tambah/Edit -->
<div id="itemModal" style="display:none; position:fixed; z-index:1001; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.5);">
    <div class="card" style="max-width:500px; margin: 100px auto; padding: 2rem;">
        <h2 id="modalTitle" class="mb-4">Tambah Barang</h2>
        <form id="itemForm" method="POST" action="{{ route('logistik.store') }}">
            @csrf
            <div id="methodField"></div>
            <div class="form-group">
                <label class="form-label">Nama Barang</label>
                <input type="text" name="nama_barang" id="nama_barang" class="form-control" required placeholder="Contoh: Pakan A">
            </div>
            <div class="form-group">
                <label class="form-label">Harga Satuan (Rp)</label>
                <input type="number" name="harga_satuan" id="harga_satuan" class="form-control" required min="0">
            </div>
            <div class="d-flex gap-2 mt-4">
                <button type="button" class="btn btn-secondary flex-grow-1" onclick="hideModal()">Batal</button>
                <button type="submit" class="btn btn-primary flex-grow-1">Simpan</button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
    const modal = document.getElementById('itemModal');
    const form = document.getElementById('itemForm');
    const modalTitle = document.getElementById('modalTitle');
    const methodField = document.getElementById('methodField');

    function showAddModal() {
        modalTitle.innerText = 'Tambah Barang';
        form.action = "{{ route('logistik.store') }}";
        methodField.innerHTML = '';
        document.getElementById('nama_barang').value = '';
        document.getElementById('harga_satuan').value = '';
        modal.style.display = 'block';
    }

    function showEditModal(item) {
        modalTitle.innerText = 'Edit Barang';
        form.action = `/logistik/${item.id}`;
        methodField.innerHTML = '@method("PUT")';
        document.getElementById('nama_barang').value = item.nama_barang;
        document.getElementById('harga_satuan').value = item.harga_satuan;
        modal.style.display = 'block';
    }

    function hideModal() {
        modal.style.display = 'none';
    }

    // Close on outside click
    window.onclick = function(event) {
        if (event.target == modal) hideModal();
    }
</script>

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
    
    .action-btn.edit {
        background: #fef9c3;
        color: #a16207;
    }
    .action-btn.edit:hover {
        background: #a16207;
        color: white;
    }

    .action-btn.delete {
        background: #fee2e2;
        color: #b91c1c;
    }
    .action-btn.delete:hover {
        background: #b91c1c;
        color: white;
    }
</style>
@endsection
