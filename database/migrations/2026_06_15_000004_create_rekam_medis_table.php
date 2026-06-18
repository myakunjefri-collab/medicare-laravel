<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rekam_medis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pasien_id')->constrained('users')->onDelete('cascade');
            $table->string('pasien_name');
            $table->text('keluhan');
            $table->integer('usia')->nullable();
            $table->date('tanggal');
            $table->string('status')->default('menunggu');
            $table->string('diagnosa')->nullable();
            $table->string('resep')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rekam_medis');
    }
};
