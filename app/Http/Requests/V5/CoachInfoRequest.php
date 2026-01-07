<?php

namespace App\Http\Requests\V5;

use Illuminate\Foundation\Http\FormRequest;

class CoachInfoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'coach_id' => 'required|integer|exists:coach,id',
            'level' => 'nullable|string',
            'start' => 'nullable|string',
            'end' => 'nullable|string',
            'startm' => 'nullable|string',
            'endm' => 'nullable|string',
            'startb' => 'nullable|string',
            'endb' => 'nullable|string',
            'startt' => 'nullable|string',
            'endt' => 'nullable|string',
            'children' => 'nullable|string',
        ];
    }
}

