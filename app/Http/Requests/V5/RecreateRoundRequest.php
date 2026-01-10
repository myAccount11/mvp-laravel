<?php

namespace App\Http\Requests\V5;

use Illuminate\Foundation\Http\FormRequest;

class RecreateRoundRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            '*.number' => 'required|integer',
            '*.tournament_id' => 'nullable|integer',
            '*.from_date' => 'required|date',
            '*.to_date' => 'required|date',
            '*.week' => 'nullable|integer',
            '*.year' => 'nullable|integer',
            '*.type' => 'nullable|integer',
            '*.force_cross' => 'nullable|boolean',
            '*.deleted' => 'nullable|boolean',
        ];
    }
}

