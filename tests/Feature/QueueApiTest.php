<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\JanjiTemu;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QueueApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_registration_validation_and_token_issue()
    {
        $response = $this->postJson('/api/register', [
            'fullname' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'password' => 'password123',
            'confirm_password' => 'password123',
            'age' => 28,
            'gender' => 'Laki-laki',
            'phone' => '08123456789',
            'alamat' => 'Jakarta, Indonesia'
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'access_token',
                'token_type',
                'user' => ['id', 'name', 'email', 'role']
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'budi@example.com',
            'role' => 'pasien'
        ]);
    }

    public function test_api_login_invalid_credentials_and_successful_token_issue()
    {
        $this->seed();

        // 1. Invalid login
        $response = $this->postJson('/api/login', [
            'email' => 'pasien@med.com',
            'password' => 'wrongpassword'
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Email/Username atau password salah!'
            ]);

        // 2. Valid login
        $response = $this->postJson('/api/login', [
            'email' => 'pasien@med.com',
            'password' => 'pasien123'
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'access_token',
                'token_type',
                'user' => ['id', 'name', 'email', 'role']
            ]);
    }

    public function test_api_queues_unauthenticated_denied()
    {
        $response = $this->getJson('/api/queues');
        $response->assertStatus(401);
    }

    public function test_api_queues_crud_workflow()
    {
        $this->seed();
        $patient = User::where('email', 'pasien@med.com')->first();
        $token = $patient->createToken('test_token')->plainTextToken;

        // 1. Create (POST) a queue
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/queues', [
                'dokter_name' => 'dr. Andi Wijaya, Sp.PD',
                'poli' => 'Penyakit Dalam',
                'tanggal' => '2026-07-03',
                'jam' => '09:00',
                'keluhan' => 'Demam tinggi selama 3 hari'
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id', 'pasien_name', 'dokter_name', 'poli', 'tanggal', 'jam', 'nomor_antrean', 'status', 'keluhan', 'dibuat_pada'
                ],
                'message'
            ])
            ->assertJson([
                'data' => [
                    'dokter_name' => 'DR. ANDI WIJAYA, SP.PD',
                    'nomor_antrean' => 'A-001',
                    'status' => 'menunggu'
                ],
                'message' => 'Antrean berhasil dibuat'
            ]);

        $queueId = $response->json('data.id');

        // 2. Get All (GET) with pagination
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/queues');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data', 'links', 'meta'
            ]);

        // 3. Show (GET by ID)
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/queues/' . $queueId);

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $queueId);

        // 4. Update (PUT)
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/queues/' . $queueId, [
                'dokter_name' => 'dr. Andi Wijaya, Sp.PD',
                'poli' => 'Penyakit Dalam',
                'tanggal' => '2026-07-03',
                'jam' => '10:00',
                'keluhan' => 'Demam tinggi selama 3 hari dan mual'
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.jam', '10:00')
            ->assertJsonPath('data.keluhan', 'Demam tinggi selama 3 hari dan mual');

        // 5. Delete (DELETE)
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson('/api/queues/' . $queueId);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Data antrean berhasil dihapus'
            ]);

        // 6. Verify 404
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/queues/' . $queueId);

        $response->assertStatus(404)
            ->assertJson([
                'error' => 'Resource tidak ditemukan'
            ]);
    }

    public function test_api_queues_filtering_and_validation_errors()
    {
        $this->seed();
        $patient = User::where('email', 'pasien@med.com')->first();
        $token = $patient->createToken('test_token')->plainTextToken;

        // Create two queue entries on different dates
        JanjiTemu::create([
            'pasien_id' => $patient->id,
            'pasien_name' => $patient->name,
            'dokter_name' => 'dr. Andi Wijaya, Sp.PD',
            'poli' => 'Penyakit Dalam',
            'tanggal' => '2026-07-03',
            'jam' => '09:00',
            'nomor_antrean' => 'A-001',
            'status' => 'menunggu',
            'keluhan' => 'Demam'
        ]);

        JanjiTemu::create([
            'pasien_id' => $patient->id,
            'pasien_name' => $patient->name,
            'dokter_name' => 'dr. Sarah Melati, Sp.A',
            'poli' => 'Anak',
            'tanggal' => '2026-07-10',
            'jam' => '11:00',
            'nomor_antrean' => 'A-001',
            'status' => 'menunggu',
            'keluhan' => 'Flu'
        ]);

        // Test filtering by date range: 2026-07-01 to 2026-07-05 (should return only 1)
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/queues?start_date=2026-07-01&end_date=2026-07-05');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.poli', 'Penyakit Dalam');

        // Test validation error handler formats as JSON (global exception testing)
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/queues', [
                'dokter_name' => '', // blank should trigger validation error
                'poli' => 'Penyakit Dalam',
                'tanggal' => 'not-a-date', // invalid date
                'jam' => '09:00',
                'keluhan' => 'Demam'
            ]);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => ['dokter_name', 'tanggal']
            ])
            ->assertJson([
                'message' => 'Data yang dikirim tidak valid'
            ]);
    }
}
