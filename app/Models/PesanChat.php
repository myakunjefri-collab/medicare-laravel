<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PesanChat extends Model
{
    use HasFactory;

    protected $fillable = [
        'konsultasi_id',
        'pengirim',
        'pengirim_name',
        'pesan',
        'gambar',
        'waktu',
        'is_read',
    ];

    public function konsultasi()
    {
        return $this->belongsTo(Konsultasi::class, 'konsultasi_id');
    }
}
