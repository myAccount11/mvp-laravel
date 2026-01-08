<?php

namespace App\Http\Requests\V5;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLeagueRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|string',
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date',
            'club_id' => 'nullable|integer|exists:clubs,id',
            'deleted' => 'nullable|boolean',
            'season_sport_id' => 'sometimes|integer|exists:season_sports,id',
            'is_active' => 'sometimes|boolean',
            'user_id' => 'nullable|integer|exists:users,id',
            'organizer_id' => 'nullable|integer|exists:organizers,id',
            'information' => 'nullable|string',
            'sport_id' => 'nullable|integer|exists:sports,id',
        ];
    }
}

