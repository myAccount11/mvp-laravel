<?php

namespace App\Http\Requests\V5;

use Illuminate\Foundation\Http\FormRequest;

class CreateClubRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'nullable|string',
            'building' => 'nullable|string',
            'address_line1' => 'nullable|string',
            'address_line2' => 'nullable|string',
            'postal_code' => 'nullable|string',
            'postal_city' => 'nullable|string',
            'country' => 'nullable|string',
            'region_id' => 'nullable|string',
            'phone_number1' => 'nullable|string',
            'phone_number2' => 'nullable|string',
            'email' => 'nullable|email',
            'public_notes' => 'nullable|string',
            'internal_notes' => 'nullable|string',
            'web_address' => 'nullable|string',
            'short_name' => 'nullable|string',
            'deleted' => 'nullable|boolean',
            'district' => 'nullable|string',
            'status' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            'cal_key' => 'nullable|string',
            'license' => 'nullable|integer',
        ];
    }
}

