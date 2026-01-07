<?php

namespace App\Http\Requests\V5;

use Illuminate\Foundation\Http\FormRequest;

class CreateReservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'start_time' => 'nullable|string',
            'end_time' => 'nullable|string',
            'text' => 'nullable|string',
            'type_id' => 'nullable|integer|exists:reservation_types,id',
            'time_slot_id' => 'required|integer|exists:time_slots,id',
            'club_id' => 'nullable|integer|exists:clubs,id',
            'team_id' => 'nullable|integer|exists:teams,id',
            'game_id' => 'nullable|integer|exists:games,id',
            'user_id' => 'nullable|integer|exists:users,id',
            'age_group' => 'nullable|string',
            'is_deleted' => 'nullable|boolean',
        ];
    }
}

