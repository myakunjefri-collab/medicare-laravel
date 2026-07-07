<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('janji_temus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pasien_id')->constrained('users')->onDelete('cascade');
            $table->string('pasien_name');
            $table->string('dokter_name');
            $table->date('tanggal');
            $table->string('jam'); // menyimpan waktu janji temu
            $table->enum('status', ['menunggu', 'konfirmasi', 'selesai', 'dibatalkan'])->default('menunggu');
            $table->string('nomor_antrean');
            $table->text('keluhan');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('janji_temus');
    }
};
