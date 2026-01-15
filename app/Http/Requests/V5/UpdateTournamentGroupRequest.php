<?php

namespace App\Http\Requests\V5;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTournamentGroupRequest extends FormRequest
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
            'name' => 'sometimes|string',
            'short_name' => 'nullable|string',
            'gender' => 'nullable|string',
            'age_group' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            'min_teams' => 'nullable|integer',
            'max_teams' => 'nullable|integer',
            'region_id' => 'nullable|integer',
            'information' => 'nullable|string',
            'tournament_structure_id' => 'nullable|integer',
            'tournament_registration_type_id' => 'nullable|integer',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'set_game_strategy_id' => 'nullable|integer',
            'moving_strategy_id' => 'nullable|integer',
            'league_id' => 'nullable|integer',
            'free_reschedule_until_date' => 'nullable|date',
            'registration_dead_line' => 'nullable|date',
            'minimum_warmup_minutes' => 'nullable|integer',
            'expected_duration_minutes' => 'nullable|integer',
            'earliest_start' => 'nullable|string',
            'latest_start' => 'nullable|string',
        ];
    }
}
