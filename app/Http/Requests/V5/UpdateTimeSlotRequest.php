<?php

namespace App\Http\Requests\V5;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTimeSlotRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'date' => 'sometimes|date',
            'start_time' => 'sometimes|string',
            'end_time' => 'sometimes|string',
            'expiration' => 'nullable|date',
            'court_id' => 'sometimes|integer|exists:courts,id',
            'club_id' => 'nullable|integer|exists:clubs,id',
            'season_sport_id' => 'sometimes|integer|exists:season_sport,id',
            'is_deleted' => 'nullable|boolean',
        ];
    }
}

