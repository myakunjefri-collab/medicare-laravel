<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>MedicareSystem - Masuk / Daftar Baru</title>
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
            background: linear-gradient(135deg, #0b2b4d 0%, #1a6d8f 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            max-width: 1100px;
            width: 100%;
            display: flex;
            background: white;
            border-radius: 32px;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        /* LEFT PANEL */
        .left-panel {
            flex: 0.9;
            background: linear-gradient(135deg, #0b2b4d 0%, #175a78 100%);
            padding: 50px 40px;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .left-panel .logo {
            font-size: 1.8rem;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 12px;
            letter-spacing: -0.5px;
        }
        .left-panel .logo i {
            color: #2b9e6e;
            background: white;
            padding: 10px;
            border-radius: 50%;
            font-size: 1.3rem;
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        }
        .left-panel .brand-text {
            margin: 40px 0;
        }
        .left-panel h2 {
            font-size: 2.2rem;
            margin-bottom: 20px;
            line-height: 1.25;
            font-weight: 800;
            letter-spacing: -0.5px;
        }
        .left-panel p {
            opacity: 0.9;
            line-height: 1.6;
            font-size: 0.95rem;
        }
        .features {
            margin-top: 30px;
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 14px;
        }
        .features li {
            display: flex;
            align-items: center;
            gap: 14px;
            font-size: 0.9rem;
            font-weight: 500;
        }
        .features li i {
            background: rgba(255, 255, 255, 0.15);
            padding: 8px;
            border-radius: 50%;
            font-size: 0.85rem;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        /* RIGHT PANEL */
        .right-panel {
            flex: 1.1;
            padding: 50px;
            background: white;
            overflow-y: auto;
            max-height: 90vh;
        }
        .tab-buttons {
            display: flex;
            gap: 24px;
            margin-bottom: 30px;
            border-bottom: 2px solid #f1f5f9;
        }
        .tab-btn {
            background: none;
            border: none;
            padding: 12px 0;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            color: #94a3b8;
            transition: all 0.3s ease;
            position: relative;
        }
        .tab-btn.active {
            color: #2b9e6e;
        }
        .tab-btn.active::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            right: 0;
            height: 3px;
            background: #2b9e6e;
            border-radius: 3px;
        }
        .form-panel {
            display: none;
            animation: fadeIn 0.4s ease;
        }
        .form-panel.active {
            display: block;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .form-group {
            margin-bottom: 18px;
        }
        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: #0b2b4d;
            font-size: 0.85rem;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 12px 18px;
            border: 2px solid #cbd5e1;
            border-radius: 40px;
            font-size: 0.9rem;
            font-family: inherit;
            transition: all 0.3s ease;
            background: #f8fafc;
        }
        .form-group input:focus, .form-group select:focus {
            outline: none;
            border-color: #2b9e6e;
            background: white;
            box-shadow: 0 0 0 4px rgba(43, 158, 110, 0.12);
        }
        .form-row {
            display: flex;
            gap: 16px;
        }
        .form-row .form-group {
            flex: 1;
        }
        .btn {
            width: 100%;
            background: linear-gradient(135deg, #2b9e6e 0%, #228058 100%);
            color: white;
            border: none;
            padding: 14px;
            border-radius: 40px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 10px 20px rgba(43, 158, 110, 0.2);
            transition: all 0.3s ease;
            margin-top: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 24px rgba(43, 158, 110, 0.35);
        }
        .alert {
            padding: 12px 18px;
            border-radius: 20px;
            margin-bottom: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .alert-error {
            background: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fca5a5;
        }
        .alert-success {
            background: #dcfce7;
            color: #15803d;
            border: 1px solid #86efac;
        }
        .demo-info {
            margin-top: 24px;
            padding: 16px;
            background: #f0f7fc;
            border-radius: 24px;
            font-size: 0.8rem;
            line-height: 1.6;
            border: 1px solid #e2e8f0;
        }
        .demo-info strong {
            color: #0b2b4d;
        }
        .warning-info {
            margin-top: 16px;
            padding: 12px;
            background: #fffbeb;
            border-radius: 20px;
            font-size: 0.75rem;
            color: #b45309;
            border: 1px solid #fde68a;
            text-align: center;
        }
        
        /* RESPONSIVE */
        @media (max-width: 992px) {
            .container {
                flex-direction: column;
                max-width: 580px;
            }
            .left-panel {
                padding: 40px 30px;
                text-align: center;
            }
            .left-panel .logo {
                justify-content: center;
            }
            .features {
                display: none; /* Hide features list on smaller screens to keep it short */
            }
            .left-panel .brand-text {
                margin: 20px 0 0;
            }
            .right-panel {
                padding: 40px 30px;
            }
        }
        
        @media (max-width: 480px) {
            .form-row {
                flex-direction: column;
                gap: 0;
            }
            .right-panel {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- LEFT PANEL -->
        <div class="left-panel">
            <div class="logo">
                <i class="fas fa-notes-medical"></i> 
                <span>MedicareSystem</span>
            </div>
            <div class="brand-text">
                <h2>Kesehatan Digital<br>Dalam Genggaman</h2>
                <p>Platform terintegrasi untuk mengelola jadwal praktik dokter, konsultasi langsung, rekam medis instan, dan antrean online tanpa ribet.</p>
            </div>
            <ul class="features">
                <li><i class="fas fa-calendar-check"></i> Jadwal Dokter Real-time</li>
                <li><i class="fas fa-comments"></i> Konsultasi Chat Langsung</li>
                <li><i class="fas fa-notes-medical"></i> Rekam Medis Elektronik</li>
                <li><i class="fas fa-ticket-alt"></i> Nomor Antrean Otomatis</li>
            </ul>
        </div>
        
        <!-- RIGHT PANEL -->
        <div class="right-panel">
            <div class="tab-buttons">
                <button class="tab-btn {{ !session('register_error') && !session('register_success') ? 'active' : '' }}" onclick="showPanel('login')">Masuk</button>
                <button class="tab-btn {{ session('register_error') || session('register_success') ? 'active' : '' }}" onclick="showPanel('register')">Daftar Baru</button>
            </div>

            <!-- LOGIN FORM -->
            <div id="loginPanel" class="form-panel {{ !session('register_error') && !session('register_success') ? 'active' : '' }}">
                @if(session('error'))
                    <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
                @endif
                <form action="/login" method="POST">
                    @csrf
                    <div class="form-group">
                        <label><i class="fas fa-envelope"></i> Email atau Username</label>
                        <input type="text" name="email" value="{{ old('email') }}" placeholder="email@contoh.com atau username" required autocomplete="username">
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-lock"></i> Password</label>
                        <input type="password" name="password" placeholder="********" required autocomplete="current-password">
                    </div>
                    <button type="submit" class="btn"><i class="fas fa-sign-in-alt"></i> Masuk Sekarang</button>
                </form>
            </div>

            <!-- REGISTER FORM -->
            <div id="registerPanel" class="form-panel {{ session('register_error') || session('register_success') ? 'active' : '' }}">
                @if(session('register_error'))
                    <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> {{ session('register_error') }}</div>
                @endif
                @if(session('register_success'))
                    <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('register_success') }}</div>
                @endif
                <form action="/register" method="POST">
                    @csrf
                    <div class="form-row">
                        <div class="form-group">
                            <label><i class="fas fa-user"></i> Nama Lengkap</label>
                            <input type="text" name="fullname" value="{{ old('fullname') }}" placeholder="Ahmad Budi" required>
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-calendar"></i> Usia</label>
                            <input type="number" name="age" value="{{ old('age') }}" placeholder="25" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label><i class="fas fa-envelope"></i> Email</label>
                            <input type="email" name="email" value="{{ old('email') }}" placeholder="email@contoh.com" required>
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-phone"></i> No. Telepon</label>
                            <input type="tel" name="phone" value="{{ old('phone') }}" placeholder="08123456789" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label><i class="fas fa-venus-mars"></i> Jenis Kelamin</label>
                            <select name="gender" required>
                                <option value="">Pilih</option>
                                <option value="Laki-laki" {{ old('gender') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="Perempuan" {{ old('gender') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-map-marker-alt"></i> Alamat</label>
                            <input type="text" name="alamat" value="{{ old('alamat') }}" placeholder="Jakarta, Indonesia">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label><i class="fas fa-lock"></i> Password</label>
                            <input type="password" name="password" placeholder="Minimal 6 karakter" required autocomplete="new-password">
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-check-circle"></i> Konfirmasi Password</label>
                            <input type="password" name="confirm_password" placeholder="Ulangi password" required autocomplete="new-password">
                        </div>
                    </div>
                    <div class="form-group">
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-weight: 500;">
                            <input type="checkbox" name="agree" required style="width: 16px; height: 16px; margin: 0; cursor: pointer;"> 
                            <span style="font-size: 0.75rem; color: #475569;">Saya menyetujui Syarat & Ketentuan MedicareSystem</span>
                        </label>
                    </div>
                    <button type="submit" class="btn"><i class="fas fa-user-plus"></i> Daftar Sekarang</button>
                </form>
                <div class="warning-info">
                    <i class="fas fa-info-circle"></i> Pendaftaran online hanya berlaku untuk <strong>PASIEN</strong>. Akun Dokter dan Administrator dikelola oleh Admin.
                </div>
            </div>
        </div>
    </div>

    <script>
        function showPanel(panel) {
            const loginPanel = document.getElementById('loginPanel');
            const registerPanel = document.getElementById('registerPanel');
            const tabs = document.querySelectorAll('.tab-btn');
            
            if(panel === 'login') {
                loginPanel.classList.add('active');
                registerPanel.classList.remove('active');
                tabs[0].classList.add('active');
                tabs[1].classList.remove('active');
            } else {
                loginPanel.classList.remove('active');
                registerPanel.classList.add('active');
                tabs[0].classList.remove('active');
                tabs[1].classList.add('active');
            }
        }
    </script>
</body>
</html>
