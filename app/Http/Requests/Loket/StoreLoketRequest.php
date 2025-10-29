<?php

namespace App\Http\Requests\Loket;

use Illuminate\Foundation\Http\FormRequest;

class StoreLoketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_loket' => ['required', 'string', 'max:100'],
            'kode_prefix' => ['required', 'string', 'max:5'],
            'deskripsi' => ['nullable', 'string'],
        ];
    }
}
