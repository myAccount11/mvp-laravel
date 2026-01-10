<?php

namespace App\Http\Requests\V5;

use Illuminate\Foundation\Http\FormRequest;

class CreateVenueRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Merge query parameters into the request data for validation
        $this->merge([
            'season_sport_id' => $this->query('season_sport_id'),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'season_sport_id' => 'required|integer',
            'name'            => 'nullable|string',
            'address_line1'   => 'nullable|string',
            'address_line2'   => 'nullable|string',
            'postal_code'     => 'nullable|string',
            'postal_city'     => 'nullable|string',
            'country'         => 'nullable|string',
            'phone_number'    => 'nullable|string|regex:/^\+?[1-9]\d{1,14}$/',
            'web_address'     => 'nullable|string|url',
            'is_active'       => 'nullable|boolean',
            'lat_lng'         => 'nullable|string',
            'place_id'        => 'nullable|string',
            'cal_key'         => 'nullable|string',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'season_sport_id.required' => 'The season_sport_id query parameter is required.',
            'season_sport_id.integer'  => 'The season_sport_id must be an integer.',
            'season_sport_id.exists'   => 'The selected season_sport_id does not exist.',
            'phone_number.regex'       => 'The phone number format is invalid. It must be E.164 formatted.',
            'web_address.url'          => 'The web address must be a valid URL.',
        ];
    }

    /**
     * Get the validated season sport ID from query parameters.
     *
     * @return int
     */
    public function getSeasonSportId(): int
    {
        return (int)$this->validated()['season_sport_id'];
    }
}

