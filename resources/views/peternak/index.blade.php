@extends('layouts.app')

@section('title', 'Data Mitra - SIPERAH')

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col-md-6">
        <h1 class="h3 mb-0"><i class="fas fa-user-friends"></i> Data Mitra</h1>
        <p class="text-muted">Manajemen data mitra peternak dan sub-penampung anggota unit</p>
    </div>
    <div class="col-md-6 text-md-end">
        <button class="btn btn-primary" onclick="showAddModal()">
            <i class="fas fa-plus"></i> Tambah Mitra Baru
        </button>
    </div>
</div>

<div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
    <div class="card-body p-3">
        <form action="{{ route('peternak.index') }}" method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Cari nama/lokasi..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="status_mitra" class="form-select" onchange="this.form.submit()">
                    <option value="">-- Semua Kategori --</option>
                    <option value="peternak" {{ request('status_mitra') == 'peternak' ? 'selected' : '' }}>Peternak</option>
                    <option value="sub_penampung_tr" {{ request('status_mitra') == 'sub_penampung_tr' ? 'selected' : '' }}>Sub-Penampung TR</option>
                    <option value="sub_penampung_p" {{ request('status_mitra') == 'sub_penampung_p' ? 'selected' : '' }}>Sub-Penampung P</option>
                    <option value="sub_penampung" {{ request('status_mitra') == 'sub_penampung' ? 'selected' : '' }}>Sub-Penampung (Umum)</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="per_page" class="form-select" onchange="this.form.submit()">
                    <option value="5" {{ request('per_page') == 5 ? 'selected' : '' }}>5 baris</option>
                    <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10 baris</option>
                    <option value="15" {{ request('per_page') == 15 ? 'selected' : '' }}>15 baris</option>
                    <option value="20" {{ request('per_page', 20) == 20 ? 'selected' : '' }}>20 baris</option>
                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 baris</option>
                </select>
            </div>
            <div class="col-md-auto">
                <button type="submit" class="btn btn-secondary"><i class="fas fa-search"></i> Filter</button>
                <a href="{{ route('peternak.index') }}" class="btn btn-outline-secondary"><i class="fas fa-sync"></i> Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm border-0" style="border-radius: 12px;">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="px-4 py-3">No. Mitra</th>
                    <th class="py-3">Nama Mitra</th>
                    <th class="py-3">Desa / Lokasi</th>
                    <th class="py-3">Kategori</th>
                    <th class="py-3 text-center px-4">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($peternaks as $p)
                    <tr>
                        <td class="px-4 py-3 fw-bold">{{ $p->no_peternak ?? '-' }}</td>
                        <td class="py-3">
                            {{ $p->nama_peternak }}
                            <small class="d-block text-muted">{{ $p->user->email ?? '' }}</small>
                        </td>
                        <td class="py-3">{{ $p->lokasi ?? '-' }}</td>
                        <td class="py-3">
                            @php
                                $badgeClass = 'bg-success';
                                $catLabel = 'Peternak';
                                if ($p->status_mitra === 'sub_penampung_tr') {
                                    $badgeClass = 'bg-primary';
                                    $catLabel = 'Sub-Penampung TR';
                                } elseif ($p->status_mitra === 'sub_penampung_p') {
                                    $badgeClass = 'bg-info';
                                    $catLabel = 'Sub-Penampung P';
                                } elseif ($p->status_mitra === 'sub_penampung') {
                                    $badgeClass = 'bg-secondary';
                                    $catLabel = 'Sub-Penampung';
                                }
                            @endphp
                            <span class="badge {{ $badgeClass }}" style="font-weight: 500; padding: 0.5rem 0.75rem; border-radius: 6px; color: white;">
                                {{ $catLabel }}
                            </span>
                        </td>
                        <td class="py-3 px-4">
                            <div class="d-flex gap-2 justify-content-center">
                                <button class="action-btn edit" onclick="showEditModal({{ json_encode($p) }})" title="Edit Mitra">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('peternak.destroy', $p->idpeternak) }}" method="POST" onsubmit="return confirm('Hapus data mitra ini? User terkait juga akan dihapus.')" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn delete" title="Hapus Mitra">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">Belum ada data mitra.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($peternaks->hasPages())
        <div class="card-footer bg-white border-0 p-4">
            {{ $peternaks->appends(request()->query())->links() }}
        </div>
    @endif
</div>

