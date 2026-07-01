<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\JadwalDokter;
use App\Models\JanjiTemu;
use App\Models\RekamMedis;
use App\Models\Berita;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $total_dokter = User::where('role', 'dokter')->count();
        $total_pasien = User::where('role', 'pasien')->count();
        $total_janji = JanjiTemu::where('status', 'menunggu')->count();
        $total_rekam = RekamMedis::where('status', 'menunggu')->count();

        return view('admin.dashboard', compact('user', 'total_dokter', 'total_pasien', 'total_janji', 'total_rekam'))
            ->with('page', 'home');
    }

    public function dokter()
    {
        $user = Auth::user();
        $dokter_list = User::where('role', 'dokter')->orderBy('name')->get();

        return view('admin.dashboard', compact('user', 'dokter_list'))
            ->with('page', 'dokter');
    }

    public function tambahDokter(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:50|unique:users,username',
            'password' => 'required|string|min:6',
            'name' => 'required|string|max:100',
            'spesialis' => 'required|string|max:100',
            'no_hp' => 'nullable|string|max:20',
        ], [
            'username.unique' => 'Username sudah digunakan!',
            'password.min' => 'Password minimal 6 karakter!',
        ]);

        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->username . '@med.com', // fallback mock email
            'password' => Hash::make($request->password),
            'role' => 'dokter',
            'spesialis' => $request->spesialis,
            'no_hp' => $request->no_hp,
            'phone' => $request->no_hp,
            'is_active' => true,
        ]);

        return redirect('/admin/dokter')->with('success', 'Dokter berhasil ditambahkan!');
    }

    public function updateDokter(Request $request, $id)
    {
        $dokter = User::where('id', $id)->where('role', 'dokter')->firstOrFail();

        $request->validate([
            'name' => 'required|string|max:100',
            'spesialis' => 'required|string|max:100',
            'no_hp' => 'nullable|string|max:20',
            'username' => 'required|string|max:50|unique:users,username,' . $id,
            'password' => 'nullable|string|min:6',
        ], [
            'username.unique' => 'Username sudah digunakan!',
            'password.min' => 'Password minimal 6 karakter!',
        ]);

        $updateData = [
            'name' => $request->name,
            'spesialis' => $request->spesialis,
            'no_hp' => $request->no_hp,
            'phone' => $request->no_hp,
            'username' => $request->username,
            'email' => $request->username . '@med.com',
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $dokter->update($updateData);

        return redirect('/admin/dokter')->with('success', 'Data Dokter berhasil diperbarui!');
    }

    public function hapusDokter($id)
    {
        $dokter = User::where('id', $id)->where('role', 'dokter')->firstOrFail();
        $dokter->delete();

        return redirect('/admin/dokter')->with('success', 'Dokter berhasil dihapus!');
    }

    public function pasien()
    {
        $user = Auth::user();
        $pasien_list = User::where('role', 'pasien')->orderBy('name')->get();

        return view('admin.dashboard', compact('user', 'pasien_list'))
            ->with('page', 'pasien');
    }

    public function jadwal()
    {
        $user = Auth::user();
        $jadwal_list = JadwalDokter::orderBy('tanggal', 'desc')->get();
        $dokter_list = User::where('role', 'dokter')->orderBy('name')->get();

        // Prepare events for FullCalendar
        $events = [];
        foreach ($jadwal_list as $e) {
            $warna = '#2b9e6e';
            if ($e->spesialis === 'Jantung') $warna = '#e74c3c';
            elseif ($e->spesialis === 'Anak') $warna = '#3498db';
            elseif ($e->spesialis === 'Kandungan') $warna = '#e91e63';
            elseif ($e->spesialis === 'Saraf') $warna = '#9b59b6';
            elseif ($e->spesialis === 'Mata') $warna = '#1abc9c';
            elseif ($e->spesialis === 'Gigi') $warna = '#f39c12';

            $events[] = [
                'title' => $e->doctor_name,
                'start' => $e->tanggal,
                'color' => $warna
            ];
        }

        return view('admin.dashboard', compact('user', 'jadwal_list', 'dokter_list', 'events'))
            ->with('page', 'jadwal');
    }

    public function hapusJadwal($id)
    {
        $jadwal = JadwalDokter::findOrFail($id);
        $jadwal->delete();

        return redirect('/admin/jadwal')->with('success', 'Jadwal berhasil dihapus!');
    }

    public function janji()
    {
        $user = Auth::user();
        $janji_list = JanjiTemu::orderBy('tanggal', 'desc')->get();

        return view('admin.dashboard', compact('user', 'janji_list'))
            ->with('page', 'janji');
    }

    public function updateStatusJanji(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:menunggu,konfirmasi,selesai,dibatalkan'
        ]);

        $janji = JanjiTemu::findOrFail($id);
        $janji->update([
            'status' => $request->status
        ]);

        return redirect('/admin/janji-temu')->with('success', 'Status janji temu diperbarui!');
    }

    public function rekam()
    {
        $user = Auth::user();
        $rekam_list = RekamMedis::orderBy('tanggal', 'desc')->get();

        return view('admin.dashboard', compact('user', 'rekam_list'))
            ->with('page', 'rekam');
    }

    public function berita()
    {
        $user = Auth::user();
        $berita_list = Berita::orderBy('tanggal', 'desc')->get();

        return view('admin.dashboard', compact('user', 'berita_list'))
            ->with('page', 'berita');
    }

    public function tambahBerita(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'konten' => 'required|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $gambarPath = null;
        if ($request->hasFile('gambar')) {
            $gambarPath = $request->file('gambar')->store('berita', 'public');
        }

        Berita::create([
            'judul' => $request->judul,
            'konten' => $request->konten,
            'tanggal' => date('Y-m-d'),
            'gambar' => $gambarPath,
        ]);

        return redirect('/admin/berita')->with('success', 'Berita berhasil diterbitkan!');
    }

    public function editBerita(Request $request, $id)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'konten' => 'required|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $berita = Berita::findOrFail($id);

        $updateData = [
            'judul' => $request->judul,
            'konten' => $request->konten,
        ];

        if ($request->hasFile('gambar')) {
            // Delete old image if it exists
            if ($berita->gambar && \Illuminate\Support\Facades\Storage::disk('public')->exists($berita->gambar)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($berita->gambar);
            }
            $updateData['gambar'] = $request->file('gambar')->store('berita', 'public');
        }

        $berita->update($updateData);

        return redirect('/admin/berita')->with('success', 'Berita berhasil diperbarui!');
    }

    public function hapusBerita($id)
    {
        $berita = Berita::findOrFail($id);
        if ($berita->gambar && \Illuminate\Support\Facades\Storage::disk('public')->exists($berita->gambar)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($berita->gambar);
        }
        $berita->delete();

        return redirect('/admin/berita')->with('success', 'Berita berhasil dihapus!');
    }

    public function pesananObat()
    {
        $user = Auth::user();
        $pesanan_list = \App\Models\PesananObat::orderBy('created_at', 'desc')->get();
        return view('admin.dashboard', compact('user', 'pesanan_list'))->with('page', 'pesanan_obat');
    }

    public function updateStatusPesanan($id, Request $request)
    {
        $request->validate([
            'status' => 'required|in:menunggu_pembayaran,diproses,dikirim,selesai,dibatalkan'
        ]);

        $pesanan = \App\Models\PesananObat::findOrFail($id);
        $pesanan->update([
            'status' => $request->status
        ]);

        return redirect('/admin/pesanan-obat')->with('success', 'Status pesanan obat berhasil diperbarui!');
    }

    public function tambahPasien(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|string|email|max:255|unique:users,email',
            'phone' => 'required|string|max:20',
            'age' => 'required|integer|min:1',
            'gender' => 'required|string|in:Laki-laki,Perempuan',
            'alamat' => 'nullable|string|max:255',
            'password' => 'required|string|min:6',
        ], [
            'email.unique' => 'Email sudah terdaftar!',
            'password.min' => 'Password minimal 6 karakter!',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'pasien',
            'age' => $request->age,
            'gender' => $request->gender,
            'phone' => $request->phone,
            'alamat' => $request->alamat,
            'is_active' => true,
        ]);

        return redirect('/admin/pasien')->with('success', 'Akun Pasien berhasil ditambahkan!');
    }

    public function updatePasien(Request $request, $id)
    {
        $pasien = User::where('id', $id)->where('role', 'pasien')->firstOrFail();

        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'phone' => 'required|string|max:20',
            'age' => 'required|integer|min:1',
            'gender' => 'required|string|in:Laki-laki,Perempuan',
            'alamat' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:6',
        ], [
            'email.unique' => 'Email sudah terdaftar!',
            'password.min' => 'Password minimal 6 karakter!',
        ]);

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'age' => $request->age,
            'gender' => $request->gender,
            'phone' => $request->phone,
            'alamat' => $request->alamat,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $pasien->update($updateData);

        return redirect('/admin/pasien')->with('success', 'Akun Pasien berhasil diperbarui!');
    }

    public function hapusPasien($id)
    {
        $pasien = User::where('id', $id)->where('role', 'pasien')->firstOrFail();
        $pasien->delete();

        return redirect('/admin/pasien')->with('success', 'Akun Pasien berhasil dihapus!');
    }

    public function tambahJadwal(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:users,id',
            'tanggal' => 'required|date',
            'start_time' => 'required|string',
            'end_time' => 'required|string',
            'ruangan' => 'nullable|string|max:100',
            'kuota' => 'required|integer|min:1',
        ]);

        $doctor = User::findOrFail($request->doctor_id);

        JadwalDokter::create([
            'doctor_id' => $doctor->id,
            'doctor_name' => $doctor->name,
            'spesialis' => $doctor->spesialis ?? 'Umum',
            'tanggal' => $request->tanggal,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'kuota' => $request->kuota,
            'ruangan' => $request->ruangan,
        ]);

        return redirect('/admin/jadwal')->with('success', 'Jadwal dokter berhasil ditambahkan oleh Admin!');
    }

    public function updateJadwal(Request $request, $id)
    {
        $request->validate([
            'doctor_id' => 'required|exists:users,id',
            'tanggal' => 'required|date',
            'start_time' => 'required|string',
            'end_time' => 'required|string',
            'ruangan' => 'nullable|string|max:100',
            'kuota' => 'required|integer|min:1',
        ]);

        $doctor = User::findOrFail($request->doctor_id);
        $jadwal = JadwalDokter::findOrFail($id);

        $jadwal->update([
            'doctor_id' => $doctor->id,
            'doctor_name' => $doctor->name,
            'spesialis' => $doctor->spesialis ?? 'Umum',
            'tanggal' => $request->tanggal,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'kuota' => $request->kuota,
            'ruangan' => $request->ruangan,
        ]);

        return redirect('/admin/jadwal')->with('success', 'Jadwal dokter berhasil diperbarui oleh Admin!');
    }

    public function bantuan()
    {
        $user = Auth::user();
        $bantuan_list = \App\Models\CustomerService::orderBy('created_at', 'desc')->get();

        return view('admin.dashboard', compact('user', 'bantuan_list'))
            ->with('page', 'bantuan');
    }

    public function balasBantuan(Request $request, $id)
    {
        $request->validate([
            'balasan' => 'required|string',
        ]);

        $bantuan = \App\Models\CustomerService::findOrFail($id);
        $bantuan->update([
            'balasan' => $request->balasan,
            'status' => 'selesai',
        ]);

        return redirect('/admin/bantuan')->with('success', 'Tanggapan Customer Service berhasil dikirim!');
    }

    public function selesaiBantuan($id)
    {
        $bantuan = \App\Models\CustomerService::findOrFail($id);
        $bantuan->update([
            'status' => 'selesai',
        ]);

        return redirect('/admin/bantuan')->with('success', 'Tiket bantuan ditandai selesai!');
    }
}
