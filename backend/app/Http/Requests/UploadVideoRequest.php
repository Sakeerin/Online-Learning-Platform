<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadVideoRequest extends FormRequest
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
            'video' => ['required', 'file', 'mimes:mp4,avi,mov,wmv,flv,webm', 'max:2048'], // 2GB max
            'filename' => ['nullable', 'string', 'max:255'],
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
            'video.required' => 'Video file is required.',
            'video.file' => 'The uploaded file must be a valid video file.',
            'video.mimes' => 'Video must be one of: mp4, avi, mov, wmv, flv, webm.',
            'video.max' => 'Video file size cannot exceed 2GB.',
        ];
    }
}

