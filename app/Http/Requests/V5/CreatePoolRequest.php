<?php

namespace App\Http\Requests\V5;

use Illuminate\Foundation\Http\FormRequest;

class CreatePoolRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            '*.id' => 'nullable|integer',
            '*.name' => 'required|string',
            '*.tournament_id' => 'required|integer',
            '*.games_between' => 'required|integer',
            '*.teams_count' => 'required|integer',
            '*.deleted' => 'nullable|boolean',
        ];
    }
}

