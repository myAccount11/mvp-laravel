<?php

namespace App\Http\Requests\V5;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTimeSlotRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'date'             => ['sometimes', 'date'],
            'start_time'       => ['sometimes', 'string', 'regex:/^([01][0-9]|2[0-3]):[0-5][0-9](:00)?$/'],
            'end_time'         => ['sometimes', 'string', 'regex:/^([01][0-9]|2[0-3]):[0-5][0-9](:00)?$/'],
            'expiration'       => ['nullable', 'date'],
            'court_id'         => ['sometimes', 'integer', 'exists:courts,id'],
            'club_id'          => ['nullable', 'integer', 'exists:clubs,id'],
            'season_sport_id'  => ['sometimes', 'integer', 'exists:season_sports,id'],
            'is_deleted'       => ['nullable', 'boolean'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'start_time.regex' => 'The start time must be in HH:MM or HH:MM:SS format.',
            'end_time.regex'   => 'The end time must be in HH:MM or HH:MM:SS format.',
            'court_id.exists'  => 'The selected court does not exist.',
            'club_id.exists'   => 'The selected club does not exist.',
            'season_sport_id.exists' => 'The selected season sport does not exist.',
        ];
    }
}

