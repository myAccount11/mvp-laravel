<?php

namespace App\Http\Requests\V5;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type_id' => 'sometimes|integer',
            'user_id' => 'sometimes|integer|exists:users,id',
            'restriction' => 'sometimes|integer',
            'to_id' => 'sometimes|integer',
            'html' => 'sometimes|string',
            'notification_time' => 'nullable|string',
            'email' => 'nullable|email',
            'subject' => 'nullable|string',
        ];
    }
}

