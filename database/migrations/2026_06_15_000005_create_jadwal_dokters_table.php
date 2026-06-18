<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal_dokters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained('users')->onDelete('cascade');
            $table->string('doctor_name');
            $table->string('spesialis');
            $table->date('tanggal');
            $table->string('start_time');
            $table->string('end_time');
            $table->integer('kuota')->default(10);
            $table->string('ruangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_dokters');
    }
};
