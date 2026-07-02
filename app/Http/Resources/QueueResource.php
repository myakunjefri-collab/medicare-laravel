<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QueueResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'pasien_name' => $this->pasien_name,
            'dokter_name' => strtoupper($this->dokter_name),
            'poli' => $this->poli,
            'tanggal' => $this->tanggal,
            'jam' => $this->jam,
            'nomor_antrean' => $this->nomor_antrean,
            'status' => $this->status,
            'keluhan' => $this->keluhan,
            'dibuat_pada' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
