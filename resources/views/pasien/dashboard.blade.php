@extends('layouts.app')

@section('title', 'MedicareSystem - Dashboard Pasien')

@section('content')

    <!-- ==================== HOME TAB ==================== -->
    @if($page === 'home')
        <div class="stats-grid">
            <div class="stat-card">
                <div class="icon-wrapper"><i class="fas fa-user-md"></i></div>
                <div>
                    <h3>{{ $total_dokter }}</h3>
                    <p>Dokter Spesialis</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="icon-wrapper"><i class="fas fa-ticket-alt"></i></div>
                <div>
                    <h3>{{ $total_antrean }}</h3>
                    <p>Antrean Aktif</p>
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
                <div class="icon-wrapper"><i class="fas fa-notes-medical"></i></div>
                <div>
                    <h3>Digital</h3>
                    <p>Rekam Medis</p>
                </div>
            </div>
        </div>

        <div class="card">
            <h2><i class="fas fa-user-md"></i> Konsultasi Dokter Spesialis</h2>
            <div class="doctor-list">
                @forelse($dokter_list as $dok)
                    <div class="doctor-item">
                        <div class="doctor-info">
                            <h4><i class="fas fa-stethoscope"></i> {{ $dok->name }}</h4>
                            <p>{{ $dok->spesialis }}</p>
                        </div>
                        <a href="/pasien/chat/{{ $dok->id }}" class="btn btn-sm"><i class="fas fa-comment-dots"></i> Chat Sekarang</a>
                    </div>
                @empty
                    <p>Tidak ada data dokter saat ini.</p>
                @endforelse
            </div>
        </div>
    @endif

    <!-- ==================== JADWAL TAB ==================== -->
    @if($page === 'jadwal')
        <div class="card">
            <h2><i class="fas fa-calendar-alt"></i> Kalender Jadwal Dokter</h2>
            <div style="margin-top: 15px;">
                <div id="calendar"></div>
            </div>
        </div>

        <div class="card">
            <h2><i class="fas fa-list"></i> Daftar Lengkap Jadwal Dokter</h2>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Dokter</th>
                            <th>Spesialis</th>
                            <th>Tanggal</th>
                            <th>Jam</th>
                            <th>Ruangan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($jadwal as $row)
                            <tr>
                                <td><strong>{{ $row->doctor_name }}</strong></td>
                                <td>{{ $row->spesialis }}</td>
                                <td>{{ date('d/m/Y', strtotime($row->tanggal)) }}</td>
                                <td>{{ $row->start_time }} - {{ $row->end_time }}</td>
                                <td>{{ $row->ruangan ?? '-' }}</td>
                                <td>
                                    <a href="/pasien/chat/{{ $row->doctor_id }}" class="btn btn-sm"><i class="fas fa-comment-dots"></i> Chat</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align: center;">Tidak ada jadwal praktik terdekat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- ==================== CHAT TAB ==================== -->
    @if($page === 'chat')
        <div class="card">
            <h2><i class="fas fa-comments"></i> Konsultasi Chat</h2>
            
            @if(!$selected_dokter)
                <p style="margin-bottom: 20px; color: var(--text-muted);">Pilih dokter spesialis untuk memulai sesi konsultasi:</p>
                <div class="doctor-list">
                    @forelse($dokter_list as $doc)
                        <div class="doctor-item">
                            <div class="doctor-info">
                                <h4><i class="fas fa-stethoscope"></i> {{ $doc->name }}</h4>
                                <p>{{ $doc->spesialis }}</p>
                            </div>
                            <a href="/pasien/chat/{{ $doc->id }}" class="btn btn-sm"><i class="fas fa-comment-dots"></i> Chat</a>
                        </div>
                    @empty
                        <p>Tidak ada dokter spesialis yang tersedia.</p>
                    @endforelse
                </div>
            @else
                <div style="margin-bottom: 15px;">
                    <a href="/pasien/chat" class="btn btn-sm btn-warning"><i class="fas fa-arrow-left"></i> Kembali ke List Dokter</a>
                </div>
                <h3><i class="fas fa-user-md"></i> Chat dengan dr. {{ $selected_dokter->name }} ({{ $selected_dokter->spesialis }})</h3>
                <div class="chat-container" style="margin-top: 15px;">
                    <div class="chat-messages" id="chatMessages">
                        @forelse($pesan_list as $msg)
                            <div class="chat-bubble {{ $msg->pengirim === 'pasien' ? 'chat-bubble-right' : 'chat-bubble-left' }}">
                                <strong>{{ $msg->pengirim_name }}</strong>
                                {!! nl2br(e($msg->pesan)) !!}
                                @if($msg->gambar)
                                    <div style="margin-top: 8px;">
                                        <a href="{{ asset('storage/' . $msg->gambar) }}" target="_blank">
                                            <img src="{{ asset('storage/' . $msg->gambar) }}" class="chat-image" style="max-width:200px; border-radius:12px; display:block; box-shadow: var(--shadow-sm); cursor: pointer;">
                                        </a>
                                    </div>
                                @endif
                                <small>{{ date('H:i', strtotime($msg->waktu)) }}</small>
                            </div>
                        @empty
                            <p style="text-align:center; color:#94a3b8; padding: 40px 0;">✨ Belum ada pesan. Silakan kirim pesan keluhan Anda untuk memulai konsultasi!</p>
                        @endforelse
                    </div>
                    <form action="/pasien/chat/send" method="POST" class="chat-input" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="dokter_id" value="{{ $selected_dokter->id }}">
                        <label for="gambar" class="btn btn-sm" style="background:#64748b; display:flex; align-items:center; justify-content:center; width:45px; height:45px; border-radius:50%; margin:0; box-shadow:none; cursor:pointer;"><i class="fas fa-paperclip"></i></label>
                        <input type="file" name="gambar" id="gambar" accept="image/*" style="display:none;" onchange="document.getElementById('fileName').innerHTML = this.files[0] ? this.files[0].name : '';">
                        <input type="text" name="pesan" placeholder="Ketik keluhan atau pesan Anda di sini..." autocomplete="off">
                        <button type="submit" class="btn"><i class="fas fa-paper-plane"></i> Kirim</button>
                    </form>
                    <div id="fileName" style="font-size: 0.75rem; color: var(--text-muted); padding: 5px 20px; font-weight: 600;"></div>
                </div>
            @endif
        </div>
    @endif

    <!-- ==================== REKAM MEDIS TAB ==================== -->
    @if($page === 'rekam')
        <div class="card">
            <h2><i class="fas fa-notes-medical"></i> Ajukan Rekam Medis (Konsultasi Gejala)</h2>
            <form action="/pasien/rekam-medis" method="POST">
                @csrf
                <div class="form-group">
                    <label for="keluhan">Keluhan Utama / Gejala yang Dirasakan</label>
                    <textarea name="keluhan" id="keluhan" rows="4" placeholder="Ceritakan keluhan fisik atau gejala yang Anda rasakan sedetail mungkin..." required></textarea>
                </div>
                <div class="form-group">
                    <label for="usia">Usia Saat Ini</label>
                    <input type="number" name="usia" id="usia" value="{{ $user->age }}" placeholder="Usia">
                </div>
                <button type="submit" class="btn"><i class="fas fa-save"></i> Kirim ke Dokter</button>
            </form>
        </div>

        <div class="card">
            <h2><i class="fas fa-history"></i> Riwayat Diagnosa & Rekam Medis</h2>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Keluhan</th>
                            <th>Diagnosa Dokter</th>
                            <th>Resep Obat</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($riwayat as $r)
                            <tr>
                                <td>{{ date('d/m/Y', strtotime($r->tanggal)) }}</td>
                                <td>{{ $r->keluhan }}</td>
                                <td><strong>{{ $r->diagnosa ?: '-' }}</strong></td>
                                <td>
                                    {{ $r->resep ?: '-' }}
                                    @if($r->status === 'selesai' && $r->resep)
                                        <div style="margin-top: 5px;">
                                            <a href="/pasien/pesan-obat/{{ $r->id }}" class="btn btn-sm" style="padding: 4px 10px; font-size: 0.7rem;"><i class="fas fa-shopping-cart"></i> Pesan Obat</a>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <span class="status-badge {{ $r->status === 'selesai' ? 'status-selesai' : 'status-menunggu' }}">
                                        {{ $r->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align: center;">Belum ada riwayat rekam medis terdaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- ==================== ANTREAN TAB ==================== -->
    @if($page === 'antrean')
        <div class="card">
            <h2><i class="fas fa-ticket-alt"></i> Antrean Saya</h2>
            <p style="color: var(--text-muted); margin-bottom: 20px;">
                Jika dokter mendiagnosa <strong>PARAH</strong>, jadwal janji temu dan nomor antrean otomatis akan diterbitkan di sini.
            </p>

            @forelse($antrean as $a)
                <div class="antrean-card" id="antrean-{{ $a->id }}">
                    <div class="grid-details">
                        <strong>Dokter Pemeriksa:</strong> <span>{{ $a->dokter_name }}</span>
                        <strong>Tanggal Janji:</strong> <span>{{ date('d/m/Y', strtotime($a->tanggal)) }}</span>
                        <strong>Jam Praktik:</strong> <span>{{ $a->jam }} WIB</span>
                        <strong>Nomor Antrean:</strong> <span class="queue-no">{{ $a->nomor_antrean }}</span>
                        <strong>Status Tiket:</strong> 
                        <span class="status-badge {{ $a->status === 'menunggu' ? 'status-menunggu' : ($a->status === 'konfirmasi' ? 'status-konfirmasi' : ($a->status === 'selesai' ? 'status-selesai' : 'status-batal')) }}">
                            {{ $a->status }}
                        </span>
                    </div>

                    <!-- Print layout (hidden on screen by CSS mock but shown when printed) -->
                    <div id="print-area-{{ $a->id }}" class="print-area" style="display: none;">
                        <h3><i class="fas fa-notes-medical" style="color: #2b9e6e;"></i> MedicareSystem</h3>
                        <h4>KARTU ANTREAN JANJI TEMU</h4>
                        <hr>
                        <p><strong>Nomor Antrean:</strong> <span style="font-size: 1.4rem; font-weight:800; color:#2b9e6e;">{{ $a->nomor_antrean }}</span></p>
                        <p><strong>Nama Pasien:</strong> {{ $a->pasien_name }}</p>
                        <p><strong>Dokter Spesialis:</strong> {{ $a->dokter_name }}</p>
                        <p><strong>Jadwal Temu:</strong> {{ date('d/m/Y', strtotime($a->tanggal)) }} | {{ $a->jam }}</p>
                        <p><strong>Keluhan:</strong> {{ $a->keluhan }}</p>
                        <hr>
                        <p style="font-size:0.8rem; font-weight: 500;">Silakan datang ke Poliklinik 15 menit sebelum jadwal pemeriksaan.</p>
                        <p style="font-size:0.65rem; color:#94a3b8; margin-top:10px;">© MedicareSystem Digital Health</p>
                    </div>

                    <div style="margin-top: 20px; display: flex; gap: 12px; flex-wrap: wrap;">
                        <button class="btn btn-sm" onclick="cetakAntrean({{ $a->id }})"><i class="fas fa-print"></i> Cetak Tiket</button>
                        @if($a->status === 'menunggu')
                            <a href="/pasien/antrean/{{ $a->id }}/batal" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin membatalkan janji temu ini?')">
                                <i class="fas fa-times"></i> Batalkan Janji
                            </a>
                        @endif
                    </div>
                </div>
            @empty
                <p style="text-align: center; color: var(--text-muted); padding: 40px 0;">Belum ada antrean janji temu aktif.</p>
            @endforelse
        </div>
    @endif

    <!-- ==================== PESAN OBAT TAB ==================== -->
    @if($page === 'pesan_obat')
        <div class="card">
            <h2><i class="fas fa-shopping-cart"></i> Pemesanan Obat Resep</h2>
            <div style="background: #f8fafc; padding: 20px; border-radius: var(--border-radius-lg); margin-bottom: 24px; border: 1px solid var(--border-color);">
                <h4 style="color: var(--primary); margin-bottom: 10px;"><i class="fas fa-file-medical"></i> Rincian Resep Medis</h4>
                <p><strong>Tanggal Diagnosa:</strong> {{ date('d/m/Y', strtotime($rm->tanggal)) }}</p>
                <p><strong>Keluhan:</strong> {{ $rm->keluhan }}</p>
                <p><strong>Diagnosa Dokter:</strong> {{ $rm->diagnosa }}</p>
                <p style="margin-top: 10px; font-size: 1.1rem; color: var(--accent);"><strong>Resep Obat:</strong> {{ $rm->resep }}</p>
            </div>

            <form action="/pasien/pesan-obat" method="POST">
                @csrf
                <input type="hidden" name="rekam_medis_id" value="{{ $rm->id }}">
                
                <div class="form-group">
                    <label for="alamat_kirim">Alamat Pengiriman Lengkap</label>
                    <textarea name="alamat_kirim" id="alamat_kirim" rows="3" placeholder="Masukkan alamat pengiriman lengkap Anda (Jalan, RT/RW, Kecamatan, Kota, Kode Pos)..." required>{{ $user->alamat }}</textarea>
                </div>

                <div class="demo-info" style="margin-bottom: 20px;">
                    <i class="fas fa-info-circle" style="color: var(--accent);"></i> Setelah pesanan dibuat, silakan lakukan pembayaran. Admin akan memproses pengiriman obat ke alamat Anda.
                </div>

                <button type="submit" class="btn"><i class="fas fa-check-circle"></i> Buat Pesanan Obat</button>
                <a href="/pasien/rekam-medis" class="btn btn-danger"><i class="fas fa-times"></i> Batal</a>
            </form>
        </div>
    @endif

    <!-- ==================== DAFTAR PESANAN TAB ==================== -->
    @if($page === 'daftar_pesanan')
        <div class="card">
            <h2><i class="fas fa-pills"></i> Riwayat Pesanan Obat Saya</h2>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>ID Pesanan</th>
                            <th>Tanggal Pemesanan</th>
                            <th>Resep Obat</th>
                            <th>Alamat Kirim</th>
                            <th>Total Pembayaran</th>
                            <th>Status Pesanan</th>
                            <th>Aksi / Bukti</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pesanan as $p)
                            <tr>
                                <td><strong>#ORD-{{ $p->id }}</strong></td>
                                <td>{{ date('d/m/Y H:i', strtotime($p->created_at)) }}</td>
                                <td><span style="font-weight: 600; color: var(--accent);">{{ $p->resep }}</span></td>
                                <td>{{ $p->alamat_kirim }}</td>
                                <td><strong>Rp {{ number_format($p->total_harga, 0, ',', '.') }}</strong></td>
                                <td>
                                    @php
                                        $badgeClass = 'status-menunggu';
                                        if ($p->status === 'diproses') $badgeClass = 'status-konfirmasi';
                                        elseif ($p->status === 'dikirim') $badgeClass = 'status-konfirmasi';
                                        elseif ($p->status === 'selesai') $badgeClass = 'status-selesai';
                                        elseif ($p->status === 'dibatalkan') $badgeClass = 'status-batal';
                                    @endphp
                                    <span class="status-badge {{ $badgeClass }}">{{ str_replace('_', ' ', $p->status) }}</span>
                                    @if($p->status === 'menunggu_pembayaran' && $p->bukti_transfer)
                                        <div style="font-size: 0.7rem; color: var(--text-muted); margin-top: 4px; font-weight: 600;">
                                            <i class="fas fa-clock"></i> Menunggu Verifikasi
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    @if($p->status === 'menunggu_pembayaran')
                                        @if(!$p->bukti_transfer)
                                            <button class="btn btn-sm" style="padding: 4px 10px; font-size: 0.7rem;" onclick="bukaModalBayar({{ $p->id }}, {{ $p->total_harga }})">
                                                <i class="fas fa-wallet"></i> Bayar Sekarang
                                            </button>
                                        @else
                                            <a href="{{ asset('storage/' . $p->bukti_transfer) }}" target="_blank" class="btn btn-sm btn-secondary" style="padding: 4px 10px; font-size: 0.7rem;">
                                                <i class="fas fa-image"></i> Lihat Bukti
                                            </a>
                                        @endif
                                    @elseif(in_array($p->status, ['diproses', 'dikirim', 'selesai']))
                                        <a href="/pasien/pesanan-obat/{{ $p->id }}/cetak-struk" target="_blank" class="btn btn-sm btn-success" style="padding: 4px 10px; font-size: 0.7rem; background: #10b981; border: none; box-shadow: none;">
                                            <i class="fas fa-print"></i> Cetak Struk
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align: center;">Belum ada riwayat pemesanan obat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- PAYMENT MODAL -->
        <div id="modalBayar" class="modal">
            <div class="modal-content" style="max-width: 500px;">
                <h3><i class="fas fa-file-invoice-dollar" style="color: var(--accent);"></i> Pembayaran Pesanan Obat</h3>
                <div style="margin-top: 15px; background: #f8fafc; padding: 15px; border-radius: var(--border-radius-lg); border: 1px solid var(--border-color); font-size: 0.9rem; line-height: 1.6;">
                    <p style="margin-bottom: 5px; color: var(--text-muted);">Silakan lakukan transfer sebesar:</p>
                    <p style="font-size: 1.4rem; font-weight: 800; color: var(--accent); margin-bottom: 15px;" id="bayarTotal">Rp 0</p>
                    <p style="font-weight: 600; margin-bottom: 6px;">Metode Pembayaran (Transfer Bank):</p>
                    <p style="display:flex; align-items:center; gap:8px; margin-bottom: 4px;"><i class="fas fa-university" style="color: var(--primary);"></i> Bank Mandiri: <strong>123-456-7890</strong> <span style="font-size: 0.75rem; color: var(--text-muted);">(a/n Medicare Digital)</span></p>
                    <p style="display:flex; align-items:center; gap:8px;"><i class="fas fa-university" style="color: var(--primary);"></i> Bank BCA: <strong>098-765-4321</strong> <span style="font-size: 0.75rem; color: var(--text-muted);">(a/n Medicare Digital)</span></p>
                </div>
                
                <form id="formBayar" action="" method="POST" enctype="multipart/form-data" style="margin-top: 20px;">
                    @csrf
                    <div class="form-group">
                        <label for="bukti_transfer" style="font-weight: 600;">Unggah Foto Bukti Transfer</label>
                        <input type="file" name="bukti_transfer" id="bukti_transfer" accept="image/*" required style="padding: 10px; border-radius: 8px;">
                    </div>
                    <div style="margin-top: 24px; display: flex; gap: 10px; justify-content: flex-end;">
                        <button type="button" class="btn btn-danger" onclick="tutupModalBayar()">Batal</button>
                        <button type="submit" class="btn"><i class="fas fa-upload"></i> Kirim Bukti Transfer</button>
                    </div>
                </form>
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
                        handleWindowResize: true,
                        eventClick: function(info) {
                            alert('🩺 Dokter Praktik: ' + info.event.title + '\n📅 Silakan buka menu chat untuk konsultasi langsung.');
                        }
                    });
                    calendar.render();
                }
            });
        </script>
    @endif

    @if($page === 'chat' && $selected_dokter)
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

    @if($page === 'antrean')
        <script>
            function cetakAntrean(id) {
                var printContents = document.getElementById('print-area-' + id).innerHTML;
                var originalContents = document.body.innerHTML;
                document.body.innerHTML = printContents;
                window.print();
                document.body.innerHTML = originalContents;
                location.reload();
            }
        </script>
    @endif

    @if($page === 'daftar_pesanan')
        <script>
            function bukaModalBayar(id, total) {
                document.getElementById('formBayar').action = '/pasien/pesanan-obat/' + id + '/upload-bukti';
                document.getElementById('bayarTotal').innerHTML = 'Rp ' + total.toLocaleString('id-ID');
                document.getElementById('modalBayar').style.display = 'flex';
            }
            function tutupModalBayar() {
                document.getElementById('modalBayar').style.display = 'none';
            }
        </script>
    @endif
@endsection
