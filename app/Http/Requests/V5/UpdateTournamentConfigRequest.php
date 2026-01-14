<?php

namespace App\Http\Requests\V5;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTournamentConfigRequest extends FormRequest
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
            'free_reschedule_until_date' => 'nullable|date',
            'registration_dead_line' => 'nullable|date',
            'minimum_warmup_minutes' => 'nullable|integer',
            'expected_duration_minutes' => 'sometimes|integer',
            'season_sport_id' => 'nullable|integer',
            'information' => 'nullable|string',
            'earliest_start' => 'nullable|string',
            'latest_start' => 'nullable|string',
        ];
    }
}
