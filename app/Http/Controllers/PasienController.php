<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\JadwalDokter;
use App\Models\Konsultasi;
use App\Models\PesanChat;
use App\Models\RekamMedis;
use App\Models\JanjiTemu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PasienController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $total_dokter = User::where('role', 'dokter')->count();
        $total_antrean = JanjiTemu::where('pasien_id', $user->id)->where('status', 'menunggu')->count();
        $total_chat = Konsultasi::where('pasien_id', $user->id)->count();
        
        $dokter_list = User::where('role', 'dokter')->orderBy('name')->get();

        return view('pasien.dashboard', compact('user', 'total_dokter', 'total_antrean', 'total_chat', 'dokter_list'))
            ->with('page', 'home');
    }

    public function jadwal()
    {
        $user = Auth::user();
        $jadwal = JadwalDokter::where('tanggal', '>=', date('Y-m-d'))->orderBy('tanggal')->get();
        
        // Prepare events for FullCalendar
        $events = [];
        $all_schedules = JadwalDokter::all();
        foreach ($all_schedules as $e) {
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

        return view('pasien.dashboard', compact('user', 'jadwal', 'events'))
            ->with('page', 'jadwal');
    }

    public function chat($dokter_id = null)
    {
        $user = Auth::user();
        $dokter_list = User::where('role', 'dokter')->orderBy('name')->get();
        $selected_dokter = null;
        $pesan_list = collect();
        $konsultasi_id = null;

        if ($dokter_id) {
            $selected_dokter = User::where('id', $dokter_id)->where('role', 'dokter')->firstOrFail();
            
            // Check if consultation session already exists
            $konsultasi = Konsultasi::firstOrCreate(
                [
                    'pasien_id' => $user->id,
                    'dokter_id' => $dokter_id
                ],
                [
                    'pasien_name' => $user->name,
                    'dokter_name' => $selected_dokter->name
                ]
            );

            $konsultasi_id = $konsultasi->id;
            $pesan_list = PesanChat::where('konsultasi_id', $konsultasi_id)->orderBy('waktu')->get();
        }

        return view('pasien.dashboard', compact('user', 'dokter_list', 'selected_dokter', 'pesan_list', 'konsultasi_id'))
            ->with('page', 'chat');
    }

    public function kirimChat(Request $request)
    {
        $request->validate([
            'dokter_id' => 'required|exists:users,id',
            'pesan' => 'required_without:gambar|nullable|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $user = Auth::user();
        $dokter_id = $request->dokter_id;
        $dokter = User::findOrFail($dokter_id);

        $konsultasi = Konsultasi::firstOrCreate(
            [
                'pasien_id' => $user->id,
                'dokter_id' => $dokter_id
            ],
            [
                'pasien_name' => $user->name,
                'dokter_name' => $dokter->name
            ]
        );

        $gambarPath = null;
        if ($request->hasFile('gambar')) {
            $gambarPath = $request->file('gambar')->store('chats', 'public');
        }

        PesanChat::create([
            'konsultasi_id' => $konsultasi->id,
            'pengirim' => 'pasien',
            'pengirim_name' => $user->name,
            'pesan' => $request->pesan ?? '',
            'gambar' => $gambarPath,
            'waktu' => now(),
            'is_read' => false
        ]);

        return redirect('/pasien/chat/' . $dokter_id)->with('success', 'Pesan terkirim!');
    }

    public function rekamMedis()
    {
        $user = Auth::user();
        $riwayat = RekamMedis::where('pasien_id', $user->id)->orderBy('tanggal', 'desc')->get();

        return view('pasien.dashboard', compact('user', 'riwayat'))
            ->with('page', 'rekam');
    }

    public function simpanRekamMedis(Request $request)
    {
        $request->validate([
            'keluhan' => 'required|string',
            'usia' => 'nullable|integer',
        ]);

        $user = Auth::user();

        RekamMedis::create([
            'pasien_id' => $user->id,
            'pasien_name' => $user->name,
            'keluhan' => $request->keluhan,
            'usia' => $request->usia ?? $user->age,
            'tanggal' => date('Y-m-d'),
            'status' => 'menunggu'
        ]);

        return redirect('/pasien/rekam-medis')->with('success', 'Rekam medis berhasil dikirim ke Dokter!');
    }

    public function antrean()
    {
        $user = Auth::user();
        $antrean = JanjiTemu::where('pasien_id', $user->id)->orderBy('tanggal', 'desc')->get();

        return view('pasien.dashboard', compact('user', 'antrean'))
            ->with('page', 'antrean');
    }

    public function batalJanji($id)
    {
        $user = Auth::user();
        $janji = JanjiTemu::where('id', $id)->where('pasien_id', $user->id)->firstOrFail();
        
        $janji->update([
            'status' => 'dibatalkan'
        ]);

        return redirect('/pasien/antrean')->with('success', 'Janji temu berhasil dibatalkan!');
    }

    public function pesanObatForm($rekam_medis_id)
    {
        $user = Auth::user();
        $rm = RekamMedis::where('id', $rekam_medis_id)->where('pasien_id', $user->id)->firstOrFail();
        return view('pasien.dashboard', compact('user', 'rm'))->with('page', 'pesan_obat');
    }
    public function simpanPesananObat(Request $request)
    {
        $request->validate([
            'rekam_medis_id' => 'required|exists:rekam_medis,id',
            'alamat_kirim' => 'required|string|max:255',
        ]);

        $user = Auth::user();
        $rm = RekamMedis::where('id', $request->rekam_medis_id)->where('pasien_id', $user->id)->firstOrFail();

        $resep = strtolower($rm->resep ?: '');
        $harga_obat = 0;
        
        // Predefined market price map for common medicines (in Rupiah)
        $daftar_harga = [
            'paracetamol' => 8000,
            'sanamol' => 10000,
            'panadol' => 12000,
            'bodrex' => 6000,
            'amoxicillin' => 15000,
            'antibiotik' => 18000,
            'antasida' => 8000,
            'promag' => 9000,
            'mylanta' => 12000,
            'ranitidin' => 14000,
            'omeprazole' => 22000,
            'obh' => 15000,
            'siladex' => 16000,
            'sanaflu' => 10000,
            'enervon' => 12000,
            'vitamin' => 10000,
            'asam mefenamat' => 10000,
            'loperamide' => 8000,
            'diapet' => 7000,
            'cetirizine' => 11000,
            'metformin' => 16000,
            'amlodipine' => 20000,
            'salbutamol' => 18000,
            'betadine' => 15000,
        ];

        $matched = false;
        foreach ($daftar_harga as $key => $val) {
            if (str_contains($resep, $key)) {
                $harga_obat += $val;
                $matched = true;
            }
        }

        // If no predefined medicine matches, use a base price based on length of prescription text or a default
        if (!$matched || $harga_obat === 0) {
            $harga_obat = rand(15, 45) * 1000; // default range for generic/custom obat
        }

        $total_harga = $harga_obat + 15000; // with shipping fee

        \App\Models\PesananObat::create([
            'pasien_id' => $user->id,
            'pasien_name' => $user->name,
            'rekam_medis_id' => $rm->id,
            'resep' => $rm->resep ?: 'Obat Umum',
            'alamat_kirim' => $request->alamat_kirim,
            'status' => 'menunggu_pembayaran',
            'total_harga' => $total_harga
        ]);

        return redirect('/pasien/pesanan-obat')->with('success', 'Pesanan obat berhasil dibuat! Silakan lakukan pembayaran.');
    }

    public function daftarPesananObat()
    {
        $user = Auth::user();
        $pesanan = \App\Models\PesananObat::where('pasien_id', $user->id)->orderBy('created_at', 'desc')->get();
        return view('pasien.dashboard', compact('user', 'pesanan'))->with('page', 'daftar_pesanan');
    }

    public function uploadBuktiTransfer(Request $request, $id)
    {
        $request->validate([
            'bukti_transfer' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $user = Auth::user();
        $pesanan = \App\Models\PesananObat::where('id', $id)->where('pasien_id', $user->id)->firstOrFail();

        $buktiPath = null;
        if ($request->hasFile('bukti_transfer')) {
            $buktiPath = $request->file('bukti_transfer')->store('bukti_transfer', 'public');
        }

        $pesanan->update([
            'bukti_transfer' => $buktiPath,
        ]);

        return redirect('/pasien/pesanan-obat')->with('success', 'Bukti transfer berhasil diunggah! Menunggu konfirmasi admin.');
    }

    public function cetakStruk($id)
    {
        $user = Auth::user();
        $pesanan = \App\Models\PesananObat::where('id', $id)->where('pasien_id', $user->id)->firstOrFail();
        return view('pasien.struk', compact('user', 'pesanan'));
    }
}
