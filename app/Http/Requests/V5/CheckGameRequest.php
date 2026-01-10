<?php

namespace App\Http\Requests\V5;

use Illuminate\Foundation\Http\FormRequest;

class CheckGameRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'date'          => ['nullable', 'date'],
            'time'          => ['nullable', 'string', 'regex:/^([01][0-9]|2[0-3]):[0-5][0-9](:00)?$/'],
            'court_id'      => ['nullable', 'integer', 'exists:courts,id'],
            'suggestion_id' => ['nullable', 'integer', 'exists:suggestions,id'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'time.regex'          => 'The time must be in HH:MM or HH:MM:SS format.',
            'court_id.exists'     => 'The selected court does not exist.',
            'suggestionId.exists' => 'The selected suggestion does not exist.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Normalize courtId to court_id if provided
        if ($this->has('courtId')) {
            $this->merge([
                'court_id' => $this->input('courtId'),
            ]);
        }
    }
}

