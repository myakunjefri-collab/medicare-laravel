@extends('layouts.app')

@section('title', 'MedicareSystem - Dashboard Admin')

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
                <div class="icon-wrapper"><i class="fas fa-users"></i></div>
                <div>
                    <h3>{{ $total_pasien }}</h3>
                    <p>Pasien Terdaftar</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="icon-wrapper"><i class="fas fa-ticket-alt"></i></div>
                <div>
                    <h3>{{ $total_janji }}</h3>
                    <p>Janji Menunggu</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="icon-wrapper"><i class="fas fa-notes-medical"></i></div>
                <div>
                    <h3>{{ $total_rekam }}</h3>
                    <p>Rekam Medis Menunggu</p>
                </div>
            </div>
        </div>

        <div class="card">
            <h2>Selamat Datang, Admin</h2>
            <p style="color: var(--text-muted); line-height: 1.6;">
                MedicareSystem Administration Portal. Gunakan panel navigasi di sebelah kiri untuk mengelola daftar dokter, melihat data pasien terdaftar, memantau jadwal praktik, memverifikasi janji temu dan antrean, serta mempublikasikan portal berita kesehatan.
            </p>
        </div>
    @endif

    <!-- ==================== KELOLA DOKTER TAB ==================== -->
    @if($page === 'dokter')
        <div class="card">
            <h2><i class="fas fa-user-plus"></i> Kelola Dokter Spesialis</h2>
            <button class="btn" onclick="bukaModalDokter()"><i class="fas fa-plus"></i> Tambah Dokter Baru</button>
        </div>

        <div class="card">
            <h2><i class="fas fa-user-md"></i> Daftar Dokter Aktif</h2>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Nama Lengkap</th>
                            <th>Spesialisasi</th>
                            <th>No. Handphone</th>
                            <th>Username</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dokter_list as $d)
                            <tr>
                                <td><strong>{{ $d->name }}</strong></td>
                                <td>{{ $d->spesialis ?: 'Umum' }}</td>
                                <td>{{ $d->no_hp ?: '-' }}</td>
                                <td>{{ $d->username }}</td>
                                <td>
                                    <a href="/admin/dokter/{{ $d->id }}/hapus" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus akun Dokter ini?')">
                                        <i class="fas fa-trash"></i> Hapus
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align: center;">Belum ada dokter terdaftar di sistem.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ADD DOCTOR MODAL -->
        <div id="modalDokter" class="modal">
            <div class="modal-content">
                <h3>Tambah Dokter Baru</h3>
                <form action="/admin/dokter/tambah" method="POST" style="margin-top: 15px;">
                    @csrf
                    <div class="form-group">
                        <label for="name">Nama Lengkap</label>
                        <input type="text" name="name" id="name" required placeholder="Contoh: dr. Ahmad Subarjo, Sp.A">
                    </div>
                    <div class="form-group">
                        <label for="spesialis">Spesialisasi</label>
                        <input type="text" name="spesialis" id="spesialis" required placeholder="Contoh: Anak, Jantung, Mata, Saraf">
                    </div>
                    <div class="form-group">
                        <label for="no_hp">No. Handphone</label>
                        <input type="text" name="no_hp" id="no_hp" placeholder="Contoh: 08123456789">
                    </div>
                    <div class="form-group">
                        <label for="username">Username Akun</label>
                        <input type="text" name="username" id="username" required placeholder="Contoh: ahmadsubarjo">
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" name="password" id="password" required placeholder="Minimal 6 karakter">
                    </div>
                    <div style="margin-top: 24px; display: flex; gap: 10px; justify-content: flex-end;">
                        <button type="button" class="btn btn-danger" onclick="tutupModalDokter()">Batal</button>
                        <button type="submit" class="btn">Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- ==================== KELOLA PASIEN TAB ==================== -->
    @if($page === 'pasien')
        <div class="card">
            <h2><i class="fas fa-user-plus"></i> Kelola Akun Pasien</h2>
            <button class="btn" onclick="bukaModalPasien()"><i class="fas fa-plus"></i> Tambah Pasien Baru</button>
        </div>

        <div class="card">
            <h2><i class="fas fa-users"></i> Daftar Pasien Terdaftar</h2>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Nama Lengkap</th>
                            <th>Email</th>
                            <th>No. Telepon</th>
                            <th>Usia</th>
                            <th>Alamat</th>
                            <th>Tanggal Terdaftar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pasien_list as $p)
                            <tr>
                                <td><strong>{{ $p->name }}</strong></td>
                                <td>{{ $p->email }}</td>
                                <td>{{ $p->phone ?: '-' }}</td>
                                <td>{{ $p->age ?: '-' }} Tahun</td>
                                <td>{{ $p->alamat ?: '-' }}</td>
                                <td>{{ date('d/m/Y H:i', strtotime($p->created_at)) }}</td>
                                <td>
                                    <button class="btn btn-sm btn-warning" onclick="bukaModalEditPasien({{ json_encode($p) }})">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <a href="/admin/pasien/{{ $p->id }}/hapus" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus akun Pasien ini?')">
                                        <i class="fas fa-trash"></i> Hapus
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align: center;">Belum ada pasien yang mendaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ADD PATIENT MODAL -->
        <div id="modalPasien" class="modal">
            <div class="modal-content">
                <h3>Tambah Pasien Baru</h3>
                <form action="/admin/pasien/tambah" method="POST" style="margin-top: 15px;">
                    @csrf
                    <div class="form-group">
                        <label for="name">Nama Lengkap</label>
                        <input type="text" name="name" required placeholder="Contoh: Budi Santoso">
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" name="email" required placeholder="Contoh: budi@gmail.com">
                    </div>
                    <div class="form-group">
                        <label for="phone">No. Telepon</label>
                        <input type="text" name="phone" required placeholder="Contoh: 08123456789">
                    </div>
                    <div class="form-group">
                        <label for="age">Usia (Tahun)</label>
                        <input type="number" name="age" required min="1" placeholder="Contoh: 25">
                    </div>
                    <div class="form-group">
                        <label for="gender">Jenis Kelamin</label>
                        <select name="gender" required>
                            <option value="Laki-laki">Laki-laki</option>
                            <option value="Perempuan">Perempuan</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="alamat">Alamat</label>
                        <textarea name="alamat" rows="2" placeholder="Contoh: Jl. Merdeka No. 10"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" name="password" required placeholder="Minimal 6 karakter">
                    </div>
                    <div style="margin-top: 24px; display: flex; gap: 10px; justify-content: flex-end;">
                        <button type="button" class="btn btn-danger" onclick="tutupModalPasien()">Batal</button>
                        <button type="submit" class="btn">Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- EDIT PATIENT MODAL -->
        <div id="modalEditPasien" class="modal">
            <div class="modal-content">
                <h3>Edit Akun Pasien</h3>
                <form id="formEditPasien" action="" method="POST" style="margin-top: 15px;">
                    @csrf
                    <div class="form-group">
                        <label for="edit_name">Nama Lengkap</label>
                        <input type="text" name="name" id="edit_name" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_email">Email</label>
                        <input type="email" name="email" id="edit_email" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_phone">No. Telepon</label>
                        <input type="text" name="phone" id="edit_phone" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_age">Usia (Tahun)</label>
                        <input type="number" name="age" id="edit_age" required min="1">
                    </div>
                    <div class="form-group">
                        <label for="edit_gender">Jenis Kelamin</label>
                        <select name="gender" id="edit_gender" required>
                            <option value="Laki-laki">Laki-laki</option>
                            <option value="Perempuan">Perempuan</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit_alamat">Alamat</label>
                        <textarea name="alamat" id="edit_alamat" rows="2"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="edit_password">Password Baru (Opsional)</label>
                        <input type="password" name="password" id="edit_password" placeholder="Kosongkan jika tidak ingin mengubah password">
                    </div>
                    <div style="margin-top: 24px; display: flex; gap: 10px; justify-content: flex-end;">
                        <button type="button" class="btn btn-danger" onclick="tutupModalEditPasien()">Batal</button>
                        <button type="submit" class="btn">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
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
                            <th>Kuota</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($jadwal_list as $j)
                            <tr>
                                <td><strong>{{ $j->doctor_name }}</strong></td>
                                <td>{{ $j->spesialis }}</td>
                                <td>{{ date('d/m/Y', strtotime($j->tanggal)) }}</td>
                                <td>{{ $j->start_time }} - {{ $j->end_time }}</td>
                                <td>{{ $j->ruangan ?: '-' }}</td>
                                <td>{{ $j->kuota }} Pasien</td>
                                <td>
                                    <a href="/admin/jadwal/{{ $j->id }}/hapus" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus jadwal ini?')">
                                        <i class="fas fa-trash"></i> Hapus
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align: center;">Tidak ada jadwal praktik terdaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- ==================== JANJI TEMU TAB ==================== -->
    @if($page === 'janji')
        <div class="card">
            <h2><i class="fas fa-ticket-alt"></i> Kelola Janji Temu Pasien (Antrean)</h2>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Nama Pasien</th>
                            <th>Dokter Rujukan</th>
                            <th>Tanggal</th>
                            <th>Jam</th>
                            <th>Nomor Antrean</th>
                            <th>Status Antrean</th>
                            <th>Perbarui Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($janji_list as $j)
                            <tr>
                                <td><strong>{{ $j->pasien_name }}</strong></td>
                                <td>{{ $j->dokter_name }}</td>
                                <td>{{ date('d/m/Y', strtotime($j->tanggal)) }}</td>
                                <td>{{ $j->jam }} WIB</td>
                                <td><span class="queue-no" style="font-size: 1.1rem;">{{ $j->nomor_antrean }}</span></td>
                                <td>
                                    <span class="status-badge {{ $j->status === 'menunggu' ? 'status-menunggu' : ($j->status === 'konfirmasi' ? 'status-konfirmasi' : ($j->status === 'selesai' ? 'status-selesai' : 'status-batal')) }}">
                                        {{ $j->status }}
                                    </span>
                                </td>
                                <td>
                                    <select onchange="window.location.href='/admin/janji-temu/' + {{ $j->id }} + '/update-status?status=' + this.value" style="padding: 6px 12px; font-size: 0.8rem; border-radius: 20px;">
                                        <option value="menunggu" {{ $j->status === 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                                        <option value="konfirmasi" {{ $j->status === 'konfirmasi' ? 'selected' : '' }}>Konfirmasi</option>
                                        <option value="selesai" {{ $j->status === 'selesai' ? 'selected' : '' }}>Selesai</option>
                                        <option value="dibatalkan" {{ $j->status === 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                                    </select>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align: center;">Belum ada janji temu pasien terdaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- ==================== REKAM MEDIS TAB ==================== -->
    @if($page === 'rekam')
        <div class="card">
            <h2><i class="fas fa-notes-medical"></i> Log Rekam Medis Pasien</h2>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Nama Pasien</th>
                            <th>Keluhan</th>
                            <th>Diagnosa Dokter</th>
                            <th>Resep Obat</th>
                            <th>Status Pemeriksaan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rekam_list as $r)
                            <tr>
                                <td>{{ date('d/m/Y', strtotime($r->tanggal)) }}</td>
                                <td><strong>{{ $r->pasien_name }}</strong></td>
                                <td>{{ $r->keluhan }}</td>
                                <td><strong>{{ $r->diagnosa ?: '-' }}</strong></td>
                                <td>{{ $r->resep ?: '-' }}</td>
                                <td>
                                    <span class="status-badge {{ $r->status === 'selesai' ? 'status-selesai' : 'status-menunggu' }}">
                                        {{ $r->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align: center;">Belum ada rekam medis terdaftar di log.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- ==================== BERITA TAB ==================== -->
    @if($page === 'berita')
        <div class="card">
            <h2><i class="fas fa-plus-circle"></i> Tambah Berita Baru</h2>
            <form action="/admin/berita/tambah" method="POST">
                @csrf
                <div class="form-group">
                    <label for="judul">Judul Berita</label>
                    <input type="text" name="judul" id="judul" required placeholder="Masukkan judul berita kesehatan...">
                </div>
                <div class="form-group">
                    <label for="konten">Konten Berita</label>
                    <textarea name="konten" id="konten" rows="5" required placeholder="Tulis artikel berita secara detail di sini..."></textarea>
                </div>
                <button type="submit" class="btn"><i class="fas fa-paper-plane"></i> Publikasikan Artikel</button>
            </form>
        </div>

        <div class="card">
            <h2><i class="fas fa-newspaper"></i> Daftar Artikel Terbit</h2>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Judul Berita</th>
                            <th>Ringkasan Konten</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($berita_list as $b)
                            <tr>
                                <td>{{ date('d/m/Y', strtotime($b->tanggal)) }}</td>
                                <td><strong>{{ $b->judul }}</strong></td>
                                <td>{{ Str::limit($b->konten, 90) }}</td>
                                <td>
                                    <a href="/admin/berita/{{ $b->id }}/hapus" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus berita ini?')">
                                        <i class="fas fa-trash"></i> Hapus
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="text-align: center;">Belum ada artikel berita yang dipublikasikan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- ==================== KELOLA PESANAN OBAT TAB ==================== -->
    @if($page === 'pesanan_obat')
        <div class="card">
            <h2><i class="fas fa-pills"></i> Kelola Pesanan Obat Pasien</h2>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>ID Pesanan</th>
                            <th>Nama Pasien</th>
                            <th>Resep Obat</th>
                            <th>Alamat Kirim</th>
                            <th>Total Harga</th>
                            <th>Tanggal Pemesanan</th>
                            <th>Bukti Transfer</th>
                            <th>Status</th>
                            <th>Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pesanan_list as $p)
                            <tr>
                                <td><strong>#ORD-{{ $p->id }}</strong></td>
                                <td>{{ $p->pasien_name }}</td>
                                <td><span style="font-weight:600; color: var(--accent);">{{ $p->resep }}</span></td>
                                <td>{{ $p->alamat_kirim }}</td>
                                <td><strong>Rp {{ number_format($p->total_harga, 0, ',', '.') }}</strong></td>
                                <td>{{ date('d/m/Y H:i', strtotime($p->created_at)) }}</td>
                                <td>
                                    @if($p->bukti_transfer)
                                        <a href="{{ asset('storage/' . $p->bukti_transfer) }}" target="_blank" class="btn btn-sm" style="padding: 4px 10px; font-size: 0.7rem; background: var(--primary); border: none; box-shadow: none; display: inline-flex; align-items: center; gap: 4px;">
                                            <i class="fas fa-image"></i> Lihat Bukti
                                        </a>
                                    @else
                                        <span style="color: var(--text-muted); font-size: 0.8rem; font-style: italic;">Belum Bayar</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $badgeClass = 'status-menunggu';
                                        if ($p->status === 'diproses') $badgeClass = 'status-konfirmasi';
                                        elseif ($p->status === 'dikirim') $badgeClass = 'status-konfirmasi';
                                        elseif ($p->status === 'selesai') $badgeClass = 'status-selesai';
                                        elseif ($p->status === 'dibatalkan') $badgeClass = 'status-batal';
                                    @endphp
                                    <span class="status-badge {{ $badgeClass }}">{{ str_replace('_', ' ', $p->status) }}</span>
                                </td>
                                <td>
                                    <select onchange="window.location.href='/admin/pesanan-obat/' + {{ $p->id }} + '/update-status?status=' + this.value" style="padding: 6px 12px; font-size: 0.8rem; border-radius: 20px;">
                                        <option value="menunggu_pembayaran" {{ $p->status === 'menunggu_pembayaran' ? 'selected' : '' }}>Menunggu Pembayaran</option>
                                        <option value="diproses" {{ $p->status === 'diproses' ? 'selected' : '' }}>Diproses</option>
                                        <option value="dikirim" {{ $p->status === 'dikirim' ? 'selected' : '' }}>Dikirim</option>
                                        <option value="selesai" {{ $p->status === 'selesai' ? 'selected' : '' }}>Selesai</option>
                                        <option value="dibatalkan" {{ $p->status === 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                                    </select>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" style="text-align: center;">Belum ada pesanan obat dari pasien.</td>
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

    @if($page === 'dokter')
        <script>
            function bukaModalDokter() {
                document.getElementById('modalDokter').style.display = 'flex';
            }
            function tutupModalDokter() {
                document.getElementById('modalDokter').style.display = 'none';
            }
        </script>
    @endif

    @if($page === 'pasien')
        <script>
            function bukaModalPasien() {
                document.getElementById('modalPasien').style.display = 'flex';
            }
            function tutupModalPasien() {
                document.getElementById('modalPasien').style.display = 'none';
            }
            function bukaModalEditPasien(pasien) {
                document.getElementById('formEditPasien').action = '/admin/pasien/' + pasien.id + '/update';
                document.getElementById('edit_name').value = pasien.name;
                document.getElementById('edit_email').value = pasien.email;
                document.getElementById('edit_phone').value = pasien.phone || '';
                document.getElementById('edit_age').value = pasien.age || '';
                document.getElementById('edit_gender').value = pasien.gender || 'Laki-laki';
                document.getElementById('edit_alamat').value = pasien.alamat || '';
                document.getElementById('edit_password').value = '';
                document.getElementById('modalEditPasien').style.display = 'flex';
            }
            function tutupModalEditPasien() {
                document.getElementById('modalEditPasien').style.display = 'none';
            }
        </script>
    @endif
@endsection
