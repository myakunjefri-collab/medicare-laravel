<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('janji_temus', function (Blueprint $table) {
            $table->string('poli')->nullable()->after('dokter_name');
        });
    }

    public function down(): void
    {
        Schema::table('janji_temus', function (Blueprint $table) {
            $table->dropColumn('poli');
        });
    }
};
