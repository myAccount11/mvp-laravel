<?php

namespace App\Http\Requests\V5;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVenueRequest extends FormRequest
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
            // Basic venue fields (all optional for update)
            'name' => 'nullable|string',
            'address_line1' => 'nullable|string',
            'address_line2' => 'nullable|string',
            'postal_code' => 'nullable|string',
            'postal_city' => 'nullable|string',
            'country' => 'nullable|string',
            'phone_number' => 'nullable|string|regex:/^\+?[1-9]\d{1,14}$/',
            'web_address' => 'nullable|string|url',
            'is_active' => 'nullable|boolean',
            'lat_lng' => 'nullable|string',
            'place_id' => 'nullable|string',
            'cal_key' => 'nullable|string',

            // Club associations
            'deleted_license_numbers' => 'nullable|array',
            'deleted_license_numbers.*' => 'integer',
            'club_license_numbers' => 'nullable|array',
            'club_license_numbers.*' => 'integer',
            'club_team_names' => 'nullable|array',
            'club_team_names.*' => 'string',

            // Courts creation
            'courts_create' => 'nullable|array',
            'courts_create.*.create_court_dto' => 'required|array',
            'courts_create.*.create_court_dto.name' => 'nullable|string',
            'courts_create.*.create_court_dto.venue_id' => 'nullable|integer|exists:venues,id',
            'courts_create.*.create_court_dto.length' => 'nullable|integer',
            'courts_create.*.create_court_dto.width' => 'nullable|integer',
            'courts_create.*.create_court_dto.side_space' => 'nullable|integer',
            'courts_create.*.create_court_dto.end_space' => 'nullable|integer',
            'courts_create.*.create_court_dto.in_active' => 'nullable|boolean',
            'courts_create.*.create_court_dto.deleted' => 'nullable|boolean',
            'courts_create.*.court_requirements' => 'nullable|array',
            'courts_create.*.court_requirements.court_requirement_1' => 'nullable|integer',
            'courts_create.*.court_requirements.court_requirement_2' => 'nullable|integer',
            'courts_create.*.court_requirements.court_requirement_3' => 'nullable|integer',
            'courts_create.*.court_requirements.court_requirement_4' => 'nullable|integer',

            // Courts update
            'courts_update' => 'nullable|array',
            'courts_update.*.id' => 'required|integer|exists:courts,id',
            'courts_update.*.update_court_dto' => 'required|array',
            'courts_update.*.update_court_dto.name' => 'nullable|string',
            'courts_update.*.update_court_dto.venue_id' => 'nullable|integer|exists:venues,id',
            'courts_update.*.update_court_dto.length' => 'nullable|integer',
            'courts_update.*.update_court_dto.width' => 'nullable|integer',
            'courts_update.*.update_court_dto.side_space' => 'nullable|integer',
            'courts_update.*.update_court_dto.end_space' => 'nullable|integer',
            'courts_update.*.update_court_dto.in_active' => 'nullable|boolean',
            'courts_update.*.update_court_dto.deleted' => 'nullable|boolean',
            'courts_update.*.court_requirements' => 'nullable|array',
            'courts_update.*.court_requirements.court_requirement_1' => 'nullable|integer',
            'courts_update.*.court_requirements.court_requirement_2' => 'nullable|integer',
            'courts_update.*.court_requirements.court_requirement_3' => 'nullable|integer',
            'courts_update.*.court_requirements.court_requirement_4' => 'nullable|integer',

            // Courts deletion
            'delete_courts' => 'nullable|array',
            'delete_courts.*.id' => 'required|integer|exists:courts,id',
            'delete_courts.*.venue_id' => 'required|integer|exists:venues,id',
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
            'phone_number.regex' => 'The phone number format is invalid. It must be E.164 formatted.',
            'web_address.url' => 'The web address must be a valid URL.',
            'courts_create.*.create_court_dto.required' => 'Each court creation must include create_court_dto.',
            'courts_update.*.id.required' => 'Each court update must include an id.',
            'courts_update.*.update_court_dto.required' => 'Each court update must include update_court_dto.',
            'delete_courts.*.id.required' => 'Each court deletion must include an id.',
            'delete_courts.*.venue_id.required' => 'Each court deletion must include a venue_id.',
        ];
    }
}

