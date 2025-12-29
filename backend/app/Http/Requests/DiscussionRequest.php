<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DiscussionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled in controller/service
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'question' => ['required', 'string', 'min:10', 'max:1000'],
            'lesson_id' => ['nullable', 'uuid', 'exists:lessons,id'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'question.required' => 'Question is required.',
            'question.min' => 'Question must be at least 10 characters.',
            'question.max' => 'Question cannot exceed 1000 characters.',
            'lesson_id.exists' => 'The selected lesson does not exist.',
        ];
    }
}

