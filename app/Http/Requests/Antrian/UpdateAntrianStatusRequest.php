<?php

namespace App\Http\Requests\Antrian;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAntrianStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'in:menunggu,dipanggil,selesai'],
        ];
    }
}
