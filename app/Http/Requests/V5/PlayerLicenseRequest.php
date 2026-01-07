<?php

namespace App\Http\Requests\V5;

use Illuminate\Foundation\Http\FormRequest;

class PlayerLicenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'license_id' => 'nullable|integer|exists:player_license,id',
            'start' => 'nullable|date',
            'end' => 'nullable|date',
            'on_contract' => 'nullable|boolean',
            'season_sport_id' => 'nullable|integer|exists:season_sport,id',
            'user_id' => 'nullable|integer|exists:users,id',
        ];
    }
}

