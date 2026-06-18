<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\RekamMedis;
use App\Models\PesananObat;
use App\Models\PesanChat;
use App\Models\Konsultasi;
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
}

