<?php

namespace App\Http\Requests\Loket;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLoketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_loket' => ['sometimes', 'string', 'max:100'],
            'kode_prefix' => ['sometimes', 'string', 'max:5'],
            'deskripsi' => ['nullable', 'string'],
        ];
    }
}
