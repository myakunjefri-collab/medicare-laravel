<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalDokter extends Model
{
    use HasFactory;

    protected $fillable = [
        'doctor_id',
        'doctor_name',
        'spesialis',
        'tanggal',
        'start_time',
        'end_time',
        'kuota',
        'ruangan',
    ];

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }
}
