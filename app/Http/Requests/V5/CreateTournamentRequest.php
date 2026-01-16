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
            'name' => 'required|string',
            'short_name' => 'nullable|string',
            'gender' => 'nullable|string',
            'age_group' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            'region_id' => 'nullable|integer',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'pool_count' => 'nullable|integer',
            'standing_group_count' => 'nullable|integer',
            'cross_pool_game_count' => 'nullable|integer',
            'cross_standing_group_game_count' => 'nullable|integer',
            'round_type' => 'nullable|integer',
            'information' => 'nullable|string',
            'team_count' => 'nullable|integer',
            'deleted' => 'nullable|boolean',
            'tournament_program_id' => 'nullable|integer',
            'tournament_structure_id' => 'nullable|integer',
            'tournament_registration_type_id' => 'nullable|integer',
            'set_game_strategy_id' => 'nullable|integer',
            'moving_strategy_id' => 'nullable|integer',
            'league_id' => 'nullable|integer',
            'free_reschedule_until_date' => 'nullable|date',
            'registration_dead_line' => 'nullable|date',
            'minimum_warmup_minutes' => 'nullable|integer',
            'expected_duration_minutes' => 'required|integer',
            'earliest_start' => 'nullable|string',
            'latest_start' => 'nullable|string',
            'season_sport_id' => 'nullable|integer',
        ];
    }
}

