@extends('layouts.app')

@section('title', 'Kelola Pengguna - SIPERAH')

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col-md-6">
        <h1 class="h3 mb-0"><i class="fas fa-users-cog"></i> Kelola Pengguna</h1>
        <p class="text-muted">Manajemen akses akun Admin, Pengelola, dan Peternak</p>
    </div>
    <div class="col-md-6 text-md-end">
        <button class="btn btn-primary" onclick="showAddModal()">
            <i class="fas fa-plus"></i> Tambah Pengguna
        </button>
    </div>
</div>

<div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
    <div class="card-body p-3">
        <form action="{{ route('users.index') }}" method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Cari nama atau email..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="role" class="form-select" onchange="this.form.submit()">
                    <option value="">-- Semua Role --</option>
                    <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="pengelola" {{ request('role') == 'pengelola' ? 'selected' : '' }}>Pengelola</option>
                    <option value="peternak" {{ request('role') == 'peternak' ? 'selected' : '' }}>Peternak</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="per_page" class="form-select" onchange="this.form.submit()">
                    <option value="10" {{ $users->perPage() == 10 ? 'selected' : '' }}>10 baris</option>
                    <option value="20" {{ $users->perPage() == 20 ? 'selected' : '' }}>20 baris</option>
                    <option value="50" {{ $users->perPage() == 50 ? 'selected' : '' }}>50 baris</option>
                </select>
            </div>
            <div class="col-md-auto">
                <button type="submit" class="btn btn-secondary"><i class="fas fa-search"></i> Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm border-0" style="border-radius: 12px;">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="px-4 py-3" style="text-align: left;">Nama</th>
                    <th class="py-3" style="text-align: left;">Email</th>
                    <th class="py-3 text-center">Role</th>
                    <th class="py-3 text-end px-4">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td class="px-4 py-3 fw-bold" style="text-align: left;">{{ $user->nama }}</td>
                        <td class="py-3" style="text-align: left;">{{ $user->email }}</td>
                        <td class="py-3 text-center">
                            <span class="badge @if($user->role == 'admin') bg-danger @elseif($user->role == 'pengelola') bg-primary @else bg-success @endif" style="font-weight: 500; padding: 0.5rem 0.75rem; border-radius: 6px; color: white;">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td class="py-3 text-end px-4">
                            <div class="d-flex gap-2 justify-content-end">
                                <button class="btn btn-sm btn-outline-primary" onclick="showEditModal({{ json_encode($user) }})">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                @if($user->iduser != auth()->id())
                                <form action="{{ route('users.destroy', $user->iduser) }}" method="POST" onsubmit="return confirm('Hapus pengguna ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-5 text-muted">Data pengguna tidak ditemukan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
        <div class="card-footer bg-white border-0 p-4">
            {{ $users->appends(request()->query())->links() }}
        </div>
    @endif
</div>

<!-- Modal Add -->
<div id="addModal" style="display:none; position:fixed; z-index:1001; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.5); overflow-y: auto;">
    <div class="card shadow-lg" style="max-width:500px; margin: 50px auto; padding: 0; border-radius: 12px; overflow: hidden;">
        <div style="background: var(--primary); color: white; padding: 1.5rem; display: flex; justify-content: space-between; align-items: center;">
            <h2 class="h5 mb-0" style="color: white;">Tambah Pengguna Baru</h2>
            <button onclick="hideAddModal()" style="background: none; border: none; color: white; font-size: 1.5rem; cursor: pointer;">&times;</button>
        </div>
        <div style="padding: 1.5rem;">
            <form action="{{ route('users.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-bold">Nama Lengkap</label>
                    <input type="text" name="nama" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Email (Username)</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Role</label>
                    <select name="role" class="form-select" required>
                        <option value="admin">Admin</option>
                        <option value="pengelola">Pengelola</option>
                        <option value="peternak">Peternak</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Password</label>
                    <input type="password" name="password" class="form-control" required minlength="8">
                </div>
                <div class="d-flex gap-2 mt-4">
                    <button type="button" class="btn btn-secondary flex-grow-1" onclick="hideAddModal()">Batal</button>
                    <button type="submit" class="btn btn-primary flex-grow-1">Simpan Pengguna</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit -->
<div id="editModal" style="display:none; position:fixed; z-index:1001; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.5); overflow-y: auto;">
    <div class="card shadow-lg" style="max-width:500px; margin: 50px auto; padding: 0; border-radius: 12px; overflow: hidden;">
        <div style="background: var(--primary); color: white; padding: 1.5rem; display: flex; justify-content: space-between; align-items: center;">
            <h2 class="h5 mb-0" style="color: white;">Edit Pengguna</h2>
            <button onclick="hideEditModal()" style="background: none; border: none; color: white; font-size: 1.5rem; cursor: pointer;">&times;</button>
        </div>
        <div style="padding: 1.5rem;">
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label class="form-label fw-bold">Nama Lengkap</label>
                    <input type="text" name="nama" id="edit_nama" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Email (Username)</label>
                    <input type="email" name="email" id="edit_email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Role</label>
                    <select name="role" id="edit_role" class="form-select" required>
                        <option value="admin">Admin</option>
                        <option value="pengelola">Pengelola</option>
                        <option value="peternak">Peternak</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Ganti Password (Kosongkan jika tidak diubah)</label>
                    <input type="password" name="password" class="form-control" minlength="8">
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

    function showEditModal(user) {
        editForm.action = `/settings/users/${user.iduser}`;
        document.getElementById('edit_nama').value = user.nama;
        document.getElementById('edit_email').value = user.email;
        document.getElementById('edit_role').value = user.role;
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
@endsection
