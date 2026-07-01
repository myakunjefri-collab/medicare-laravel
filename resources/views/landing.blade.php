<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>MedicareSystem - Solusi Kesehatan Digital</title>
    <meta name="description" content="MedicareSystem menyediakan layanan konsultasi dokter spesialis, cek jadwal praktik, rekam medis digital, dan antrean online.">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Outfit Font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
            overflow-x: hidden;
            scroll-behavior: smooth;
        }
        
        /* Navbar */
        .navbar {
            position: fixed;
            top: 0;
            width: 100%;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            padding: 18px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 1000;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.03);
            border-bottom: 1px solid rgba(226, 232, 240, 0.8);
            transition: all 0.3s ease;
        }
        .logo {
            font-size: 1.4rem;
            font-weight: 800;
            color: #0b2b4d;
            display: flex;
            align-items: center;
            gap: 10px;
            letter-spacing: -0.5px;
        }
        .logo i {
            color: #2b9e6e;
            background: rgba(43, 158, 110, 0.1);
            padding: 8px;
            border-radius: 50%;
            font-size: 1.1rem;
        }
        .nav-links {
            display: flex;
            align-items: center;
            gap: 30px;
        }
        .nav-links a {
            text-decoration: none;
            color: #475569;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }
        .nav-links a:hover {
            color: #2b9e6e;
        }
        .btn-login {
            background: linear-gradient(135deg, #2b9e6e, #228058);
            color: white !important;
            padding: 10px 24px;
            border-radius: 30px;
            box-shadow: 0 4px 12px rgba(43, 158, 110, 0.2);
            transition: all 0.3s ease;
        }
        .btn-login:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(43, 158, 110, 0.35);
        }
        
        /* Hero Section */
        .hero {
            min-height: 100vh;
            background: radial-gradient(circle at 10% 20%, rgba(11, 43, 77, 1) 0%, rgba(26, 109, 143, 1) 90.2%);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 120px 8% 80px;
            color: white;
            position: relative;
            overflow: hidden;
            gap: 40px;
        }
        .hero-content {
            flex: 1.2;
            z-index: 10;
        }
        .hero-content h1 {
            font-size: 3.2rem;
            line-height: 1.15;
            font-weight: 800;
            margin-bottom: 24px;
            letter-spacing: -1px;
        }
        .hero-content p {
            font-size: 1.15rem;
            margin-bottom: 36px;
            opacity: 0.9;
            max-width: 580px;
            line-height: 1.6;
        }
        .hero-buttons {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
        }
        .btn-primary {
            background: linear-gradient(135deg, #2b9e6e, #228058);
            color: white;
            padding: 14px 32px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 700;
            font-size: 1rem;
            box-shadow: 0 10px 20px rgba(43, 158, 110, 0.3);
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 26px rgba(43, 158, 110, 0.45);
        }
        .btn-outline {
            border: 2px solid white;
            color: white;
            padding: 12px 30px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 700;
            font-size: 1rem;
            transition: all 0.3s ease;
            backdrop-filter: blur(4px);
            background: rgba(255, 255, 255, 0.05);
        }
        .btn-outline:hover {
            background: white;
            color: #0b2b4d;
            transform: translateY(-2px);
        }
        .hero-image {
            flex: 0.8;
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 10;
        }
        .hero-image i {
            font-size: 12rem;
            color: rgba(255,255,255,0.08);
            animation: float 4s ease-in-out infinite;
            filter: drop-shadow(0 20px 40px rgba(0,0,0,0.3));
        }
        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(3deg); }
        }
        
        /* Features Section */
        .features {
            padding: 100px 8%;
            background: #f8fafc;
            text-align: center;
        }
        .section-title {
            font-size: 2.2rem;
            color: #0b2b4d;
            margin-bottom: 50px;
            font-weight: 800;
            letter-spacing: -0.5px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
        }
        .section-title i {
            color: #2b9e6e;
        }
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 32px;
            max-width: 1200px;
            margin: 0 auto;
        }
        .feature-card {
            background: white;
            padding: 40px 30px;
            border-radius: 24px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.02);
            border: 1px solid #e2e8f0;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            text-align: left;
        }
        .feature-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(11, 43, 77, 0.06);
            border-color: rgba(43, 158, 110, 0.3);
        }
        .feature-card .icon-box {
            background: rgba(43, 158, 110, 0.1);
            color: #2b9e6e;
            width: 60px;
            height: 60px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            margin-bottom: 24px;
        }
        .feature-card h3 {
            font-size: 1.25rem;
            margin-bottom: 12px;
            color: #0b2b4d;
            font-weight: 700;
        }
        .feature-card p {
            font-size: 0.95rem;
            color: #64748b;
            line-height: 1.6;
        }
        
        /* Doctors Section */
        .doctors {
            padding: 100px 8%;
            background: white;
            text-align: center;
        }
        .doctors-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 24px;
            max-width: 1200px;
            margin: 0 auto;
        }
        .doctor-card {
            background: #f8fafc;
            padding: 30px 20px;
            border-radius: 24px;
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
        }
        .doctor-card:hover {
            transform: translateY(-5px);
            background: #e8f5f0;
            border-color: rgba(43, 158, 110, 0.3);
        }
        .doctor-card .avatar-box {
            background: rgba(43, 158, 110, 0.1);
            color: #2b9e6e;
            width: 70px;
            height: 70px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.2rem;
            margin: 0 auto 16px;
        }
        .doctor-card h4 {
            font-size: 1rem;
            margin-bottom: 6px;
            color: #0b2b4d;
            font-weight: 700;
        }
        .doctor-card p {
            font-size: 0.8rem;
            color: #2b9e6e;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        /* BPS Section */
        .bps-section {
            padding: 100px 8%;
            background: linear-gradient(135deg, #e8f5f0 0%, #d4ede4 100%);
            text-align: center;
        }
        .bps-title {
            font-size: 2.2rem;
            color: #0b2b4d;
            font-weight: 800;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }
        .bps-subtitle {
            color: #475569;
            margin-bottom: 50px;
            font-size: 1rem;
            font-weight: 500;
        }
        .bps-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 24px;
            max-width: 1200px;
            margin: 0 auto;
        }
        .bps-card {
            background: white;
            border-radius: 24px;
            padding: 30px 20px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(11, 43, 77, 0.03);
            border: 1px solid rgba(226, 232, 240, 0.8);
            transition: all 0.3s ease;
        }
        .bps-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(11, 43, 77, 0.08);
        }
        .bps-card i {
            font-size: 2.2rem;
            color: #2b9e6e;
            background: rgba(43, 158, 110, 0.08);
            padding: 12px;
            border-radius: 16px;
            margin-bottom: 16px;
            display: inline-block;
        }
        .bps-card .nilai {
            font-size: 2rem;
            font-weight: 800;
            color: #0b2b4d;
            margin: 10px 0;
            letter-spacing: -1px;
        }
        .bps-card h3 {
            font-size: 0.95rem;
            color: #334155;
            margin-bottom: 6px;
            font-weight: 700;
        }
        .bps-card p {
            color: #64748b;
            font-size: 0.8rem;
            font-weight: 500;
        }
        .bps-card small {
            display: block;
            margin-top: 10px;
            font-size: 0.7rem;
            color: #94a3b8;
            border-top: 1px solid #f1f5f9;
            padding-top: 10px;
        }
        .bps-info {
            margin-top: 40px;
            font-size: 0.85rem;
            color: #475569;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        /* CTA Section */
        .cta {
            padding: 80px 8%;
            background: linear-gradient(135deg, #2b9e6e, #228058);
            text-align: center;
            color: white;
            position: relative;
        }
        .cta h2 {
            font-size: 2.2rem;
            margin-bottom: 16px;
            font-weight: 800;
            letter-spacing: -0.5px;
        }
        .cta p {
            font-size: 1.1rem;
            margin-bottom: 30px;
            opacity: 0.9;
        }
        .cta .btn-primary {
            background: white;
            color: #2b9e6e;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
        }
        .cta .btn-primary:hover {
            background: #f8fafc;
            transform: translateY(-2px);
        }
        
        /* Footer */
        footer {
            background: #0b1d2e;
            color: #94a3b8;
            text-align: center;
            padding: 50px 20px;
            border-top: 1px solid #1e293b;
        }
        footer p {
            margin-top: 10px;
            font-size: 0.85rem;
        }
        
        /* Modal Style for Landing Page */
        .modal {
            display: none;
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(8, 18, 37, 0.6);
            z-index: 2000;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(6px);
            animation: fadeIn 0.3s ease;
        }
        .modal-content {
            background: #ffffff;
            border-radius: 30px;
            padding: 40px;
            width: 90%;
            max-width: 600px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            border: 1px solid #e2e8f0;
            animation: scaleUp 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes scaleUp {
            from { transform: scale(0.95); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }
        .berita-card:hover {
            transform: translateY(-8px);
            border-color: rgba(43, 158, 110, 0.35) !important;
            box-shadow: 0 20px 40px rgba(11, 43, 77, 0.04) !important;
        }
        .berita-card:hover img {
            transform: scale(1.05);
        }

        /* Mobile Menu Toggle */
        .menu-toggle {
            display: none;
            font-size: 1.6rem;
            cursor: pointer;
            color: #0b2b4d;
        }
        
        /* Responsive */
        @media (max-width: 992px) {
            .hero {
                flex-direction: column;
                text-align: center;
                padding-top: 140px;
            }
            .hero-content h1 {
                font-size: 2.5rem;
            }
            .hero-content p {
                margin: 0 auto 30px;
            }
            .hero-buttons {
                justify-content: center;
            }
            .features-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .bps-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        @media (max-width: 768px) {
            .navbar {
                padding: 15px 24px;
            }
            .nav-links {
                display: none;
                position: absolute;
                top: 70px;
                left: 0;
                right: 0;
                background: white;
                flex-direction: column;
                padding: 30px 20px;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
                border-bottom: 1px solid #e2e8f0;
                gap: 20px;
            }
            .nav-links.active {
                display: flex;
            }
            .menu-toggle {
                display: block;
            }
            .features-grid {
                grid-template-columns: 1fr;
            }
            .bps-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <div class="navbar">
        <div class="logo">
            <i class="fas fa-notes-medical"></i> MedicareSystem
        </div>
        <div class="menu-toggle" onclick="toggleMenu()">
            <i class="fas fa-bars"></i>
        </div>
        <div class="nav-links" id="navLinks">
            <a href="#home" onclick="closeMenu()">Beranda</a>
            <a href="#features" onclick="closeMenu()">Fitur</a>
            <a href="#doctors" onclick="closeMenu()">Dokter</a>
            <a href="#bps-data" onclick="closeMenu()">Statistik</a>
            <a href="#berita" onclick="closeMenu()">Berita</a>
            <a href="/login" class="btn-login"><i class="fas fa-sign-in-alt"></i> Masuk / Daftar</a>
        </div>
    </div>

    <!-- Hero -->
    <div class="hero" id="home">
        <div class="hero-content">
            <h1>Kesehatan Digital <br><span style="color:#3dbe8a;">Dalam Genggamanmu</span></h1>
            <p>Konsultasi dengan dokter spesialis pilihan, kelola jadwal praktis, lihat riwayat rekam medis, dan dapatkan nomor antrean faskes secara online.</p>
            <div class="hero-buttons">
                <a href="/login" class="btn-primary">Mulai Sekarang</a>
                <a href="#features" class="btn-outline">Pelajari Lebih Lanjut</a>
            </div>
        </div>
        <div class="hero-image">
            <i class="fas fa-notes-medical"></i>
        </div>
    </div>

    <!-- Features -->
    <div class="features" id="features">
        <h2 class="section-title"><i class="fas fa-star"></i> Layanan Unggulan</h2>
        <div class="features-grid">
            <div class="feature-card">
                <div class="icon-box"><i class="fas fa-calendar-alt"></i></div>
                <h3>Jadwal Praktik Dokter</h3>
                <p>Cek jadwal praktik lengkap dokter spesialis favorit Anda secara real-time dan terintegrasi kalender.</p>
            </div>
            <div class="feature-card">
                <div class="icon-box"><i class="fas fa-comments"></i></div>
                <h3>Konsultasi Chat</h3>
                <p>Konsultasi interaktif langsung bersama dokter. Mudah, nyaman, dan privasi medis Anda terjamin aman.</p>
            </div>
            <div class="feature-card">
                <div class="icon-box"><i class="fas fa-notes-medical"></i></div>
                <h3>Rekam Medis Digital</h3>
                <p>Akses riwayat diagnosa dan resep obat digital kapan saja. Semuanya tercatat otomatis dalam satu dashboard.</p>
            </div>
            <div class="feature-card">
                <div class="icon-box"><i class="fas fa-ticket-alt"></i></div>
                <h3>Antrean Online Efisien</h3>
                <p>Dapatkan tiket antrean rujukan instan dengan nomor antrean terbit otomatis dari diagnosa dokter.</p>
            </div>
        </div>
    </div>

    <!-- Doctors Showcase -->
    <div class="doctors" id="doctors">
        <h2 class="section-title"><i class="fas fa-user-md"></i> Tim Dokter Spesialis</h2>
        <div class="doctors-grid">
            @forelse($doctors as $doc)
                <div class="doctor-card">
                    <div class="avatar-box">
                        @if($doc->spesialis === 'Jantung')
                            <i class="fas fa-heartbeat"></i>
                        @elseif($doc->spesialis === 'Anak')
                            <i class="fas fa-baby"></i>
                        @elseif($doc->spesialis === 'Mata')
                            <i class="fas fa-eye"></i>
                        @elseif($doc->spesialis === 'Saraf')
                            <i class="fas fa-brain"></i>
                        @elseif($doc->spesialis === 'Gigi')
                            <i class="fas fa-tooth"></i>
                        @else
                            <i class="fas fa-user-md"></i>
                        @endif
                    </div>
                    <h4>{{ $doc->name }}</h4>
                    <p>{{ $doc->spesialis }}</p>
                </div>
            @empty
                <div class="doctor-card"><div class="avatar-box"><i class="fas fa-heartbeat"></i></div><h4>dr. Andi Wijaya</h4><p>Penyakit Dalam</p></div>
                <div class="doctor-card"><div class="avatar-box"><i class="fas fa-baby"></i></div><h4>dr. Sarah Melati</h4><p>Anak</p></div>
                <div class="doctor-card"><div class="avatar-box"><i class="fas fa-heart"></i></div><h4>dr. Budi Hartono</h4><p>Jantung</p></div>
                <div class="doctor-card"><div class="avatar-box"><i class="fas fa-eye"></i></div><h4>dr. Dewi Anggraeni</h4><p>Mata</p></div>
                <div class="doctor-card"><div class="avatar-box"><i class="fas fa-brain"></i></div><h4>dr. Rizky</h4><p>Saraf</p></div>
                <div class="doctor-card"><div class="avatar-box"><i class="fas fa-tooth"></i></div><h4>drg. Rina</h4><p>Gigi</p></div>
            @endforelse
        </div>
    </div>

    <!-- BPS Health Data Statistik -->
    <div class="bps-section" id="bps-data">
        <h2 class="bps-title"><i class="fas fa-hospital-user"></i> Statistik Kesehatan Nasional</h2>
        <p class="bps-subtitle">Indikator Fasilitas Kesehatan & Tenaga Medis Indonesia terbaru.</p>
        
        <div class="bps-grid">
            @foreach($health_data as $item)
                <div class="bps-card">
                    <i class="{{ $item['icon'] }}"></i>
                    <div class="nilai">{{ $item['nilai'] }}</div>
                    <h3>{{ $item['nama'] }}</h3>
                    <p>Tahun {{ $item['tahun'] }}</p>
                    @if(isset($item['keterangan']))
                        <small>{{ $item['keterangan'] }}</small>
                    @endif
                </div>
            @endforeach
        </div>
        
        <div class="bps-info">
            <i class="fas fa-database"></i> Sumber Data: BPS & Kementerian Kesehatan RI
        </div>
    </div>

    <!-- Sebaran Penyakit Wilayah Section -->
    <div class="bps-section" id="penyakit-wilayah" style="background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%); border-top: 1px solid rgba(226, 232, 240, 0.8);">
        <h2 class="bps-title"><i class="fas fa-virus-slash"></i> Sebaran Kasus Penyakit Daerah</h2>
        <p class="bps-subtitle">Pilih wilayah untuk memantau data penyakit dan tren kesehatan ter-update.</p>
        
        <div style="margin-bottom: 30px; display: inline-block;">
            <label for="selectWilayah" style="font-weight: 700; color: #0b2b4d; margin-right: 12px; font-size: 1.1rem;"><i class="fas fa-map-marker-alt" style="color: #2b9e6e;"></i> Pilih Provinsi:</label>
            <select id="selectWilayah" onchange="loadPenyakitWilayah(this.value)" style="padding: 12px 24px; font-size: 1rem; font-weight: 600; border-radius: 30px; border: 2px solid #2b9e6e; background: white; color: #0b2b4d; cursor: pointer; box-shadow: 0 4px 10px rgba(0,0,0,0.05); transition: all 0.3s; outline: none;">
                @foreach($provinces as $prov)
                    <option value="{{ $prov }}" {{ $prov === 'Jawa Timur' ? 'selected' : '' }}>{{ $prov }}</option>
                @endforeach
            </select>
        </div>
        
        <div class="bps-grid" id="penyakitGrid">
            <!-- Will be populated dynamically via javascript -->
        </div>
    </div>

    <!-- Berita & Informasi Kesehatan -->
    <div class="features" id="berita" style="background: white;">
        <h2 class="section-title"><i class="fas fa-newspaper"></i> Berita & Informasi Kesehatan</h2>
        <p class="features-subtitle" style="color: #64748b; margin-bottom: 50px;">Dapatkan tips kesehatan terpercaya dan info terbaru dari MedicareSystem.</p>
        
        <div class="features-grid">
            @forelse($berita as $article)
                <div class="feature-card berita-card" style="display: flex; flex-direction: column; justify-content: space-between; border-radius: 24px; padding: 0; overflow: hidden; background: white; border: 1px solid #e2e8f0; transition: all 0.3s;">
                    @if($article->gambar)
                        <div style="width: 100%; height: 180px; overflow: hidden;">
                            <img src="{{ asset('storage/' . $article->gambar) }}" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s;">
                        </div>
                    @else
                        <div style="width: 100%; height: 180px; background: rgba(43, 158, 110, 0.04); display: flex; align-items: center; justify-content: center; color: #2b9e6e;">
                            <i class="fas fa-newspaper" style="font-size: 3rem;"></i>
                        </div>
                    @endif
                    
                    <div style="padding: 24px; text-align: left; flex: 1; display: flex; flex-direction: column; justify-content: space-between;">
                        <div>
                            <div style="display: flex; align-items: center; gap: 6px; font-size: 0.75rem; color: #94a3b8; font-weight: 600; margin-bottom: 10px;">
                                <i class="far fa-calendar-alt" style="color: #2b9e6e;"></i>
                                <span>{{ $article->created_at->format('d M Y') }}</span>
                            </div>
                            <h3 style="font-size: 1.15rem; color: #0b2b4d; font-weight: 700; line-height: 1.4; margin-bottom: 10px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">{{ $article->judul }}</h3>
                            <p style="font-size: 0.85rem; color: #64748b; line-height: 1.6; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; margin-bottom: 20px;">{{ Str::limit($article->konten, 150) }}</p>
                        </div>
                        
                        <a href="javascript:void(0)" onclick="bukaModalBerita('{{ addslashes($article->judul) }}', '{{ $article->created_at->format('d M Y') }}', '{{ addslashes($article->konten) }}', '{{ $article->gambar ? asset('storage/' . $article->gambar) : '' }}')" style="color: #2b9e6e; font-weight: 700; text-decoration: none; font-size: 0.9rem; display: inline-flex; align-items: center; gap: 6px; transition: gap 0.2s;">Baca Selengkapnya <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
            @empty
                <div style="grid-column: 1/-1; text-align: center; padding: 60px; color: #64748b;">
                    <i class="fas fa-folder-open" style="font-size: 3rem; color: #cbd5e1; margin-bottom: 12px;"></i>
                    <p>Belum ada berita yang diterbitkan saat ini.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- CTA -->
    <div class="cta">
        <h2>Ingin Konsultasi Praktis Tanpa Antre?</h2>
        <p>Gabung bersama ribuan pasien lainnya dan akses layanan kesehatan digital premium sekarang.</p>
        <a href="/login" class="btn-primary">Daftar Akun Baru</a>
    </div>

    <!-- Footer -->
    <footer>
        <p><i class="fas fa-notes-medical"></i> <strong>MedicareSystem</strong> — Solusi Kesehatan Digital Terpercaya</p>
        <p>Gedung Medicare Lantai 4, Jl. Teknologi Kesehatan No. 45, Jakarta Selatan</p>
        <p>&copy; 2026 MedicareSystem. Hak Cipta Dilindungi.</p>
    </footer>

    <!-- Berita Modal -->
    <div id="modalBerita" class="modal" style="display: none;">
        <div class="modal-content" style="max-height: 85vh; display: flex; flex-direction: column; overflow: hidden; width: 90%; max-width: 600px;">
            <div style="display: flex; align-items: center; gap: 8px; font-size: 0.8rem; color: #2b9e6e; font-weight: 600; margin-bottom: 8px;">
                <i class="far fa-calendar-alt"></i>
                <span id="modalBeritaTanggal"></span>
            </div>
            <h3 id="modalBeritaJudul" style="font-size: 1.5rem; color: #0b2b4d; font-weight: 800; margin-bottom: 16px; line-height: 1.3;"></h3>
            
            <div id="modalBeritaGambarDiv" style="width: 100%; height: 220px; overflow: hidden; border-radius: 16px; margin-bottom: 16px; display: none;">
                <img id="modalBeritaGambar" src="" style="width: 100%; height: 100%; object-fit: cover;">
            </div>

            <div id="modalBeritaKonten" style="font-size: 0.95rem; color: #475569; line-height: 1.7; overflow-y: auto; padding-right: 8px; margin-bottom: 24px; white-space: pre-line; text-align: left; flex: 1;"></div>
            <div style="display: flex; justify-content: flex-end;">
                <button type="button" onclick="tutupModalBerita()" class="btn-primary" style="border: none; cursor: pointer; padding: 10px 24px; border-radius: 30px; font-weight: 700; background: linear-gradient(135deg, #2b9e6e, #228058); color: white;">Tutup</button>
            </div>
        </div>
    </div>

    <script>
        function toggleMenu() {
            document.getElementById('navLinks').classList.toggle('active');
        }
        function closeMenu() {
            document.getElementById('navLinks').classList.remove('active');
        }
        function bukaModalBerita(judul, tanggal, konten, gambarUrl) {
            document.getElementById('modalBeritaJudul').innerHTML = judul;
            document.getElementById('modalBeritaTanggal').innerHTML = tanggal;
            document.getElementById('modalBeritaKonten').innerHTML = konten;
            
            const gDiv = document.getElementById('modalBeritaGambarDiv');
            const gImg = document.getElementById('modalBeritaGambar');
            if (gambarUrl) {
                gImg.src = gambarUrl;
                gDiv.style.display = 'block';
            } else {
                gImg.src = '';
                gDiv.style.display = 'none';
            }
            document.getElementById('modalBerita').style.display = 'flex';
        }
        function tutupModalBerita() {
            document.getElementById('modalBerita').style.display = 'none';
        }

        function loadPenyakitWilayah(provinsi) {
            const grid = document.getElementById('penyakitGrid');
            grid.innerHTML = '<div style="grid-column: 1/-1; text-align: center; padding: 40px; color: #64748b;"><i class="fas fa-spinner fa-spin" style="font-size: 2rem; color: #2b9e6e; margin-bottom: 10px;"></i><p>Memuat data sebaran...</p></div>';
            
            fetch('/api/penyakit-wilayah')
                .then(response => response.json())
                .then(res => {
                    if (res.status === 'success' && res.data[provinsi]) {
                        const items = res.data[provinsi];
                        let html = '';
                        items.forEach(item => {
                            let trendBadge = '';
                            if (item.tren === 'naik') {
                                trendBadge = '<span style="display:inline-flex; align-items:center; gap:6px; padding:4px 12px; font-size:0.75rem; background:#fee2e2; color:#b91c1c; border-radius:9999px; font-weight:700; border:1px solid #fca5a5;"><i class="fas fa-arrow-trend-up"></i> Naik</span>';
                            } else if (item.tren === 'turun') {
                                trendBadge = '<span style="display:inline-flex; align-items:center; gap:6px; padding:4px 12px; font-size:0.75rem; background:#dcfce7; color:#15803d; border-radius:9999px; font-weight:700; border:1px solid #86efac;"><i class="fas fa-arrow-trend-down"></i> Turun</span>';
                            } else {
                                trendBadge = '<span style="display:inline-flex; align-items:center; gap:6px; padding:4px 12px; font-size:0.75rem; background:#f1f5f9; color:#475569; border-radius:9999px; font-weight:700; border:1px solid #cbd5e1;"><i class="fas fa-equals"></i> Tetap</span>';
                            }
                            
                            html += `
                                <div class="bps-card" style="text-align: left; display: flex; flex-direction: column; justify-content: space-between; border-radius: 24px; padding: 28px 24px; background: white; box-shadow: 0 10px 30px rgba(0,0,0,0.02); border: 1px solid #e2e8f0; transition: all 0.3s; position: relative;">
                                    <div>
                                        <div style="display:flex; justify-content:space-between; align-items:center; width: 100%;">
                                            <div style="background: rgba(43, 158, 110, 0.08); width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #2b9e6e; font-size: 1.5rem;">
                                                <i class="${item.icon}"></i>
                                            </div>
                                            <div>${trendBadge}</div>
                                        </div>
                                        <div class="nilai" style="font-size: 1.9rem; font-weight: 800; color: #0b2b4d; margin: 20px 0 6px 0; letter-spacing: -0.5px;">${item.kasus.toLocaleString('id-ID')}</div>
                                        <h3 style="font-size: 1.1rem; color: #0b2b4d; font-weight: 700; line-height: 1.3; margin-bottom: 6px;">${item.penyakit}</h3>
                                        <p style="font-size: 0.85rem; color: #64748b; margin: 0; line-height: 1.5;">Jumlah kasus terlaporkan di wilayah ini.</p>
                                    </div>
                                    <small style="margin-top: 20px; font-size: 0.75rem; color: #94a3b8; border-top: 1px solid #f1f5f9; padding-top: 12px; display: block; font-weight: 500;">Tahun 2026 | Sumber Kemenkes & BPS</small>
                                </div>
                            `;
                        });
                        grid.innerHTML = html;
                    } else {
                        grid.innerHTML = '<div style="grid-column: 1/-1; text-align: center; padding: 40px; color: #ef4444;"><p>Gagal memuat data sebaran penyakit.</p></div>';
                    }
                })
                .catch(err => {
                    grid.innerHTML = '<div style="grid-column: 1/-1; text-align: center; padding: 40px; color: #ef4444;"><p>Terjadi kesalahan koneksi.</p></div>';
                });
        }
        
        // Load initial value on DOM loaded
        document.addEventListener('DOMContentLoaded', function() {
            const selectVal = document.getElementById('selectWilayah');
            if (selectVal) {
                loadPenyakitWilayah(selectVal.value);
            }
        });
    </script>
</body>
</html>
