<?php

namespace App\Http\Requests\V5;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $userId = $this->route('id') ?? $this->route('user');
        
        return [
            'email' => [
                'nullable',
                'email',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'password' => 'nullable|string|min:8',
            'name' => 'nullable|string',
            'picture' => 'nullable|string',
            'disable_emails' => 'nullable|boolean',
            'license' => 'nullable|integer',
            'gender' => 'nullable|string',
            'birth_year' => 'nullable|integer',
            'birth_month' => 'nullable|integer',
            'birth_day' => 'nullable|integer',
            'nationality' => 'nullable|string',
            'country' => 'nullable|string',
            'city' => 'nullable|string',
            'postal_code' => 'nullable|string',
            'address_line1' => 'nullable|string',
            'address_line2' => 'nullable|string',
            'latlng' => 'nullable|string',
            'debtor_number' => 'nullable|string',
            'phone_numbers' => 'nullable|string',
            'is_verified' => 'nullable|boolean',
        ];
    }
}

