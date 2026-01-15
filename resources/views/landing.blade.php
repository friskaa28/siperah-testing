<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIPERAH - Kelola Usaha Sapi Perah Lebih Cerdas</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #2180D3;
            --primary-dark: #1A6DAA;
            --success: #22C55E;
            --warning: #F97316;
            --danger: #EF4444;
            --light: #F9FAFB;
            --white: #FFFFFF;
            --dark: #1F2937;
            --text-muted: #6B7280;
            --border: #E5E7EB;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--white);
            color: var(--dark);
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1.5rem;
        }

        /* ===== NAVBAR ===== */
        nav {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
            border-bottom: 1px solid var(--border);
            padding: 0.75rem 0;
        }

        .nav-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 1rem;
            text-decoration: none;
            font-weight: 700;
            color: var(--primary);
            font-size: 1.5rem;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            cursor: pointer;
            border: none;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
            box-shadow: 0 4px 14px 0 rgba(33, 128, 211, 0.3);
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(33, 128, 211, 0.4);
        }

        .btn-whatsapp {
            background: #25D366;
            color: white;
            box-shadow: 0 4px 14px 0 rgba(37, 211, 102, 0.3);
        }

        .btn-whatsapp:hover {
            background: #128C7E;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(37, 211, 102, 0.4);
        }

        /* ===== HERO ===== */
        .hero {
            padding: 180px 0 100px;
            background: linear-gradient(135deg, #f0f7ff 0%, #ffffff 100%);
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: -10%;
            right: -10%;
            width: 40%;
            height: 60%;
            background: radial-gradient(circle, rgba(33, 128, 211, 0.05) 0%, transparent 70%);
            z-index: 0;
        }

        .hero-content {
            display: grid;
            grid-template-columns: 1.2fr 1fr;
            gap: 4rem;
            align-items: center;
        }

        .hero-text h1 {
            font-size: 3.5rem;
            line-height: 1.2;
            margin-bottom: 1.5rem;
            color: var(--dark);
        }

        .hero-text h1 span {
            color: var(--primary);
        }

        .hero-text p {
            font-size: 1.25rem;
            color: var(--text-muted);
            margin-bottom: 2.5rem;
        }

        .hero-image img {
            width: 100%;
            border-radius: 20px;
            box-shadow: var(--shadow-lg);
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
            100% { transform: translateY(0px); }
        }

        /* ===== SECTIONS ===== */
        section {
            padding: 100px 0;
        }

        .section-title {
            text-align: center;
            margin-bottom: 4rem;
        }

        .section-title h2 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .section-title p {
            color: var(--text-muted);
            font-size: 1.1rem;
            max-width: 600px;
            margin: 0 auto;
        }

        /* ===== TIPS & TRIK ===== */
        .tips-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.5rem;
        }

        .tip-card {
            background: white;
            padding: 2rem;
            border-radius: 16px;
            border: 1px solid var(--border);
            transition: all 0.3s ease;
        }

        .tip-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow-lg);
            border-color: var(--primary);
        }

        .tip-icon {
            font-size: 2rem;
            margin-bottom: 1.5rem;
            color: var(--primary);
        }

        .tip-card h3 {
            margin-bottom: 1rem;
            font-size: 1.25rem;
        }

        /* ===== PELUANG USAHA ===== */
        .opportunities {
            background-color: var(--light);
        }

        .opp-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2.5rem;
        }

        .opp-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: var(--shadow-md);
            display: flex;
            flex-direction: column;
        }

        .opp-content {
            padding: 2rem;
        }

        .opp-card h3 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: var(--primary);
        }

        .opp-card ul {
            list-style: none;
        }

        .opp-card li {
            position: relative;
            padding-left: 1.5rem;
            margin-bottom: 0.75rem;
        }

        .opp-card li::before {
            content: '✓';
            position: absolute;
            left: 0;
            color: var(--success);
            font-weight: bold;
        }

        /* ===== AI SECTION ===== */
        .ai-section {
            background: linear-gradient(135deg, #ffffff 0%, #f0f9ff 100%);
            color: var(--dark);
            border: 1px solid var(--border);
            border-radius: 30px;
            margin: 0 1.5rem;
            padding: 80px 40px;
            position: relative;
            overflow: hidden;
            box-shadow: var(--shadow-lg);
        }

        .ai-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
        }

        .ai-text h2 {
            font-size: 2.5rem;
            margin-bottom: 1.5rem;
            color: var(--primary);
        }

        .ai-features {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }

        .ai-feature {
            background: #f1f5f9;
            padding: 1.5rem;
            border-radius: 12px;
            color: var(--dark);
            font-weight: 500;
        }

        /* ===== CTA SECTION ===== */
        .cta-section {
            text-align: center;
        }

        .cta-card {
            background: white;
            padding: 5rem 2rem;
            border-radius: 30px;
            border: 1px solid var(--border);
            box-shadow: var(--shadow-lg);
        }

        .cta-card h2 {
            font-size: 3rem;
            margin-bottom: 1.5rem;
        }

        .cta-buttons {
            display: flex;
            gap: 1.5rem;
            justify-content: center;
            margin-top: 3rem;
        }

        /* ===== FOOTER ===== */
        footer {
            padding: 80px 0 50px;
            background-color: white;
            border-top: 1px solid var(--border);
            text-align: center;
        }

        .sponsor-section {
            margin-bottom: 50px;
        }

        .sponsor-title {
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--text-muted);
            margin-bottom: 30px;
            font-weight: 600;
        }

        .sponsor-grid {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 4rem;
            flex-wrap: wrap;
            opacity: 0.7;
            transition: all 0.3s ease;
        }

        .sponsor-grid:hover {
            opacity: 1;
        }

        .sponsor-logo {
            height: 80px;
            filter: grayscale(100%);
            transition: all 0.3s ease;
            object-fit: contain;
        }

        .sponsor-logo:hover {
            filter: grayscale(0%);
            transform: scale(1.1);
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 968px) {
            .hero-content, .ai-content {
                grid-template-columns: 1fr;
                text-align: center;
                gap: 2rem;
            }
            .tips-grid, .opp-grid {
                grid-template-columns: 1fr;
            }
            .hero-text h1 { font-size: 2.2rem; }
            .hero-image { order: -1; }
            .hero-image img { max-width: 80%; margin: 0 auto; display: block; }
            .ai-features { grid-template-columns: 1fr; text-align: left; }
            .cta-buttons { flex-direction: column; }
            .nav-actions .btn { padding: 0.5rem 1rem; font-size: 0.9rem; }
            .logo { font-size: 1.2rem; }
            .ai-section { margin: 0 1rem; padding: 40px 20px; }
            .cta-card h2 { font-size: 2rem; }
        }
