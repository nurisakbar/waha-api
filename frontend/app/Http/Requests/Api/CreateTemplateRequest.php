<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class CreateTemplateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'error' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422)
        );
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'content' => 'required|string|max:4096',
            'message_type' => 'required|in:text,image,video,document,button,list',
            'variables' => 'nullable|array',
            'variables.*' => 'string|max:50',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'nullable|boolean',
            'metadata' => 'nullable|array',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Template name is required.',
            'name.string' => 'Template name must be a string.',
            'name.max' => 'Template name must not exceed 255 characters.',
            'content.required' => 'Template content is required.',
            'content.string' => 'Template content must be a string.',
            'content.max' => 'Template content must not exceed 4096 characters.',
            'message_type.required' => 'Message type is required.',
            'message_type.in' => 'Message type must be one of: text, image, video, document, button, list.',
            'variables.array' => 'Variables must be an array.',
            'variables.*.string' => 'Each variable name must be a string.',
            'variables.*.max' => 'Each variable name must not exceed 50 characters.',
            'description.string' => 'Description must be a string.',
            'description.max' => 'Description must not exceed 1000 characters.',
            'is_active.boolean' => 'is_active must be a boolean value.',
            'metadata.array' => 'Metadata must be an array.',
        ];
    }
}
