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
        Schema::table('pesanan_obats', function (Blueprint $table) {
            $table->string('bukti_transfer')->nullable()->after('status');
            $table->integer('total_harga')->default(0)->after('resep');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pesanan_obats', function (Blueprint $table) {
            $table->dropColumn(['bukti_transfer', 'total_harga']);
        });
    }
};
