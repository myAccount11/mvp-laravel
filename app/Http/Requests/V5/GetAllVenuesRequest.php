<?php

namespace App\Http\Requests\V5;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GetAllVenuesRequest extends FormRequest
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
     * Note: The season_sport_id validation checks if it exists in season_sports table.
     * The actual filtering by venue_season_sport relation is handled in VenueService.
     */
    public function rules(): array
    {
        return [
            'season_sport_id' => [
                'nullable',
                'integer',
                Rule::exists('season_sports', 'id'),
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'season_sport_id.integer' => 'The season sport ID must be an integer.',
            'season_sport_id.exists'  => 'The selected season sport does not exist.',
        ];
    }
}

