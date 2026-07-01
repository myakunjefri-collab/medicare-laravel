<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerService extends Model
{
    use HasFactory;

    protected $fillable = [
        'pasien_id',
        'pasien_name',
        'pesan',
        'balasan',
        'status',
    ];

    public function pasien()
    {
        return $this->belongsTo(User::class, 'pasien_id');
    }
}
