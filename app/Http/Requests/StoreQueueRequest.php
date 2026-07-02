<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreQueueRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'dokter_name' => 'required|string|max:255',
            'poli' => 'required|string|max:100',
            'tanggal' => 'required|date_format:Y-m-d',
            'jam' => 'required|string|max:50',
            'keluhan' => 'required|string',
        ];
    }
}
