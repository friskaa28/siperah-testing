@extends('layouts.app')

@section('title', 'Log Aktivitas - SIPERAH')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="fw-bold mb-0">📋 Log Aktivitas Sistem</h1>
        <p class="text-muted">Riwayat aktivitas pengguna di sistem SIPERAH</p>
    </div>
</div>

<!-- Filter Section -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('activity-log.index') }}" class="row g-3">
            <div class="col-md-3">
                <label class="form-label small">Pengguna</label>
                <select name="user_id" class="form-select form-select-sm">
                    <option value="">Semua Pengguna</option>
                    @foreach($users as $user)
                        <option value="{{ $user->iduser }}" {{ request('user_id') == $user->iduser ? 'selected' : '' }}>
                            {{ $user->nama }} ({{ $user->role }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small">Jenis Aktivitas</label>
                <select name="action" class="form-select form-select-sm">
                    <option value="">Semua Aktivitas</option>
                    @foreach($actions as $action)
                        <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                            {{ str_replace('_', ' ', $action) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small">Dari Tanggal</label>
                <input type="date" name="start_date" class="form-control form-control-sm" value="{{ request('start_date') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small">Sampai Tanggal</label>
                <input type="date" name="end_date" class="form-control form-control-sm" value="{{ request('end_date') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small">Tampilkan</label>
                <select name="per_page" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="25" {{ $logs->perPage() == 25 ? 'selected' : '' }}>25 baris</option>
                    <option value="50" {{ $logs->perPage() == 50 ? 'selected' : '' }}>50 baris</option>
                    <option value="100" {{ $logs->perPage() == 100 ? 'selected' : '' }}>100 baris</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small">&nbsp;</label>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-sm">🔍 Filter</button>
                    <a href="{{ route('activity-log.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Activity Log Table -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th style="width: 140px; text-align: left;">Waktu</th>
                        <th style="width: 180px; text-align: left;">Pengguna</th>
                        <th style="width: 180px; text-align: center;">Aktivitas</th>
                        <th style="text-align: left;">Deskripsi</th>
                        <th style="width: 120px; text-align: right;">IP Address</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td style="text-align: left;">
                            <small class="text-muted">{{ $log->created_at->format('d/m/Y') }}</small><br>
                            <strong>{{ $log->created_at->format('H:i:s') }}</strong>
                        </td>
                        <td style="text-align: left;">
                            @if($log->user)
                                <strong>{{ $log->user->nama }}</strong><br>
                                <small class="text-muted">{{ $log->user->role }}</small>
                            @else
                                <span class="text-muted">Sistem</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @php
                                $badgeClass = 'bg-secondary';
                                if(str_contains($log->action, 'PRINT')) $badgeClass = 'bg-info';
                                if(str_contains($log->action, 'EXPORT')) $badgeClass = 'bg-primary';
                                if(str_contains($log->action, 'DELETE')) $badgeClass = 'bg-danger';
                                if(str_contains($log->action, 'CREATE') || str_contains($log->action, 'SIGN')) $badgeClass = 'bg-success';
                                if(str_contains($log->action, 'UPDATE') || str_contains($log->action, 'EDIT')) $badgeClass = 'bg-warning';
                            @endphp
                            <span class="badge {{ $badgeClass }}">{{ str_replace('_', ' ', $log->action) }}</span>
                        </td>
                        <td style="text-align: left;">
                            {{ $log->description }}
                            @if($log->action === 'SIGN_SALARY_SLIP')
                                <div class="mt-2">
                                    <form action="{{ route('activity-log.undo', $log->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan tanda tangan ini? Slip gaji terkait akan terbuka kuncinya.')">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-danger btn-sm" style="font-size: 0.7rem; padding: 2px 8px;">
                                            <i class="fas fa-undo"></i> Buka Kunci / Batalkan Tanda Tangan
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </td>
                        <td style="text-align: right;"><small class="text-muted font-monospace">{{ $log->ip_address }}</small></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">
                            Tidak ada log aktivitas ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="text-muted small">
                Menampilkan {{ $logs->firstItem() ?? 0 }} - {{ $logs->lastItem() ?? 0 }} dari {{ $logs->total() }} log
            </div>
            <div>
                {{ $logs->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Statistics -->
<div class="row mt-4">
    <div class="col-md-4">
        <div class="card bg-light">
            <div class="card-body text-center">
                <h3 class="mb-0">{{ number_format($logs->total()) }}</h3>
                <small class="text-muted">Total Log Aktivitas</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-light">
            <div class="card-body text-center">
                <h3 class="mb-0">{{ $logs->where('action', 'PRINT_SALARY_SLIP')->count() }}</h3>
                <small class="text-muted">Cetak Slip Pembayaran (Halaman Ini)</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-light">
            <div class="card-body text-center">
                <h3 class="mb-0">{{ $logs->where('action', 'EXPORT_E_STATEMENT')->count() }}</h3>
                <small class="text-muted">Ekspor E-Statement (Halaman Ini)</small>
            </div>
        </div>
    </div>
</div>

@endsection