<!-- Modal Add -->
<div id="addModal" style="display:none; position:fixed; z-index:1001; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.5); overflow-y: auto;">
    <div class="card" style="max-width:500px; margin: 30px auto; padding: 0; border-radius: 12px; overflow: hidden;">
        <div style="background: var(--primary); color: white; padding: 1.5rem; display: flex; justify-content: space-between; align-items: center;">
            <h2 class="h5 mb-0" style="color: white;">Tambah Mitra Baru</h2>
            <button onclick="hideAddModal()" style="background: none; border: none; color: white; font-size: 1.5rem; cursor: pointer;">&times;</button>
        </div>
        <div style="padding: 1.5rem;">
            <form action="{{ route('peternak.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="nama" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Email (Username)</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required minlength="8">
                </div>
                <hr>
                <div class="form-group">
                    <label class="form-label">No. Mitra (Otomatis jika kosong)</label>
                    <input type="text" name="no_peternak" class="form-control" placeholder="Contoh: MTR-001">
                </div>
                <div class="form-group">
                    <label class="form-label">Lokasi / Desa</label>
                    <input type="text" name="lokasi" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">Kelompok</label>
                    <input type="text" name="kelompok" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">Kategori Mitra</label>
                    <select name="status_mitra" class="form-select">
                        <option value="peternak">Peternak</option>
                        <option value="sub_penampung_tr">Sub-Penampung TR</option>
                        <option value="sub_penampung_p">Sub-Penampung P</option>
                        <option value="sub_penampung">Sub-Penampung (Umum)</option>
                    </select>
                </div>
                <div class="d-flex gap-2 mt-4">
                    <button type="button" class="btn btn-secondary flex-grow-1" onclick="hideAddModal()">Batal</button>
                    <button type="submit" class="btn btn-primary flex-grow-1">Simpan Mitra</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit -->
<div id="editModal" style="display:none; position:fixed; z-index:1001; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.5); overflow-y: auto;">
    <div class="card" style="max-width:500px; margin: 50px auto; padding: 0; border-radius: 12px; overflow: hidden;">
        <div style="background: var(--primary); color: white; padding: 1.5rem; display: flex; justify-content: space-between; align-items: center;">
            <h2 class="h5 mb-0" style="color: white;">Edit Data Mitra</h2>
            <button onclick="hideEditModal()" style="background: none; border: none; color: white; font-size: 1.5rem; cursor: pointer;">&times;</button>
        </div>
        <div style="padding: 1.5rem;">
            <form id="editForm" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label">Nama Mitra</label>
                    <input type="text" id="edit_nama_peternak" class="form-control" readonly style="background-color: #f8f9fa;">
                </div>
                <div class="form-group">
                    <label class="form-label">No. Mitra</label>
                    <input type="text" name="no_peternak" id="edit_no_peternak" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">Lokasi / Desa</label>
                    <input type="text" name="lokasi" id="edit_lokasi" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">Kelompok</label>
                    <input type="text" name="kelompok" id="edit_kelompok" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">Kategori Mitra</label>
                    <select name="status_mitra" id="edit_status_mitra" class="form-select">
                        <option value="peternak">Peternak</option>
                        <option value="sub_penampung_tr">Sub-Penampung TR</option>
                        <option value="sub_penampung_p">Sub-Penampung P</option>
                        <option value="sub_penampung">Sub-Penampung (Umum)</option>
                    </select>
                </div>
                <div class="d-flex gap-2 mt-4">
                    <button type="button" class="btn btn-secondary flex-grow-1" onclick="hideEditModal()">Batal</button>
                    <button type="submit" class="btn btn-primary flex-grow-1">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    const addModal = document.getElementById('addModal');
    const editModal = document.getElementById('editModal');
    const editForm = document.getElementById('editForm');

    function showAddModal() {
        addModal.style.display = 'block';
        document.body.style.overflow = 'hidden';
    }

    function hideAddModal() {
        addModal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    function showEditModal(p) {
        editForm.action = `/peternak/${p.idpeternak}/update-status`;
        document.getElementById('edit_nama_peternak').value = p.nama_peternak;
        document.getElementById('edit_no_peternak').value = p.no_peternak || '';
        document.getElementById('edit_lokasi').value = p.lokasi || '';
        document.getElementById('edit_kelompok').value = p.kelompok || '';
        document.getElementById('edit_status_mitra').value = p.status_mitra || 'peternak';
        editModal.style.display = 'block';
        document.body.style.overflow = 'hidden';
    }

    function hideEditModal() {
        editModal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    window.onclick = function(event) {
        if (event.target == addModal) hideAddModal();
        if (event.target == editModal) hideEditModal();
    }
</script>

<style>
    .badge.bg-success { background-color: #22C55E !important; }
    .badge.bg-primary { background-color: #2180D3 !important; }

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
