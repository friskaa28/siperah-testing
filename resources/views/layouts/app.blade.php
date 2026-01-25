
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SIPERAH')</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary: #2180D3;
            --primary-dark: #1A6DAA;
            --success: #22C55E;
            --warning: #F97316;
            --danger: #EF4444;
            --light: #F5F5F5;
            --dark: #1F2937;
            --border: #E5E7EB;
            --text-light: #6B7280;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #F9FAFB;
            color: var(--dark);
            line-height: 1.6;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* ===== NAVBAR ===== */
        .navbar {
            background: white;
            border-bottom: 1px solid var(--border);
            position: sticky;
            top: 0;
            z-index: 100;
            padding: 1rem 0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .navbar .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar-brand {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .navbar-brand:hover {
            color: var(--primary-dark);
        }

        .navbar-logo-siperah {
            height: 50px;
            object-fit: contain;
        }

        .navbar-logo-innovillage {
            height: 28px;
            object-fit: contain;
        }

        .navbar-actions {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-name {
            color: var(--text-light);
            font-size: 0.9rem;
            font-weight: 500;
        }

        /* ===== MAIN LAYOUT ===== */
        .layout {
            display: flex;
            flex: 1;
        }

        /* ===== SIDEBAR ===== */
        .sidebar {
            width: 250px;
            background: white;
            border-right: 1px solid var(--border);
            padding: 1.5rem 0;
            overflow-y: auto;
            transition: all 0.3s ease;
        }

        .sidebar-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1.5rem;
            color: var(--text-light);
            text-decoration: none;
            transition: all 0.2s;
            font-size: 0.95rem;
        }

        .sidebar-item:hover {
            background-color: #F3F4F6;
            color: var(--primary);
            padding-left: 2rem;
        }

        .sidebar-item.active {
            background-color: #E0E7FF;
            color: var(--primary);
            border-left: 3px solid var(--primary);
            padding-left: 1.5rem;
        }

        .mobile-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--primary);
            padding: 0.5rem;
            border-radius: 6px;
            transition: background 0.2s;
        }

        .mobile-toggle:hover {
            background: #F3F4F6;
        }

        /* ===== MAIN CONTENT ===== */
        .content {
            flex: 1;
            padding: 2rem;
            display: flex;
            flex-direction: column;
            min-height: 100%;
        }

        .content h1 {
            font-size: 2rem;
            margin-bottom: 1.5rem;
            color: var(--dark);
        }

        .content h2 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            margin-top: 2rem;
            color: var(--dark);
        }

        /* ===== GRID ===== */
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        /* ===== CARD ===== */
        .card {
            background: white;
            border-radius: 8px;
            border: 1px solid var(--border);
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            transition: box-shadow 0.2s;
        }

        .card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .card h3 {
            font-size: 0.9rem;
            color: var(--text-light);
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .card h2 {
            font-size: 2rem;
            color: var(--primary);
            margin-bottom: 0.5rem;
        }

        .card p {
            font-size: 0.85rem;
            color: var(--text-light);
        }

        /* ===== TABLE ===== */
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        .table thead {
            background-color: #F9FAFB;
            border-bottom: 2px solid var(--border);
        }

        .table th {
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            color: var(--dark);
            font-size: 0.9rem;
        }

        .table td {
            padding: 1rem;
            border-bottom: 1px solid var(--border);
        }

        .table tbody tr:hover {
            background-color: #F9FAFB;
        }

        /* ===== FORM ===== */
        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--dark);
            font-size: 0.95rem;
        }

        .form-control,
        .form-select {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid var(--border);
            border-radius: 12px; /* More rounded for "smooth" look */
            font-size: 0.95rem;
            font-family: inherit;
            transition: all 0.3s ease; /* Smoother transition */
            background-color: #FAFAFA; /* Slight contrast input bg */
            color: var(--dark);
        }

        .form-control:focus,
        .form-select:focus {
            outline: none;
            border-color: var(--primary);
            background-color: white;
            box-shadow: 0 0 0 4px rgba(33, 128, 211, 0.15); /* Softer, larger glow */
            transform: translateY(-1px); /* Subtle lift */
        }

        .form-select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 14px 10px;
            cursor: pointer;
            padding-right: 2.5rem !important; /* Added more space for the arrow */
        }

        textarea.form-control {
            resize: vertical;
            min-height: 120px;
        }
        
        /* Floating label effect or cleaner label */
        .form-group {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--text-light);
            font-size: 0.9rem;
            letter-spacing: 0.02em;
        }

        /* ===== INPUT GROUP ===== */
        .input-group {
            position: relative;
            display: flex;
            flex-wrap: wrap;
            align-items: stretch;
            width: 100%;
        }

        .input-group > .form-control {
            position: relative;
            flex: 1 1 auto;
            width: 1%;
            min-width: 0;
        }

        .input-group-text {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
            font-weight: 600;
            line-height: 1;
            color: var(--text-light);
            text-align: center;
            white-space: nowrap;
            background-color: #F3F4F6;
            border: 1px solid var(--border);
            border-radius: 12px;
        }

        .input-group > .form-control:not(:last-child) {
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
        }

        .input-group > .input-group-text:not(:first-child) {
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
            border-left: 0;
        }

        /* ===== BUTTON ===== */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 6px;
            font-size: 0.95rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
        }

        .btn-secondary {
            background: #E5E7EB;
            color: var(--dark);
        }

        .btn-secondary:hover {
            background: #D1D5DB;
        }

        .btn-success {
            background: var(--success);
            color: white;
        }

        .btn-danger {
            background: var(--danger);
            color: white;
        }

        /* ===== RESPONSIVE: TABLET ===== */
        @media (max-width: 768px) {
            .sidebar {
                width: 200px;
            }

            .content {
                padding: 1.5rem;
            }

            .content h1 {
                font-size: 1.5rem;
            }

            .grid {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 1rem;
            }

            .table {
                font-size: 0.85rem;
            }

            .table th,
            .table td {
                padding: 0.75rem 0.5rem;
            }

            .navbar-actions {
                gap: 1rem;
            }

            .user-name {
                display: none;
            }
        }

        /* ===== RESPONSIVE: MOBILE ===== */
        @media (max-width: 640px) {
            .mobile-toggle {
                display: block;
            }

            .layout {
                position: relative;
                flex-direction: column;
            }

            .sidebar {
                position: fixed;
                left: -250px;
                top: 60px;
                bottom: 0;
                z-index: 99;
                width: 250px;
                box-shadow: 10px 0 20px rgba(0,0,0,0.05);
            }

            .sidebar.show {
                left: 0;
            }

            .content {
                padding: 1rem;
            }

            .navbar-actions .btn {
                display: none;
            }

            .table-responsive {
                border: 0;
            }

            /* Mobile-specific utilities */
            .navbar {
                padding: 0.5rem 0;
            }

            .navbar-logo-siperah {
                height: 35px !important;
            }

            .navbar-logo-innovillage {
                height: 22px !important;
            }

            .btn-sm, .btn {
                min-height: 36px;
                padding: 0.5rem 0.75rem;
            }

            .form-control, .form-select {
                font-size: 16px !important; /* Prevents zoom on iOS */
            }

            h1, .h1 {
                font-size: 1.5rem !important;
            }

            h3, .h3 {
                font-size: 1.2rem !important;
            }

            .card {
                border-radius: 8px !important;
            }

            /* Force flex-wrap on button groups */
            .d-flex.gap-2 {
                flex-wrap: wrap;
            }

            /* Make tables more touch-friendly */
            .table td, .table th {
                padding: 0.75rem 0.5rem;
                font-size: 0.875rem;
            }
        }

        /* ===== UTILITY ===== */
        .text-light {
            color: var(--text-light);
        }

        .text-primary {
            color: var(--primary);
        }

        .text-danger {
            color: var(--danger);
        }

        .text-center {
            text-align: center;
        }

        .mt-1 { margin-top: 0.5rem; }
        .mt-2 { margin-top: 1rem; }
        .mt-3 { margin-top: 1.5rem; }
        .mt-4 { margin-top: 2rem; }

        .mb-1 { margin-bottom: 0.5rem; }
        .mb-2 { margin-bottom: 1rem; }
        .mb-3 { margin-bottom: 1.5rem; }
        .mb-4 { margin-bottom: 2rem; }

        .p-1 { padding: 0.5rem; }
        .p-2 { padding: 1rem; }

        .gap-1 { gap: 0.5rem; }
        .gap-2 { gap: 1rem; }
        .gap-3 { gap: 1.5rem; }
        .gap-4 { gap: 2rem; }

        .w-auto { width: auto !important; }
        .flex-wrap { flex-wrap: wrap !important; }
        .align-items-center { align-items: center !important; }
        .justify-content-between { justify-content: space-between !important; }

        .d-flex {
            display: flex;
        }

        .flex-between {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .flex-col {
            flex-direction: column;
        }

        .align-center {
            align-items: center;
        }

        /* ===== ANIMATIONS ===== */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card {
            animation: fadeIn 0.3s ease-out;
        }

        /* ===== PAGINATION ===== */
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 2rem;
            margin-bottom: 2rem;
        }

        /* Container for the pagination (the "card" look) */
        .pagination nav {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            background: white;
            padding: 0.5rem 1rem;
            border: 1px solid var(--border);
            border-radius: 9999px; /* Pill shape */
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
            gap: 0.25rem;
        }
        
        /* Overwrite Tailwind/Laravel defaults */
        nav[role="navigation"] {
            display: flex;
            justify-content: center;
            align-items: center;
            width: auto;
        }

        nav[role="navigation"] div:first-child {
            display: none; 
        }

        nav[role="navigation"] > div:last-child {
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        /* Common style for links and spans */
        nav[role="navigation"] span,
        nav[role="navigation"] a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 2rem;
            height: 2rem;
            padding: 0 0.5rem;
            font-size: 0.9rem;
            font-weight: 500;
            color: var(--text-light);
            border-radius: 9999px; /* Round buttons */
            text-decoration: none;
            transition: all 0.2s;
            border: none; /* Remove default borders */
            background: transparent;
        }

        /* Arrows styling (Previous/Next) */
        nav[role="navigation"] a[rel="prev"],
        nav[role="navigation"] a[rel="next"] {
            color: var(--primary);
            font-weight: bold;
        }

        /* Hover state */
        nav[role="navigation"] a:hover {
            background-color: #EFF6FF; /* var(--primary) light */
            color: var(--primary);
        }

        /* Active state */
        nav[role="navigation"] span[aria-current="page"] > span {
            background-color: var(--primary);
            color: white;
            box-shadow: 0 1px 2px rgba(33, 128, 211, 0.3);
        }

        /* Disabled state */
        nav[role="navigation"] span[aria-disabled="true"] > span {
            opacity: 0.5;
            cursor: not-allowed;
        }

        nav[role="navigation"] svg {
            width: 1.25rem;
            height: 1.25rem;
        }

        /* ===== FOOTER ===== */
        .footer {
            background: white;
            border-top: 1px solid var(--border);
            padding: 1.5rem 0;
            margin-top: auto;
            text-align: center;
            color: var(--text-light);
            font-size: 0.9rem;
            width: 100%;
        }

        /* ===== TOOLTIP ===== */
        [data-tooltip] {
            position: relative;
            cursor: pointer;
        }

        [data-tooltip]:before {
            content: attr(data-tooltip);
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            padding: 5px 10px;
            background: white;
            color: #DC2626; /* Red color as requested */
            font-size: 0.75rem; /* Smaller font */
            font-weight: 400; /* Not bold */
            border: 1px solid #FECACA;
            border-radius: 6px;
            white-space: nowrap;
            opacity: 0;
            visibility: hidden;
            transition: all 0.2s ease;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            pointer-events: none;
            z-index: 1000;
            margin-bottom: 5px;
        }

        [data-tooltip]:hover:before {
            opacity: 1;
            visibility: visible;
            transform: translateX(-50%) translateY(-5px);
        }
    </style>
    @yield('styles')
