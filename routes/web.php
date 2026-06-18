<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\PasienController;
use App\Http\Controllers\DokterController;
use App\Http\Controllers\AdminController;

// Landing Page & Mock API
Route::get('/', [LandingController::class, 'index']);
Route::get('/api/bps', [LandingController::class, 'getBpsApi']);
Route::get('/api_bps.php', [LandingController::class, 'getBpsApi']); // fallback routing for compatibility

// Authentication
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::any('/logout', [AuthController::class, 'logout'])->name('logout');

// Pasien Routes
Route::middleware(['auth', 'role:pasien'])->group(function () {
    Route::get('/pasien', [PasienController::class, 'index']);
    Route::get('/pasien/jadwal', [PasienController::class, 'jadwal']);
    Route::get('/pasien/chat', [PasienController::class, 'chat']);
    Route::get('/pasien/chat/{dokter_id}', [PasienController::class, 'chat']);
    Route::post('/pasien/chat/send', [PasienController::class, 'kirimChat']);
    Route::get('/pasien/rekam-medis', [PasienController::class, 'rekamMedis']);
    Route::post('/pasien/rekam-medis', [PasienController::class, 'simpanRekamMedis']);
    Route::get('/pasien/antrean', [PasienController::class, 'antrean']);
    Route::get('/pasien/antrean/{id}/batal', [PasienController::class, 'batalJanji']);
    Route::get('/pasien/pesan-obat/{rekam_medis_id}', [PasienController::class, 'pesanObatForm']);
    Route::post('/pasien/pesan-obat', [PasienController::class, 'simpanPesananObat']);
    Route::get('/pasien/pesanan-obat', [PasienController::class, 'daftarPesananObat']);
    Route::post('/pasien/pesanan-obat/{id}/upload-bukti', [PasienController::class, 'uploadBuktiTransfer']);
    Route::get('/pasien/pesanan-obat/{id}/cetak-struk', [PasienController::class, 'cetakStruk']);
});

// Dokter Routes
Route::middleware(['auth', 'role:dokter'])->group(function () {
    Route::get('/dokter', [DokterController::class, 'index']);
    Route::get('/dokter/jadwal', [DokterController::class, 'jadwal']);
    Route::get('/dokter/kelola-jadwal', [DokterController::class, 'kelolaJadwal']);
    Route::post('/dokter/kelola-jadwal/tambah', [DokterController::class, 'tambahJadwal']);
    Route::get('/dokter/kelola-jadwal/{id}/hapus', [DokterController::class, 'hapusJadwal']);
    Route::get('/dokter/chat', [DokterController::class, 'chat']);
    Route::get('/dokter/chat/{chat_id}', [DokterController::class, 'chat']);
    Route::post('/dokter/chat/reply', [DokterController::class, 'balasChat']);
    Route::get('/dokter/diagnosa', [DokterController::class, 'diagnosa']);
    Route::post('/dokter/diagnosa/simpan', [DokterController::class, 'simpanDiagnosa']);
    Route::get('/dokter/antrean', [DokterController::class, 'antrean']);
    Route::get('/dokter/antrean/{id}/selesai', [DokterController::class, 'selesaiAntrean']);
});

// Admin Routes
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index']);
    Route::get('/admin/dokter', [AdminController::class, 'dokter']);
    Route::post('/admin/dokter/tambah', [AdminController::class, 'tambahDokter']);
    Route::get('/admin/dokter/{id}/hapus', [AdminController::class, 'hapusDokter']);
    Route::get('/admin/pasien', [AdminController::class, 'pasien']);
    Route::post('/admin/pasien/tambah', [AdminController::class, 'tambahPasien']);
    Route::post('/admin/pasien/{id}/update', [AdminController::class, 'updatePasien']);
    Route::get('/admin/pasien/{id}/hapus', [AdminController::class, 'hapusPasien']);
    Route::get('/admin/jadwal', [AdminController::class, 'jadwal']);
    Route::get('/admin/jadwal/{id}/hapus', [AdminController::class, 'hapusJadwal']);
    Route::get('/admin/janji-temu', [AdminController::class, 'janji']);
    Route::get('/admin/janji-temu/{id}/update-status', [AdminController::class, 'updateStatusJanji']);
    Route::get('/admin/rekam-medis', [AdminController::class, 'rekam']);
    Route::get('/admin/berita', [AdminController::class, 'berita']);
    Route::post('/admin/berita/tambah', [AdminController::class, 'tambahBerita']);
    Route::get('/admin/berita/{id}/hapus', [AdminController::class, 'hapusBerita']);
    Route::get('/admin/pesanan-obat', [AdminController::class, 'pesananObat']);
    Route::get('/admin/pesanan-obat/{id}/update-status', [AdminController::class, 'updateStatusPesanan']);
});