/* ... existing css ... */ 
        /* BOOK SECTION */
        .book-section {
            padding: 80px 0;
            background: #f8fafc;
            overflow: hidden;
        }

        .book-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 20px;
            margin-top: 40px;
            perspective: 2000px;
        }

        .open-book {
            position: relative;
            width: 900px;
            height: 550px;
            background: white;
            border-radius: 10px;
            box-shadow: 
                0 20px 50px rgba(0,0,0,0.15), 
                0 0 0 2px #e2e8f0; /* Border mimic */
            display: flex;
            overflow: hidden;
        }

        .book-spine {
            position: absolute;
            left: 50%;
            top: 0;
            bottom: 0;
            width: 4px;
            background: linear-gradient(to right, rgba(0,0,0,0.05), rgba(0,0,0,0.2) 50%, rgba(0,0,0,0.05));
            z-index: 10;
            transform: translateX(-50%);
        }

        .page {
            flex: 1;
            padding: 50px;
            position: relative;
            background: #fff;
            overflow-y: auto; /* Allow scroll if content long */
        }

        .left-page {
            background: linear-gradient(to right, #f8fafc 0%, #ffffff 20%);
            border-right: 1px solid #f1f5f9;
        }

        .right-page {
            background: linear-gradient(to left, #f8fafc 0%, #ffffff 20%);
        }

        /* Page Content Animation */
        .page-content {
            display: none;
            opacity: 0;
            transition: opacity 0.4s ease-in-out;
            height: 100%;
            flex-direction: column;
            justify-content: center;
        }

        .page-content.active {
            display: flex;
            opacity: 1;
        }

        .page-number {
            position: absolute;
            bottom: 20px;
            font-size: 0.9rem;
            color: var(--text-muted);
        }
        .left-page .page-number { left: 30px; }
        .right-page .page-number { right: 30px; }

        .nav-btn {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: white;
            border: 1px solid var(--border);
            color: var(--primary);
            font-size: 1.5rem;
            cursor: pointer;
            box-shadow: var(--shadow-md);
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 20;
        }

        .nav-btn:hover {
            background: var(--primary);
            color: white;
            transform: scale(1.1);
        }

        .guide-img {
            width: 100%;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            object-fit: cover;
            max-height: 300px;
        }

        .step-number {
            display: inline-block;
            width: 30px;
            height: 30px;
            background: var(--primary);
            color: white;
            border-radius: 50%;
            text-align: center;
            line-height: 30px;
            font-weight: bold;
            margin-bottom: 15px;
        }

        /* Mobile Responsive */
        @media (max-width: 968px) {
            .book-wrapper { flex-direction: column; }
            .open-book { width: 100%; height: auto; flex-direction: column; }
            .book-spine { display: none; }
            .left-page, .right-page { border: none; padding: 30px; background: white; min-height: 300px; }
            .nav-btn { position: static; margin-top: 10px; }
            .book-controls-mobile { display: flex; gap: 20px; }
        }
    </style>
</head>
<body>

    <nav>
        <div class="container nav-container">
            <a href="/" class="logo">
                <img src="{{ asset('img/logo-siperah.png') }}" alt="SIPERAH Logo" style="height: 48px;">
                SIPERAH
            </a>
            <div class="nav-actions">
                <a href="{{ route('login') }}" class="btn btn-primary">Login ke Aplikasi</a>
            </div>
        </div>
    </nav>

    <header class="hero">
        <div class="container hero-content">
            <div class="hero-text">
                <h1>Kelola Usaha <span>Lebih Pasti</span>, Pendapatan Lebih Transparan</h1>
                <p>SIPERAH hadir untuk membantu peternak sapi perah mengelola keuangan dan usaha secara mudah, rapi, dan transparan.</p>
                <div class="hero-actions">
                    <a href="{{ route('login') }}" class="btn btn-primary" style="padding: 1rem 2.5rem; font-size: 1.1rem;">Mulai Sekarang</a>
                </div>
            </div>
            <div class="hero-image">
                <img src="{{ asset('img/landing_hero_new_1768312452018.png') }}" alt="Modern Dairy Farm">
            </div>
        </div>
    </header>

    <section id="tips">
        <div class="container">
            <div class="section-title">
                <h2>Tips & Trik Keuangan Sehari-hari</h2>
                <p>Bisa langsung dipraktikkan untuk menjaga stabilitas ekonomi keluarga peternak.</p>
            </div>
            <div class="tips-grid">
                <div class="tip-card">
                    <div class="tip-icon">🧾</div>
                    <h3>Catat Pengeluaran Kecil</h3>
                    <p>Uang pakan, transport, kopi harian—semua memengaruhi keuangan. Catatan kecil membuat usaha lebih terkendali.</p>
                </div>
                <div class="tip-card">
                    <div class="tip-icon">🎯</div>
                    <h3>Target Mingguan</h3>
                    <p>Tentukan target mingguan, bukan hanya bulanan. Target kecil lebih mudah dicapai dan menjaga arus kas aman.</p>
                </div>
                <div class="tip-card">
                    <div class="tip-icon">💰</div>
                    <h3>Sisihkan di Awal</h3>
                    <p>Menabung dan dana darurat sebaiknya diambil saat menerima hasil, bukan menunggu sisa.</p>
                </div>
                <div class="tip-card">
                    <div class="tip-icon">📊</div>
                    <h3>Evaluasi dengan Data</h3>
                    <p>Gunakan angka untuk menilai usaha, bukan perasaan. Data membantu mengambil keputusan lebih tenang.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="opportunities">
        <div class="container">
            <div class="section-title">
                <h2>Jangan Bergantung pada Satu Sumber</h2>
                <p>Susu adalah utama, tapi bukan satu-satunya peluang. Diversifikasi untuk pendapatan berkelanjutan.</p>
            </div>
            <div class="opp-grid">
                <div class="opp-card">
                    <div class="opp-content">
                        <h3>🍦 Olahan Susu Bernilai Tambah</h3>
                        <p>Yogurt, susu pasteurisasi, atau produk sederhana yang bisa dikelola keluarga untuk meningkatkan nilai jual.</p>
                    </div>
                </div>
                <div class="opp-card">
                    <div class="opp-content">
                        <h3>🌱 Pemanfaatan Limbah Ternak</h3>
                        <p>Pupuk kandang, biogas, atau pakan fermentasi untuk menekan biaya produksi dan menambah pemasukan.</p>
                    </div>
                </div>
                <div class="opp-card">
                    <div class="opp-content">
                        <h3>🏠 Usaha Pendukung Rumah Tangga</h3>
                        <p>Warung kecil, usaha musiman, atau jasa lokal untuk menjaga stabilitas ekonomi keluarga di luar sektor utama.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- BOOK PANDUAN SECTION -->
    <section id="guidebook" class="book-section">
        <div class="container">
            <div class="section-title">
                <h2>📘 Buku Panduan Aplikasi</h2>
                <p>Panduan praktis menggunakan SIPERAH. Geser halaman untuk mempelajari fitur-fiturnya.</p>
            </div>
            
            <div class="book-wrapper">
                <button class="nav-btn prev-btn" onclick="changePage(-1)">❮</button>
                
                <div class="open-book">
                    <div class="book-spine d-none d-md-block"></div>
                    
                    <!-- LEFT PAGE -->
                    <div class="page left-page">
                        
                        <!-- Page 1 Left: Cover -->
                        <div class="page-content active" data-idx="0">
                            <div style="text-align: center; margin-top: 50px;">
                                <img src="{{ asset('img/logo-siperah.png') }}" alt="Logo" style="width: 100px; margin-bottom: 20px;">
                                <h3 style="font-size: 2rem; color: var(--primary); margin-bottom: 10px;">BUKU PANDUAN</h3>
                                <p style="font-size: 1.2rem; color: var(--text-muted);">Aplikasi SIPERAH v1.0</p>
                                <div style="margin-top: 40px; padding: 20px; background: #f0f9ff; border-radius: 10px; display: inline-block;">
                                    <p style="margin-bottom: 5px;"><strong>Daftar Isi:</strong></p>
                                    <ul style="list-style: none; text-align: left;">
                                        <li>1. Login & Dashboard</li>
                                        <li>2. Input Produksi</li>
                                        <li>3. Laporan Keuangan</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Page 2 Left: Login Image -->
                        <div class="page-content" data-idx="1">
                            <div class="step-number">1</div>
                            <h3 style="margin-bottom: 15px;">Login ke Aplikasi</h3>
                            <img src="{{ asset('img/ai_assistant_vector.png') }}" class="guide-img" alt="Guide Image" style="object-position: top; max-height: 200px;">
                            <p class="small text-muted">Halaman login aman dan mudah.</p>
                        </div>

                        <!-- Page 3 Left: Input Image -->
                        <div class="page-content" data-idx="2">
                            <div class="step-number">2</div>
                            <h3 style="margin-bottom: 15px;">Input Produksi Harian</h3>
                            <img src="{{ asset('img/ai_assistant_vector.png') }}" class="guide-img" alt="Guide Image" style="object-position: top; max-height: 200px;">
                            <p class="small text-muted">Formulir digital pengganti kertas.</p>
                        </div>

                        <!-- Page 4 Left: Report Image -->
                        <div class="page-content" data-idx="3">
                            <div class="step-number">3</div>
                            <h3 style="margin-bottom: 15px;">Laporan Transparan</h3>
                            <img src="{{ asset('img/ai_assistant_vector.png') }}" class="guide-img" alt="Guide Image" style="object-position: top; max-height: 200px;">
                            <p class="small text-muted">Semua data tercatat otomatis.</p>
                        </div>

                        <div class="page-number">Halaman Kiri</div>
                    </div>
                    
                    <!-- RIGHT PAGE -->
                    <div class="page right-page">
                        
                        <!-- Page 1 Right: Intro -->
                        <div class="page-content active" data-idx="0">
                            <div style="display: flex; flex-direction: column; justify-content: center; height: 100%;">
                                <h4 style="margin-bottom: 20px;">Selamat Datang! 👋</h4>
                                <p>Buku panduan ini akan membantu Anda memahami cara menggunakan aplikasi SIPERAH dengan maksimal.</p>
                                <p>Aplikasi ini dirancang untuk memudahkan:</p>
                                <ul style="margin-left: 20px; margin-bottom: 30px;">
                                    <li>Pencatatan produksi susu harian.</li>
                                    <li>Pemantauan pendapatan secara real-time.</li>
                                    <li>Pengelolaan kasbon dan logistik.</li>
                                </ul>
                                <p class="text-muted small"><em>Klik tombol panah di samping untuk membuka halaman selanjutnya &raquo;</em></p>
                            </div>
                        </div>

                        <!-- Page 2 Right: Login Steps -->
                        <div class="page-content" data-idx="1">
                            <h4>Cara Masuk Akun:</h4>
                            <p>Untuk menjaga keamanan data, setiap peternak memiliki akun pribadi.</p>
                            <ol style="margin-left: 20px; margin-top: 15px; margin-bottom: 20px;">
                                <li style="margin-bottom: 10px;">Buka halaman utama aplikasi.</li>
                                <li style="margin-bottom: 10px;">Klik tombol <strong>"Login ke Aplikasi"</strong> di pojok kanan atas.</li>
                                <li style="margin-bottom: 10px;">Masukkan <strong>Email/No HP</strong> dan <strong>Password</strong> yang telah diberikan oleh pengelola.</li>
                                <li>Klik <strong>Masuk</strong>.</li>
                            </ol>
                            <div class="alert" style="background: #fff3cd; color: #856404; padding: 10px; border-radius: 5px; font-size: 0.9rem;">
                                💡 <strong>Tips:</strong> Jangan berikan password Anda kepada orang lain.
                            </div>
                        </div>

                        <!-- Page 3 Right: Input Steps -->
                        <div class="page-content" data-idx="2">
                            <h4>Mencatat Setoran Susu:</h4>
                            <p>Anda tidak perlu lagi mencatat di kertas yang mudah hilang.</p>
                            <ul style="margin-left: 20px; margin-top: 15px; margin-bottom: 20px;">
                                <li style="margin-bottom: 10px;">Setelah login, pengelola akan menginput data setoran Pagi & Sore.</li>
                                <li style="margin-bottom: 10px;">Anda akan menerima notifikasi otomatis via WhatsApp setiap kali data diinput.</li>
                                <li>Pastikan jumlah liter yang diinput sesuai dengan timbangan.</li>
                            </ul>
                            <p>Data yang diinput hari ini akan langsung masuk ke perhitungan gaji bulanan.</p>
                        </div>

                        <!-- Page 4 Right: Report Steps -->
                        <div class="page-content" data-idx="3">
                            <h4>Cek Gaji & Laporan:</h4>
                            <p>Transparansi adalah kunci kemitraan yang sehat.</p>
                            <ul style="margin-left: 20px; margin-top: 15px;">
                                <li style="margin-bottom: 10px;">Menu <strong>Laporan</strong> menampilkan grafik pendapatan Anda bulan ini.</li>
                                <li style="margin-bottom: 10px;">Menu <strong>Slip Gaji</strong> menampilkan rincian total susu, potongan pakan/kasbon, dan uang bersih yang diterima.</li>
                                <li>Anda bisa mengunduh Laporan dalam bentuk PDF untuk pengajuan kredit ke bank atau koperasi.</li>
                            </ul>
                            <div style="margin-top: 30px; text-align: center;">
                                <a href="{{ route('login') }}" class="btn btn-primary">Coba SIPERAH Sekarang</a>
                            </div>
                        </div>

                        <div class="page-number">Halaman Kanan</div>
                    </div>
                </div>

                <button class="nav-btn next-btn" onclick="changePage(1)">❯</button>
            </div>
            

        </div>
    </section>

    <section class="cta-section">
        <div class="container">
            <div class="cta-card">
                <h2>Saatnya Peternak Naik Kelas🚀</h2>
                <p style="font-size: 1.25rem; color: var(--text-muted); max-width: 800px; margin: 0 auto 3rem;">Dengan pencatatan yang baik dan strategi usaha yang tepat, peternak tidak hanya bekerja keras—tetapi bekerja cerdas.</p>
                
                <p style="font-weight: 700; color: var(--dark); margin-bottom: 2rem;">Mulai Sekarang, Data Anda Lebih Terkendali 🔐</p>
                
                <div class="cta-buttons">
                    <a href="{{ route('login') }}" class="btn btn-primary" style="padding: 1.25rem 3rem; font-size: 1.2rem;">
                        Login ke SIPERAH Sekarang
                    </a>
                    <a href="https://wa.me/6281318271630?text=Halo%20SIPERAH%2C%20saya%20butuh%20bantuan%20untuk%20diskusi%20rencana%20usaha%20saya." class="btn btn-whatsapp" style="padding: 1.25rem 3rem; font-size: 1.2rem;" target="_blank">
                        Bantuan via WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </section>

    <footer>
        <div class="container">
            <div class="sponsor-section">
                <p class="sponsor-title">Didukung & Disponsori Oleh</p>
                <div class="sponsor-grid">
                    <img src="{{ asset('img/sponsor_innovillage.png') }}" alt="Innovillage" class="sponsor-logo">
                    <img src="{{ asset('img/sponsor_danantara.png') }}" alt="Danantara" class="sponsor-logo">
                    <img src="{{ asset('img/sponsor_telkom_univ.png') }}" alt="Telkom University" class="sponsor-logo">
                    <img src="{{ asset('img/sponsor_telkom_id.png') }}" alt="Telkom Indonesia" class="sponsor-logo">
                </div>
            </div>
            
            <p style="color: var(--text-muted);">&copy; {{ date('Y') }} SIPERAH. <span style="font-weight: 600; color: var(--primary);">GOGO Team - FRS</span></p>
            <p style="font-size: 0.8rem; margin-top: 10px; color: var(--text-muted);">Peternak Berdaya, Ekonomi Keluarga Terjaga</p>
        </div>
    </footer>


    <script>
        let currentPage = 0;
        const totalPages = 4; // Total slides pairs

        function changePage(direction) {
            const next = currentPage + direction;
            
            if (next >= 0 && next < totalPages) {
                // Fade out current
                updatePageContent(currentPage, false);
                
                // Update index
                currentPage = next;
                
                // Fade in new
                setTimeout(() => {
                    updatePageContent(currentPage, true);
                }, 400); // Wait for fade out
            }
        }

        function updatePageContent(idx, isActive) {
            const leftContents = document.querySelectorAll('.left-page .page-content');
            const rightContents = document.querySelectorAll('.right-page .page-content');
            
            if (isActive) {
                leftContents.forEach(el => el.classList.remove('active'));
                rightContents.forEach(el => el.classList.remove('active'));
                
                leftContents[idx].classList.add('active');
                rightContents[idx].classList.add('active');
            } else {
                leftContents[idx].classList.remove('active');
                rightContents[idx].classList.remove('active');
            }
        }
    </script>
</body>
</html>
