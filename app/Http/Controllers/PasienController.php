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
        $konsultasi = null;

        // Retrieve consultation chat history for this patient
        $chat_history = Konsultasi::where('pasien_id', $user->id)
            ->orderBy('updated_at', 'desc')
            ->get();

        if ($dokter_id) {
            $selected_dokter = User::where('id', $dokter_id)->where('role', 'dokter')->firstOrFail();
            
            // Check for an active consultation, or one that is finished but not yet rated.
            $konsultasi = Konsultasi::where('pasien_id', $user->id)
                ->where('dokter_id', $dokter_id)
                ->where(function($query) {
                    $query->where('status', 'aktif')
                          ->orWhere(function($q) {
                              $q->where('status', 'selesai')->where('is_rated', false);
                          });
                })
                ->orderBy('created_at', 'desc')
                ->first();

            if (!$konsultasi) {
                $konsultasi = Konsultasi::create([
                    'pasien_id' => $user->id,
                    'dokter_id' => $dokter_id,
                    'pasien_name' => $user->name,
                    'dokter_name' => $selected_dokter->name,
                    'status' => 'aktif',
                    'is_rated' => false
                ]);
            }

            $konsultasi_id = $konsultasi->id;
            $pesan_list = PesanChat::where('konsultasi_id', $konsultasi_id)->orderBy('waktu')->get();
        }

        return view('pasien.dashboard', compact('user', 'dokter_list', 'selected_dokter', 'pesan_list', 'konsultasi_id', 'konsultasi', 'chat_history'))
            ->with('page', 'chat');
    }

    public function chatbot()
    {
        $user = Auth::user();
        $dokter_list = User::where('role', 'dokter')->orderBy('name')->get();

        return view('pasien.dashboard', compact('user', 'dokter_list'))
            ->with('page', 'chatbot');
    }

    public function chatbotQuery(Request $request)
    {
        $request->validate([
            'pesan' => 'required|string',
        ]);

        $pesan = $request->pesan;
        $apiKey = config('services.gemini.key');

        if (empty($apiKey)) {
            return response()->json([
                'success' => false,
                'message' => 'API Key Gemini belum dikonfigurasi di server.',
            ]);
        }

        // Retrieve list of doctors with their spec, rating, status
        $dokterList = User::where('role', 'dokter')
            ->orderBy('name')
            ->get()
            ->map(function ($doc) {
                return [
                    'id' => $doc->id,
                    'name' => $doc->name,
                    'spesialis' => $doc->spesialis,
                    'status_dokter' => $doc->status_dokter,
                    'average_rating' => $doc->average_rating,
                    'review_count' => $doc->review_count,
                ];
            });

        // Call Gemini API
        try {
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent?key={$apiKey}", [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => $this->buildGeminiPrompt($pesan, $dokterList)
                            ]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'responseMimeType' => 'application/json',
                ]
            ]);

            if ($response->successful()) {
                $result = $response->json();
                $responseText = $result['candidates'][0]['content']['parts'][0]['text'] ?? '{}';
                
                // Decode the JSON returned by the model
                $aiData = json_decode(trim($responseText), true);

                if (json_last_error() === JSON_ERROR_NONE) {
                    return response()->json([
                        'success' => true,
                        'message' => $aiData['message'] ?? 'Maaf, saya tidak dapat memahami keluhan Anda.',
                        'recommended_doctor_ids' => $aiData['recommended_doctor_ids'] ?? [],
                    ]);
                }
            }

            $errorData = $response->json();
            $errorMessage = $errorData['error']['message'] ?? 'Gagal mendapatkan respon dari AI.';
            return response()->json([
                'success' => false,
                'message' => $errorMessage,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghubungi layanan AI: ' . $e->getMessage(),
            ]);
        }
    }

    private function buildGeminiPrompt($pesan, $dokterList)
    {
        $doctorsJson = json_encode($dokterList, JSON_PRETTY_PRINT);
        
        return "Anda adalah Asisten Virtual Medicare, sebuah AI medis pintar. Tugas Anda adalah membantu pasien menganalisis keluhan mereka, memberikan saran kesehatan awal yang ramah dan aman, serta merekomendasikan dokter spesialis yang tepat dari daftar dokter terdaftar di bawah ini.

Daftar Dokter Terdaftar:
{$doctorsJson}

Pertanyaan/Keluhan Pasien:
\"{$pesan}\"

Aturan penting:
1. Analisis keluhan pasien secara sopan, ramah, dan menenangkan dalam Bahasa Indonesia yang baik dan profesional.
2. Rekomendasikan dokter spesialis yang paling cocok dari daftar di atas. Jika tidak ada dokter spesialis yang cocok dengan keluhan, rekomendasikan Dokter Umum / Penyakit Dalam.
3. Anda HANYA diperbolehkan merekomendasikan ID dokter yang ada di daftar di atas. Jangan mengarang dokter baru.
4. Format keluaran Anda HARUS berupa JSON valid dengan struktur persis seperti berikut:
{
  \"message\": \"Tanggapan dan analisis Anda dalam format teks dengan tag HTML dasar (seperti <br>, <strong>, <ul>, <li> untuk pemformatan rapi)\",
  \"recommended_doctor_ids\": [id_dokter_yang_direkomendasikan]
}
5. Jangan sertakan teks penjelasan lain sebelum atau sesudah JSON. Jangan gunakan codeblock markdown (no ```json). Kembalikan JSON murni.";
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

        $konsultasi = Konsultasi::where('pasien_id', $user->id)
            ->where('dokter_id', $dokter_id)
            ->where('status', 'aktif')
            ->first();

        if (!$konsultasi) {
            $konsultasi = Konsultasi::create([
                'pasien_id' => $user->id,
                'dokter_id' => $dokter_id,
                'pasien_name' => $user->name,
                'dokter_name' => $dokter->name,
                'status' => 'aktif',
                'is_rated' => false
            ]);
        }

        // Do not allow sending chats if the consultation is ended
        if ($konsultasi->status === 'selesai') {
            return redirect('/pasien/chat/' . $dokter_id)->with('error', 'Sesi konsultasi telah berakhir.');
        }

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

    public function simpanRating(Request $request, $id)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'ulasan' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();
        $konsultasi = Konsultasi::where('id', $id)
            ->where('pasien_id', $user->id)
            ->firstOrFail();

        $konsultasi->update([
            'rating' => $request->rating,
            'ulasan' => $request->ulasan,
            'is_rated' => true
        ]);

        return redirect('/pasien/chat/' . $konsultasi->dokter_id)->with('success', 'Terima kasih atas ulasan Anda!');
    }

    public function deleteChatSession($id)
    {
        $user = Auth::user();
        $konsultasi = Konsultasi::where('id', $id)
            ->where('pasien_id', $user->id)
            ->firstOrFail();

        // Delete associated chat messages
        $konsultasi->pesanChats()->delete();
        
        // Delete consultation session
        $konsultasi->delete();

        return redirect('/pasien/chat')->with('success', 'Sesi konsultasi chat berhasil dihapus!');
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
            'tensi_darah' => 'nullable|string|regex:/^\d+\/\d+$/',
            'suhu_tubuh' => 'nullable|numeric|min:30|max:45',
            'detak_jantung' => 'nullable|integer|min:30|max:220',
            'berat_badan' => 'nullable|integer|min:1|max:300',
        ], [
            'tensi_darah.regex' => 'Format tekanan darah harus Systolic/Diastolic (contoh: 120/80)',
        ]);

        $user = Auth::user();

        // Auto-calculate kesimpulan_awal
        $suhu = $request->suhu_tubuh ? floatval($request->suhu_tubuh) : null;
        $tensi = $request->tensi_darah;
        $detak = $request->detak_jantung ? intval($request->detak_jantung) : null;
        
        $indikator = [];

        if ($suhu) {
            if ($suhu >= 37.5) {
                $indikator[] = "Hipertermia/Demam ({$suhu} °C)";
            } elseif ($suhu < 36.0) {
                $indikator[] = "Hipotermia ({$suhu} °C)";
            } else {
                $indikator[] = "Suhu Tubuh Normal ({$suhu} °C)";
            }
        }

        if ($tensi && preg_match('/^(\d+)\/(\d+)$/', $tensi, $matches)) {
            $systolic = intval($matches[1]);
            $diastolic = intval($matches[2]);
            if ($systolic >= 140 || $diastolic >= 90) {
                $indikator[] = "Kecenderungan Hipertensi ({$tensi} mmHg)";
            } elseif ($systolic < 90 || $diastolic < 60) {
                $indikator[] = "Kecenderungan Hipotensi ({$tensi} mmHg)";
            } else {
                $indikator[] = "Tekanan Darah Normal ({$tensi} mmHg)";
            }
        }

        if ($detak) {
            if ($detak > 100) {
                $indikator[] = "Takikardia (Detak Jantung Cepat: {$detak} bpm)";
            } elseif ($detak < 60) {
                $indikator[] = "Bradikardia (Detak Jantung Lambat: {$detak} bpm)";
            } else {
                $indikator[] = "Detak Jantung Normal ({$detak} bpm)";
            }
        }

        $kesimpulan = "";
        if (!empty($indikator)) {
            $kesimpulan = "Berdasarkan parameter fisik: " . implode(', ', $indikator) . ". ";
        } else {
            $kesimpulan = "Parameter klinis awal tidak lengkap. ";
        }

        $keluhan_lower = strtolower($request->keluhan);
        if (str_contains($keluhan_lower, 'dada') || str_contains($keluhan_lower, 'jantung') || str_contains($keluhan_lower, 'sesak')) {
            $kesimpulan .= "Indikasi berkaitan dengan sistem pernapasan atau kardiovaskular. Disarankan konsultasi dengan Dokter Spesialis Jantung.";
        } elseif (str_contains($keluhan_lower, 'anak') || str_contains($keluhan_lower, 'bayi') || str_contains($keluhan_lower, 'balita')) {
            $kesimpulan .= "Disarankan konsultasi dengan Dokter Spesialis Anak.";
        } elseif (str_contains($keluhan_lower, 'mata') || str_contains($keluhan_lower, 'buram') || str_contains($keluhan_lower, 'kabur')) {
            $kesimpulan .= "Disarankan konsultasi dengan Dokter Spesialis Mata.";
        } elseif (str_contains($keluhan_lower, 'gigi') || str_contains($keluhan_lower, 'gusi') || str_contains($keluhan_lower, 'linu')) {
            $kesimpulan .= "Disarankan konsultasi dengan Dokter Gigi.";
        } elseif (str_contains($keluhan_lower, 'saraf') || str_contains($keluhan_lower, 'pusing') || str_contains($keluhan_lower, 'vertigo')) {
            $kesimpulan .= "Disarankan konsultasi dengan Dokter Spesialis Saraf.";
        } else {
            $kesimpulan .= "Disarankan konsultasi lanjutan dengan Dokter Spesialis Penyakit Dalam / Umum.";
        }

        RekamMedis::create([
            'pasien_id' => $user->id,
            'pasien_name' => $user->name,
            'keluhan' => $request->keluhan,
            'usia' => $request->usia ?? $user->age,
            'tensi_darah' => $request->tensi_darah,
            'suhu_tubuh' => $request->suhu_tubuh,
            'detak_jantung' => $request->detak_jantung,
            'berat_badan' => $request->berat_badan,
            'kesimpulan_awal' => $kesimpulan,
            'tanggal' => date('Y-m-d'),
            'status' => 'menunggu'
        ]);

        return redirect('/pasien/rekam-medis')->with('success', 'Rekam medis & data pemeriksaan awal berhasil dikirim ke Dokter!');
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

    public function bantuan()
    {
        $user = Auth::user();
        $bantuan_list = \App\Models\CustomerService::where('pasien_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();
        return view('pasien.dashboard', compact('user', 'bantuan_list'))
            ->with('page', 'bantuan');
    }

    public function kirimBantuan(Request $request)
    {
        $request->validate([
            'pesan' => 'required|string',
        ]);

        $user = Auth::user();

        \App\Models\CustomerService::create([
            'pasien_id' => $user->id,
            'pasien_name' => $user->name,
            'pesan' => $request->pesan,
            'status' => 'menunggu',
        ]);

        return redirect('/pasien/bantuan')->with('success', 'Pesan bantuan berhasil dikirim! CS Medicare akan membalas secepatnya.');
    }
}
