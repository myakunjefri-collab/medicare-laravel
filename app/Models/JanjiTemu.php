<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JanjiTemu extends Model
{
    use HasFactory;

    protected $fillable = [
        'pasien_id',
        'pasien_name',
        'dokter_name',
        'tanggal',
        'jam',
        'status',
        'nomor_antrean',
        'keluhan',
    ];

    public function pasien()
    {
        return $this->belongsTo(User::class, 'pasien_id');
    }
}
