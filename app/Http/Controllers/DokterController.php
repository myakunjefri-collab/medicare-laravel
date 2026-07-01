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
use Illuminate\Support\Facades\DB;

class DokterController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $total_diagnosa = RekamMedis::where('status', 'menunggu')->count();
        $total_jadwal = JadwalDokter::where('doctor_id', $user->id)
            ->where('tanggal', '>=', date('Y-m-d'))
            ->count();
        $total_antrean = JanjiTemu::where('dokter_name', $user->name)
            ->where('status', 'menunggu')
            ->count();
        $total_chat = Konsultasi::where('dokter_id', $user->id)->count();

        return view('dokter.dashboard', compact('user', 'total_diagnosa', 'total_jadwal', 'total_antrean', 'total_chat'))
            ->with('page', 'home');
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'status_dokter' => 'required|in:online,sibuk,offline',
        ]);

        $user = Auth::user();
        User::where('id', $user->id)->update([
            'status_dokter' => $request->status_dokter,
        ]);

        return redirect('/dokter')->with('success', 'Status konsultasi Anda berhasil diperbarui!');
    }

    public function jadwal()
    {
        $user = Auth::user();
        $jadwal = JadwalDokter::where('tanggal', '>=', date('Y-m-d'))->orderBy('tanggal')->get();

        // Prepare events for FullCalendar
        $events = [];
        foreach ($jadwal as $e) {
            $warna = '#2b9e6e';
            if ($e->spesialis === 'Jantung') $warna = '#e74c3c';
            elseif ($e->spesialis === 'Anak') $warna = '#3498db';
            
            $events[] = [
                'title' => $e->doctor_name,
                'start' => $e->tanggal,
                'color' => $warna
            ];
        }

        return view('dokter.dashboard', compact('user', 'jadwal', 'events'))
            ->with('page', 'jadwal');
    }

    public function kelolaJadwal()
    {
        $user = Auth::user();
        $my_schedules = JadwalDokter::where('doctor_id', $user->id)
            ->where('tanggal', '>=', date('Y-m-d'))
            ->orderBy('tanggal')
            ->get();

        return view('dokter.dashboard', compact('user', 'my_schedules'))
            ->with('page', 'kelola_jadwal');
    }

    public function tambahJadwal(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'start_time' => 'required|string',
            'end_time' => 'required|string',
            'ruangan' => 'nullable|string|max:100',
            'kuota' => 'required|integer|min:1',
        ]);

        $user = Auth::user();

        JadwalDokter::create([
            'doctor_id' => $user->id,
            'doctor_name' => $user->name,
            'spesialis' => $user->spesialis ?? 'Umum',
            'tanggal' => $request->tanggal,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'kuota' => $request->kuota,
            'ruangan' => $request->ruangan,
        ]);

        return redirect('/dokter/kelola-jadwal')->with('success', 'Jadwal berhasil ditambahkan!');
    }

    public function hapusJadwal($id)
    {
        $user = Auth::user();
        $jadwal = JadwalDokter::where('id', $id)->where('doctor_id', $user->id)->firstOrFail();
        $jadwal->delete();

        return redirect('/dokter/kelola-jadwal')->with('success', 'Jadwal berhasil dihapus!');
    }

    public function chat($chat_id = null)
    {
        $user = Auth::user();
        $chat_list = Konsultasi::where('dokter_id', $user->id)->orderBy('created_at', 'desc')->get();
        $selected_chat = null;
        $pesan_list = collect();

        if ($chat_id) {
            $selected_chat = Konsultasi::where('id', $chat_id)->where('dokter_id', $user->id)->firstOrFail();
            
            // Mark incoming patient messages as read
            PesanChat::where('konsultasi_id', $chat_id)
                ->where('pengirim', 'pasien')
                ->update(['is_read' => true]);

            $pesan_list = PesanChat::where('konsultasi_id', $chat_id)->orderBy('waktu')->get();
        }

        return view('dokter.dashboard', compact('user', 'chat_list', 'selected_chat', 'pesan_list'))
            ->with('page', 'chat_dokter');
    }

    public function balasChat(Request $request)
    {
        $request->validate([
            'konsultasi_id' => 'required|exists:konsultasis,id',
            'pesan_balasan' => 'required_without:gambar|nullable|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $user = Auth::user();
        $chat = Konsultasi::where('id', $request->konsultasi_id)->where('dokter_id', $user->id)->firstOrFail();

        $gambarPath = null;
        if ($request->hasFile('gambar')) {
            $gambarPath = $request->file('gambar')->store('chats', 'public');
        }

        PesanChat::create([
            'konsultasi_id' => $chat->id,
            'pengirim' => 'dokter',
            'pengirim_name' => $user->name,
            'pesan' => $request->pesan_balasan ?? '',
            'gambar' => $gambarPath,
            'waktu' => now(),
            'is_read' => false
        ]);

        return redirect('/dokter/chat/' . $chat->id)->with('success', 'Balasan terkirim!');
    }

    public function akhiriKonsultasi($id)
    {
        $user = Auth::user();
        $chat = Konsultasi::where('id', $id)->where('dokter_id', $user->id)->firstOrFail();

        $chat->update([
            'status' => 'selesai'
        ]);

        return redirect('/dokter/chat/' . $chat->id)->with('success', 'Sesi konsultasi telah diakhiri!');
    }

    public function diagnosa()
    {
        $user = Auth::user();
        $rekam_medis = RekamMedis::where('status', 'menunggu')->orderBy('tanggal', 'desc')->get();

        return view('dokter.dashboard', compact('user', 'rekam_medis'))
            ->with('page', 'diagnosa');
    }

    public function simpanDiagnosa(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:rekam_medis,id',
            'diagnosa' => 'required|string|max:255',
            'status_diagnosa' => 'required|in:ringan,parah',
            'resep' => 'required_if:status_diagnosa,ringan|nullable|string|max:255',
            'tgl_janji' => 'required_if:status_diagnosa,parah|nullable|date',
            'jam_janji' => 'required_if:status_diagnosa,parah|nullable|string',
        ]);

        $user = Auth::user();
        $rm = RekamMedis::findOrFail($request->id);

        $rm->update([
            'diagnosa' => $request->diagnosa,
            'resep' => $request->status_diagnosa === 'ringan' ? $request->resep : null,
            'status' => 'selesai'
        ]);

        if ($request->status_diagnosa === 'parah') {
            // Determine Poliklinik & Prefix
            $spec = strtolower($user->spesialis ?? '');
            $poli = "Poli Umum";
            $prefix = "U";
            if (str_contains($spec, 'anak')) {
                $poli = "Poli Anak";
                $prefix = "A";
            } elseif (str_contains($spec, 'jantung')) {
                $poli = "Poli Jantung";
                $prefix = "J";
            } elseif (str_contains($spec, 'mata')) {
                $poli = "Poli Mata";
                $prefix = "M";
            } elseif (str_contains($spec, 'saraf')) {
                $poli = "Poli Saraf";
                $prefix = "S";
            } elseif (str_contains($spec, 'gigi')) {
                $poli = "Poli Gigi";
                $prefix = "G";
            } elseif (str_contains($spec, 'kandungan')) {
                $poli = "Poli Kandungan";
                $prefix = "K";
            }

            $tgl_janji = $request->tgl_janji ?? date('Y-m-d', strtotime('+2 days'));
            $jam_janji = $request->jam_janji ?? '10:00';

            // Calculate sequential queue number for that polyclinic and date
            $existing_count = JanjiTemu::where('poli', $poli)
                ->where('tanggal', $tgl_janji)
                ->count();
            $seq = $existing_count + 1;
            $no_antrean = $prefix . '-' . str_pad($seq, 2, '0', STR_PAD_LEFT);

            JanjiTemu::create([
                'pasien_id' => $rm->pasien_id,
                'pasien_name' => $rm->pasien_name,
                'dokter_name' => $user->name,
                'poli' => $poli,
                'tanggal' => $tgl_janji,
                'jam' => $jam_janji,
                'status' => 'menunggu',
                'nomor_antrean' => $no_antrean,
                'keluhan' => $rm->keluhan,
            ]);

            return redirect('/dokter/diagnosa')->with('alert_parah', [
                'no_antrean' => $no_antrean,
                'tanggal' => date('d/m/Y', strtotime($tgl_janji)),
                'jam' => $jam_janji
            ]);
        }

        return redirect('/dokter/diagnosa')->with('success', 'Diagnosa Ringan berhasil disimpan! Resep: ' . $request->resep);
    }

    public function antrean()
    {
        $user = Auth::user();
        $antrean = JanjiTemu::where('dokter_name', $user->name)->orderBy('tanggal')->get();

        return view('dokter.dashboard', compact('user', 'antrean'))
            ->with('page', 'lihat_antrean');
    }

    public function selesaiAntrean($id)
    {
        $user = Auth::user();
        $janji = JanjiTemu::where('id', $id)->where('dokter_name', $user->name)->firstOrFail();
        
        $janji->update([
            'status' => 'selesai'
        ]);

        return redirect('/dokter/antrean')->with('success', 'Antrean pasien divalidasi selesai!');
    }
}
