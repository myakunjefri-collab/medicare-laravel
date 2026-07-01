<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rekam_medis', function (Blueprint $table) {
            $table->string('tensi_darah')->nullable()->after('usia');
            $table->decimal('suhu_tubuh', 4, 2)->nullable()->after('tensi_darah');
            $table->integer('detak_jantung')->nullable()->after('suhu_tubuh');
            $table->integer('berat_badan')->nullable()->after('detak_jantung');
            $table->text('kesimpulan_awal')->nullable()->after('berat_badan');
        });
    }

    public function down(): void
    {
        Schema::table('rekam_medis', function (Blueprint $table) {
            $table->dropColumn(['tensi_darah', 'suhu_tubuh', 'detak_jantung', 'berat_badan', 'kesimpulan_awal']);
        });
    }
};
