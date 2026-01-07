<?php

namespace App\Http\Requests\V5;

use Illuminate\Foundation\Http\FormRequest;

class AssignUserRoleRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'season_sport_id' => 'required|integer',
            'team_id' => 'nullable|integer',
            'club_id' => 'nullable|integer',
        ];
    }
}

