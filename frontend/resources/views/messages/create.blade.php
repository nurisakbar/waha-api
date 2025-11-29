@extends('layouts.base')

@section('title', 'Send Message')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Send Message') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('messages.store') }}" enctype="multipart/form-data" id="message-form" novalidate>
                        @csrf

                        <div class="mb-3">
                            <label for="session_id" class="form-label">{{ __('Select Session') }}</label>
                            <select class="form-select @error('session_id') is-invalid @enderror" 
                                    id="session_id" name="session_id" required>
                                <option value="">{{ __('Choose a session...') }}</option>
                                @foreach($sessions as $session)
                                    <option value="{{ $session->id }}" 
                                            {{ ($selectedSession && $selectedSession->id == $session->id) || request('session_id') == $session->id ? 'selected' : '' }}>
                                        {{ $session->session_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('session_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="to_number" class="form-label">{{ __('Phone Number') }}</label>
                            <input type="text" class="form-control @error('to_number') is-invalid @enderror" 
                                   id="to_number" name="to_number" value="{{ old('to_number', request('to_number')) }}" 
                                   required placeholder="081234567890 atau 6281234567890">
                            @error('to_number')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            <small class="form-text text-muted">
                                {{ __('Masukkan nomor HP tujuan. Format: 08xxxxxxxxxx atau 628xxxxxxxxxx (tanpa spasi atau tanda +)') }}
                                <br>
                                <strong>Contoh:</strong> 081234567890 atau 6281234567890
                            </small>
                        </div>

                        <div class="mb-3">
                            <label for="message_type" class="form-label">{{ __('Message Type') }}</label>
                            <select class="form-select @error('message_type') is-invalid @enderror" 
                                    id="message_type" name="message_type" required onchange="toggleMessageFields()">
                                <option value="text" {{ old('message_type') == 'text' ? 'selected' : '' }}>{{ __('Text Message') }}</option>
                                <option value="image" {{ old('message_type') == 'image' ? 'selected' : '' }}>{{ __('Image') }}</option>
                                <option value="document" {{ old('message_type') == 'document' ? 'selected' : '' }}>{{ __('Document') }}</option>
                            </select>
                            @error('message_type')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3" id="text-content-field">
                            <label for="content" class="form-label">{{ __('Message Content') }} <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('content') is-invalid @enderror" 
                                      id="content" name="content" rows="5" 
                                      placeholder="{{ __('Type your message here...') }}">{{ old('content') }}</textarea>
                            @error('content')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3" id="media-field" style="display: none;">
                            <label for="media" class="form-label">{{ __('Image File') }}</label>
                            <input type="file" class="form-control @error('media') is-invalid @enderror" 
                                   id="media" name="media" accept="image/*">
                            @error('media')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            <small class="form-text text-muted">
                                {{ __('Max file size: 10MB') }}
                            </small>
                        </div>

                        <div class="mb-3" id="document-file-field" style="display: none;">
                            <label for="document_file" class="form-label">{{ __('Document File') }}</label>
                            <input type="file" class="form-control @error('document_file') is-invalid @enderror" 
                                   id="document_file" name="document_file" 
                                   accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.zip,.rar">
                            @error('document_file')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            <small class="form-text text-muted">
                                {{ __('Max file size: 10MB. Supported: PDF, DOC, XLS, PPT, TXT, ZIP, etc.') }}
                            </small>
                        </div>

                        <div class="mb-3" id="document-url-field" style="display: none;">
                            <label for="document_url" class="form-label">{{ __('Or Document URL') }} <small class="text-muted">({{ __('Optional if uploading file') }})</small></label>
                            <input type="url" class="form-control @error('document_url') is-invalid @enderror"
                                   id="document_url" name="document_url"
                                   placeholder="https://example.com/file.pdf"
                                   value="{{ old('document_url') }}">
                            @error('document_url')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                            <small class="form-text text-muted">
                                {{ __('Document will be fetched from this URL and sent via WhatsApp.') }}
                            </small>
                        </div>

                        <div class="mb-3" id="caption-field" style="display: none;">
                            <label for="caption" class="form-label">{{ __('Caption') }} <small class="text-muted">({{ __('Optional') }})</small></label>
                            <textarea class="form-control @error('caption') is-invalid @enderror" 
                                      id="caption" name="caption" rows="2" 
                                      placeholder="{{ __('Add a caption...') }}">{{ old('caption') }}</textarea>
                            @error('caption')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('messages.index') }}" class="btn btn-secondary">
                                {{ __('Cancel') }}
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> {{ __('Send Message') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleMessageFields() {
    const messageType = document.getElementById('message_type').value;
    const textField = document.getElementById('text-content-field');
    const mediaField = document.getElementById('media-field');
    const documentFileField = document.getElementById('document-file-field');
    const documentUrlField = document.getElementById('document-url-field');
    const captionField = document.getElementById('caption-field');
    const contentInput = document.getElementById('content');
    const mediaInput = document.getElementById('media');
    const documentFileInput = document.getElementById('document_file');
    const documentUrlInput = document.getElementById('document_url');

    if (messageType === 'text') {
        textField.style.display = 'block';
        mediaField.style.display = 'none';
        documentFileField.style.display = 'none';
        documentUrlField.style.display = 'none';
        captionField.style.display = 'none';
        if (contentInput) {
            contentInput.required = true;
        }
        if (mediaInput) {
            mediaInput.required = false;
        }
        if (documentFileInput) {
            documentFileInput.required = false;
        }
        if (documentUrlInput) {
            documentUrlInput.required = false;
        }
    } else if (messageType === 'image') {
        textField.style.display = 'none';
        mediaField.style.display = 'block';
        documentFileField.style.display = 'none';
        documentUrlField.style.display = 'none';
        captionField.style.display = 'block';
        if (contentInput) {
            contentInput.required = false;
        }
        if (mediaInput) {
            mediaInput.required = true;
        }
        if (documentFileInput) {
            documentFileInput.required = false;
        }
        if (documentUrlInput) {
            documentUrlInput.required = false;
        }
    } else if (messageType === 'document') {
        textField.style.display = 'none';
        mediaField.style.display = 'none';
        documentFileField.style.display = 'block';
        documentUrlField.style.display = 'block';
        captionField.style.display = 'block';
        if (contentInput) {
            contentInput.required = false;
        }
        if (mediaInput) {
            mediaInput.required = false;
        }
        if (documentFileInput) {
            documentFileInput.required = false; // Not required if URL is provided
        }
        if (documentUrlInput) {
            documentUrlInput.required = false; // Not required if file is uploaded
        }
    }
}

// Initialize on page load
toggleMessageFields();
</script>
@endsection

