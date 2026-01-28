@extends('layouts.app')

@section('title', 'Edit Slip Gaji - SIPERAH')

@section('content')
<div class="mb-4">
    <a href="{{ route('gaji.index', ['bulan' => $slip->bulan, 'tahun' => $slip->tahun]) }}" class="text-primary fw-bold" style="text-decoration: none;"><i class="fas fa-arrow-left"></i> Kembali ke Daftar</a>
    <div class="d-flex justify-content-between align-items-center mt-3">
        <h1 class="h3 mb-0 fw-bold">Pratinjau Slip Gaji - {{ $slip->peternak->nama_peternak }}</h1>
        @if(!$slip->isSigned())
            <button type="button" class="btn btn-warning fw-bold" id="btn-edit-toggle">
                <i class="fas fa-edit"></i> Edit Data
            </button>
        @endif
    </div>
    @if($slip->isSigned())
        <div class="alert alert-info mt-3 border-0 shadow-sm" style="border-radius: 12px; background: #EFF6FF; border-left: 5px solid var(--primary) !important;">
            <div class="d-flex align-items-center">
                <i class="fas fa-lock fa-2x me-3 text-primary"></i>
                <div>
                    <h6 class="fw-bold mb-1 text-primary">Slip ini telah Terbayar & Ditandatangani Digital</h6>
                    <p class="small mb-0">Perubahan data telah dikunci untuk menjaga integritas laporan. Silakan cetak slip di bawah.</p>
                </div>
            </div>
        </div>
    @endif
</div>

