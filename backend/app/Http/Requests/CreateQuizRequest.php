<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateQuizRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->isInstructor() ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'passing_score' => ['nullable', 'integer', 'min:0', 'max:100'],
            'allow_retake' => ['nullable', 'boolean'],
            'questions' => ['required', 'array', 'min:1'],
            'questions.*.question_text' => ['required', 'string', 'min:10'],
            'questions.*.question_type' => ['required', 'string', 'in:multiple_choice,true_false'],
            'questions.*.options' => ['required', 'array'],
            'questions.*.options.*' => ['required', 'string'],
            'questions.*.correct_answer' => ['required', 'string'],
            'questions.*.explanation' => ['nullable', 'string'],
            'questions.*.order' => ['nullable', 'integer', 'min:1'],
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
            'title.required' => 'Quiz title is required.',
            'questions.required' => 'At least one question is required.',
            'questions.min' => 'At least one question is required.',
            'questions.*.question_text.required' => 'Question text is required.',
            'questions.*.question_text.min' => 'Question text must be at least 10 characters.',
            'questions.*.question_type.required' => 'Question type is required.',
            'questions.*.options.required' => 'Question options are required.',
            'questions.*.correct_answer.required' => 'Correct answer is required.',
        ];
    }
}