</head>
<body>
    <!-- NAVBAR -->
    <nav class="navbar">
        <div class="container">
            <div class="d-flex align-items-center gap-2">
                @auth
                <button class="mobile-toggle" id="sidebarToggle"><i class="fas fa-bars"></i></button>
                @endauth
                <a href="/" class="navbar-brand" style="display: flex; align-items: center; gap: 12px;">
                    <img src="{{ asset('img/logo-siperah.png') }}" alt="SIPERAH Logo" class="navbar-logo-siperah">
                    <img src="{{ asset('img/logo-innovillage.png') }}" alt="Innovillage Logo" class="navbar-logo-innovillage">
                </a>
            </div>
            <div class="navbar-actions">
                @auth
                    @if(\App\Models\Setting::isEnabled('feature_notifikasi'))
                        <a href="/notifikasi" class="btn btn-secondary" style="background: transparent; border: none; padding: 0.5rem; position: relative; margin-right: 0.5rem; color: var(--text-light);">
                            <i class="fas fa-bell" style="font-size: 1.2rem;"></i>
                            @php
                                $unreadCount = auth()->user()->notifikasi()
                                    ->where('status_baca', 'belum_baca')
                                    ->where('kategori', '!=', 'bagi_hasil')
                                    ->count();
                            @endphp
                            @if($unreadCount > 0)
                                <span style="position: absolute; top: 0; right: 0; background: var(--danger); color: white; border-radius: 50%; width: 18px; height: 18px; font-size: 0.7rem; display: flex; align-items: center; justify-content: center; border: 2px solid white;">
                                    {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                                </span>
                            @endif
                        </a>
                    @endif

                    <div class="user-info">
                        <span class="user-name">{{ auth()->user()->nama ?? auth()->user()->email }}</span>
                        <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-primary" style="padding: 0.5rem 1rem; font-size: 0.85rem;">
                                Keluar
                            </button>
                        </form>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="btn btn-primary">Login</a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- MAIN LAYOUT -->
    <div class="layout">
        <!-- SIDEBAR -->
        @auth
            <aside class="sidebar">
                @if(auth()->user()->role === 'peternak')
                    <a href="/dashboard-peternak" class="sidebar-item @if(request()->is('dashboard-peternak')) active @endif">
                        <i class="fas fa-chart-line"></i> Dashboard
                    </a>
                    

                    <a href="{{ route('produksi.riwayat') }}" class="sidebar-item @if(request()->is('riwayat-produksi')) active @endif">
                        <i class="fas fa-history"></i> Riwayat Setor Susu
                    </a>
                    <a href="{{ route('peternak.laporan.index') }}" class="sidebar-item @if(request()->is('laporan*')) active @endif">
                        <i class="fas fa-file-invoice-dollar"></i> Laporan Pendapatan
                    </a>

                    <div style="padding: 0.75rem 1.5rem; font-size: 0.8rem; font-weight: 600; color: var(--text-light); text-transform: uppercase; margin-top: 1rem;">
                        System
                    </div>
                    <a href="{{ route('panduan.index') }}" class="sidebar-item @if(request()->is('panduan*')) active @endif">
                        <i class="fas fa-book"></i> Panduan Aplikasi
                    </a>
                @elseif(auth()->user()->role === 'pengelola' || auth()->user()->role === 'admin')
                    <a href="/dashboard-pengelola" class="sidebar-item @if(request()->is('dashboard-pengelola')) active @endif">
                        <i class="fas fa-chart-line"></i> Dashboard
                    </a>
                    
                    <div style="padding: 0.75rem 1.5rem; font-size: 0.8rem; font-weight: 600; color: var(--text-light); text-transform: uppercase; margin-top: 1rem;">
                        Input Data
                    </div>
                    @if(\App\Models\Setting::isEnabled('feature_produksi'))
                    <a href="/produksi/input" class="sidebar-item @if(request()->is('produksi/input')) active @endif">
                        <i class="fas fa-plus-circle"></i> Input Setor Susu
                    </a>
                    @endif
                    <a href="{{ route('kasbon.index') }}" class="sidebar-item @if(request()->is('kasbon*')) active @endif">
                        <i class="fas fa-minus-circle"></i> Input Potongan
                    </a>

                    <div style="padding: 0.75rem 1.5rem; font-size: 0.8rem; font-weight: 600; color: var(--text-light); text-transform: uppercase; margin-top: 1rem;">
                        Laporan
                    </div>
                    <a href="/produksi" class="sidebar-item @if(request()->is('produksi')) active @endif">
                        <i class="fas fa-history"></i> Riwayat Setor Susu
                    </a>
                    <a href="/gaji" class="sidebar-item @if(request()->is('gaji*')) active @endif">
                        <i class="fas fa-money-bill-wave"></i> Manajemen Gaji
                    </a>
                    <a href="{{ route('laporan.data') }}" class="sidebar-item @if(request()->is('laporan/data*')) active @endif">
                        <i class="fas fa-file-invoice"></i> Laporan Data
                    </a>

                    <div style="padding: 0.75rem 1.5rem; font-size: 0.8rem; font-weight: 600; color: var(--text-light); text-transform: uppercase; margin-top: 1rem;">
                        Master Data
                    </div>
                    <a href="{{ route('harga_susu.index') }}" class="sidebar-item @if(request()->is('harga-susu*')) active @endif">
                        <i class="fas fa-tags"></i> Harga Susu
                    </a>
                    <a href="{{ route('logistik.index') }}" class="sidebar-item @if(request()->is('logistik*')) active @endif">
                        <i class="fas fa-boxes"></i> Katalog Barang
                    </a>
                    <a href="{{ route('peternak.index') }}" class="sidebar-item @if(request()->is('peternak*')) active @endif">
                        <i class="fas fa-user-friends"></i> Data Mitra
                    </a>





                    <div style="padding: 0.75rem 1.5rem; font-size: 0.8rem; font-weight: 600; color: var(--text-light); text-transform: uppercase; margin-top: 1rem;">
                        Sistem
                    </div>

                    <a href="{{ route('bantuan.index') }}" class="sidebar-item @if(request()->is('system/bantuan*')) active @endif">
                        <i class="fas fa-question-circle"></i> Bantuan & Panduan
                    </a>

                    @if(auth()->user()->role === 'admin')
                    {{-- Kelola Pengguna moved to Settings --}}
                    <a href="{{ route('activity-log.index') }}" class="sidebar-item @if(request()->is('activity-log*')) active @endif">
                        <i class="fas fa-history"></i> Log Aktivitas
                    </a>
                    <a href="{{ route('settings.index') }}" class="sidebar-item @if(request()->is('settings')) @if(!request()->is('settings/users*')) active @endif @endif">
                        <i class="fas fa-cog"></i> Pengaturan Fitur
                    </a>
                    @endif
                @endif

                <!-- Mobile Logout Button -->
                <div class="mobile-logout d-md-none mt-4 border-top pt-4 px-3">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-danger w-100 py-2 fw-bold">
                            <i class="fas fa-sign-out-alt"></i> KELUAR APLIKASI
                        </button>
                    </form>
                </div>
                
                <div class="px-3 mt-4 text-center d-md-none">
                     <small class="text-muted" style="font-size: 0.7rem;">&copy; {{ date('Y') }} SIPERAH</small>
                </div>
            </aside>
        @endauth

        <!-- CONTENT -->
        <div class="content">
            @if(session('success'))
                <div style="background: #DCFCE7; color: #166534; padding: 1rem; border-radius: 8px; border: 1px solid #BBF7D0; margin-bottom: 1.5rem;">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div style="background: #FEE2E2; color: #991B1B; padding: 1rem; border-radius: 8px; border: 1px solid #FECACA; margin-bottom: 1.5rem;">
                    <ul style="margin: 0; padding-left: 1.2rem;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')

            <!-- FOOTER -->
            <footer class="footer">
                <div class="container">
                    &copy; {{ date('Y') }} SIPERAH. <span style="font-weight: 600;">GOGO Team - FRS</span>
                </div>
            </footer>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggle = document.getElementById('sidebarToggle');
            const sidebar = document.querySelector('.sidebar');
            
            if (toggle && sidebar) {
                toggle.addEventListener('click', function(e) {
                    sidebar.classList.toggle('show');
                    e.stopPropagation();
                });

                document.addEventListener('click', function(e) {
                    if (!sidebar.contains(e.target) && sidebar.classList.contains('show')) {
                        sidebar.classList.remove('show');
                    }
                });
            }
        });
    </script>
</body>
</html>