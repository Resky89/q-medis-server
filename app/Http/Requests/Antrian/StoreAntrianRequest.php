<?php

namespace App\Http\Requests\Antrian;

use Illuminate\Foundation\Http\FormRequest;

class StoreAntrianRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'loket_id' => ['required', 'integer', 'exists:lokets,id'],
        ];
    }
}
