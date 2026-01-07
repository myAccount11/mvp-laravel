<?php

namespace App\Http\Requests\V5;

use Illuminate\Foundation\Http\FormRequest;

class CreateMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type_id' => 'required|integer',
            'user_id' => 'required|integer|exists:users,id',
            'restriction' => 'required|integer',
            'to_id' => 'required|integer',
            'html' => 'required|string',
            'notification_time' => 'nullable|string',
            'email' => 'nullable|email',
            'subject' => 'nullable|string',
            'files' => 'nullable|array',
            'files.*' => 'file|max:10240', // 10MB max per file
        ];
    }
}

