<?php

namespace App\Http\Requests\V5;

use Illuminate\Foundation\Http\FormRequest;

class CreateTimeSlotRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'date' => 'required|date',
            'start_time' => 'required|string',
            'end_time' => 'required|string',
            'expiration' => 'nullable|date',
            'court_id' => 'required|integer|exists:courts,id',
            'club_id' => 'nullable|integer|exists:clubs,id',
            'season_sport_id' => 'required|integer|exists:season_sport,id',
            'is_deleted' => 'nullable|boolean',
            'create_weekly' => 'nullable|boolean',
            'use_different' => 'nullable|boolean',
        ];
    }
}

