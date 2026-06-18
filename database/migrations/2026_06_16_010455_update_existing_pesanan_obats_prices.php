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
        // Update any existing orders that have 0 price to be dynamic (between Rp 35k - 95k + 15k shipping)
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
        // No operation needed for rollback as this updates data values
    }
};
