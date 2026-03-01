@extends('layouts.app')

@section('title', 'Input Data KPI - SIPERAH')

@section('styles')
<style>
    .page-header { margin-bottom: 1.5rem; }
    .page-header h1 { font-size: 1.6rem; font-weight: 700; color: var(--dark); }
    .page-header p  { color: var(--text-light); font-size: 0.9rem; }

    .section-card {
        background: white; border-radius: 14px; padding: 1.75rem;
        border: 1px solid var(--border); margin-bottom: 2rem;
    }
    .section-card h2 { font-size: 1.1rem; font-weight: 700; margin-bottom: 1.5rem; padding-bottom: 0.75rem; border-bottom: 1px solid var(--border); color: var(--dark); }

    .form-row { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; }
    @media(max-width:800px) { .form-row { grid-template-columns: repeat(2,1fr); } }
    @media(max-width:500px) { .form-row { grid-template-columns: 1fr; } }

    .mini-table { width: 100%; border-collapse: collapse; font-size: 0.85rem; }
    .mini-table th { padding: 0.6rem 0.75rem; text-align: left; color: var(--text-light); font-weight: 500; font-size: 0.8rem; border-bottom: 1px solid var(--border); background: #F9FAFB; }
    .mini-table td { padding: 0.65rem 0.75rem; border-bottom: 1px solid #F3F4F6; }
    .mini-table tr:last-child td { border:none; }

    .badge {
        display: inline-flex; align-items: center; gap: 0.3rem;
        padding: 0.2rem 0.6rem; border-radius: 99px; font-size: 0.75rem; font-weight: 500;
    }
    .badge-danger  { background: #FEF2F2; color: #DC2626; }
    .badge-success { background: #ECFDF5; color: #16A34A; }
    .badge-warning { background: #FFFBEB; color: #D97706; }
</style>
@endsection

@section('content')
<div class="page-header">
    <h1><i class="fas fa-edit" style="color:#2180D3; margin-right:0.5rem;"></i>Input Data KPI</h1>
    <p>Kelola data profit dan pencatatan error untuk keperluan analitik KPI</p>
</div>

@if(session('success'))
    <div style="background:#ECFDF5; border:1px solid #86EFAC; border-radius:10px; padding:1rem 1.25rem; margin-bottom:1rem; color:#16A34A; display:flex; align-items:center; gap:0.5rem;">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
@endif

{{-- ===================== PROFIT INPUT ===================== --}}
<div class="section-card">
    <h2><i class="fas fa-coins" style="color:#7C3AED; margin-right:0.5rem;"></i>Data Profit & Revenue</h2>

    <form method="POST" action="{{ route('analytics.profit.store') }}">
        @csrf
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Periode (YYYY-MM) *</label>
                <input type="month" name="period" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Revenue Sebelum (Rp)</label>
                <input type="number" name="revenue_before" class="form-control" placeholder="0" min="0" step="1">
            </div>
            <div class="form-group">
                <label class="form-label">Revenue Sesudah (Rp)</label>
                <input type="number" name="revenue_after" class="form-control" placeholder="0" min="0" step="1">
            </div>
            <div class="form-group">
                <label class="form-label">Biaya Sebelum (Rp)</label>
                <input type="number" name="cost_before" class="form-control" placeholder="0" min="0" step="1">
            </div>
            <div class="form-group">
                <label class="form-label">Biaya Sesudah (Rp)</label>
                <input type="number" name="cost_after" class="form-control" placeholder="0" min="0" step="1">
            </div>
            <div class="form-group">
                <label class="form-label">Vol Susu Sebelum (L)</label>
                <input type="number" name="milk_volume_before" class="form-control" placeholder="0" min="0" step="0.01">
            </div>
            <div class="form-group">
                <label class="form-label">Vol Susu Sesudah (L)</label>
                <input type="number" name="milk_volume_after" class="form-control" placeholder="0" min="0" step="0.01">
            </div>
            <div class="form-group" style="grid-column: span 2;">
                <label class="form-label">Catatan</label>
                <input type="text" name="notes" class="form-control" placeholder="Keterangan tambahan (opsional)">
            </div>
        </div>
        <div style="margin-top:1rem;">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Data Profit</button>
        </div>
    </form>

    {{-- Existing Profit Data --}}
    @if($profits->isNotEmpty())
    <div style="margin-top:2rem; overflow-x:auto;">
        <table class="mini-table">
            <thead>
                <tr>
                    <th>Periode</th>
                    <th>Revenue Sebelum</th>
                    <th>Revenue Sesudah</th>
                    <th>Profit Sebelum</th>
                    <th>Profit Sesudah</th>
                    <th>Vol Susu</th>
                    <th>Catatan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($profits as $p)
                <tr>
                    <td><strong>{{ $p->period }}</strong></td>
                    <td>Rp {{ number_format($p->revenue_before ?? 0, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($p->revenue_after  ?? 0, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($p->profit_before, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($p->profit_after,  0, ',', '.') }}</td>
                    <td>{{ number_format($p->milk_volume_before ?? 0, 1, ',', '.') }} → {{ number_format($p->milk_volume_after ?? 0, 1, ',', '.') }} L</td>
                    <td style="color:var(--text-light);">{{ $p->notes ? Str::limit($p->notes, 40) : '—' }}</td>
                    <td>
                        <form method="POST" action="{{ route('analytics.profit.destroy', $p->id) }}" onsubmit="return confirm('Hapus data periode {{ $p->period }}?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger" style="padding:0.3rem 0.75rem; font-size:0.8rem;">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

{{-- ===================== ERROR INPUT ===================== --}}
<div class="section-card">
    <h2><i class="fas fa-bug" style="color:#EF4444; margin-right:0.5rem;"></i>Catat Error Sistem</h2>

    <form method="POST" action="{{ route('analytics.error.store') }}">
        @csrf
        <div style="display:grid; grid-template-columns: 1fr 1fr 1fr; gap:1rem;">
            <div class="form-group">
                <label class="form-label">Tipe Error *</label>
                <select name="error_type" class="form-select" required>
                    @foreach($errorTypes as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Periode (YYYY-MM)</label>
                <input type="month" name="period" class="form-control" value="{{ now()->format('Y-m') }}">
            </div>
            <div class="form-group" style="grid-column:span 3;">
                <label class="form-label">Deskripsi Error *</label>
                <textarea name="description" class="form-control" rows="2" required placeholder="Jelaskan kesalahan yang terjadi..."></textarea>
            </div>
        </div>
        <button type="submit" class="btn btn-danger"><i class="fas fa-plus"></i> Catat Error</button>
    </form>

    {{-- Error Table --}}
    @if($errorLogs->isNotEmpty())
    <div style="margin-top:2rem; overflow-x:auto;">
        <table class="mini-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tipe</th>
                    <th>Deskripsi</th>
                    <th>Periode</th>
                    <th>Dilaporkan</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($errorLogs as $err)
                <tr>
                    <td style="color:var(--text-light);">{{ $err->id }}</td>
                    <td>
                        <span class="badge @if($err->error_type === 'salary_calc') badge-warning @elseif($err->error_type === 'data_entry') badge-danger @else badge-danger @endif">
                            {{ $err->error_type_label }}
                        </span>
                    </td>
                    <td style="max-width:240px;">{{ Str::limit($err->description, 60) }}</td>
                    <td>{{ $err->period ?? '—' }}</td>
                    <td>{{ $err->reporter->nama ?? '—' }}</td>
                    <td>
                        <form method="POST" action="{{ route('analytics.error.update', $err->id) }}" style="display:inline;">
                            @csrf @method('PUT')
                            <input type="hidden" name="resolved" value="{{ $err->resolved ? '0' : '1' }}">
                            <button type="submit" class="badge {{ $err->resolved ? 'badge-success' : 'badge-danger' }}" style="border:none;cursor:pointer;">
                                {{ $err->resolved ? '✓ Selesai' : '✗ Pending' }}
                            </button>
                        </form>
                    </td>
                    <td>
                        <form method="POST" action="{{ route('analytics.error.destroy', $err->id) }}" onsubmit="return confirm('Hapus error ini?')" style="display:inline;">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger" style="padding:0.25rem 0.6rem; font-size:0.78rem;">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="pagination">{{ $errorLogs->links() }}</div>
    </div>
    @endif
</div>
@endsection
