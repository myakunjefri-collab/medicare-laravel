<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\RekamMedis;
use App\Models\PesananObat;
use App\Models\PesanChat;
use App\Models\Konsultasi;
use App\Models\JadwalDokter;
use App\Models\CustomerService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MedicareSystemTest extends TestCase
{
    use RefreshDatabase;

    public function test_full_workflow_for_medicine_ordering_and_chat_upload()
    {
        // 1. Seed the database with default seeders (DatabaseSeeder)
        $this->seed();

        // Retrieve users seeded
        $patient = User::where('email', 'pasien@med.com')->first();
        $doctor = User::where('email', 'dokter1@med.com')->first();
        $admin = User::where('email', 'admin@med.com')->first();

        $this->assertNotNull($patient);
        $this->assertNotNull($doctor);
        $this->assertNotNull($admin);

        // 2. Patient creates a RekamMedis request
        $response = $this->actingAs($patient)->post('/pasien/rekam-medis', [
            'keluhan' => 'Nyeri lambung yang parah disertai rasa mual.',
            'usia' => 25,
        ]);
        $response->assertRedirect('/pasien/rekam-medis');
        $this->assertDatabaseHas('rekam_medis', [
            'pasien_id' => $patient->id,
            'keluhan' => 'Nyeri lambung yang parah disertai rasa mual.',
            'status' => 'menunggu',
        ]);

        $rekamMedis = RekamMedis::where('pasien_id', $patient->id)->first();

        // 3. Patient starts a chat session with doctor and uploads an image
        Storage::fake('public');
        $file = UploadedFile::fake()->create('complaint.png', 100, 'image/png');

        // Note: The patient needs to hit /pasien/chat/{dokter_id} first to create the Konsultasi room
        $response = $this->actingAs($patient)->get('/pasien/chat/' . $doctor->id);
        $response->assertStatus(200);

        // Now post a chat message with the image
        $response = $this->actingAs($patient)->post('/pasien/chat/send', [
            'dokter_id' => $doctor->id,
            'pesan' => 'Halo dokter Andi, saya kirimkan foto ulu hati saya.',
            'gambar' => $file,
        ]);
        $response->assertRedirect('/pasien/chat/' . $doctor->id);

        // Verify the message and file are stored
        $this->assertDatabaseHas('pesan_chats', [
            'pengirim' => 'pasien',
            'pesan' => 'Halo dokter Andi, saya kirimkan foto ulu hati saya.',
        ]);
        $pesanPatient = PesanChat::where('pengirim', 'pasien')->first();
        $this->assertNotNull($pesanPatient->gambar);
        Storage::disk('public')->assertExists($pesanPatient->gambar);

        // 4. Doctor diagnoses the RekamMedis request
        $response = $this->actingAs($doctor)->post('/dokter/diagnosa/simpan', [
            'id' => $rekamMedis->id,
            'diagnosa' => 'Gastritis Akut Ringan',
            'status_diagnosa' => 'ringan',
            'resep' => 'Antasida 3x1 tablet kunyah, Ranitidin 150mg 2x1',
        ]);
        $response->assertRedirect('/dokter/diagnosa');
        $this->assertDatabaseHas('rekam_medis', [
            'id' => $rekamMedis->id,
            'diagnosa' => 'Gastritis Akut Ringan',
            'resep' => 'Antasida 3x1 tablet kunyah, Ranitidin 150mg 2x1',
            'status' => 'selesai',
        ]);

        // 5. Doctor replies in the chat room with an image
        $konsultasi = Konsultasi::where('pasien_id', $patient->id)->where('dokter_id', $doctor->id)->first();
        $this->assertNotNull($konsultasi);

        $replyFile = UploadedFile::fake()->create('prescription_scan.png', 100, 'image/png');
        $response = $this->actingAs($doctor)->post('/dokter/chat/reply', [
            'konsultasi_id' => $konsultasi->id,
            'pesan_balasan' => 'Baik, diagnosa ringan sudah dikirim. Ini scan petunjuk resepnya.',
            'gambar' => $replyFile,
        ]);
        $response->assertRedirect('/dokter/chat/' . $konsultasi->id);

        $this->assertDatabaseHas('pesan_chats', [
            'pengirim' => 'dokter',
            'pesan' => 'Baik, diagnosa ringan sudah dikirim. Ini scan petunjuk resepnya.',
        ]);
        $pesanDoctor = PesanChat::where('pengirim', 'dokter')->where('konsultasi_id', $konsultasi->id)->get()->last();
        $this->assertNotNull($pesanDoctor->gambar);
        Storage::disk('public')->assertExists($pesanDoctor->gambar);

        // 6. Patient places an order for the medicine
        $response = $this->actingAs($patient)->post('/pasien/pesan-obat', [
            'rekam_medis_id' => $rekamMedis->id,
            'alamat_kirim' => 'Jl. Kebagusan Raya No. 45, Jakarta Selatan',
        ]);
        $response->assertRedirect('/pasien/pesanan-obat');

        $this->assertDatabaseHas('pesanan_obats', [
            'pasien_id' => $patient->id,
            'rekam_medis_id' => $rekamMedis->id,
            'alamat_kirim' => 'Jl. Kebagusan Raya No. 45, Jakarta Selatan',
            'status' => 'menunggu_pembayaran',
        ]);

        $pesanan = PesananObat::where('pasien_id', $patient->id)->first();
        $this->assertNotNull($pesanan);
        $this->assertEquals(37000, $pesanan->total_harga); // Antasida (8000) + Ranitidin (14000) + Ongkir (15000)

        // 6b. Patient uploads bank transfer proof
        $buktiFile = UploadedFile::fake()->create('receipt.png', 100, 'image/png');
        $response = $this->actingAs($patient)->post('/pasien/pesanan-obat/' . $pesanan->id . '/upload-bukti', [
            'bukti_transfer' => $buktiFile,
        ]);
        $response->assertRedirect('/pasien/pesanan-obat');

        $pesanan->refresh();
        $this->assertNotNull($pesanan->bukti_transfer);
        Storage::disk('public')->assertExists($pesanan->bukti_transfer);

        // 7. Admin updates the order status to diproses
        $response = $this->actingAs($admin)->get('/admin/pesanan-obat/' . $pesanan->id . '/update-status?status=diproses');
        $response->assertRedirect('/admin/pesanan-obat');
        $this->assertDatabaseHas('pesanan_obats', [
            'id' => $pesanan->id,
            'status' => 'diproses',
        ]);

        // Admin updates the order status to dikirim
        $response = $this->actingAs($admin)->get('/admin/pesanan-obat/' . $pesanan->id . '/update-status?status=dikirim');
        $response->assertRedirect('/admin/pesanan-obat');
        $this->assertDatabaseHas('pesanan_obats', [
            'id' => $pesanan->id,
            'status' => 'dikirim',
        ]);

        // Admin updates the order status to selesai
        $response = $this->actingAs($admin)->get('/admin/pesanan-obat/' . $pesanan->id . '/update-status?status=selesai');
        $response->assertRedirect('/admin/pesanan-obat');
        $this->assertDatabaseHas('pesanan_obats', [
            'id' => $pesanan->id,
            'status' => 'selesai',
        ]);

        // 8. Patient prints the purchase receipt
        $response = $this->actingAs($patient)->get('/pasien/pesanan-obat/' . $pesanan->id . '/cetak-struk');
        $response->assertStatus(200);
        $response->assertSee('Struk Pembelian');
        $response->assertSee($patient->name);
        $response->assertSee('Lunas');
    }

    public function test_admin_patient_management_and_doctor_queue_validation()
    {
        $this->seed();

        $admin = User::where('email', 'admin@med.com')->first();
        $doctor = User::where('email', 'dokter1@med.com')->first();
        
        // 1. Admin adds a new patient
        $response = $this->actingAs($admin)->post('/admin/pasien/tambah', [
            'name' => 'Pasien Baru Tes',
            'email' => 'pasienbaru@med.com',
            'phone' => '08123123123',
            'age' => 30,
            'gender' => 'Laki-laki',
            'alamat' => 'Alamat Tes Baru',
            'password' => 'pasienbaru123',
        ]);
        $response->assertRedirect('/admin/pasien');
        $this->assertDatabaseHas('users', [
            'email' => 'pasienbaru@med.com',
            'role' => 'pasien',
            'name' => 'Pasien Baru Tes'
        ]);

        $newPatient = User::where('email', 'pasienbaru@med.com')->first();

        // 2. Admin edits the new patient
        $response = $this->actingAs($admin)->post('/admin/pasien/' . $newPatient->id . '/update', [
            'name' => 'Pasien Baru Tes Terupdate',
            'email' => 'pasienbaru@med.com',
            'phone' => '08123123999',
            'age' => 31,
            'gender' => 'Laki-laki',
            'alamat' => 'Alamat Tes Terupdate',
        ]);
        $response->assertRedirect('/admin/pasien');
        $this->assertDatabaseHas('users', [
            'id' => $newPatient->id,
            'name' => 'Pasien Baru Tes Terupdate',
            'phone' => '08123123999',
            'age' => 31
        ]);

        // 3. Create a queue / JanjiTemu for the doctor
        $janji = \App\Models\JanjiTemu::create([
            'pasien_id' => $newPatient->id,
            'pasien_name' => $newPatient->name,
            'dokter_name' => $doctor->name,
            'tanggal' => date('Y-m-d'),
            'jam' => '09:00',
            'status' => 'menunggu',
            'nomor_antrean' => 'ANT9999',
            'keluhan' => 'Keluhan Tes'
        ]);

        // 4. Doctor validates/completes the queue
        $response = $this->actingAs($doctor)->get('/dokter/antrean/' . $janji->id . '/selesai');
        $response->assertRedirect('/dokter/antrean');
        $this->assertDatabaseHas('janji_temus', [
            'id' => $janji->id,
            'status' => 'selesai'
        ]);

        // 5. Admin deletes the patient
        $response = $this->actingAs($admin)->get('/admin/pasien/' . $newPatient->id . '/hapus');
        $response->assertRedirect('/admin/pasien');
        $this->assertDatabaseMissing('users', [
            'id' => $newPatient->id
        ]);
    }

    public function test_chatbot_query_api_behavior()
    {
        $this->seed();
        $patient = User::where('role', 'pasien')->first();

        // 1. Unauthenticated request should fail/redirect
        $response = $this->postJson('/pasien/chatbot/query', ['pesan' => 'Halo']);
        $response->assertStatus(401);

        // 2. Authenticated request without API key should return false success with warning
        // Force config key to be empty for this check
        config(['services.gemini.key' => null]);
        $response = $this->actingAs($patient)->postJson('/pasien/chatbot/query', ['pesan' => 'Halo']);
        $response->assertStatus(200);
        $response->assertJson([
            'success' => false,
            'message' => 'API Key Gemini belum dikonfigurasi di server.'
        ]);

        // 3. Authenticated request with mocked Gemini API call
        config(['services.gemini.key' => 'TEST_API_KEY']);
        \Illuminate\Support\Facades\Http::fake([
            'generativelanguage.googleapis.com/*' => \Illuminate\Support\Facades\Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                [
                                    'text' => json_encode([
                                        'message' => 'Anda mengalami stres, kami rekomendasikan dokter Psikolog.',
                                        'recommended_doctor_ids' => [2]
                                    ])
                                ]
                            ]
                        ]
                    ]
                ]
            ], 200)
        ]);

        $response = $this->actingAs($patient)->postJson('/pasien/chatbot/query', ['pesan' => 'Saya merasa sangat stres dan jenuh']);
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Anda mengalami stres, kami rekomendasikan dokter Psikolog.',
            'recommended_doctor_ids' => [2]
        ]);
    }

    public function test_new_features_implementation()
    {
        $this->seed();

        $patient = User::where('email', 'pasien@med.com')->first();
        $doctor = User::where('email', 'dokter1@med.com')->first();
        $admin = User::where('email', 'admin@med.com')->first();

        // 1. Verify regional disease stats endpoint
        $response = $this->get('/api/penyakit-wilayah');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'data' => [
                'DKI Jakarta',
                'Jawa Barat',
                'Jawa Tengah',
                'Jawa Timur',
                'Banten',
            ]
        ]);

        // 2. Patient submits medical record with physical parameters
        $response = $this->actingAs($patient)->post('/pasien/rekam-medis', [
            'keluhan' => 'Saya merasa sesak dan nyeri dada saat beraktivitas berat.',
            'usia' => 30,
            'tensi_darah' => '145/95',
            'suhu_tubuh' => 38.2,
            'detak_jantung' => 105,
            'berat_badan' => 70,
        ]);
        $response->assertRedirect('/pasien/rekam-medis');

        // Verify rekam medis in database has calculated conclusion
        $this->assertDatabaseHas('rekam_medis', [
            'pasien_id' => $patient->id,
            'usia' => 30,
            'tensi_darah' => '145/95',
            'suhu_tubuh' => 38.2,
            'detak_jantung' => 105,
            'berat_badan' => 70,
        ]);

        $rekamMedis = RekamMedis::where('pasien_id', $patient->id)->orderBy('id', 'desc')->first();
        $this->assertNotNull($rekamMedis);
        // Verify conclusion contains physical readings and recommended spec
        $this->assertStringContainsString('Hipertermia/Demam (38.2 °C)', $rekamMedis->kesimpulan_awal);
        $this->assertStringContainsString('Kecenderungan Hipertensi (145/95 mmHg)', $rekamMedis->kesimpulan_awal);
        $this->assertStringContainsString('Takikardia (Detak Jantung Cepat: 105 bpm)', $rekamMedis->kesimpulan_awal);
        $this->assertStringContainsString('Dokter Spesialis Jantung', $rekamMedis->kesimpulan_awal);

        // 3. Customer Service ticketing
        // Patient submits ticket
        $response = $this->actingAs($patient)->post('/pasien/bantuan/kirim', [
            'pesan' => 'Saya kesulitan melakukan verifikasi pembayaran untuk resep.',
        ]);
        $response->assertRedirect('/pasien/bantuan');
        $this->assertDatabaseHas('customer_services', [
            'pasien_id' => $patient->id,
            'pesan' => 'Saya kesulitan melakukan verifikasi pembayaran untuk resep.',
            'status' => 'menunggu',
        ]);

        $ticket = \App\Models\CustomerService::where('pasien_id', $patient->id)->orderBy('id', 'desc')->first();
        $this->assertNotNull($ticket);

        // Admin replies to ticket
        $response = $this->actingAs($admin)->post('/admin/bantuan/' . $ticket->id . '/balas', [
            'balasan' => 'Baik pak Jefri, pembayaran bapak sudah kami verifikasi secara manual. Silakan cek status pesanan.',
        ]);
        $response->assertRedirect('/admin/bantuan');
        $this->assertDatabaseHas('customer_services', [
            'id' => $ticket->id,
            'balasan' => 'Baik pak Jefri, pembayaran bapak sudah kami verifikasi secara manual. Silakan cek status pesanan.',
            'status' => 'selesai',
        ]);

        // 4. Polyclinic prefix-based queue numbers
        // Update doctor specification to Anak (or verify what is seeded)
        $doctor->update(['spesialis' => 'Anak']);

        // Patient submits another rekam medis
        $this->actingAs($patient)->post('/pasien/rekam-medis', [
            'keluhan' => 'Anak saya demam tinggi sejak kemarin sore.',
            'usia' => 5,
            'suhu_tubuh' => 39.5,
        ]);
        $rekamMedisAnak1 = RekamMedis::where('pasien_id', $patient->id)->orderBy('id', 'desc')->first();

        // Doctor diagnoses as parah
        $tgl_janji = date('Y-m-d', strtotime('+1 day'));
        $response = $this->actingAs($doctor)->post('/dokter/diagnosa/simpan', [
            'id' => $rekamMedisAnak1->id,
            'diagnosa' => 'Demam Berdarah Dengue',
            'status_diagnosa' => 'parah',
            'tgl_janji' => $tgl_janji,
            'jam_janji' => '09:00',
        ]);
        $response->assertRedirect('/dokter/diagnosa');
        
        // Verify queue was created with prefix 'A' for Poli Anak and sequence '01' -> 'A-01'
        $this->assertDatabaseHas('janji_temus', [
            'pasien_id' => $patient->id,
            'poli' => 'Poli Anak',
            'tanggal' => $tgl_janji,
            'nomor_antrean' => 'A-01',
        ]);

        // Submit another rekam medis to test sequence increment
        $this->actingAs($patient)->post('/pasien/rekam-medis', [
            'keluhan' => 'Balita saya batuk pilek berat.',
            'usia' => 3,
        ]);
        $rekamMedisAnak2 = RekamMedis::where('pasien_id', $patient->id)->orderBy('id', 'desc')->first();

        // Doctor diagnoses second as parah on the same day
        $response = $this->actingAs($doctor)->post('/dokter/diagnosa/simpan', [
            'id' => $rekamMedisAnak2->id,
            'diagnosa' => 'Bronkopneumonia Ringan',
            'status_diagnosa' => 'parah',
            'tgl_janji' => $tgl_janji,
            'jam_janji' => '09:30',
        ]);
        
        $this->assertDatabaseHas('janji_temus', [
            'pasien_id' => $patient->id,
            'poli' => 'Poli Anak',
            'tanggal' => $tgl_janji,
            'nomor_antrean' => 'A-02',
        ]);

        // 5. Admin schedule CRUD
        $response = $this->actingAs($admin)->post('/admin/jadwal/tambah', [
            'doctor_id' => $doctor->id,
            'tanggal' => date('Y-m-d', strtotime('+3 days')),
            'start_time' => '08:00',
            'end_time' => '12:00',
            'ruangan' => 'Poliklinik Anak 1',
            'kuota' => 15,
        ]);
        $response->assertRedirect('/admin/jadwal');
        $this->assertDatabaseHas('jadwal_dokters', [
            'doctor_id' => $doctor->id,
            'tanggal' => date('Y-m-d', strtotime('+3 days')),
            'start_time' => '08:00',
            'end_time' => '12:00',
            'ruangan' => 'Poliklinik Anak 1',
            'kuota' => 15,
        ]);

        $jadwal = JadwalDokter::where('doctor_id', $doctor->id)->orderBy('id', 'desc')->first();
        $this->assertNotNull($jadwal);

        // Edit schedule
        $response = $this->actingAs($admin)->post('/admin/jadwal/' . $jadwal->id . '/update', [
            'doctor_id' => $doctor->id,
            'tanggal' => date('Y-m-d', strtotime('+3 days')),
            'start_time' => '08:00',
            'end_time' => '11:00', // change end time
            'ruangan' => 'Poliklinik Anak Utama', // change room
            'kuota' => 20, // change quota
        ]);
        $response->assertRedirect('/admin/jadwal');
        $this->assertDatabaseHas('jadwal_dokters', [
            'id' => $jadwal->id,
            'start_time' => '08:00',
            'end_time' => '11:00',
            'ruangan' => 'Poliklinik Anak Utama',
            'kuota' => 20,
        ]);
    }

    public function test_chat_session_history_and_deletion()
    {
        $this->seed();

        $patient = User::where('email', 'pasien@med.com')->first();
        $doctor = User::where('email', 'dokter1@med.com')->first();

        // 1. Visit chat page with doctor ID to create consultation session
        $response = $this->actingAs($patient)->get('/pasien/chat/' . $doctor->id);
        $response->assertStatus(200);

        // Verify consultation created
        $this->assertDatabaseHas('konsultasis', [
            'pasien_id' => $patient->id,
            'dokter_id' => $doctor->id,
            'status' => 'aktif',
        ]);

        $konsultasi = Konsultasi::where('pasien_id', $patient->id)->where('dokter_id', $doctor->id)->first();

        // 2. Patient sends a message
        $response = $this->actingAs($patient)->post('/pasien/chat/send', [
            'dokter_id' => $doctor->id,
            'pesan' => 'Halo Dokter, ini tes pesan.',
        ]);
        $response->assertRedirect('/pasien/chat/' . $doctor->id);

        $this->assertDatabaseHas('pesan_chats', [
            'konsultasi_id' => $konsultasi->id,
            'pesan' => 'Halo Dokter, ini tes pesan.',
        ]);

        // 3. Go back to main chat page to check history
        $response = $this->actingAs($patient)->get('/pasien/chat');
        $response->assertStatus(200);
        $response->assertSee('Riwayat Konsultasi Chat Anda');
        $response->assertSee('dr. ' . $doctor->name);

        // 4. Delete the chat session
        $response = $this->actingAs($patient)->delete('/pasien/chat/session/' . $konsultasi->id);
        $response->assertRedirect('/pasien/chat');

        // Verify consultation and message are gone from database
        $this->assertDatabaseMissing('konsultasis', [
            'id' => $konsultasi->id,
        ]);
        $this->assertDatabaseMissing('pesan_chats', [
            'konsultasi_id' => $konsultasi->id,
        ]);
    }

    public function test_admin_edit_delete_features()
    {
        $this->seed();

        $admin = User::where('email', 'admin@med.com')->first();
        $patient = User::where('email', 'pasien@med.com')->first();
        $doctor = User::where('email', 'dokter1@med.com')->first();

        // 1. Create resources to edit/delete
        $janji = \App\Models\JanjiTemu::create([
            'pasien_id' => $patient->id,
            'pasien_name' => $patient->name,
            'dokter_name' => $doctor->name,
            'poli' => 'Poli Umum',
            'tanggal' => date('Y-m-d'),
            'jam' => '09:00',
            'status' => 'menunggu',
            'nomor_antrean' => 'A-01',
            'keluhan' => 'Pusing kepala',
        ]);

        $rekam = \App\Models\RekamMedis::create([
            'pasien_id' => $patient->id,
            'pasien_name' => $patient->name,
            'keluhan' => 'Demam tinggi',
            'usia' => 25,
            'tanggal' => date('Y-m-d'),
            'status' => 'menunggu',
        ]);

        $pesanan = \App\Models\PesananObat::create([
            'pasien_id' => $patient->id,
            'pasien_name' => $patient->name,
            'rekam_medis_id' => $rekam->id,
            'resep' => 'Paracetamol',
            'alamat_kirim' => 'Jalan Kenangan No. 5',
            'total_harga' => 15000,
            'status' => 'menunggu_pembayaran',
        ]);

        $bantuan = \App\Models\CustomerService::create([
            'pasien_id' => $patient->id,
            'pasien_name' => $patient->name,
            'pesan' => 'Bagaimana cara bayar obat?',
            'status' => 'menunggu',
        ]);

        // 2. Test Editing JanjiTemu
        $response = $this->actingAs($admin)->post('/admin/janji-temu/' . $janji->id . '/update', [
            'pasien_id' => $patient->id,
            'dokter_name' => 'dr. Sarah Melati, Sp.A',
            'poli' => 'Poli Anak',
            'tanggal' => date('Y-m-d'),
            'jam' => '11:00',
            'nomor_antrean' => 'B-02',
            'status' => 'konfirmasi',
            'keluhan' => 'Batuk pilek',
        ]);
        $response->assertRedirect('/admin/janji-temu');
        $this->assertDatabaseHas('janji_temus', [
            'id' => $janji->id,
            'dokter_name' => 'dr. Sarah Melati, Sp.A',
            'poli' => 'Poli Anak',
            'jam' => '11:00',
            'status' => 'konfirmasi',
        ]);

        // 3. Test Deleting JanjiTemu
        $response = $this->actingAs($admin)->get('/admin/janji-temu/' . $janji->id . '/hapus');
        $response->assertRedirect('/admin/janji-temu');
        $this->assertDatabaseMissing('janji_temus', ['id' => $janji->id]);

        // 4. Test Editing RekamMedis
        $response = $this->actingAs($admin)->post('/admin/rekam-medis/' . $rekam->id . '/update', [
            'pasien_id' => $patient->id,
            'keluhan' => 'Demam berdarah',
            'usia' => 26,
            'tensi_darah' => '110/70',
            'suhu_tubuh' => 38.5,
            'detak_jantung' => 85,
            'berat_badan' => 60,
            'kesimpulan_awal' => 'Suhu di atas normal',
            'tanggal' => date('Y-m-d'),
            'status' => 'selesai',
            'diagnosa' => 'Dengue Fever',
            'resep' => 'Obat penurun panas',
        ]);
        $response->assertRedirect('/admin/rekam-medis');
        $this->assertDatabaseHas('rekam_medis', [
            'id' => $rekam->id,
            'keluhan' => 'Demam berdarah',
            'usia' => 26,
            'suhu_tubuh' => 38.5,
            'diagnosa' => 'Dengue Fever',
        ]);

        // 5. Test Deleting RekamMedis
        $response = $this->actingAs($admin)->get('/admin/rekam-medis/' . $rekam->id . '/hapus');
        $response->assertRedirect('/admin/rekam-medis');
        $this->assertDatabaseMissing('rekam_medis', ['id' => $rekam->id]);

        // 6. Test Editing PesananObat
        $response = $this->actingAs($admin)->post('/admin/pesanan-obat/' . $pesanan->id . '/update', [
            'resep' => 'Ibuprofen 400mg',
            'alamat_kirim' => 'Jalan Damai No. 10',
            'total_harga' => 20000,
            'status' => 'diproses',
        ]);
        $response->assertRedirect('/admin/pesanan-obat');
        $this->assertDatabaseHas('pesanan_obats', [
            'id' => $pesanan->id,
            'resep' => 'Ibuprofen 400mg',
            'alamat_kirim' => 'Jalan Damai No. 10',
            'total_harga' => 20000,
            'status' => 'diproses',
        ]);

        // 7. Test Deleting PesananObat
        $response = $this->actingAs($admin)->get('/admin/pesanan-obat/' . $pesanan->id . '/hapus');
        $response->assertRedirect('/admin/pesanan-obat');
        $this->assertDatabaseMissing('pesanan_obats', ['id' => $pesanan->id]);

        // 8. Test Deleting CustomerService ticket
        $response = $this->actingAs($admin)->get('/admin/bantuan/' . $bantuan->id . '/hapus');
        $response->assertRedirect('/admin/bantuan');
        $this->assertDatabaseMissing('customer_services', ['id' => $bantuan->id]);
    }
}


