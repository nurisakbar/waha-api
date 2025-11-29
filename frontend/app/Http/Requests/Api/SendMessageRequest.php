<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class SendMessageRequest extends FormRequest
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
     * Configure the validator instance.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $messageType = $this->input('message_type');
            
            if ($messageType === 'button') {
                $buttons = $this->input('buttons', []);
                
                foreach ($buttons as $index => $button) {
                    $buttonType = $button['type'] ?? null;
                    
                    if ($buttonType === 'call' && empty($button['phoneNumber'] ?? null)) {
                        $validator->errors()->add("buttons.{$index}.phoneNumber", "Phone number is required for call buttons.");
                    }
                    
                    if ($buttonType === 'copy' && empty($button['copyCode'] ?? null)) {
                        $validator->errors()->add("buttons.{$index}.copyCode", "Copy code is required for copy buttons.");
                    }
                    
                    if ($buttonType === 'url' && empty($button['url'] ?? null)) {
                        $validator->errors()->add("buttons.{$index}.url", "URL is required for url buttons.");
                    }
                }
            }
        });
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        // session_id is optional if provided in URL
        $rules = [
            'session_id' => 'nullable|string|max:255',
            'message_type' => 'required_without:template_id|in:text,image,video,document,poll,button,list',
            'to' => 'required|string|max:255', // Increased for group IDs
            'chat_type' => 'nullable|in:personal,group', // Type of chat: personal or group
            'template_id' => 'nullable|string|exists:templates,id',
            'variables' => 'nullable|array',
            'variables.*' => 'string|max:1000',
        ];

        // If template_id is provided, skip message type specific validations
        if ($this->input('template_id')) {
            return $rules;
        }

        // Add validation rules based on message type
        $messageType = $this->input('message_type');

        if ($messageType === 'text') {
            // Accept both 'text' and 'message' fields for text messages
            // At least one must be provided
            $rules['text'] = 'required_without:message|string|max:4096';
            $rules['message'] = 'required_without:text|string|max:4096';
        } elseif ($messageType === 'image') {
            $rules['image'] = 'required|url|max:500';
            $rules['caption'] = 'nullable|string|max:1024';
        } elseif ($messageType === 'video') {
            $rules['video'] = 'required|url|max:500';
            $rules['caption'] = 'nullable|string|max:1024';
            $rules['as_note'] = 'nullable|boolean';
            $rules['convert'] = 'nullable|boolean';
        } elseif ($messageType === 'document') {
            $rules['document'] = 'required|url|max:500';
            $rules['filename'] = 'nullable|string|max:255';
            $rules['caption'] = 'nullable|string|max:1024';
        } elseif ($messageType === 'poll') {
            $rules['poll_name'] = 'required|string|max:255';
            $rules['poll_options'] = 'required|array|min:2|max:12';
            $rules['poll_options.*'] = 'required|string|max:100';
            $rules['multiple_answers'] = 'nullable|boolean';
            $rules['fallback_to_text'] = 'nullable|boolean';
        } elseif ($messageType === 'button') {
            $rules['body'] = 'required|string|max:1024';
            $rules['buttons'] = 'required|array|min:1|max:3';
            $rules['buttons.*.type'] = 'required|in:reply,call,copy,url';
            $rules['buttons.*.text'] = 'required|string|max:20';
            // Optional fields based on button type - validate if present
            $rules['buttons.*.phoneNumber'] = 'nullable|string|max:20';
            $rules['buttons.*.copyCode'] = 'nullable|string|max:20';
            $rules['buttons.*.url'] = 'nullable|url|max:500';
            $rules['header'] = 'nullable|string|max:60';
            $rules['footer'] = 'nullable|string|max:60';
            $rules['header_image'] = 'nullable|array';
            $rules['header_image.mimetype'] = 'required_with:header_image|string';
            $rules['header_image.filename'] = 'required_with:header_image|string|max:255';
            $rules['header_image.url'] = 'required_with:header_image|url|max:500';
            $rules['fallback_to_text'] = 'nullable|boolean';
        } elseif ($messageType === 'list') {
            $rules['message'] = 'required|array';
            $rules['message.title'] = 'required|string|max:60';
            $rules['message.description'] = 'nullable|string|max:72';
            $rules['message.footer'] = 'nullable|string|max:60';
            $rules['message.button'] = 'required|string|max:20';
            $rules['message.sections'] = 'required|array|min:1';
            $rules['message.sections.*.title'] = 'required|string|max:24';
            $rules['message.sections.*.rows'] = 'required|array|min:1|max:10';
            $rules['message.sections.*.rows.*.title'] = 'required|string|max:24';
            $rules['message.sections.*.rows.*.rowId'] = 'required|string|max:200';
            $rules['message.sections.*.rows.*.description'] = 'nullable|string|max:72';
            $rules['reply_to'] = 'nullable|string|max:255';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'session_id.string' => 'Session ID must be a string.',
            'message_type.required' => 'Message type is required.',
            'message_type.in' => 'Message type must be one of: text, image, video, document, poll, button, list.',
            'to.required' => 'Recipient number or group ID is required.',
            'to.string' => 'Recipient number or group ID must be a string.',
            'to.max' => 'Recipient number or group ID must not exceed 255 characters.',
            'chat_type.in' => 'Chat type must be either "personal" or "group".',
            'text.required_without' => 'Text content is required for text messages (or use "message" field).',
            'text.string' => 'Text content must be a string.',
            'text.max' => 'Text content must not exceed 4096 characters.',
            'message.required_without' => 'Message content is required for text messages (or use "text" field).',
            'message.string' => 'Message content must be a string.',
            'message.max' => 'Message content must not exceed 4096 characters.',
            'image.required' => 'Image URL is required for image messages.',
            'image.url' => 'Image must be a valid URL.',
            'image.max' => 'Image URL must not exceed 500 characters.',
            'video.required' => 'Video URL is required for video messages.',
            'video.url' => 'Video must be a valid URL.',
            'video.max' => 'Video URL must not exceed 500 characters.',
            'as_note.boolean' => 'as_note must be a boolean value.',
            'convert.boolean' => 'convert must be a boolean value.',
            'document.required' => 'Document URL is required for document messages.',
            'document.url' => 'Document must be a valid URL.',
            'document.max' => 'Document URL must not exceed 500 characters.',
            'filename.string' => 'Filename must be a string.',
            'filename.max' => 'Filename must not exceed 255 characters.',
            'caption.string' => 'Caption must be a string.',
            'caption.max' => 'Caption must not exceed 1024 characters.',
            'poll_name.required' => 'Poll name is required for poll messages.',
            'poll_name.string' => 'Poll name must be a string.',
            'poll_name.max' => 'Poll name must not exceed 255 characters.',
            'poll_options.required' => 'Poll options are required for poll messages.',
            'poll_options.array' => 'Poll options must be an array.',
            'poll_options.min' => 'Poll must have at least 2 options.',
            'poll_options.max' => 'Poll must have at most 12 options.',
            'poll_options.*.required' => 'Each poll option is required.',
            'poll_options.*.string' => 'Each poll option must be a string.',
            'poll_options.*.max' => 'Each poll option must not exceed 100 characters.',
            'multiple_answers.boolean' => 'multiple_answers must be a boolean value.',
            'body.required' => 'Body is required for button messages.',
            'body.string' => 'Body must be a string.',
            'body.max' => 'Body must not exceed 1024 characters.',
            'buttons.required' => 'Buttons are required for button messages.',
            'buttons.array' => 'Buttons must be an array.',
            'buttons.min' => 'Button message must have at least 1 button.',
            'buttons.max' => 'Button message must have at most 3 buttons.',
            'buttons.*.type.required' => 'Button type is required.',
            'buttons.*.type.in' => 'Button type must be one of: reply, call, copy, url.',
            'buttons.*.text.required' => 'Button text is required.',
            'buttons.*.text.string' => 'Button text must be a string.',
            'buttons.*.text.max' => 'Button text must not exceed 20 characters.',
            'buttons.*.phoneNumber.required_if' => 'Phone number is required for call buttons.',
            'buttons.*.phoneNumber.string' => 'Phone number must be a string.',
            'buttons.*.phoneNumber.max' => 'Phone number must not exceed 20 characters.',
            'buttons.*.copyCode.required_if' => 'Copy code is required for copy buttons.',
            'buttons.*.copyCode.string' => 'Copy code must be a string.',
            'buttons.*.copyCode.max' => 'Copy code must not exceed 20 characters.',
            'buttons.*.url.required_if' => 'URL is required for url buttons.',
            'buttons.*.url.url' => 'URL must be a valid URL.',
            'buttons.*.url.max' => 'URL must not exceed 500 characters.',
            'header.string' => 'Header must be a string.',
            'header.max' => 'Header must not exceed 60 characters.',
            'footer.string' => 'Footer must be a string.',
            'footer.max' => 'Footer must not exceed 60 characters.',
            'header_image.array' => 'Header image must be an array.',
            'header_image.mimetype.required_with' => 'Header image mimetype is required when header image is provided.',
            'header_image.filename.required_with' => 'Header image filename is required when header image is provided.',
            'header_image.url.required_with' => 'Header image URL is required when header image is provided.',
            'header_image.url.url' => 'Header image must be a valid URL.',
        ];
    }
}

