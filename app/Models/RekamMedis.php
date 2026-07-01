<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RekamMedis extends Model
{
    use HasFactory;

    protected $fillable = [
        'pasien_id',
        'pasien_name',
        'keluhan',
        'usia',
        'tensi_darah',
        'suhu_tubuh',
        'detak_jantung',
        'berat_badan',
        'kesimpulan_awal',
        'tanggal',
        'status',
        'diagnosa',
        'resep',
    ];

    public function pasien()
    {
        return $this->belongsTo(User::class, 'pasien_id');
    }
}
