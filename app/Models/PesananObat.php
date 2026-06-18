<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PesananObat extends Model
{
    use HasFactory;

    protected $table = 'pesanan_obats';

    protected $fillable = [
        'pasien_id',
        'pasien_name',
        'rekam_medis_id',
        'resep',
        'alamat_kirim',
        'status',
        'bukti_transfer',
        'total_harga',
    ];

    public function pasien()
    {
        return $this->belongsTo(User::class, 'pasien_id');
    }

    public function rekamMedis()
    {
        return $this->belongsTo(RekamMedis::class, 'rekam_medis_id');
    }
}
