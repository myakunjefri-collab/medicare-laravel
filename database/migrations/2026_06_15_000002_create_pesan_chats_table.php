<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pesan_chats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('konsultasi_id')->constrained('konsultasis')->onDelete('cascade');
            $table->enum('pengirim', ['pasien', 'dokter']);
            $table->string('pengirim_name');
            $table->text('pesan');
            $table->timestamp('waktu')->useCurrent();
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pesan_chats');
    }
};
