<?php

namespace App\Http\Requests\V5;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePoolRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|string',
            'tournament_id' => 'sometimes|integer|exists:tournaments,id',
            'games_between' => 'sometimes|integer',
            'teams_count' => 'sometimes|integer',
            'deleted' => 'nullable|boolean',
        ];
    }
}

