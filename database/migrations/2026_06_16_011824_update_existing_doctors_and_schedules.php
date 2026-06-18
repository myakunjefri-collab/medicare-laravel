<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $doctorsMapping = [
            'Penyakit Dalam' => 'dr. Andi Wijaya, Sp.PD',
            'Anak' => 'dr. Sarah Melati, Sp.A',
            'Jantung' => 'dr. Budi Hartono, Sp.JP',
            'Mata' => 'dr. Dewi Anggraeni, Sp.M',
            'Saraf' => 'dr. Rizky, Sp.S',
            'Gigi' => 'drg. Rina, Sp.KGA',
        ];

        foreach ($doctorsMapping as $spesialis => $newName) {
            $oldName = explode(',', $newName)[0]; // e.g. "dr. Andi Wijaya"
            
            $doctor = \App\Models\User::where('role', 'dokter')
                ->where('name', $oldName)
                ->first();

            if ($doctor) {
                $doctor->update(['name' => $newName]);

                // Update doctor's name in schedule table
                \App\Models\JadwalDokter::where('doctor_id', $doctor->id)
                    ->update(['doctor_name' => $newName]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No operations needed for down()
    }
};
