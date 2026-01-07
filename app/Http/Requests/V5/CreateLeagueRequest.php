<?php

namespace App\Http\Requests\V5;

use Illuminate\Foundation\Http\FormRequest;

class CreateLeagueRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'club_id' => 'nullable|integer|exists:clubs,id',
            'deleted' => 'nullable|boolean',
            'season_sport_id' => 'required|integer|exists:season_sport,id',
            'is_active' => 'required|boolean',
            'user_id' => 'nullable|integer|exists:users,id',
            'organizer_id' => 'nullable|integer|exists:organizers,id',
            'information' => 'nullable|string',
            'sport_id' => 'nullable|integer|exists:sports,id',
        ];
    }
}

