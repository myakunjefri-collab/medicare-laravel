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
        </div>

        <div class="card">
            <h2>Selamat Datang, Dr. {{ $user->name }}</h2>
            <p style="color: var(--text-muted); line-height: 1.6;">
                Selamat datang di sistem manajemen klinik MedicareSystem. Gunakan menu navigasi di samping untuk mengelola jadwal praktik, membalas konsultasi pesan pasien, memberi diagnosa rekam medis, dan memantau antrean janji temu.
            </p>
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
                <div style="margin-bottom: 15px;">
                    <a href="/dokter/chat" class="btn btn-sm btn-warning"><i class="fas fa-arrow-left"></i> Kembali ke List Chat</a>
                </div>
                <h3><i class="fas fa-comments"></i> Sesi Konsultasi: {{ $selected_chat->pasien_name }}</h3>
                
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
                    <form action="/dokter/chat/reply" method="POST" class="chat-input" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="konsultasi_id" value="{{ $selected_chat->id }}">
                        <label for="gambar" class="btn btn-sm" style="background:#64748b; display:flex; align-items:center; justify-content:center; width:45px; height:45px; border-radius:50%; margin:0; box-shadow:none; cursor:pointer;"><i class="fas fa-paperclip"></i></label>
                        <input type="file" name="gambar" id="gambar" accept="image/*" style="display:none;" onchange="document.getElementById('fileName').innerHTML = this.files[0] ? this.files[0].name : '';">
                        <input type="text" name="pesan_balasan" placeholder="Ketik balasan konsultasi di sini..." autocomplete="off">
                        <button type="submit" class="btn"><i class="fas fa-paper-plane"></i> Kirim</button>
                    </form>
                    <div id="fileName" style="font-size: 0.75rem; color: var(--text-muted); padding: 5px 20px; font-weight: 600;"></div>
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
                                <td>{{ $row->keluhan }}</td>
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
                                <td>{{ date('d/m/Y', strtotime($a->tanggal)) }}</td>
                                <td>{{ $a->jam }} WIB</td>
                                <td><span class="queue-no" style="font-size: 1.1rem;">{{ $a->nomor_antrean }}</span></td>
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
