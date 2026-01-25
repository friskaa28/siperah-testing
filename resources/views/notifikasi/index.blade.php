
@extends('layouts.app')

@section('title', 'Notifikasi - SIPERAH')

@section('content')
<h1>Notifikasi</h1>

<!-- Filter Buttons -->
<div style="margin-bottom: 24px; display: flex; gap: 8px; flex-wrap: wrap;">
    <a href="/notifikasi?kategori=semua" 
       class="btn @if($kategoriAktif === 'semua') btn-primary @else btn-secondary @endif">
        Semua
    </a>
    <a href="/notifikasi?kategori=jadwal" 
       class="btn @if($kategoriAktif === 'jadwal') btn-primary @else btn-secondary @endif">
        Jadwal
    </a>
</div>

<!-- Notifikasi List -->
<div>
    @forelse($notifikasi as $n)
        <div class="card" style="margin-bottom: 16px;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div style="flex: 1;">
                    <h3 style="margin-bottom: 8px;">{{ $n->judul }}</h3>
                    <p style="color: var(--text-light); margin-bottom: 8px;">{{ $n->pesan }}</p>
                    <p style="font-size: 12px; color: var(--text-light);">
                        {{ $n->created_at->diffForHumans() }}
                    </p>
                </div>
                <div style="display: flex; gap: 8px;">
                    @if($n->status_baca === 'belum_baca')
                        <form action="{{ route('notifikasi.markAsRead', $n->idnotif) }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-primary" style="font-size: 12px; padding: 6px 12px;">
                                Tandai Baca
                            </button>
                        </form>
                    @endif
                    <form action="{{ route('notifikasi.delete', $n->idnotif) }}" method="POST" style="display: inline;" 
                          onsubmit="return confirm('Hapus notifikasi ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" style="font-size: 12px; padding: 6px 12px;">
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="card">
            <p style="color: var(--text-light); text-align: center;">Tidak ada notifikasi</p>
        </div>
    @endforelse
</div>

<!-- Pagination -->
@if($notifikasi->hasPages())
    <div style="margin-top: 24px; padding-top: 24px; border-top: 1px solid var(--border);">
        {{ $notifikasi->links() }}
    </div>
@endif

@endsection