<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>@yield('title', 'MedicareSystem - Platform Kesehatan Digital')</title>
    <meta name="description" content="Platform kesehatan terpadu dengan jadwal dokter, konsultasi chat, rekam medis elektronik, dan antrean online.">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- FullCalendar (for calendars) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <!-- Custom Theme CSS -->
    <link rel="stylesheet" href="{{ asset('css/medicare.css') }}">
    @yield('styles')
</head>
<body>
    @auth
        <!-- Navbar for authenticated dashboards -->
        <div class="navbar">
            <div style="display: flex; align-items: center; gap: 16px;">
                <button id="sidebarToggle" style="background: transparent; border: none; color: var(--text-white); font-size: 1.3rem; cursor: pointer; display: flex; align-items: center; justify-content: center; padding: 4px; border-radius: 4px; transition: var(--transition);">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="logo">
                    <i class="fas fa-notes-medical"></i>
                    <span>MedicareSystem</span>
                </div>
            </div>
            <div class="user-info">
                <span>Halo, {{ Auth::user()->role === 'dokter' ? 'Dr. ' : '' }}{{ Auth::user()->name }}</span>
                <a href="{{ route('logout') }}" class="logout" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i> Keluar
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>
        </div>

        <div class="app-container" id="appContainer">
            <!-- Sidebar Overlay for mobile -->
            <div class="sidebar-overlay" id="sidebarOverlay"></div>
            <!-- Sidebar for authenticated dashboards -->
            <div class="sidebar">
                <!-- Mobile Sidebar Header -->
                <div class="sidebar-header" style="display: none; justify-content: space-between; align-items: center; padding-bottom: 16px; margin-bottom: 12px; border-bottom: 1px solid var(--border-color);">
                    <div style="font-weight: 800; color: var(--primary); display: flex; align-items: center; gap: 8px; font-size: 1.1rem;">
                        <i class="fas fa-notes-medical" style="color: var(--accent);"></i>
                        <span>MedicareSystem</span>
                    </div>
                    <button id="sidebarClose" style="background: transparent; border: none; color: var(--text-muted); font-size: 1.25rem; cursor: pointer; display: flex; align-items: center; justify-content: center; padding: 4px;">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                @if(Auth::user()->role === 'pasien')
                    <a href="/pasien" class="{{ $page === 'home' ? 'active' : '' }}"><i class="fas fa-home"></i> Dashboard</a>
                    <a href="/pasien/jadwal" class="{{ $page === 'jadwal' ? 'active' : '' }}"><i class="fas fa-calendar-alt"></i> Jadwal Praktik</a>
                    <a href="/pasien/chat" class="{{ $page === 'chat' ? 'active' : '' }}"><i class="fas fa-comments"></i> Konsultasi Chat</a>
                    <a href="/pasien/chatbot" class="{{ $page === 'chatbot' ? 'active' : '' }}"><i class="fas fa-robot"></i> Asisten Medicare</a>
                    <a href="/pasien/rekam-medis" class="{{ $page === 'rekam' ? 'active' : '' }}"><i class="fas fa-notes-medical"></i> Rekam Medis</a>
                    <a href="/pasien/antrean" class="{{ $page === 'antrean' ? 'active' : '' }}"><i class="fas fa-ticket-alt"></i> Antrean Saya</a>
                    <a href="/pasien/pesanan-obat" class="{{ $page === 'daftar_pesanan' || $page === 'pesan_obat' ? 'active' : '' }}"><i class="fas fa-pills"></i> Pesanan Obat</a>
                    <a href="/pasien/bantuan" class="{{ $page === 'bantuan' ? 'active' : '' }}"><i class="fas fa-question-circle"></i> Bantuan / CS</a>
                @elseif(Auth::user()->role === 'dokter')
                    <a href="/dokter" class="{{ $page === 'home' ? 'active' : '' }}"><i class="fas fa-home"></i> Dashboard</a>
                    <a href="/dokter/jadwal" class="{{ $page === 'jadwal' ? 'active' : '' }}"><i class="fas fa-calendar-alt"></i> Jadwal Praktik</a>
                    <a href="/dokter/kelola-jadwal" class="{{ $page === 'kelola_jadwal' ? 'active' : '' }}"><i class="fas fa-calendar-plus"></i> Kelola Jadwal</a>
                    <a href="/dokter/chat" class="{{ $page === 'chat_dokter' ? 'active' : '' }}"><i class="fas fa-comments"></i> Chat Pasien</a>
                    <a href="/dokter/diagnosa" class="{{ $page === 'diagnosa' ? 'active' : '' }}"><i class="fas fa-notes-medical"></i> Beri Diagnosa</a>
                    <a href="/dokter/antrean" class="{{ $page === 'lihat_antrean' ? 'active' : '' }}"><i class="fas fa-ticket-alt"></i> Antrean Pasien</a>
                @elseif(Auth::user()->role === 'admin')
                    <a href="/admin" class="{{ $page === 'home' ? 'active' : '' }}"><i class="fas fa-home"></i> Dashboard</a>
                    <a href="/admin/dokter" class="{{ $page === 'dokter' ? 'active' : '' }}"><i class="fas fa-user-md"></i> Kelola Dokter</a>
                    <a href="/admin/pasien" class="{{ $page === 'pasien' ? 'active' : '' }}"><i class="fas fa-users"></i> Kelola Pasien</a>
                    <a href="/admin/jadwal" class="{{ $page === 'jadwal' ? 'active' : '' }}"><i class="fas fa-calendar-alt"></i> Kelola Jadwal</a>
                    <a href="/admin/janji-temu" class="{{ $page === 'janji' ? 'active' : '' }}"><i class="fas fa-ticket-alt"></i> Janji Temu</a>
                    <a href="/admin/rekam-medis" class="{{ $page === 'rekam' ? 'active' : '' }}"><i class="fas fa-notes-medical"></i> Rekam Medis</a>
                    <a href="/admin/berita" class="{{ $page === 'berita' ? 'active' : '' }}"><i class="fas fa-newspaper"></i> Kelola Berita</a>
                    <a href="/admin/pesanan-obat" class="{{ $page === 'pesanan_obat' ? 'active' : '' }}"><i class="fas fa-pills"></i> Kelola Pesanan</a>
                    <a href="/admin/bantuan" class="{{ $page === 'bantuan' ? 'active' : '' }}"><i class="fas fa-headset"></i> Tiket Bantuan CS</a>
                @endif
            </div>

            <!-- Content Area -->
            <div class="content">
                @if(session('error'))
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ session('error') }}
                    </div>
                @endif
                @if(session('success'))
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        {{ session('success') }}
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    @else
        <!-- For non-authenticated layout (Landing, Login) -->
        @yield('content')
    @endauth

    @yield('scripts')

    @auth
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const sidebarToggle = document.getElementById('sidebarToggle');
                const sidebarClose = document.getElementById('sidebarClose');
                const sidebarOverlay = document.getElementById('sidebarOverlay');
                const appContainer = document.getElementById('appContainer');
                
                // Muat status collapse tersimpan
                if (window.innerWidth > 768) {
                    const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
                    if (isCollapsed) {
                        appContainer.classList.add('sidebar-collapsed');
                    }
                }
                
                // Alihkan sidebar
                if (sidebarToggle) {
                    sidebarToggle.addEventListener('click', function(e) {
                        e.stopPropagation();
                        if (window.innerWidth <= 768) {
                            appContainer.classList.toggle('sidebar-open');
                        } else {
                            appContainer.classList.toggle('sidebar-collapsed');
                             // Simpan status ke localStorage
                             const collapsed = appContainer.classList.contains('sidebar-collapsed');
                            localStorage.setItem('sidebarCollapsed', collapsed);
                        }
                    });
                }
                
                // Tutup sidebar di mobile
                function closeMobileSidebar() {
                    if (window.innerWidth <= 768) {
                        appContainer.classList.remove('sidebar-open');
                    }
                }
                
                if (sidebarClose) {
                    sidebarClose.addEventListener('click', closeMobileSidebar);
                }
                if (sidebarOverlay) {
                    sidebarOverlay.addEventListener('click', closeMobileSidebar);
                }
                
                // Tutup sidebar mobile saat resize
                window.addEventListener('resize', function() {
                    if (window.innerWidth > 768) {
                        appContainer.classList.remove('sidebar-open');
                    }
                });
            });
        </script>
    @endauth
</body>
</html>
