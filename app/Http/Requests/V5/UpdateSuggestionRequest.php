<?php

namespace App\Http\Requests\V5;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSuggestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'game_id' => 'sometimes|integer|exists:games,id',
            'date' => 'nullable|date',
            'time' => 'nullable|string',
            'court_id' => 'nullable|integer|exists:courts,id',
            'requested_by' => 'nullable|integer|exists:users,id',
            'accepted_by' => 'nullable|integer|exists:users,id',
            'rejected_by' => 'nullable|integer|exists:users,id',
            'approved_by' => 'nullable|integer|exists:users,id',
            'requested_date' => 'nullable|date',
            'accepted_date' => 'nullable|date',
            'rejected_date' => 'nullable|date',
            'approved_date' => 'nullable|date',
        ];
    }
}

