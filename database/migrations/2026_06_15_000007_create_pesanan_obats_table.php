<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pesanan_obats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pasien_id')->constrained('users')->onDelete('cascade');
            $table->string('pasien_name');
            $table->foreignId('rekam_medis_id')->nullable()->constrained('rekam_medis')->onDelete('set null');
            $table->string('resep');
            $table->string('alamat_kirim');
            $table->enum('status', ['menunggu_pembayaran', 'diproses', 'dikirim', 'selesai', 'dibatalkan'])->default('menunggu_pembayaran');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pesanan_obats');
    }
};
