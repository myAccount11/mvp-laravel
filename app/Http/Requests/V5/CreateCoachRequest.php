<?php

namespace App\Http\Requests\V5;

use Illuminate\Foundation\Http\FormRequest;

class CreateCoachRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'license' => 'required|integer',
            'person_id' => 'required|integer|exists:person,id',
            'level' => 'required|string',
            'start' => 'required|date',
            'end' => 'required|date',
            'master_license' => 'nullable|integer',
        ];
    }
}

