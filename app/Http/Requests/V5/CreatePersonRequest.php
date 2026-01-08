<?php

namespace App\Http\Requests\V5;

use Illuminate\Foundation\Http\FormRequest;

class CreatePersonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'external_id' => 'nullable|integer',
            'email' => 'nullable|email',
            'name' => 'nullable|string',
            'season_sport_id' => 'nullable|integer|exists:season_sports,id',
            'deleted' => 'nullable|boolean',
            'user_id' => 'nullable|integer|exists:users,id',
            'address_line1' => 'nullable|string',
            'address_line2' => 'nullable|string',
            'postal_code' => 'nullable|string',
            'city' => 'nullable|string',
            'country' => 'nullable|string',
            'phone_numbers' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            'latlng' => 'nullable|string',
            'place_id' => 'nullable|string',
        ];
    }
}