<form id="form-gaji" action="{{ route('gaji.update', $slip->idslip) }}" method="POST">
    @csrf
    @method('PUT')
    
    <div class="grid" style="grid-template-columns: 1fr 1fr; gap: 2rem;">
        <!-- INFO DASAR -->
        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
            <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark"><i class="fas fa-info-circle text-primary"></i> Informasi Pembayaran</h5>
            </div>
            <div class="card-body pt-0">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label small fw-bold text-muted">Jumlah Susu (Liter)</label>
                        <input type="number" step="any" name="jumlah_susu" id="jumlah_susu" class="form-control bg-light" value="{{ $slip->jumlah_susu }}" required disabled>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted">Harga Satuan (Rp)</label>
                        <div class="input-group">
                            <input type="number" name="harga_satuan" id="harga_satuan" class="form-control bg-light" value="{{ (int)$slip->harga_satuan }}" required disabled>
                            @if($slip->harga_satuan <= 0)
                                <button type="button" class="btn btn-sm btn-outline-primary" id="btn-apply-price" style="display: none;" onclick="applyCurrentPrice({{ $currentHarga }})">
                                    <i class="fas fa-magic"></i> Gunakan Rp{{ number_format($currentHarga, 0, ',', '.') }}
                                </button>
                            @endif
                        </div>
                        @if($slip->harga_satuan <= 0)
                            <small class="text-danger fw-bold" id="price-warning"><i class="fas fa-exclamation-triangle"></i> Harga belum diatur!</small>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted">Status Bayar</label>
                        <select name="status" class="form-select bg-light" disabled>
                            <option value="pending" {{ $slip->status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="dibayar" {{ $slip->status == 'dibayar' ? 'selected' : '' }}>Dibayar</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-bold text-muted">Total Pembayaran (Gross)</label>
                        <input type="number" name="total_pembayaran" id="total_pembayaran" class="form-control bg-light fw-bold" value="{{ (int)$slip->total_pembayaran }}" required readonly>
                        <small class="text-muted" style="font-size: 0.7rem;">Dihitung otomatis: Liter x Harga</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- POTONGAN -->
        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="fw-bold mb-0 text-dark"><i class="fas fa-minus-circle text-danger"></i> Daftar Potongan (Rp)</h5>
            </div>
            <div class="card-body pt-0">
                <div class="row g-2">
                    @php
                        $potongans = [
                            'potongan_shr' => '1. SHR',
                            'potongan_hutang_bl_ll' => '2. HUT. BL LL',
                            'potongan_pakan_a' => '3. PAKAN A',
                            'potongan_pakan_b' => '4. PAKAN B',
                            'potongan_vitamix' => '5. VITAMIX',
                            'potongan_konsentrat' => '6. KONSENTRAT',
                            'potongan_skim' => '7. SKIM',
                            'potongan_ib_keswan' => '8. IB/KESWAN',
                            'potongan_susu_a' => '9. SUSU A',
                            'potongan_kas_bon' => '10. KAS BON',
                            'potongan_pakan_b_2' => '11. PAKAN B (2)',
                            'potongan_sp' => '12. SP',
                            'potongan_karpet' => '13. KARPET',
                            'potongan_vaksin' => '14. VAKSIN',
                            'potongan_lain_lain' => '15. LAIN-LAIN',
                        ];
                    @endphp

                    @foreach($potongans as $key => $label)
                    <div class="col-md-6">
                        <div class="form-group mb-1">
                            <label class="form-label x-small fw-bold text-muted mb-0" style="font-size: 0.7rem;">{{ $label }}</label>
                            <input type="number" name="{{ $key }}" class="form-control form-control-sm bg-light" value="{{ (int)$slip->$key }}" disabled>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="mt-4 p-3 border-0 bg-dark text-white shadow-sm" style="border-radius: 12px;">
                    <div class="d-flex justify-content-between mb-2 opacity-75">
                        <span class="small">Total Potongan:</span>
                        <span id="total_p_display">Rp {{ number_format($slip->total_potongan, 0, ',', '.') }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold fs-5">SISA BAYAR:</span>
                        <span id="sisa_p_display" class="fw-bold fs-4 text-success">Rp {{ number_format($slip->sisa_pembayaran, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FOOTER ACTIONS -->
    <div class="mt-5 d-flex justify-content-between align-items-center p-4 bg-white shadow-sm border" style="border-radius: 12px;">
        <div class="d-flex gap-2">
            <a href="{{ route('gaji.print', $slip->idslip) }}" target="_blank" class="btn btn-outline-primary px-4">
                <i class="fas fa-print"></i> Cetak Slip Gaji
            </a>
        </div>
        
        <div class="d-flex gap-3 align-items-center">
            @if(!$slip->isSigned())
                <button type="submit" class="btn btn-primary px-5 fw-bold" id="btn-save" style="display: none;">
                    <i class="fas fa-save"></i> Simpan Data
                </button>
                
                <button type="button" class="btn btn-success px-5 fw-bold" onclick="showSignPopup()">
                    <i class="fas fa-pen-nib"></i> Tanda Tangan & Selesaikan
                </button>
            @endif
        </div>
    </div>
</form>

<!-- POPUP SIGN -->
<div id="signModal" class="no-print" style="display:none; position:fixed; z-index:2001; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.6); backdrop-filter: blur(4px);">
    <div class="card shadow-lg border-0" style="max-width:450px; margin: 15vh auto; border-radius: 20px; overflow: hidden;">
        <div class="bg-success py-4 text-center text-white">
            <i class="fas fa-certificate fa-3x mb-3"></i>
            <h4 class="fw-bold mb-0 text-white">Tanda Tangan Digital</h4>
        </div>
        <div class="card-body p-4 text-center">
            <p class="mb-4">Apakah Anda yakin telah memverifikasi data gaji untuk <strong>{{ $slip->peternak->nama_peternak }}</strong>?</p>
            <div class="p-3 bg-light rounded mb-4 text-start">
                <div class="d-flex justify-content-between small mb-1">
                    <span>Periode:</span>
                    <span class="fw-bold">{{ date('F Y', mktime(0, 0, 0, $slip->bulan, 1, $slip->tahun)) }}</span>
                </div>
                <div class="d-flex justify-content-between small">
                    <span>Neto Bayar:</span>
                    <span class="fw-bold text-success">Rp {{ number_format($slip->sisa_pembayaran, 0, ',', '.') }}</span>
                </div>
            </div>
            <p class="small text-muted mb-4"><i class="fas fa-exclamation-triangle"></i> Slip yang telah ditandatangani akan dikunci secara permanen.</p>
            
            <form action="{{ route('gaji.sign', $slip->idslip) }}" method="POST">
                @csrf
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-success fw-bold py-2">YA, TANDA TANGANI & SELESAIKAN</button>
                    <button type="button" class="btn btn-light" onclick="hideSignPopup()">BATAL</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const btnToggle = document.getElementById('btn-edit-toggle');
    const btnSave = document.getElementById('btn-save');
    const btnApplyPrice = document.getElementById('btn-apply-price');
    const form = document.getElementById('form-gaji');
    const signModal = document.getElementById('signModal');

    if (btnToggle) {
        btnToggle.addEventListener('click', () => {
            const isEditing = btnToggle.classList.contains('active');
            const allInputs = form.querySelectorAll('input, select');
            
            if (!isEditing) {
                // Switch to Edit Mode
                allInputs.forEach(input => {
                    if (input.id !== 'total_pembayaran') { // Keep gross readonly
                        input.disabled = false;
                        input.classList.remove('bg-light');
                    }
                });
                btnToggle.innerHTML = '<i class="fas fa-times"></i> Batal Edit';
                btnToggle.classList.replace('btn-warning', 'btn-outline-danger');
                btnToggle.classList.add('active');
                btnSave.style.display = 'block';
                if (btnApplyPrice) btnApplyPrice.style.display = 'block';
            } else {
                // Back to Preview Mode
                location.reload(); 
            }
        });
    }

    function applyCurrentPrice(price) {
        document.getElementById('harga_satuan').value = price;
        const warning = document.getElementById('price-warning');
        if (warning) warning.style.display = 'none';
        calculate();
    }

    // Auto-calculate logic for all numeric inputs
    form.addEventListener('input', function(e) {
        if (e.target.type === 'number') {
            calculate();
        }
    });

    function calculate() {
        const liter = parseFloat(document.getElementById('jumlah_susu').value) || 0;
        const harga = parseFloat(document.getElementById('harga_satuan').value) || 0;
        const totalGross = liter * harga;
        
        document.getElementById('total_pembayaran').value = Math.round(totalGross);
        
        let totalPotongan = 0;
        @foreach($potongans as $key => $label)
            totalPotongan += parseFloat(document.getElementsByName('{{ $key }}')[0].value) || 0;
        @endforeach

        const sisa = totalGross - totalPotongan;

        document.getElementById('total_p_display').innerText = 'Rp ' + Math.round(totalPotongan).toLocaleString('id-ID');
        document.getElementById('sisa_p_display').innerText = 'Rp ' + Math.round(sisa).toLocaleString('id-ID');
    }
</script>
@endsection
