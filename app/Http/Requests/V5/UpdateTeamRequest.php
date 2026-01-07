<?php

namespace App\Http\Requests\V5;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTeamRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'club_id' => 'nullable|integer',
            'local_name' => 'nullable|string',
            'tournament_name' => 'nullable|string',
            'gender' => 'nullable|string',
            'age_group' => 'nullable|string',
            'official_type_id' => 'nullable|integer',
            'official_team_id' => 'nullable|integer',
            'club_rank' => 'nullable|integer',
        ];
    }
}

