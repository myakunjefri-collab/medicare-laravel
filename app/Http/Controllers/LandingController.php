<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    public function index()
    {
        // Default health statistics
        $health_data = [
            ["icon" => "fas fa-hospital", "nilai" => "3.012", "nama" => "Jumlah Rumah Sakit", "tahun" => "2024", "keterangan" => "RS umum dan khusus"],
            ["icon" => "fas fa-clinic-medical", "nilai" => "10.264", "nama" => "Jumlah Puskesmas", "tahun" => "2024", "keterangan" => "Seluruh Indonesia"],
            ["icon" => "fas fa-user-md", "nilai" => "72.845", "nama" => "Dokter Umum", "tahun" => "2024", "keterangan" => "Tersebar di 514 kab/kota"],
            ["icon" => "fas fa-user-md", "nilai" => "45.320", "nama" => "Dokter Spesialis", "tahun" => "2024", "keterangan" => "23 spesialisasi"],
            ["icon" => "fas fa-chart-line", "nilai" => "0,47", "nama" => "Rasio Dokter", "tahun" => "2024", "keterangan" => "Per 1.000 penduduk"],
            ["icon" => "fas fa-user-nurse", "nilai" => "520.000", "nama" => "Jumlah Perawat", "tahun" => "2024", "keterangan" => "Perawat profesional"],
            ["icon" => "fas fa-baby-carriage", "nilai" => "340.000", "nama" => "Jumlah Bidan", "tahun" => "2024", "keterangan" => "Bidan di seluruh Indonesia"],
            ["icon" => "fas fa-capsules", "nilai" => "45.000", "nama" => "Jumlah Apoteker", "tahun" => "2024", "keterangan" => "Tenaga kefarmasian"],
            ["icon" => "fas fa-baby", "nilai" => "21,6%", "nama" => "Prevalensi Stunting", "tahun" => "2024", "keterangan" => "Target 14%"],
            ["icon" => "fas fa-chart-line", "nilai" => "96,5%", "nama" => "Cakupan JKN/KIS", "tahun" => "2024", "keterangan" => "Penduduk Indonesia"],
            ["icon" => "fas fa-chart-line", "nilai" => "189", "nama" => "AKI", "tahun" => "2024", "keterangan" => "Per 100.000 kelahiran"],
            ["icon" => "fas fa-chart-line", "nilai" => "16,85", "nama" => "AKB", "tahun" => "2024", "keterangan" => "Per 1.000 kelahiran"],
            ["icon" => "fas fa-flask", "nilai" => "850", "nama" => "Laboratorium", "tahun" => "2024", "keterangan" => "Unit terakreditasi"],
            ["icon" => "fas fa-ambulance", "nilai" => "84,5%", "nama" => "Imunisasi Dasar", "tahun" => "2024", "keterangan" => "Bayi 0-11 bulan"],
            ["icon" => "fas fa-lungs", "nilai" => "824.000", "nama" => "Kasus TB", "tahun" => "2024", "keterangan" => "Target eliminasi 2030"],
            ["icon" => "fas fa-shield-virus", "nilai" => "87%", "nama" => "Persalinan di Faskes", "tahun" => "2024", "keterangan" => "Meningkat 5%"]
        ];

        // Fetch registered doctors list to showcase on the landing page dynamically!
        $doctors = User::where('role', 'dokter')->get();

        return view('landing', compact('health_data', 'doctors'));
    }

    public function getBpsApi()
    {
        // Mock BPS API data
        return response()->json([
            'status' => 'success',
            'data' => [
                ['nilai' => '3012.00', 'nama_data' => 'Jumlah Rumah Sakit', 'tahun' => '2024'],
                ['nilai' => '10264.00', 'nama_data' => 'Jumlah Puskesmas', 'tahun' => '2024'],
                ['nilai' => '72845.00', 'nama_data' => 'Dokter Umum', 'tahun' => '2024'],
                ['nilai' => '45320.00', 'nama_data' => 'Dokter Spesialis', 'tahun' => '2024'],
                ['nilai' => '0.47', 'nama_data' => 'Rasio Dokter per 1.000 Penduduk', 'tahun' => '2024'],
                ['nilai' => '520000.00', 'nama_data' => 'Jumlah Perawat', 'tahun' => '2024'],
                ['nilai' => '340000.00', 'nama_data' => 'Jumlah Bidan', 'tahun' => '2024'],
                ['nilai' => '45000.00', 'nama_data' => 'Jumlah Apoteker', 'tahun' => '2024'],
                ['nilai' => '21.60', 'nama_data' => 'Prevalensi Stunting (%)', 'tahun' => '2024'],
                ['nilai' => '96.50', 'nama_data' => 'Cakupan JKN/KIS (%)', 'tahun' => '2024'],
                ['nilai' => '189.00', 'nama_data' => 'Angka Kematian Ibu (AKI)', 'tahun' => '2024'],
                ['nilai' => '16.85', 'nama_data' => 'Angka Kematian Bayi (AKB)', 'tahun' => '2024'],
            ]
        ]);
    }
}
