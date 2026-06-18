<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('konsultasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pasien_id')->constrained('users')->onDelete('cascade');
            $table->string('pasien_name');
            $table->foreignId('dokter_id')->constrained('users')->onDelete('cascade');
            $table->string('dokter_name');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('konsultasis');
    }
};
