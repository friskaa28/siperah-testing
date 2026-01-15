@extends('layouts.app')

@section('title', 'Harga Susu - SIP-SUSU')

@section('content')
<div class="mb-4">
    <h1 class="fw-bold mb-0">Pengaturan Harga Susu</h1>
    <p class="text-muted">Tetapkan harga beli susu per liter yang berlaku.</p>
</div>

<div class="grid" style="grid-template-columns: 1fr 2fr; gap: 2rem;">
    <!-- Form Update Harga -->
    <div class="card" style="height: fit-content;">
        <h3 class="mb-3">Update Harga Sekarang</h3>
        <div class="text-center p-3 mb-4" style="background: #f0f7ff; border-radius: 12px; border: 1px solid #dbeafe;">
            <p class="mb-1 text-muted small">Harga Berlaku Saat Ini</p>
            <h2 class="fw-bold text-primary mb-0">Rp {{ number_format($currentPrice, 0, ',', '.') }}</h2>
        </div>

        <form action="{{ route('harga_susu.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Harga Baru (Rp/Liter)</label>
                <div class="input-group">
                    <span class="input-group-text">Rp</span>
                    <input type="number" name="harga" class="form-control" required min="0" placeholder="7000">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Berlaku Mulai Tanggal</label>
                <input type="date" name="tanggal_berlaku" class="form-control" required value="{{ date('Y-m-d') }}">
            </div>
            <button type="submit" class="btn btn-primary w-100 mt-2">Update Harga</button>
        </form>
    </div>

    <!-- Riwayat Harga -->
    <div class="card">
        <h3 class="mb-4">Riwayat Perubahan Harga</h3>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Tanggal Berlaku</th>
                        <th>Harga</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($history as $item)
                    <tr>
                        <td class="fw-bold">{{ $item->tanggal_berlaku->format('d M Y') }}</td>
                        <td>Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                        <td class="text-end">
                            <form action="{{ route('harga_susu.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus riwayat ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="text-center py-4 text-muted">Belum ada riwayat harga.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
