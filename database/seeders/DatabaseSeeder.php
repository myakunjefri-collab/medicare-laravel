<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Users
        // Admin
        User::create([
            'name' => 'Admin Medicare',
            'email' => 'admin@med.com',
            'username' => 'admin',
            'password' => \Illuminate\Support\Facades\Hash::make('admin123'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        // Doctors
        $doc1 = User::create([
            'name' => 'dr. Andi Wijaya, Sp.PD',
            'email' => 'dokter1@med.com',
            'username' => 'dokter1',
            'password' => \Illuminate\Support\Facades\Hash::make('dokter123'),
            'role' => 'dokter',
            'spesialis' => 'Penyakit Dalam',
            'no_hp' => '08122334455',
            'is_active' => true,
        ]);

        $doc2 = User::create([
            'name' => 'dr. Sarah Melati, Sp.A',
            'email' => 'dokter2@med.com',
            'username' => 'dokter2',
            'password' => \Illuminate\Support\Facades\Hash::make('dokter234'),
            'role' => 'dokter',
            'spesialis' => 'Anak',
            'no_hp' => '08122334466',
            'is_active' => true,
        ]);

        $doc3 = User::create([
            'name' => 'dr. Budi Hartono, Sp.JP',
            'email' => 'dokter3@med.com',
            'username' => 'dokter3',
            'password' => \Illuminate\Support\Facades\Hash::make('dokter345'),
            'role' => 'dokter',
            'spesialis' => 'Jantung',
            'no_hp' => '08122334477',
            'is_active' => true,
        ]);

        User::create([
            'name' => 'dr. Dewi Anggraeni, Sp.M',
            'email' => 'dokter4@med.com',
            'username' => 'dokter4',
            'password' => \Illuminate\Support\Facades\Hash::make('dokter456'),
            'role' => 'dokter',
            'spesialis' => 'Mata',
            'no_hp' => '08122334488',
            'is_active' => true,
        ]);

        User::create([
            'name' => 'dr. Rizky, Sp.S',
            'email' => 'dokter5@med.com',
            'username' => 'dokter5',
            'password' => \Illuminate\Support\Facades\Hash::make('dokter567'),
            'role' => 'dokter',
            'spesialis' => 'Saraf',
            'no_hp' => '08122334499',
            'is_active' => true,
        ]);

        User::create([
            'name' => 'drg. Rina, Sp.KGA',
            'email' => 'dokter6@med.com',
            'username' => 'dokter6',
            'password' => \Illuminate\Support\Facades\Hash::make('dokter678'),
            'role' => 'dokter',
            'spesialis' => 'Gigi',
            'no_hp' => '08122334411',
            'is_active' => true,
        ]);

        User::firstOrCreate(['email' => 'dokter7@med.com'], [
            'name' => 'dr. Hendra Kurniawan, Sp.THT',
            'email' => 'dokter7@med.com',
            'username' => 'dokter7',
            'password' => \Illuminate\Support\Facades\Hash::make('dokter789'),
            'role' => 'dokter',
            'spesialis' => 'THT',
            'no_hp' => '08122334422',
            'is_active' => true,
        ]);

        User::firstOrCreate(['email' => 'dokter8@med.com'], [
            'name' => 'dr. Linda Lestari, Sp.KK',
            'email' => 'dokter8@med.com',
            'username' => 'dokter8',
            'password' => \Illuminate\Support\Facades\Hash::make('dokter890'),
            'role' => 'dokter',
            'spesialis' => 'Kulit & Kelamin',
            'no_hp' => '08122334433',
            'is_active' => true,
        ]);

        User::firstOrCreate(['email' => 'dokter9@med.com'], [
            'name' => 'dr. Yusuf Pratama, Sp.B',
            'email' => 'dokter9@med.com',
            'username' => 'dokter9',
            'password' => \Illuminate\Support\Facades\Hash::make('dokter901'),
            'role' => 'dokter',
            'spesialis' => 'Bedah',
            'no_hp' => '08122334444',
            'is_active' => true,
        ]);


        // Patient
        User::create([
            'name' => 'Pasien Demo',
            'email' => 'pasien@med.com',
            'username' => 'pasien',
            'password' => \Illuminate\Support\Facades\Hash::make('pasien123'),
            'role' => 'pasien',
            'age' => 25,
            'gender' => 'Laki-laki',
            'phone' => '08987654321',
            'alamat' => 'Jakarta, Indonesia',
            'is_active' => true,
        ]);

        // 2. Seed Jadwal Dokter
        \Illuminate\Support\Facades\DB::table('jadwal_dokters')->insert([
            [
                'doctor_id' => $doc1->id,
                'doctor_name' => $doc1->name,
                'spesialis' => $doc1->spesialis,
                'tanggal' => date('Y-m-d', strtotime('+1 day')),
                'start_time' => '09:00',
                'end_time' => '12:00',
                'kuota' => 15,
                'ruangan' => 'Poliklinik A1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'doctor_id' => $doc2->id,
                'doctor_name' => $doc2->name,
                'spesialis' => $doc2->spesialis,
                'tanggal' => date('Y-m-d', strtotime('+2 days')),
                'start_time' => '10:00',
                'end_time' => '14:00',
                'kuota' => 10,
                'ruangan' => 'Poliklinik B3',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'doctor_id' => $doc3->id,
                'doctor_name' => $doc3->name,
                'spesialis' => $doc3->spesialis,
                'tanggal' => date('Y-m-d', strtotime('+3 days')),
                'start_time' => '08:00',
                'end_time' => '11:00',
                'kuota' => 8,
                'ruangan' => 'Poliklinik C2',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // 3. Seed Berita
        \Illuminate\Support\Facades\DB::table('beritas')->insert([
            [
                'judul' => 'Pentingnya Menjaga Pola Makan di Masa Pancaroba',
                'konten' => 'Perubahan cuaca yang ekstrem di masa pancaroba menuntut tubuh kita untuk beradaptasi lebih cepat. Salah satu cara menjaga daya tahan tubuh adalah dengan mengonsumsi makanan bergizi seimbang, terutama yang kaya vitamin C dan serat. Pastikan juga asupan air putih tercukupi minimal 2 liter per hari agar tubuh tetap terhidrasi.',
                'tanggal' => date('Y-m-d'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'judul' => 'MedicareSystem Luncurkan Fitur Antrean Online Terbaru',
                'konten' => 'Kini pasien MedicareSystem tidak perlu lagi mengantre lama di rumah sakit. Dengan fitur antrean online terintegrasi, nomor antrean janji temu akan didapatkan secara otomatis setelah dokter memberikan diagnosa rujukan. Sistem ini diharapkan memangkas waktu tunggu hingga 70%.',
                'tanggal' => date('Y-m-d', strtotime('-2 days')),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
