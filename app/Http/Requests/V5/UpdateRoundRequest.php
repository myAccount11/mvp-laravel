<?php

namespace App\Http\Requests\V5;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRoundRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            '*.id' => 'required|integer',
            '*.tournament_id' => 'sometimes|integer',
            '*.number' => 'sometimes|integer',
            '*.from_date' => 'nullable|date',
            '*.to_date' => 'nullable|date',
            '*.week' => 'nullable|integer',
            '*.year' => 'nullable|integer',
            '*.type' => 'nullable|integer',
            '*.force_cross' => 'nullable|boolean',
            '*.deleted' => 'nullable|boolean',
        ];
    }
}

