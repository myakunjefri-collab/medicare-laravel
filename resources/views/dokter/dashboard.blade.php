@extends('layouts.app')

@section('title', 'MedicareSystem - Dashboard Dokter')

@section('content')

    @if(session('alert_parah'))
        <script>
            alert("⚠️ DIAGNOSA PARAH!\nJanji temu/antrean pasien berhasil dibuat.\n\nNomor Antrean: {{ session('alert_parah.no_antrean') }}\nTanggal: {{ session('alert_parah.tanggal') }}\nJam: {{ session('alert_parah.jam') }} WIB");
        </script>
    @endif

    <!-- ==================== HOME TAB ==================== -->
    @if($page === 'home')
        <div class="stats-grid">
            <div class="stat-card">
                <div class="icon-wrapper"><i class="fas fa-notes-medical"></i></div>
                <div>
                    <h3>{{ $total_diagnosa }}</h3>
                    <p>Menunggu Diagnosa</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="icon-wrapper"><i class="fas fa-calendar-check"></i></div>
                <div>
                    <h3>{{ $total_jadwal }}</h3>
                    <p>Jadwal Praktik Aktif</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="icon-wrapper"><i class="fas fa-ticket-alt"></i></div>
                <div>
                    <h3>{{ $total_antrean }}</h3>
                    <p>Antrean Rujukan</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="icon-wrapper"><i class="fas fa-comments"></i></div>
                <div>
                    <h3>{{ $total_chat }}</h3>
                    <p>Sesi Konsultasi</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="icon-wrapper"><i class="fas fa-star" style="color: #f59e0b;"></i></div>
                <div>
                    <h3>{{ number_format($user->average_rating, 1) }}</h3>
                    <p>Rating Dokter ({{ $user->review_count }} Ulasan)</p>
                </div>
            </div>
        </div>

        <div class="card">
            <h2>Selamat Datang, Dr. {{ $user->name }}</h2>
            <p style="color: var(--text-muted); line-height: 1.6;">
                Selamat datang di sistem manajemen klinik MedicareSystem. Gunakan menu navigasi di samping untuk mengelola jadwal praktik, membalas konsultasi pesan pasien, memberi diagnosa rekam medis, dan memantau antrean janji temu.
            </p>
        </div>

        <div class="card">
            <h2><i class="fas fa-toggle-on"></i> Status Konsultasi Saya</h2>
            <p style="color: var(--text-muted); margin-bottom: 15px;">
                Tentukan status ketersediaan Anda agar pasien mengetahui apakah Anda sedang aktif atau tidak dapat menerima konsultasi.
            </p>
            <form action="/dokter/status/update" method="POST" class="status-dokter-form" style="display: flex; gap: 15px; align-items: center; flex-wrap: wrap;">
                @csrf
                <div style="display: flex; gap: 20px; flex-wrap: wrap;">
                    <label style="display: flex; align-items: center; gap: 8px; font-weight: 600; cursor: pointer; margin: 0;">
                        <input type="radio" name="status_dokter" value="online" {{ $user->status_dokter === 'online' ? 'checked' : '' }} onchange="this.form.submit()" style="width: auto; margin: 0; cursor: pointer;">
                        <span class="status-badge status-selesai" style="text-transform: none; display: flex; align-items: center; gap: 6px; cursor: pointer;">
                            <span style="display: inline-block; width: 8px; height: 8px; background: #22c55e; border-radius: 50%;"></span>
                            Online & Siap Konsultasi
                        </span>
                    </label>
                    <label style="display: flex; align-items: center; gap: 8px; font-weight: 600; cursor: pointer; margin: 0;">
                        <input type="radio" name="status_dokter" value="sibuk" {{ $user->status_dokter === 'sibuk' ? 'checked' : '' }} onchange="this.form.submit()" style="width: auto; margin: 0; cursor: pointer;">
                        <span class="status-badge status-menunggu" style="text-transform: none; display: flex; align-items: center; gap: 6px; cursor: pointer;">
                            <span style="display: inline-block; width: 8px; height: 8px; background: #eab308; border-radius: 50%;"></span>
                            Sedang Sibuk / Gabisa Konsul
                        </span>
                    </label>
                    <label style="display: flex; align-items: center; gap: 8px; font-weight: 600; cursor: pointer; margin: 0;">
                        <input type="radio" name="status_dokter" value="offline" {{ $user->status_dokter === 'offline' ? 'checked' : '' }} onchange="this.form.submit()" style="width: auto; margin: 0; cursor: pointer;">
                        <span class="status-badge status-batal" style="text-transform: none; display: flex; align-items: center; gap: 6px; cursor: pointer;">
                            <span style="display: inline-block; width: 8px; height: 8px; background: #ef4444; border-radius: 50%;"></span>
                            Offline / Tidak Aktif
                        </span>
                    </label>
                </div>
            </form>
        </div>

        <div class="card">
            <h2><i class="fas fa-star" style="color: #f59e0b;"></i> Ulasan & Feedback Pasien</h2>
            @if($user->rating_reviews->isEmpty())
                <p style="color: var(--text-muted);">Belum ada ulasan dari pasien.</p>
            @else
                <div class="reviews-container">
                    @foreach($user->rating_reviews as $rev)
                        <div class="review-item">
                            <div class="review-header">
                                <span class="review-author"><i class="fas fa-user-circle"></i> {{ $rev->pasien_name }}</span>
                                <span class="star-display">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fa{{ $i <= $rev->rating ? 's' : 'r' }} fa-star"></i>
                                    @endfor
                                </span>
                            </div>
                            <div class="review-content">
                                "{{ $rev->ulasan }}"
                            </div>
                            <div class="review-date" style="text-align: right; margin-top: 6px;">
                                <small><i class="fas fa-clock"></i> {{ date('d/m/Y H:i', strtotime($rev->updated_at)) }}</small>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endif

    <!-- ==================== JADWAL TAB ==================== -->
    @if($page === 'jadwal')
        <div class="card">
            <h2><i class="fas fa-calendar-alt"></i> Kalender Jadwal Praktik Dokter</h2>
            <div style="margin-top: 15px;">
                <div id="calendar"></div>
            </div>
        </div>

        <div class="card">
            <h2><i class="fas fa-list"></i> Daftar Jadwal Praktik Dokter</h2>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Dokter</th>
                            <th>Spesialis</th>
                            <th>Tanggal</th>
                            <th>Jam</th>
                            <th>Ruangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($jadwal as $j)
                            <tr>
                                <td><strong>{{ $j->doctor_name }}</strong></td>
                                <td>{{ $j->spesialis }}</td>
                                <td>{{ date('d/m/Y', strtotime($j->tanggal)) }}</td>
                                <td>{{ $j->start_time }} - {{ $j->end_time }}</td>
                                <td>{{ $j->ruangan ?: '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align: center;">Tidak ada jadwal praktik terdaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- ==================== KELOLA JADWAL TAB ==================== -->
    @if($page === 'kelola_jadwal')
        <div class="card">
            <h2><i class="fas fa-calendar-plus"></i> Tambah Jadwal Baru</h2>
            <form action="/dokter/kelola-jadwal/tambah" method="POST">
                @csrf
                <div class="form-group">
                    <label for="tanggal">Tanggal Praktik</label>
                    <input type="date" name="tanggal" id="tanggal" required min="{{ date('Y-m-d') }}">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="start_time">Jam Mulai</label>
                        <input type="time" name="start_time" id="start_time" required>
                    </div>
                    <div class="form-group">
                        <label for="end_time">Jam Selesai</label>
                        <input type="time" name="end_time" id="end_time" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="ruangan">Ruangan</label>
                        <input type="text" name="ruangan" id="ruangan" placeholder="Contoh: Poliklinik A-12">
                    </div>
                    <div class="form-group">
                        <label for="kuota">Kuota Pasien</label>
                        <input type="number" name="kuota" id="kuota" value="10" min="1" required>
                    </div>
                </div>
                <button type="submit" class="btn"><i class="fas fa-save"></i> Simpan Jadwal</button>
            </form>
        </div>

        <div class="card">
            <h2><i class="fas fa-list"></i> Jadwal Praktik Saya</h2>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Jam</th>
                            <th>Ruangan</th>
                            <th>Kuota</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($my_schedules as $j)
                            <tr>
                                <td>{{ date('d/m/Y', strtotime($j->tanggal)) }}</td>
                                <td>{{ $j->start_time }} - {{ $j->end_time }}</td>
                                <td>{{ $j->ruangan ?: '-' }}</td>
                                <td>{{ $j->kuota }} Pasien</td>
                                <td>
                                    <a href="/dokter/kelola-jadwal/{{ $j->id }}/hapus" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus jadwal ini?')">
                                        <i class="fas fa-trash"></i> Hapus
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align: center;">Belum ada jadwal praktik yang Anda buat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- ==================== CHAT TAB ==================== -->
    @if($page === 'chat_dokter')
        <div class="card">
            <h2><i class="fas fa-comments"></i> Konsultasi Chat Pasien</h2>

            @if(!$selected_chat)
                <p style="color: var(--text-muted); margin-bottom: 20px;">Pilih sesi konsultasi pasien aktif di bawah:</p>
                <div class="doctor-list">
                    @forelse($chat_list as $c)
                        <div class="doctor-item" style="cursor: pointer;" onclick="window.location.href='/dokter/chat/{{ $c->id }}'">
                            <div class="doctor-info">
                                <h4><i class="fas fa-user"></i> {{ $c->pasien_name }}</h4>
                                <p>Konsultasi sejak {{ date('d/m/Y H:i', strtotime($c->created_at)) }}</p>
                            </div>
                            <span class="btn btn-sm"><i class="fas fa-reply"></i> Balas Chat</span>
                        </div>
                    @empty
                        <p>Belum ada sesi konsultasi aktif dari pasien.</p>
                    @endforelse
                </div>
            @else
                <div style="margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                    <a href="/dokter/chat" class="btn btn-sm btn-warning"><i class="fas fa-arrow-left"></i> Kembali ke List Chat</a>
                    @if($selected_chat->status === 'aktif')
                        <form action="/dokter/chat/{{ $selected_chat->id }}/akhiri" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mengakhiri sesi konsultasi ini? Setelah diakhiri, pasien dapat memberikan rating dan ulasan.')">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-times-circle"></i> Akhiri Konsultasi</button>
                        </form>
                    @endif
                </div>
                <h3><i class="fas fa-comments"></i> Sesi Konsultasi: {{ $selected_chat->pasien_name }}
                    @if($selected_chat->status === 'selesai')
                        <span class="status-badge status-selesai" style="font-size: 0.7rem; vertical-align: middle; margin-left: 8px;">Selesai</span>
                    @else
                        <span class="status-badge status-konfirmasi" style="font-size: 0.7rem; vertical-align: middle; margin-left: 8px;">Aktif</span>
                    @endif
                </h3>
                
                <div class="chat-container" style="margin-top: 15px;">
                    <div class="chat-messages" id="chatMessages">
                        @forelse($pesan_list as $p)
                            <div class="chat-bubble {{ $p->pengirim === 'dokter' ? 'chat-bubble-right' : 'chat-bubble-left' }}">
                                <strong>{{ $p->pengirim_name }}</strong>
                                {!! nl2br(e($p->pesan)) !!}
                                @if($p->gambar)
                                    <div style="margin-top: 8px;">
                                        <a href="{{ asset('storage/' . $p->gambar) }}" target="_blank">
                                            <img src="{{ asset('storage/' . $p->gambar) }}" class="chat-image" style="max-width:200px; border-radius:12px; display:block; box-shadow: var(--shadow-sm); cursor: pointer;">
                                        </a>
                                    </div>
                                @endif
                                <small>{{ date('H:i', strtotime($p->waktu)) }}</small>
                            </div>
                        @empty
                            <p style="text-align: center; color: var(--text-muted); padding: 40px 0;">Belum ada riwayat pesan chat.</p>
                        @endforelse
                    </div>

                    @if($selected_chat->status === 'selesai')
                        <div class="end-consultation-banner" style="margin: 20px; border-radius: var(--border-radius-md);">
                            <i class="fas fa-info-circle"></i> Sesi konsultasi telah berakhir.
                        </div>
                    @else
                        <form action="/dokter/chat/reply" method="POST" class="chat-input" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="konsultasi_id" value="{{ $selected_chat->id }}">
                            <label for="gambar" class="btn btn-sm" style="background:#64748b; display:flex; align-items:center; justify-content:center; width:45px; height:45px; border-radius:50%; margin:0; box-shadow:none; cursor:pointer;"><i class="fas fa-paperclip"></i></label>
                            <input type="file" name="gambar" id="gambar" accept="image/*" style="display:none;" onchange="document.getElementById('fileName').innerHTML = this.files[0] ? this.files[0].name : '';">
                            <input type="text" name="pesan_balasan" placeholder="Ketik balasan konsultasi di sini..." autocomplete="off">
                            <button type="submit" class="btn"><i class="fas fa-paper-plane"></i> Kirim</button>
                        </form>
                        <div id="fileName" style="font-size: 0.75rem; color: var(--text-muted); padding: 5px 20px; font-weight: 600;"></div>
                    @endif
                </div>
            @endif
        </div>
    @endif

    <!-- ==================== DIAGNOSA TAB ==================== -->
    @if($page === 'diagnosa')
        <div class="card">
            <h2><i class="fas fa-notes-medical"></i> Rekam Medis Menunggu Diagnosa</h2>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Nama Pasien</th>
                            <th>Usia</th>
                            <th>Keluhan Pasien</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rekam_medis as $row)
                            <tr>
                                <td>{{ date('d/m/Y', strtotime($row->tanggal)) }}</td>
                                <td><strong>{{ $row->pasien_name }}</strong></td>
                                <td>{{ $row->usia ?: '-' }} Tahun</td>
                                <td>
                                    <strong>Keluhan:</strong> {{ $row->keluhan }}<br>
                                    <div style="margin-top: 6px; font-size: 0.8rem; background: #f1f5f9; padding: 6px 10px; border-radius: 8px; border: 1px solid #e2e8f0; display: inline-block;">
                                        🩺 Tensi: {{ $row->tensi_darah ?: '-' }} | 🌡️ Suhu: {{ $row->suhu_tubuh ? $row->suhu_tubuh . ' °C' : '-' }} | 💓 Nadi: {{ $row->detak_jantung ? $row->detak_jantung . ' bpm' : '-' }} | ⚖️ BB: {{ $row->berat_badan ? $row->berat_badan . ' kg' : '-' }}
                                    </div>
                                    <div style="margin-top: 6px; font-size: 0.8rem; color: #228058; font-weight: 600;">
                                        💡 Kesimpulan Awal: <span>{{ $row->kesimpulan_awal ?: 'Tidak ada parameter klinis.' }}</span>
                                    </div>
                                </td>
                                <td>
                                    <button class="btn btn-sm" onclick="bukaModalDiagnosa({{ $row->id }}, '{{ $row->pasien_name }}')">
                                        <i class="fas fa-edit"></i> Diagnosa
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align: center;">Tidak ada rekam medis yang menunggu diagnosa saat ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- DIAGNOSA MODAL -->
        <div id="modalDiagnosa" class="modal">
            <div class="modal-content">
                <h3 id="judulModal">Beri Diagnosa Pasien</h3>
                <form action="/dokter/diagnosa/simpan" method="POST" style="margin-top: 15px;">
                    @csrf
                    <input type="hidden" name="id" id="rekam_id">
                    
                    <div class="form-group">
                        <label for="diagnosa">Diagnosa Hasil Pemeriksaan</label>
                        <input type="text" name="diagnosa" id="diagnosa" placeholder="Contoh: Influenza, Gastritis akut" required>
                    </div>

                    <div class="form-group">
                        <label for="status_diagnosa">Status Diagnosa Pasien</label>
                        <select name="status_diagnosa" id="status_diagnosa" required>
                            <option value="ringan">Ringan - Resep Obat Digital</option>
                            <option value="parah">Parah - Janji Temu / Antrean Fisik</option>
                        </select>
                    </div>

                    <div id="resepDiv" class="form-group">
                        <label for="resep">Resep Obat</label>
                        <input type="text" name="resep" id="resep" placeholder="Contoh: Paracetamol 500mg (3x1)">
                    </div>

                    <div id="janjiDiv" style="display: none;">
                        <div class="form-group">
                            <label for="tgl_janji">Tanggal Janji Temu</label>
                            <input type="date" name="tgl_janji" id="tgl_janji" value="{{ date('Y-m-d', strtotime('+2 days')) }}">
                        </div>
                        <div class="form-group">
                            <label for="jam_janji">Jam Janji Temu</label>
                            <input type="time" name="jam_janji" id="jam_janji" value="10:00">
                        </div>
                    </div>

                    <div style="margin-top: 24px; display: flex; gap: 10px; justify-content: flex-end;">
                        <button type="button" class="btn btn-danger" onclick="tutupModalDiagnosa()">Batal</button>
                        <button type="submit" class="btn">Simpan Diagnosa</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- ==================== ANTREAN TAB ==================== -->
    @if($page === 'lihat_antrean')
        <div class="card">
            <h2><i class="fas fa-ticket-alt"></i> Antrean Pasien (Janji Temu Rujukan Anda)</h2>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Nama Pasien</th>
                            <th>Tanggal</th>
                            <th>Jam</th>
                            <th>Nomor Antrean</th>
                            <th>Status Antrean</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($antrean as $a)
                            <tr>
                                <td><strong>{{ $a->pasien_name }}</strong></td>
                                <td>
                                    <strong>Poliklinik:</strong> <span style="color: var(--accent); font-weight:700;">{{ $a->poli ?: 'Poli Umum' }}</span><br>
                                    <strong>Jadwal:</strong> {{ date('d/m/Y', strtotime($a->tanggal)) }}
                                </td>
                                <td>{{ $a->jam }} WIB</td>
                                <td><span class="queue-no" style="font-size: 1.2rem; font-weight: 800; color: #2b9e6e; background: rgba(43, 158, 110, 0.1); padding: 4px 10px; border-radius: 6px;">{{ $a->nomor_antrean }}</span></td>
                                <td>
                                    <span class="status-badge {{ $a->status === 'menunggu' ? 'status-menunggu' : ($a->status === 'konfirmasi' ? 'status-konfirmasi' : ($a->status === 'selesai' ? 'status-selesai' : 'status-batal')) }}">
                                        {{ $a->status }}
                                    </span>
                                </td>
                                <td>
                                    @if($a->status === 'menunggu' || $a->status === 'konfirmasi')
                                        <a href="/dokter/antrean/{{ $a->id }}/selesai" class="btn btn-sm" onclick="return confirm('Apakah Anda yakin pemeriksaan pasien ini sudah selesai?')">
                                            <i class="fas fa-check-circle"></i> Selesai
                                        </a>
                                    @else
                                        <span style="color: var(--text-muted); font-size: 0.85rem; font-style: italic;">Selesai diperiksa</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align: center;">Belum ada antrean janji temu rujukan yang terdaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

@endsection

@section('scripts')
    @if($page === 'jadwal')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var events = @json($events ?? []);
                var calendarEl = document.getElementById('calendar');
                if (calendarEl) {
                    var calendar = new FullCalendar.Calendar(calendarEl, {
                        initialView: 'dayGridMonth',
                        headerToolbar: {
                            left: 'prev,next today',
                            center: 'title',
                            right: 'dayGridMonth'
                        },
                        buttonText: {
                            today: 'Hari Ini',
                            month: 'Bulan'
                        },
                        events: events,
                        height: 'auto',
                        handleWindowResize: true
                    });
                    calendar.render();
                }
            });
        </script>
    @endif

    @if($page === 'chat_dokter' && $selected_chat)
        <script>
            // Scroll to bottom of chat
            document.addEventListener('DOMContentLoaded', function() {
                var chatMessages = document.getElementById('chatMessages');
                if (chatMessages) {
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                }
            });
        </script>
    @endif

    @if($page === 'diagnosa')
        <script>
            function bukaModalDiagnosa(id, nama) {
                document.getElementById('rekam_id').value = id;
                document.getElementById('judulModal').innerHTML = 'Beri Diagnosa Pasien: ' + nama;
                document.getElementById('modalDiagnosa').style.display = 'flex';
                document.getElementById('resepDiv').style.display = 'block';
                document.getElementById('janjiDiv').style.display = 'none';
                document.getElementById('status_diagnosa').value = 'ringan';
            }
            function tutupModalDiagnosa() {
                document.getElementById('modalDiagnosa').style.display = 'none';
            }

            // Dynamic Modal Fields
            var statusSelect = document.getElementById('status_diagnosa');
            if (statusSelect) {
                statusSelect.onchange = function() {
                    if (this.value === 'parah') {
                        document.getElementById('resepDiv').style.display = 'none';
                        document.getElementById('janjiDiv').style.display = 'block';
                        document.getElementById('resep').removeAttribute('required');
                    } else {
                        document.getElementById('resepDiv').style.display = 'block';
                        document.getElementById('janjiDiv').style.display = 'none';
                        document.getElementById('resep').setAttribute('required', 'required');
                    }
                }
            }
        </script>
    @endif
@endsection
