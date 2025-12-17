<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCourseRequest extends FormRequest
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
        $categories = ['Development', 'Design', 'Business', 'Marketing', 'Photography', 'Music', 'Health', 'Lifestyle', 'Other'];

        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:500'],
            'description' => ['sometimes', 'string', 'min:100'],
            'learning_objectives' => ['nullable', 'array'],
            'learning_objectives.*' => ['string', 'max:255'],
            'category' => ['sometimes', 'string', Rule::in($categories)],
            'subcategory' => ['nullable', 'string', 'max:100'],
            'difficulty_level' => ['sometimes', 'string', Rule::in(['beginner', 'intermediate', 'advanced'])],
            'thumbnail' => ['nullable', 'string', 'max:255'],
            'price' => ['sometimes', 'numeric', 'min:0', 'max:9999.99'],
            'currency' => ['nullable', 'string', 'size:3'],
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
            'title.max' => 'Course title cannot exceed 255 characters.',
            'description.min' => 'Course description must be at least 100 characters.',
            'category.in' => 'Please select a valid category.',
            'difficulty_level.in' => 'Please select a valid difficulty level.',
            'price.min' => 'Course price cannot be negative.',
            'price.max' => 'Course price cannot exceed 9999.99.',
        ];
    }
}

