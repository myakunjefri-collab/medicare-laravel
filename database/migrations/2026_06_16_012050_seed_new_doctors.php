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
        if (app()->environment('testing')) {
            return;
        }

        $newDoctors = [
            [
                'name' => 'dr. Hendra Kurniawan, Sp.THT',
                'email' => 'dokter7@med.com',
                'username' => 'dokter7',
                'password' => \Illuminate\Support\Facades\Hash::make('dokter789'),
                'role' => 'dokter',
                'spesialis' => 'THT',
                'no_hp' => '08122334422',
                'phone' => '08122334422',
                'is_active' => true,
            ],
            [
                'name' => 'dr. Linda Lestari, Sp.KK',
                'email' => 'dokter8@med.com',
                'username' => 'dokter8',
                'password' => \Illuminate\Support\Facades\Hash::make('dokter890'),
                'role' => 'dokter',
                'spesialis' => 'Kulit & Kelamin',
                'no_hp' => '08122334433',
                'phone' => '08122334433',
                'is_active' => true,
            ],
            [
                'name' => 'dr. Yusuf Pratama, Sp.B',
                'email' => 'dokter9@med.com',
                'username' => 'dokter9',
                'password' => \Illuminate\Support\Facades\Hash::make('dokter901'),
                'role' => 'dokter',
                'spesialis' => 'Bedah',
                'no_hp' => '08122334444',
                'phone' => '08122334444',
                'is_active' => true,
            ],
        ];

        foreach ($newDoctors as $doc) {
            if (!\App\Models\User::where('email', $doc['email'])->exists()) {
                \App\Models\User::create($doc);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \App\Models\User::whereIn('email', ['dokter7@med.com', 'dokter8@med.com', 'dokter9@med.com'])->delete();
    }
};
