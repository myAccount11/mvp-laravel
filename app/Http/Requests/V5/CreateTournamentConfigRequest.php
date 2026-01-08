<?php

namespace App\Http\Requests\V5;

use Illuminate\Foundation\Http\FormRequest;

class CreateTournamentConfigRequest extends FormRequest
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
            'game_dead_line' => 'nullable|date',
            'free_reschedule_until_date' => 'nullable|date',
            'registration_dead_line' => 'nullable|date',
            'minimum_warmup_minutes' => 'nullable|integer',
            'expected_duration_minutes' => 'required|integer',
            'cooldown_minutes' => 'nullable|integer',
            'refs_per_game' => 'nullable|integer',
            'refs_from_associations' => 'nullable|boolean',
            '24sec_required' => 'nullable|boolean',
            'stats_required' => 'nullable|boolean',
            'report_required' => 'nullable|boolean',
            'default_score_sheet_type' => 'nullable|integer',
            'allow_adjusted_score_sheet' => 'nullable|boolean',
            'in_active' => 'nullable|boolean',
            'court_requirement_id' => 'nullable|integer',
            'season_sport_id' => 'nullable|integer',
            'deleted' => 'nullable|boolean',
            'tl_edit_enabled' => 'nullable|boolean',
            'games_hidden' => 'nullable|boolean',
            'information' => 'nullable|string',
            'earliest_start' => 'nullable|string',
            'cm_time_set_until' => 'nullable|date',
            'latest_start' => 'nullable|string',
            'coach_license_type_id' => 'nullable|integer',
            'ref_prio' => 'nullable|integer',
            'allow_mentor_prospect' => 'nullable|integer',
            'star_rating' => 'nullable|integer',
            'transportation_fee' => 'nullable|integer',
        ];
    }
}
