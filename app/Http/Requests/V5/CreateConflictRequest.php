<?php

namespace App\Http\Requests\V5;

use Illuminate\Foundation\Http\FormRequest;

class CreateConflictRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'game_id' => 'required|integer|exists:games,id',
            'start_time' => 'nullable|string',
            'blocked_association' => 'nullable|string',
            'blocked_team' => 'nullable|string',
            'games_to_close' => 'nullable|string',
            'games_on_court' => 'nullable|string',
            'reservations' => 'nullable|string',
            'coaches' => 'nullable|string',
            'has_court' => 'nullable|string',
            'ignore_associations' => 'nullable|boolean',
            'ignore_home' => 'nullable|boolean',
            'ignore_away' => 'nullable|boolean',
        ];
    }
}

