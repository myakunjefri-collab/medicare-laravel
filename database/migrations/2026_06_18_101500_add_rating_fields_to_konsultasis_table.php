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
        Schema::table('konsultasis', function (Blueprint $table) {
            $table->enum('status', ['aktif', 'selesai'])->default('aktif')->after('dokter_name');
            $table->unsignedTinyInteger('rating')->nullable()->after('status');
            $table->text('ulasan')->nullable()->after('rating');
            $table->boolean('is_rated')->default(false)->after('ulasan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('konsultasis', function (Blueprint $table) {
            $table->dropColumn(['status', 'rating', 'ulasan', 'is_rated']);
        });
    }
};
