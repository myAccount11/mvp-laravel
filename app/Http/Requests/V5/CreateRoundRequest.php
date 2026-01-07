<?php

namespace App\Http\Requests\V5;

use Illuminate\Foundation\Http\FormRequest;

class CreateRoundRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tournament_id' => 'required|integer|exists:tournaments,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ];
    }
}

