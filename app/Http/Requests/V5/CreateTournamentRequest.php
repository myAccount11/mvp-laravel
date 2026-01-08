<?php

namespace App\Http\Requests\V5;

use Illuminate\Foundation\Http\FormRequest;

class CreateTournamentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'alias' => 'required|string',
            'short_name' => 'nullable|string',
            'region_id' => 'nullable|integer',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'pool_count' => 'nullable|integer',
            'standing_group_count' => 'nullable|integer',
            'cross_pool_game_count' => 'nullable|integer',
            'cross_standing_group_game_count' => 'nullable|integer',
            'round_type' => 'nullable|integer',
            'information' => 'nullable|string',
            'tournament_group_id' => 'nullable|integer',
            'team_count' => 'nullable|integer',
            'deleted' => 'nullable|boolean',
            'tournament_program_id' => 'nullable|integer',
        ];
    }
}

