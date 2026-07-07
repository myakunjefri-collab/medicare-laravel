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
        // Buat harga nol menjadi dinamis
        foreach (\App\Models\PesananObat::where('total_harga', 0)->get() as $pesanan) {
            $harga_obat = rand(35, 95) * 1000;
            $pesanan->update([
                'total_harga' => $harga_obat + 15000
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tidak perlu operasi rollback
    }
};
