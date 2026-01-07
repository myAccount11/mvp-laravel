<?php

namespace App\Http\Requests\V5;

use Illuminate\Foundation\Http\FormRequest;

class CreatePlayerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'license' => 'required|string',
            'person_id' => 'required|integer|exists:person,id',
            'jersey_number' => 'required|string',
        ];
    }
}

