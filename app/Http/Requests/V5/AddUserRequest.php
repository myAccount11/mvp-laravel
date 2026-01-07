<?php

namespace App\Http\Requests\V5;

use Illuminate\Foundation\Http\FormRequest;

class AddUserRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'email' => 'required|email',
            'season_sport_id' => 'required|integer',
        ];
    }
}

