<?php

namespace App\Http\Requests\V5;

use Illuminate\Foundation\Http\FormRequest;

class CreateTournamentGroupRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'short_name' => 'nullable|string',
            'gender' => 'nullable|string',
            'age_group' => 'nullable|string',
            'hide_from_rankings' => 'nullable|boolean',
            'allow_mentor_prospect' => 'nullable|boolean',
            'star_rating' => 'nullable|integer',
            'score_sheet_type_id' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
            'min_teams' => 'nullable|integer',
            'max_teams' => 'nullable|integer',
            'region_id' => 'nullable|integer',
            'registration_fee' => 'nullable|integer',
            'information' => 'nullable|string',
            'tournament_type_id' => 'nullable|integer',
            'tournament_structure_id' => 'nullable|integer',
            'tournament_registration_type_id' => 'nullable|integer',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'ref_nomination_id' => 'nullable|integer',
            'officials_type_id' => 'nullable|integer',
            'levels' => 'nullable|integer',
            'set_game_strategy_id' => 'nullable|integer',
            'moving_strategy_id' => 'nullable|integer',
            'player_license_type_id' => 'nullable|integer',
            'penalty_type_id' => 'nullable|integer',
            'show_birth_in_score_sheet' => 'nullable|boolean',
            'tournament_configs_id' => 'nullable|integer',
            'league_id' => 'nullable|integer',
        ];
    }
}
