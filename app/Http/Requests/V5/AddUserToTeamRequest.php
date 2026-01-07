<?php

namespace App\Http\Requests\V5;

use Illuminate\Foundation\Http\FormRequest;

class AddUserToTeamRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'email' => 'required|email',
            'name' => 'nullable|string',
            'season_sport_id' => 'required|integer',
            'number' => 'nullable|integer',
        ];
    }
}

