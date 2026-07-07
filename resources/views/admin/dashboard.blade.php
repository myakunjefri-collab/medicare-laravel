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

        <div class="card">
            <h2><i class="fas fa-hospital-user"></i> Status Antrean Aktif Per Poliklinik</h2>
            <p style="color: var(--text-muted); margin-bottom: 15px; font-size: 0.9rem;">Jumlah pasien terdaftar dalam antrean aktif yang sedang berjalan saat ini.</p>
            <div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 15px;">
                @php
                    $polis = [
                        'Poli Umum' => ['color' => '#2b9e6e', 'icon' => 'fas fa-stethoscope'],
                        'Poli Anak' => ['color' => '#3498db', 'icon' => 'fas fa-baby'],
                        'Poli Jantung' => ['color' => '#e74c3c', 'icon' => 'fas fa-heartbeat'],
                        'Poli Saraf' => ['color' => '#9b59b6', 'icon' => 'fas fa-brain'],
                        'Poli Mata' => ['color' => '#1abc9c', 'icon' => 'fas fa-eye'],
                        'Poli Gigi' => ['color' => '#f39c12', 'icon' => 'fas fa-tooth'],
                        'Poli Kandungan' => ['color' => '#e91e63', 'icon' => 'fas fa-baby-carriage']
                    ];
                @endphp
                @foreach($polis as $name => $meta)
                    @php
                        $count = \App\Models\JanjiTemu::where('poli', $name)->whereIn('status', ['menunggu', 'konfirmasi'])->count();
                    @endphp
                    <div class="stat-card" style="border-left: 4px solid {{ $meta['color'] }}; padding: 15px; background: white; border-radius: 12px; display: flex; align-items: center; gap: 12px; box-shadow: var(--shadow-sm);">
                        <div class="icon-wrapper" style="background: rgba(0,0,0,0.02); color: {{ $meta['color'] }}; width: 40px; height: 40px; font-size: 1.2rem; display: flex; align-items: center; justify-content: center; border-radius: 50%;"><i class="{{ $meta['icon'] }}"></i></div>
                        <div>
                            <h3 style="font-size: 1.3rem; font-weight: 800; color: #0b2b4d; margin: 0;">{{ $count }}</h3>
                            <p style="font-size: 0.8rem; color: var(--text-muted); font-weight: 600; margin: 0;">{{ $name }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
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
                                    <div class="action-buttons">
                                        <button class="btn btn-sm btn-warning" onclick="bukaModalEditDokter({{ json_encode($d) }})">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <a href="/admin/dokter/{{ $d->id }}/hapus" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus akun Dokter ini?')">
                                            <i class="fas fa-trash"></i> Hapus
                                        </a>
                                    </div>
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

        <!-- EDIT DOCTOR MODAL -->
        <div id="modalEditDokter" class="modal">
            <div class="modal-content">
                <h3>Edit Data Dokter</h3>
                <form id="formEditDokter" action="" method="POST" style="margin-top: 15px;">
                    @csrf
                    <div class="form-group">
                        <label for="edit_dokter_name">Nama Lengkap</label>
                        <input type="text" name="name" id="edit_dokter_name" required placeholder="Contoh: dr. Ahmad Subarjo, Sp.A">
                    </div>
                    <div class="form-group">
                        <label for="edit_dokter_spesialis">Spesialisasi</label>
                        <input type="text" name="spesialis" id="edit_dokter_spesialis" required placeholder="Contoh: Anak, Jantung, Mata, Saraf">
                    </div>
                    <div class="form-group">
                        <label for="edit_dokter_no_hp">No. Handphone</label>
                        <input type="text" name="no_hp" id="edit_dokter_no_hp" placeholder="Contoh: 08123456789">
                    </div>
                    <div class="form-group">
                        <label for="edit_dokter_username">Username Akun</label>
                        <input type="text" name="username" id="edit_dokter_username" required placeholder="Contoh: ahmadsubarjo">
                    </div>
                    <div class="form-group">
                        <label for="edit_dokter_password">Password Baru (Opsional)</label>
                        <input type="password" name="password" id="edit_dokter_password" placeholder="Kosongkan jika tidak ingin mengubah password">
                    </div>
                    <div style="margin-top: 24px; display: flex; gap: 10px; justify-content: flex-end;">
                        <button type="button" class="btn btn-danger" onclick="tutupModalEditDokter()">Batal</button>
                        <button type="submit" class="btn">Simpan Perubahan</button>
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
                                    <div class="action-buttons">
                                        <button class="btn btn-sm btn-warning" onclick="bukaModalEditPasien({{ json_encode($p) }})">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <a href="/admin/pasien/{{ $p->id }}/hapus" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus akun Pasien ini?')">
                                            <i class="fas fa-trash"></i> Hapus
                                        </a>
                                    </div>
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

        <div class="card" style="display: flex; justify-content: space-between; align-items: center; flex-direction: row; flex-wrap: wrap; gap: 10px;">
            <h2><i class="fas fa-list"></i> Daftar Jadwal Praktik Dokter</h2>
            <button class="btn" onclick="bukaModalJadwal()"><i class="fas fa-plus"></i> Tambah Jadwal Baru</button>
        </div>

        <div class="card">
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
                                    <div class="action-buttons">
                                        <button class="btn btn-sm btn-warning" onclick="bukaModalEditJadwal({{ json_encode($j) }})">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <a href="/admin/jadwal/{{ $j->id }}/hapus" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus jadwal ini?')">
                                            <i class="fas fa-trash"></i> Hapus
                                        </a>
                                    </div>
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
                            <th>Dokter & Poli</th>
                            <th>Tanggal</th>
                            <th>Jam</th>
                            <th>Nomor Antrean</th>
                            <th>Status Antrean</th>
                            <th>Perbarui Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($janji_list as $j)
                            <tr>
                                <td><strong>{{ $j->pasien_name }}</strong></td>
                                <td>
                                    <strong>{{ $j->dokter_name }}</strong><br>
                                    <span style="font-size: 0.8rem; font-weight: 600; color: var(--accent);">{{ $j->poli ?: 'Poli Umum' }}</span>
                                </td>
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
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn btn-sm btn-warning" onclick="bukaModalEditJanji({{ json_encode($j) }})">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <a href="/admin/janji-temu/{{ $j->id }}/hapus" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus janji temu ini?')">
                                            <i class="fas fa-trash"></i> Hapus
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" style="text-align: center;">Belum ada janji temu pasien terdaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- EDIT JANJI TEMU MODAL -->
        <div id="modalEditJanji" class="modal">
            <div class="modal-content">
                <h3>Edit Janji Temu Pasien</h3>
                <form id="formEditJanji" action="" method="POST" style="margin-top: 15px;">
                    @csrf
                    <div class="form-group">
                        <label for="edit_janji_pasien">Pilih Pasien</label>
                        <select name="pasien_id" id="edit_janji_pasien" required style="padding: 10px; border-radius: 8px; width: 100%;">
                            @foreach($pasien_list as $pas)
                                <option value="{{ $pas->id }}">{{ $pas->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit_janji_dokter_name">Nama Dokter</label>
                        <input type="text" name="dokter_name" id="edit_janji_dokter_name" required placeholder="Contoh: dr. Andi Wijaya, Sp.PD">
                    </div>
                    <div class="form-group">
                        <label for="edit_janji_poli">Poli / Spesialisasi</label>
                        <input type="text" name="poli" id="edit_janji_poli" required placeholder="Contoh: Poli Umum, Poli Jantung">
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="edit_janji_tanggal">Tanggal</label>
                            <input type="date" name="tanggal" id="edit_janji_tanggal" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_janji_jam">Jam</label>
                            <input type="text" name="jam" id="edit_janji_jam" required placeholder="Contoh: 10:00 - 11:00">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="edit_janji_antrean">Nomor Antrean</label>
                            <input type="text" name="nomor_antrean" id="edit_janji_antrean" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_janji_status">Status</label>
                            <select name="status" id="edit_janji_status" required style="width: 100%;">
                                <option value="menunggu">Menunggu</option>
                                <option value="konfirmasi">Konfirmasi</option>
                                <option value="selesai">Selesai</option>
                                <option value="dibatalkan">Dibatalkan</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="edit_janji_keluhan">Keluhan</label>
                        <textarea name="keluhan" id="edit_janji_keluhan" rows="3" required placeholder="Tulis keluhan pasien..."></textarea>
                    </div>
                    <div style="margin-top: 24px; display: flex; gap: 10px; justify-content: flex-end;">
                        <button type="button" class="btn btn-danger" onclick="tutupModalEditJanji()">Batal</button>
                        <button type="submit" class="btn">Simpan Perubahan</button>
                    </div>
                </form>
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
                            <th>Aksi</th>
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
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn btn-sm btn-warning" onclick="bukaModalEditRekam({{ json_encode($r) }})">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <a href="/admin/rekam-medis/{{ $r->id }}/hapus" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus rekam medis ini?')">
                                            <i class="fas fa-trash"></i> Hapus
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align: center;">Belum ada rekam medis terdaftar di log.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- EDIT REKAM MEDIS MODAL -->
        <div id="modalEditRekam" class="modal">
            <div class="modal-content">
                <h3>Edit Rekam Medis Pasien</h3>
                <form id="formEditRekam" action="" method="POST" style="margin-top: 15px;">
                    @csrf
                    <div class="form-group">
                        <label for="edit_rekam_pasien">Pilih Pasien</label>
                        <select name="pasien_id" id="edit_rekam_pasien" required style="padding: 10px; border-radius: 8px; width: 100%;">
                            @foreach($pasien_list as $pas)
                                <option value="{{ $pas->id }}">{{ $pas->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit_rekam_keluhan">Keluhan Utama</label>
                        <textarea name="keluhan" id="edit_rekam_keluhan" rows="2" required placeholder="Tulis keluhan utama pasien..."></textarea>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="edit_rekam_usia">Usia (Tahun)</label>
                            <input type="number" name="usia" id="edit_rekam_usia" min="0">
                        </div>
                        <div class="form-group">
                            <label for="edit_rekam_tensi">Tensi Darah</label>
                            <input type="text" name="tensi_darah" id="edit_rekam_tensi" placeholder="Contoh: 120/80">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="edit_rekam_suhu">Suhu Tubuh (°C)</label>
                            <input type="number" step="0.01" name="suhu_tubuh" id="edit_rekam_suhu" placeholder="Contoh: 36.5">
                        </div>
                        <div class="form-group">
                            <label for="edit_rekam_detak">Detak Jantung (bpm)</label>
                            <input type="number" name="detak_jantung" id="edit_rekam_detak" placeholder="Contoh: 80">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="edit_rekam_berat">Berat Badan (kg)</label>
                            <input type="number" name="berat_badan" id="edit_rekam_berat" placeholder="Contoh: 65">
                        </div>
                        <div class="form-group">
                            <label for="edit_rekam_tanggal">Tanggal Pemeriksaan</label>
                            <input type="date" name="tanggal" id="edit_rekam_tanggal" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="edit_rekam_kesimpulan">Kesimpulan Awal / Catatan Vitals</label>
                        <textarea name="kesimpulan_awal" id="edit_rekam_kesimpulan" rows="2" placeholder="Tulis catatan pemeriksaan awal..."></textarea>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="edit_rekam_diagnosa">Diagnosa Akhir Dokter</label>
                            <input type="text" name="diagnosa" id="edit_rekam_diagnosa" placeholder="Diagnosa dokter...">
                        </div>
                        <div class="form-group">
                            <label for="edit_rekam_status">Status Pemeriksaan</label>
                            <select name="status" id="edit_rekam_status" required style="width: 100%;">
                                <option value="menunggu">Menunggu</option>
                                <option value="selesai">Selesai</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="edit_rekam_resep">Resep Obat</label>
                        <textarea name="resep" id="edit_rekam_resep" rows="2" placeholder="Tulis resep obat jika ada..."></textarea>
                    </div>
                    <div style="margin-top: 24px; display: flex; gap: 10px; justify-content: flex-end;">
                        <button type="button" class="btn btn-danger" onclick="tutupModalEditRekam()">Batal</button>
                        <button type="submit" class="btn">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- ==================== BERITA TAB ==================== -->
    @if($page === 'berita')
        <div class="card">
            <h2><i class="fas fa-plus-circle"></i> Tambah Berita Baru</h2>
            <form action="/admin/berita/tambah" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="judul">Judul Berita</label>
                    <input type="text" name="judul" id="judul" required placeholder="Masukkan judul berita kesehatan...">
                </div>
                <div class="form-group">
                    <label for="konten">Konten Berita</label>
                    <textarea name="konten" id="konten" rows="5" required placeholder="Tulis artikel berita secara detail di sini..."></textarea>
                </div>
                <div class="form-group">
                    <label for="gambar">Gambar Utama Berita</label>
                    <input type="file" name="gambar" id="gambar" accept="image/*" style="padding: 10px; border-radius: var(--border-radius-md); background: #f8fafc; border: 2px solid var(--border-color);">
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
                            <th>Gambar</th>
                            <th>Judul Berita</th>
                            <th>Ringkasan Konten</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($berita_list as $b)
                            <tr>
                                <td>{{ date('d/m/Y', strtotime($b->tanggal)) }}</td>
                                <td>
                                    @if($b->gambar)
                                        <img src="{{ asset('storage/' . $b->gambar) }}" alt="{{ $b->judul }}" style="width: 60px; height: 45px; object-fit: cover; border-radius: var(--border-radius-sm); border: 1px solid var(--border-color);">
                                    @else
                                        <span style="color: var(--text-muted); font-size: 0.8rem; font-style: italic;">Tidak ada</span>
                                    @endif
                                </td>
                                <td><strong>{{ $b->judul }}</strong></td>
                                <td>{{ Str::limit($b->konten, 90) }}</td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn btn-sm btn-warning" onclick="bukaModalEditBerita({{ json_encode($b) }})">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <a href="/admin/berita/{{ $b->id }}/hapus" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus berita ini?')">
                                            <i class="fas fa-trash"></i> Hapus
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align: center;">Belum ada artikel berita yang dipublikasikan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- EDIT BERITA MODAL -->
        <div id="modalEditBerita" class="modal">
            <div class="modal-content">
                <h3>Edit Artikel Berita</h3>
                <form id="formEditBerita" action="" method="POST" enctype="multipart/form-data" style="margin-top: 15px;">
                    @csrf
                    <div class="form-group">
                        <label for="edit_berita_judul">Judul Berita</label>
                        <input type="text" name="judul" id="edit_berita_judul" required placeholder="Masukkan judul berita kesehatan...">
                    </div>
                    <div class="form-group">
                        <label for="edit_berita_konten">Konten Berita</label>
                        <textarea name="konten" id="edit_berita_konten" rows="5" required placeholder="Tulis artikel berita secara detail di sini..."></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Gambar Saat Ini</label>
                        <div id="previewEditBeritaGambarDiv" style="margin-top: 5px; margin-bottom: 10px;">
                            <img id="previewEditBeritaGambar" src="" style="max-width: 150px; border-radius: var(--border-radius-sm); border: 1px solid var(--border-color); display: none;">
                            <span id="noEditBeritaGambarText" style="color: var(--text-muted); font-size: 0.85rem; font-style: italic;">Tidak ada gambar</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="edit_berita_gambar">Gambar Utama Baru (Opsional)</label>
                        <input type="file" name="gambar" id="edit_berita_gambar" accept="image/*" style="padding: 10px; border-radius: var(--border-radius-md); background: #f8fafc; border: 2px solid var(--border-color);">
                        <small style="color: var(--text-muted); display: block; margin-top: 4px;">Kosongkan jika tidak ingin mengganti gambar utama.</small>
                    </div>

                    <div style="margin-top: 24px; display: flex; gap: 10px; justify-content: flex-end;">
                        <button type="button" class="btn btn-danger" onclick="tutupModalEditBerita()">Batal</button>
                        <button type="submit" class="btn">Simpan Perubahan</button>
                    </div>
                </form>
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
                            <th>Aksi</th>
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
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn btn-sm btn-warning" onclick="bukaModalEditPesanan({{ json_encode($p) }})">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <a href="/admin/pesanan-obat/{{ $p->id }}/hapus" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus pesanan obat ini?')">
                                            <i class="fas fa-trash"></i> Hapus
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" style="text-align: center;">Belum ada pesanan obat dari pasien.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- EDIT PESANAN OBAT MODAL -->
        <div id="modalEditPesanan" class="modal">
            <div class="modal-content">
                <h3>Edit Pesanan Obat</h3>
                <form id="formEditPesanan" action="" method="POST" style="margin-top: 15px;">
                    @csrf
                    <div class="form-group">
                        <label>Nama Pasien</label>
                        <input type="text" id="edit_pesanan_pasien_name" readonly style="background: #f1f5f9; border-color: #cbd5e1; color: #64748b; width: 100%;">
                    </div>
                    <div class="form-group">
                        <label for="edit_pesanan_resep">Resep / Nama Obat</label>
                        <input type="text" name="resep" id="edit_pesanan_resep" required placeholder="Contoh: Paracetamol 500mg, Amoxicillin">
                    </div>
                    <div class="form-group">
                        <label for="edit_pesanan_alamat">Alamat Kirim</label>
                        <textarea name="alamat_kirim" id="edit_pesanan_alamat" rows="2" required placeholder="Alamat lengkap pengiriman..."></textarea>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="edit_pesanan_harga">Total Harga (Rp)</label>
                            <input type="number" name="total_harga" id="edit_pesanan_harga" min="0" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_pesanan_status">Status Pesanan</label>
                            <select name="status" id="edit_pesanan_status" required style="width: 100%;">
                                <option value="menunggu_pembayaran">Menunggu Pembayaran</option>
                                <option value="diproses">Diproses</option>
                                <option value="dikirim">Dikirim</option>
                                <option value="selesai">Selesai</option>
                                <option value="dibatalkan">Dibatalkan</option>
                            </select>
                        </div>
                    </div>
                    <div style="margin-top: 24px; display: flex; gap: 10px; justify-content: flex-end;">
                        <button type="button" class="btn btn-danger" onclick="tutupModalEditPesanan()">Batal</button>
                        <button type="submit" class="btn">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- ==================== BANTUAN / CS TAB ==================== -->
    @if($page === 'bantuan')
        @php
            $totalTiket = $bantuan_list->count();
            $menungguTiket = $bantuan_list->where('status', 'menunggu')->count();
            $selesaiTiket = $bantuan_list->where('status', 'selesai')->count();
        @endphp

        <style>
            .admin-ticket-container {
                display: flex;
                flex-direction: column;
                gap: 20px;
            }
            .admin-ticket-card {
                background: var(--bg-card);
                border-radius: var(--border-radius-lg);
                border: 1px solid var(--border-color);
                padding: 24px;
                box-shadow: var(--shadow-sm);
                transition: var(--transition);
            }
            .admin-ticket-card:hover {
                box-shadow: var(--shadow-md);
                border-color: rgba(43, 158, 110, 0.2);
            }
            .admin-ticket-card.ticket-pending {
                border-left: 6px solid #f59e0b;
            }
            .admin-ticket-card.ticket-resolved {
                border-left: 6px solid var(--accent);
            }
            .ticket-meta-info {
                display: flex;
                justify-content: space-between;
                align-items: center;
                flex-wrap: wrap;
                gap: 12px;
                border-bottom: 1px solid var(--border-color);
                padding-bottom: 12px;
                margin-bottom: 16px;
            }
            .patient-profile {
                display: flex;
                align-items: center;
                gap: 10px;
            }
            .patient-avatar {
                width: 36px;
                height: 36px;
                border-radius: 50%;
                background: var(--accent-light);
                color: var(--accent);
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: 700;
                font-size: 0.9rem;
            }
            .patient-name-container {
                display: flex;
                flex-direction: column;
            }
            .patient-name {
                font-weight: 700;
                color: var(--primary);
                font-size: 0.95rem;
            }
            .ticket-timestamp {
                font-size: 0.75rem;
                color: var(--text-muted);
            }
            .admin-ticket-body {
                display: flex;
                flex-direction: column;
                gap: 14px;
            }
            .chat-thread {
                display: flex;
                flex-direction: column;
                gap: 12px;
            }
            .chat-msg {
                padding: 14px 18px;
                border-radius: var(--border-radius-md);
                font-size: 0.9rem;
                line-height: 1.5;
                max-width: 85%;
            }
            .chat-msg.msg-incoming {
                background: #f1f5f9;
                color: var(--text-main);
                align-self: flex-start;
                border-top-left-radius: 4px;
                border: 1px solid var(--border-color);
            }
            .chat-msg.msg-outgoing {
                background: var(--accent-light);
                color: #15803d;
                align-self: flex-end;
                border-top-right-radius: 4px;
                border: 1px solid #bbf7d0;
            }
            .chat-lbl {
                font-size: 0.75rem;
                font-weight: 700;
                margin-bottom: 4px;
                display: flex;
                align-items: center;
                gap: 6px;
            }
            .chat-msg.msg-incoming .chat-lbl {
                color: var(--primary);
            }
            .chat-msg.msg-outgoing .chat-lbl {
                color: var(--accent);
            }
            .ticket-action-bar {
                display: flex;
                justify-content: flex-end;
                gap: 12px;
                margin-top: 16px;
                border-top: 1px solid var(--border-color);
                padding-top: 16px;
                flex-wrap: wrap;
            }
        </style>

        <div class="card">
            <h2><i class="fas fa-headset" style="color: var(--accent);"></i> Tiket Layanan Bantuan (Customer Service)</h2>
            <p style="color: var(--text-muted); margin-bottom: 20px;">
                Berikut adalah daftar keluhan, kendala, atau pertanyaan yang diajukan oleh pasien. Berikan tanggapan solusi medis/teknis dan selesaikan tiket bantuan.
            </p>
        </div>

        <!-- Support Statistics Panels -->
        <div class="stats-grid" style="margin-bottom: 24px;">
            <div class="stat-card">
                <div class="icon-wrapper" style="background: rgba(11, 43, 77, 0.1); color: var(--primary);">
                    <i class="fas fa-ticket-alt"></i>
                </div>
                <div>
                    <h3>{{ $totalTiket }}</h3>
                    <p>Total Tiket Masuk</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="icon-wrapper" style="background: rgba(245, 158, 11, 0.1); color: #d97706;">
                    <i class="fas fa-clock"></i>
                </div>
                <div>
                    <h3>{{ $menungguTiket }}</h3>
                    <p>Menunggu Balasan</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="icon-wrapper" style="background: rgba(16, 185, 129, 0.1); color: #15803d;">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div>
                    <h3>{{ $selesaiTiket }}</h3>
                    <p>Terselesaikan</p>
                </div>
            </div>
        </div>

        <div class="card">
            <h2><i class="fas fa-list"></i> Daftar Tiket Masuk</h2>
            <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 20px;">Kelola keluhan aktif pasien:</p>
            
            <div class="admin-ticket-container">
                @forelse($bantuan_list as $b)
                    <div class="admin-ticket-card {{ $b->status === 'selesai' ? 'ticket-resolved' : 'ticket-pending' }}">
                        <div class="ticket-meta-info">
                            <div class="patient-profile">
                                <div class="patient-avatar">
                                    {{ strtoupper(substr($b->pasien_name, 0, 2)) }}
                                </div>
                                <div class="patient-name-container">
                                    <span class="patient-name">{{ $b->pasien_name }}</span>
                                    <span class="ticket-timestamp"><i class="far fa-clock"></i> {{ date('d/m/Y H:i', strtotime($b->created_at)) }}</span>
                                </div>
                            </div>
                            <div>
                                <span class="status-badge {{ $b->status === 'selesai' ? 'status-selesai' : 'status-menunggu' }}">
                                    {{ $b->status === 'selesai' ? 'Terselesaikan' : 'Menunggu Tindakan' }}
                                </span>
                            </div>
                        </div>
                        
                        <div class="admin-ticket-body">
                            <div class="chat-thread">
                                <!-- Patient Request -->
                                <div class="chat-msg msg-incoming">
                                    <div class="chat-lbl"><i class="fas fa-user"></i> Pasien</div>
                                    <p style="margin: 0; white-space: pre-line;">{{ $b->pesan }}</p>
                                </div>
                                
                                <!-- CS Response -->
                                @if($b->balasan)
                                    <div class="chat-msg msg-outgoing">
                                        <div class="chat-lbl"><i class="fas fa-user-shield"></i> Anda (CS)</div>
                                        <p style="margin: 0; white-space: pre-line;">{{ $b->balasan }}</p>
                                    </div>
                                @endif
                            </div>
                            
                            @if($b->status === 'menunggu')
                                <div class="ticket-action-bar">
                                    <button class="btn btn-sm btn-warning" onclick="bukaModalBalasBantuan({{ json_encode($b) }})">
                                        <i class="fas fa-reply"></i> Balas & Selesaikan
                                    </button>
                                    <a href="/admin/bantuan/{{ $b->id }}/selesai" class="btn btn-sm btn-success" onclick="return confirm('Apakah Anda yakin ingin menyelesaikan tiket ini tanpa balasan?')">
                                        <i class="fas fa-check"></i> Tandai Selesai
                                    </a>
                                    <a href="/admin/bantuan/{{ $b->id }}/hapus" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus tiket bantuan ini?')">
                                        <i class="fas fa-trash"></i> Hapus
                                    </a>
                                </div>
                            @else
                                <div class="ticket-action-bar" style="border: none; padding-top: 0; margin-top: 8px; display: flex; align-items: center; justify-content: space-between; width: 100%;">
                                    <span style="font-size: 0.85rem; color: var(--text-muted); font-style: italic;"><i class="fas fa-lock"></i> Tiket Ditutup (Closed)</span>
                                    <a href="/admin/bantuan/{{ $b->id }}/hapus" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus tiket bantuan ini?')">
                                        <i class="fas fa-trash"></i> Hapus
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div style="text-align: center; padding: 40px 20px; color: var(--text-muted);">
                        <i class="fas fa-headset" style="font-size: 3rem; color: #cbd5e1; margin-bottom: 12px;"></i>
                        <p style="font-weight: 500;">Belum ada keluhan bantuan dari pasien yang masuk.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- MODAL BALAS BANTUAN -->
        <div id="modalBalasBantuan" class="modal">
            <div class="modal-content" style="max-width: 550px;">
                <h3><i class="fas fa-reply" style="color: var(--accent);"></i> Balas Tiket Bantuan Pasien</h3>
                <p style="color: var(--text-muted); font-size: 0.85rem; margin-bottom: 12px;">Kirim tanggapan solusi untuk membantu memecahkan kendala pasien.</p>
                
                <div style="margin-top: 10px; padding: 16px; background: #f8fafc; border-radius: var(--border-radius-md); font-size: 0.9rem; border: 1px solid var(--border-color); text-align: left;">
                    <strong style="color: var(--primary); font-size: 0.85rem; display: block; margin-bottom: 4px;"><i class="fas fa-user"></i> Pesan Pasien (<span id="balasPasienNama"></span>):</strong>
                    <p id="balasPesanTeks" style="margin: 0; font-style: italic; color: #475569; white-space: pre-line;"></p>
                </div>
                
                <form id="formBalasBantuan" action="" method="POST" style="margin-top: 18px;">
                    @csrf
                    <div class="form-group">
                        <label for="balasan_teks" style="font-weight: 600;">Tanggapan Solusi CS</label>
                        <textarea name="balasan" id="balasan_teks" rows="5" placeholder="Ketik jawaban atau solusi Anda secara detail di sini..." required style="border-radius: var(--border-radius-md); border: 2px solid var(--border-color);"></textarea>
                    </div>
                    <div style="margin-top: 24px; display: flex; gap: 10px; justify-content: flex-end;">
                        <button type="button" class="btn btn-danger" onclick="tutupModalBalasBantuan()">Batal</button>
                        <button type="submit" class="btn"><i class="fas fa-paper-plane"></i> Kirim & Selesaikan</button>
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
            function bukaModalEditDokter(dokter) {
                document.getElementById('formEditDokter').action = '/admin/dokter/' + dokter.id + '/update';
                document.getElementById('edit_dokter_name').value = dokter.name;
                document.getElementById('edit_dokter_spesialis').value = dokter.spesialis || '';
                document.getElementById('edit_dokter_no_hp').value = dokter.no_hp || '';
                document.getElementById('edit_dokter_username').value = dokter.username;
                document.getElementById('edit_dokter_password').value = '';
                document.getElementById('modalEditDokter').style.display = 'flex';
            }
            function tutupModalEditDokter() {
                document.getElementById('modalEditDokter').style.display = 'none';
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

    @if($page === 'berita')
        <script>
            function bukaModalEditBerita(berita) {
                document.getElementById('formEditBerita').action = '/admin/berita/' + berita.id + '/edit';
                document.getElementById('edit_berita_judul').value = berita.judul;
                document.getElementById('edit_berita_konten').value = berita.konten;
                
                const previewImg = document.getElementById('previewEditBeritaGambar');
                const noImgText = document.getElementById('noEditBeritaGambarText');
                
                if (berita.gambar) {
                    previewImg.src = '/storage/' + berita.gambar;
                    previewImg.style.display = 'block';
                    noImgText.style.display = 'none';
                } else {
                    previewImg.src = '';
                    previewImg.style.display = 'none';
                    noImgText.style.display = 'block';
                }
                
                document.getElementById('modalEditBerita').style.display = 'flex';
            }
            function tutupModalEditBerita() {
                document.getElementById('modalEditBerita').style.display = 'none';
            }
        </script>
    @endif

    @if($page === 'jadwal')
        <!-- ADD JADWAL MODAL -->
        <div id="modalJadwal" class="modal">
            <div class="modal-content">
                <h3>Tambah Jadwal Dokter Baru</h3>
                <form action="/admin/jadwal/tambah" method="POST" style="margin-top: 15px;">
                    @csrf
                    <div class="form-group">
                        <label for="add_jadwal_doctor">Pilih Dokter Spesialis</label>
                        <select name="doctor_id" id="add_jadwal_doctor" required style="padding: 10px; border-radius: 8px;">
                            @foreach($dokter_list as $dok)
                                <option value="{{ $dok->id }}">{{ $dok->name }} ({{ $dok->spesialis }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="add_jadwal_tanggal">Tanggal Praktik</label>
                        <input type="date" name="tanggal" id="add_jadwal_tanggal" required min="{{ date('Y-m-d') }}">
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="add_jadwal_start">Jam Mulai</label>
                            <input type="time" name="start_time" id="add_jadwal_start" required>
                        </div>
                        <div class="form-group">
                            <label for="add_jadwal_end">Jam Selesai</label>
                            <input type="time" name="end_time" id="add_jadwal_end" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="add_jadwal_ruangan">Ruangan</label>
                            <input type="text" name="ruangan" id="add_jadwal_ruangan" placeholder="Contoh: Poliklinik B-10">
                        </div>
                        <div class="form-group">
                            <label for="add_jadwal_kuota">Kuota Pasien</label>
                            <input type="number" name="kuota" id="add_jadwal_kuota" value="10" min="1" required>
                        </div>
                    </div>
                    <div style="margin-top: 24px; display: flex; gap: 10px; justify-content: flex-end;">
                        <button type="button" class="btn btn-danger" onclick="tutupModalJadwal()">Batal</button>
                        <button type="submit" class="btn">Simpan Jadwal</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- EDIT JADWAL MODAL -->
        <div id="modalEditJadwal" class="modal">
            <div class="modal-content">
                <h3>Edit Jadwal Dokter</h3>
                <form id="formEditJadwal" action="" method="POST" style="margin-top: 15px;">
                    @csrf
                    <div class="form-group">
                        <label for="edit_jadwal_doctor">Pilih Dokter Spesialis</label>
                        <select name="doctor_id" id="edit_jadwal_doctor" required style="padding: 10px; border-radius: 8px;">
                            @foreach($dokter_list as $dok)
                                <option value="{{ $dok->id }}">{{ $dok->name }} ({{ $dok->spesialis }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit_jadwal_tanggal">Tanggal Praktik</label>
                        <input type="date" name="tanggal" id="edit_jadwal_tanggal" required>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="edit_jadwal_start">Jam Mulai</label>
                            <input type="time" name="start_time" id="edit_jadwal_start" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_jadwal_end">Jam Selesai</label>
                            <input type="time" name="end_time" id="edit_jadwal_end" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="edit_jadwal_ruangan">Ruangan</label>
                            <input type="text" name="ruangan" id="edit_jadwal_ruangan" placeholder="Contoh: Poliklinik B-10">
                        </div>
                        <div class="form-group">
                            <label for="edit_jadwal_kuota">Kuota Pasien</label>
                            <input type="number" name="kuota" id="edit_jadwal_kuota" min="1" required>
                        </div>
                    </div>
                    <div style="margin-top: 24px; display: flex; gap: 10px; justify-content: flex-end;">
                        <button type="button" class="btn btn-danger" onclick="tutupModalEditJadwal()">Batal</button>
                        <button type="submit" class="btn">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            function bukaModalJadwal() {
                document.getElementById('modalJadwal').style.display = 'flex';
            }
            function tutupModalJadwal() {
                document.getElementById('modalJadwal').style.display = 'none';
            }
            function bukaModalEditJadwal(jadwal) {
                document.getElementById('formEditJadwal').action = '/admin/jadwal/' + jadwal.id + '/update';
                document.getElementById('edit_jadwal_doctor').value = jadwal.doctor_id;
                document.getElementById('edit_jadwal_tanggal').value = jadwal.tanggal;
                document.getElementById('edit_jadwal_start').value = jadwal.start_time;
                document.getElementById('edit_jadwal_end').value = jadwal.end_time;
                document.getElementById('edit_jadwal_ruangan').value = jadwal.ruangan || '';
                document.getElementById('edit_jadwal_kuota').value = jadwal.kuota;
                document.getElementById('modalEditJadwal').style.display = 'flex';
            }
            function tutupModalEditJadwal() {
                document.getElementById('modalEditJadwal').style.display = 'none';
            }
        </script>
    @endif

    @if($page === 'bantuan')
        <script>
            function bukaModalBalasBantuan(bantuan) {
                document.getElementById('formBalasBantuan').action = '/admin/bantuan/' + bantuan.id + '/balas';
                document.getElementById('balasPasienNama').innerText = bantuan.pasien_name;
                document.getElementById('balasPesanTeks').innerText = bantuan.pesan;
                document.getElementById('balasan_teks').value = '';
                document.getElementById('modalBalasBantuan').style.display = 'flex';
            }
            function tutupModalBalasBantuan() {
                document.getElementById('modalBalasBantuan').style.display = 'none';
            }
        </script>
    @endif

    @if($page === 'janji')
        <script>
            function bukaModalEditJanji(janji) {
                document.getElementById('formEditJanji').action = '/admin/janji-temu/' + janji.id + '/update';
                document.getElementById('edit_janji_pasien').value = janji.pasien_id;
                document.getElementById('edit_janji_dokter_name').value = janji.dokter_name;
                document.getElementById('edit_janji_poli').value = janji.poli || '';
                document.getElementById('edit_janji_tanggal').value = janji.tanggal;
                document.getElementById('edit_janji_jam').value = janji.jam;
                document.getElementById('edit_janji_antrean').value = janji.nomor_antrean;
                document.getElementById('edit_janji_status').value = janji.status;
                document.getElementById('edit_janji_keluhan').value = janji.keluhan || '';
                document.getElementById('modalEditJanji').style.display = 'flex';
            }
            function tutupModalEditJanji() {
                document.getElementById('modalEditJanji').style.display = 'none';
            }
        </script>
    @endif

    @if($page === 'rekam')
        <script>
            function bukaModalEditRekam(rekam) {
                document.getElementById('formEditRekam').action = '/admin/rekam-medis/' + rekam.id + '/update';
                document.getElementById('edit_rekam_pasien').value = rekam.pasien_id;
                document.getElementById('edit_rekam_keluhan').value = rekam.keluhan || '';
                document.getElementById('edit_rekam_usia').value = rekam.usia || '';
                document.getElementById('edit_rekam_tensi').value = rekam.tensi_darah || '';
                document.getElementById('edit_rekam_suhu').value = rekam.suhu_tubuh || '';
                document.getElementById('edit_rekam_detak').value = rekam.detak_jantung || '';
                document.getElementById('edit_rekam_berat').value = rekam.berat_badan || '';
                document.getElementById('edit_rekam_tanggal').value = rekam.tanggal;
                document.getElementById('edit_rekam_kesimpulan').value = rekam.kesimpulan_awal || '';
                document.getElementById('edit_rekam_diagnosa').value = rekam.diagnosa || '';
                document.getElementById('edit_rekam_status').value = rekam.status;
                document.getElementById('edit_rekam_resep').value = rekam.resep || '';
                document.getElementById('modalEditRekam').style.display = 'flex';
            }
            function tutupModalEditRekam() {
                document.getElementById('modalEditRekam').style.display = 'none';
            }
        </script>
    @endif

    @if($page === 'pesanan_obat')
        <script>
            function bukaModalEditPesanan(pesanan) {
                document.getElementById('formEditPesanan').action = '/admin/pesanan-obat/' + pesanan.id + '/update';
                document.getElementById('edit_pesanan_pasien_name').value = pesanan.pasien_name;
                document.getElementById('edit_pesanan_resep').value = pesanan.resep;
                document.getElementById('edit_pesanan_alamat').value = pesanan.alamat_kirim;
                document.getElementById('edit_pesanan_harga').value = pesanan.total_harga;
                document.getElementById('edit_pesanan_status').value = pesanan.status;
                document.getElementById('modalEditPesanan').style.display = 'flex';
            }
            function tutupModalEditPesanan() {
                document.getElementById('modalEditPesanan').style.display = 'none';
            }
        </script>
    @endif
@endsection
