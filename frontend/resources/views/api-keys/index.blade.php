@extends('layouts.base')

@section('title', 'API Keys')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('API Keys') }}</span>
                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#createApiKeyModal">
                        <i class="fas fa-plus"></i> {{ __('Create API Key') }}
                    </button>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                            {{ session('success') }}
                                    @if(session('api_key_plain'))
                                        <div class="mt-2">
                                            <div class="input-group" style="max-width: 600px;">
                                                <input type="text" class="form-control" id="newApiKey" value="{{ session('api_key_plain') }}" readonly>
                                                <div class="input-group-append">
                                                    <button class="btn btn-outline-secondary" type="button" onclick="copyApiKey('newApiKey', this)">
                                                        <i class="fas fa-copy"></i> {{ __('Copy') }}
                                                    </button>
                                                </div>
                                            </div>
                                            <small class="text-muted d-block mt-1">
                                                <i class="fas fa-exclamation-triangle text-warning"></i> 
                                                {{ __('Save this key now. It won\'t be shown again!') }}
                                            </small>
                                            
                                            @if(session('api_key_plain'))
                                            <div class="mt-3 border-top pt-3">
                                                <h6 class="mb-3"><strong><i class="fas fa-code mr-2"></i>{{ __('Format Siap Pakai:') }}</strong></h6>
                                                
                                                @php
                                                    $apiKeyValue = session('api_key_plain');
                                                @endphp
                                                
                                                <!-- cURL Format -->
                                                <div class="mb-3">
                                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                                        <label class="small font-weight-bold mb-0">{{ __('cURL:') }}</label>
                                                        <button class="btn btn-sm btn-outline-secondary" type="button" onclick="copyText(document.getElementById('curlFormat').value, this)" title="{{ __('Copy cURL command') }}">
                                                            <i class="fas fa-copy"></i> {{ __('Copy') }}
                                                        </button>
                                                    </div>
                                                    <textarea class="form-control font-monospace small" id="curlFormat" rows="2" readonly style="font-size: 12px;">curl -X POST "{{ config('app.url', 'http://localhost:8000') }}/api/v1/messages" \
  -H "Content-Type: application/json" \
  -H "X-Api-Key: {{ $apiKeyValue }}" \
  -d '{"session_id":"YOUR_SESSION_ID","message_type":"text","to":"6281234567890","message":"Hello"}'</textarea>
                                                </div>
                                                
                                                <!-- JavaScript/Fetch Format -->
                                                <div class="mb-3">
                                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                                        <label class="small font-weight-bold mb-0">{{ __('JavaScript/Fetch:') }}</label>
                                                        <button class="btn btn-sm btn-outline-secondary" type="button" onclick="copyText(document.getElementById('jsFormat').value, this)" title="{{ __('Copy JavaScript code') }}">
                                                            <i class="fas fa-copy"></i> {{ __('Copy') }}
                                                        </button>
                                                    </div>
                                                    <textarea class="form-control font-monospace small" id="jsFormat" rows="5" readonly style="font-size: 12px;">fetch("{{ config('app.url', 'http://localhost:8000') }}/api/v1/messages", {
  method: "POST",
  headers: {
    "Content-Type": "application/json",
    "X-Api-Key": "{{ $apiKeyValue }}"
  },
  body: JSON.stringify({
    session_id: "YOUR_SESSION_ID",
    message_type: "text",
    to: "6281234567890",
    message: "Hello"
  })
})</textarea>
                                                </div>
                                                
                                                <!-- PHP Format -->
                                                <div class="mb-3">
                                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                                        <label class="small font-weight-bold mb-0">{{ __('PHP:') }}</label>
                                                        <button class="btn btn-sm btn-outline-secondary" type="button" onclick="copyText(document.getElementById('phpFormat').value, this)" title="{{ __('Copy PHP code') }}">
                                                            <i class="fas fa-copy"></i> {{ __('Copy') }}
                                                        </button>
                                                    </div>
                                                    <textarea class="form-control font-monospace small" id="phpFormat" rows="8" readonly style="font-size: 12px;">$url = "{{ config('app.url', 'http://localhost:8000') }}/api/v1/messages";
$data = [
    "session_id" => "YOUR_SESSION_ID",
    "message_type" => "text",
    "to" => "6281234567890",
    "message" => "Hello"
];

$options = [
    "http" => [
        "method" => "POST",
        "header" => "Content-Type: application/json\r\n" .
                    "X-Api-Key: {{ $apiKeyValue }}\r\n",
        "content" => json_encode($data)
    ]
];

$response = file_get_contents($url, false, stream_context_create($options));</textarea>
                                                </div>
                                                
                                                <!-- Header Only Format -->
                                                <div class="mb-0">
                                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                                        <label class="small font-weight-bold mb-0">{{ __('Header Only:') }}</label>
                                                        <button class="btn btn-sm btn-outline-secondary" type="button" onclick="copyText(document.getElementById('headerFormat').value, this)" title="{{ __('Copy header') }}">
                                                            <i class="fas fa-copy"></i> {{ __('Copy') }}
                                                        </button>
                                                    </div>
                                                    <input type="text" class="form-control font-monospace small" id="headerFormat" value="X-Api-Key: {{ $apiKeyValue }}" readonly style="font-size: 12px;">
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <!-- Format Siap Pakai Section (Always Visible) -->
                    @php
                        $userApiKeys = session('user_api_keys', []);
                        $latestApiKey = null;
                        
                        // Get the latest API key from session
                        if (!empty($userApiKeys)) {
                            $latestApiKeyId = array_key_last($userApiKeys);
                            $latestApiKey = $userApiKeys[$latestApiKeyId] ?? null;
                        }
                        
                        // Also check flash for backward compatibility
                        if (!$latestApiKey && session('api_key_plain')) {
                            $latestApiKey = session('api_key_plain');
                        }
                    @endphp
                    
                    @if($latestApiKey)
                    <div class="card mb-4 border-success">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0"><i class="fas fa-key mr-2"></i>{{ __('API Key Siap Pakai') }}</h6>
                        </div>
                        <div class="card-body">
                            <p class="mb-3">
                                <i class="fas fa-info-circle mr-2"></i>
                                {{ __('Copy API key di bawah ini dan paste ke Postman atau aplikasi Anda:') }}
                            </p>
                            <div class="input-group">
                                <input type="text" class="form-control font-monospace" id="readyToUseApiKey" value="{{ $latestApiKey }}" readonly style="font-size: 14px;">
                                <div class="input-group-append">
                                    <button class="btn btn-success" type="button" onclick="copyApiKey('readyToUseApiKey', this)" style="min-width: 100px;">
                                        <i class="fas fa-copy mr-2"></i>{{ __('Copy') }}
                                    </button>
                                </div>
                            </div>
                            <small class="text-muted d-block mt-2">
                                <i class="fas fa-lightbulb mr-1"></i>
                                {{ __('Gunakan di Postman:') }} <code>X-Api-Key: {{ $latestApiKey }}</code>
                            </small>
                        </div>
                    </div>
                    @endif

                    @if ($apiKeys->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>{{ __('Name') }}</th>
                                        <th>{{ __('API Key') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Last Used') }}</th>
                                        <th>{{ __('Created') }}</th>
                                        <th>{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($apiKeys as $apiKey)
                                        <tr>
                                            <td>{{ $apiKey->name }}</td>
                                            <td>
                                                @php
                                                    $userApiKeys = session('user_api_keys', []);
                                                    $plainKey = $userApiKeys[$apiKey->id] ?? null;
                                                    
                                                    // Also check flash for backward compatibility
                                                    if (!$plainKey && session('api_key_plain') && $apiKey->id === session('api_key_id')) {
                                                        $plainKey = session('api_key_plain');
                                                    }
                                                @endphp
                                                
                                                @if($plainKey)
                                                    {{-- Show full key for newly created key --}}
                                                    <div class="d-flex align-items-center">
                                                        <code class="mr-2" id="api-key-{{ $apiKey->id }}" style="font-size: 11px; word-break: break-all;">{{ $plainKey }}</code>
                                                        <button class="btn btn-sm btn-outline-secondary ml-2" type="button" onclick="copyApiKeyFull('{{ $plainKey }}', this)" title="{{ __('Copy API Key') }}">
                                                            <i class="fas fa-copy"></i>
                                                        </button>
                                                    </div>
                                                    <small class="text-warning d-block mt-1">
                                                        <i class="fas fa-exclamation-triangle"></i> 
                                                        {{ __('Save this key now. It will be hidden after you logout or session expires.') }}
                                                    </small>
                                                @else
                                                    {{-- Show masked key for existing keys --}}
                                                    <div class="d-flex align-items-center">
                                                        <code class="mr-2 text-muted">{{ __('Key hidden for security') }}</code>
                                                        <small class="text-muted ml-2">
                                                            <i class="fas fa-info-circle" title="{{ __('API key can only be viewed once when created') }}"></i>
                                                        </small>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                @if($apiKey->is_active)
                                                    <span class="badge badge-success">{{ __('Active') }}</span>
                                                @else
                                                    <span class="badge badge-secondary">{{ __('Inactive') }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $apiKey->last_used_at ? $apiKey->last_used_at->diffForHumans() : __('Never') }}</td>
                                            <td>{{ $apiKey->created_at->format('Y-m-d') }}</td>
                                            <td>
                                                <form action="{{ route('api-keys.destroy', $apiKey) }}" method="POST" class="d-inline">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                                        <i class="fas fa-trash"></i> {{ __('Delete') }}
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        {{ $apiKeys->links() }}
                    @else
                        <div class="alert alert-info">
                            <p class="mb-0">{{ __('No API keys found. Create one to start using the API.') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create API Key Modal -->
<div class="modal fade" id="createApiKeyModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('api-keys.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Create API Key') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @if ($errors->has('name'))
                        <div class="alert alert-danger">
                            {{ $errors->first('name') }}
                        </div>
                    @endif
                    <div class="mb-3">
                        <label for="name" class="form-label">{{ __('Key Name') }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" required placeholder="{{ __('e.g., Production API Key') }}" value="{{ old('name') }}">
                        @error('name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Create') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    const modal = $('#createApiKeyModal');
    const form = modal.find('form');
    
    // Reset form when modal is closed
    modal.on('hidden.bs.modal', function () {
        form[0].reset();
        // Clear any error messages
        modal.find('.alert-danger, .invalid-feedback').remove();
        // Remove error classes
        modal.find('.is-invalid').removeClass('is-invalid');
    });
    
    // If there are errors, show modal automatically
    @if($errors->has('name'))
        modal.modal('show');
    @endif
});

/**
 * Copy full API key to clipboard
 */
function copyApiKeyFull(key, button) {
    // Use modern Clipboard API if available
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(key).then(function() {
            // Show feedback
            const originalHtml = button.innerHTML;
            button.innerHTML = '<i class="fas fa-check text-success"></i>';
            button.classList.add('btn-success');
            button.classList.remove('btn-outline-secondary');
            
            // Reset after 2 seconds
            setTimeout(function() {
                button.innerHTML = originalHtml;
                button.classList.remove('btn-success');
                button.classList.add('btn-outline-secondary');
            }, 2000);
            
            showToast('{{ __('API Key copied to clipboard!') }}', 'success');
        }).catch(function(err) {
            console.error('Failed to copy:', err);
            // Fallback to execCommand
            copyApiKeyFallback(key, button);
        });
    } else {
        // Fallback for older browsers
        copyApiKeyFallback(key, button);
    }
}

/**
 * Fallback copy method using execCommand
 */
function copyApiKeyFallback(key, button) {
    // Create temporary input element
    const tempInput = document.createElement('input');
    tempInput.value = key;
    tempInput.style.position = 'fixed';
    tempInput.style.opacity = '0';
    document.body.appendChild(tempInput);
    tempInput.select();
    tempInput.setSelectionRange(0, 99999); // For mobile devices
    
    try {
        // Copy to clipboard
        document.execCommand('copy');
        
        // Show feedback
        const originalHtml = button.innerHTML;
        button.innerHTML = '<i class="fas fa-check text-success"></i>';
        button.classList.add('btn-success');
        button.classList.remove('btn-outline-secondary');
        
        // Reset after 2 seconds
        setTimeout(function() {
            button.innerHTML = originalHtml;
            button.classList.remove('btn-success');
            button.classList.add('btn-outline-secondary');
        }, 2000);
        
        // Show toast notification
        showToast('{{ __('API Key copied to clipboard!') }}', 'success');
    } catch (err) {
        console.error('Failed to copy:', err);
        showToast('{{ __('Failed to copy. Please try again.') }}', 'error');
    }
    
    // Remove temporary input
    document.body.removeChild(tempInput);
}

/**
 * Copy text to clipboard (general purpose)
 */
function copyText(text, button) {
    // Use modern Clipboard API if available
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(text).then(function() {
            // Show feedback
            const originalHtml = button.innerHTML;
            button.innerHTML = '<i class="fas fa-check text-success"></i>';
            button.classList.add('btn-success');
            button.classList.remove('btn-outline-secondary');
            
            // Reset after 2 seconds
            setTimeout(function() {
                button.innerHTML = originalHtml;
                button.classList.remove('btn-success');
                button.classList.add('btn-outline-secondary');
            }, 2000);
            
            showToast('{{ __('Copied to clipboard!') }}', 'success');
        }).catch(function(err) {
            console.error('Failed to copy:', err);
            // Fallback to execCommand
            copyTextFallback(text, button);
        });
    } else {
        // Fallback for older browsers
        copyTextFallback(text, button);
    }
}

/**
 * Fallback copy method using execCommand
 */
function copyTextFallback(text, button) {
    // Create temporary input element
    const tempInput = document.createElement('input');
    tempInput.value = text;
    tempInput.style.position = 'fixed';
    tempInput.style.opacity = '0';
    document.body.appendChild(tempInput);
    tempInput.select();
    tempInput.setSelectionRange(0, 99999); // For mobile devices
    
    try {
        // Copy to clipboard
        document.execCommand('copy');
        
        // Show feedback
        const originalHtml = button.innerHTML;
        button.innerHTML = '<i class="fas fa-check text-success"></i>';
        button.classList.add('btn-success');
        button.classList.remove('btn-outline-secondary');
        
        // Reset after 2 seconds
        setTimeout(function() {
            button.innerHTML = originalHtml;
            button.classList.remove('btn-success');
            button.classList.add('btn-outline-secondary');
        }, 2000);
        
        // Show toast notification
        showToast('{{ __('Copied to clipboard!') }}', 'success');
    } catch (err) {
        console.error('Failed to copy:', err);
        showToast('{{ __('Failed to copy. Please try again.') }}', 'error');
    }
    
    // Remove temporary input
    document.body.removeChild(tempInput);
}

/**
 * Copy API key from input field
 */
function copyApiKey(inputId, button) {
    const input = document.getElementById(inputId);
    if (input) {
        copyText(input.value, button);
            }, 2000);
            
            showToast('{{ __('API Key copied to clipboard!') }}', 'success');
        } catch (err) {
            console.error('Failed to copy:', err);
            showToast('{{ __('Failed to copy. Please try again.') }}', 'error');
        }
    }
}

/**
 * Show toast notification
 */
function showToast(message, type) {
    // Remove existing toast if any
    $('.toast-notification').remove();
    
    const bgColor = type === 'success' ? 'bg-success' : 'bg-danger';
    const toast = $('<div>')
        .addClass('toast-notification ' + bgColor)
        .css({
            'position': 'fixed',
            'top': '20px',
            'right': '20px',
            'padding': '12px 20px',
            'color': 'white',
            'border-radius': '4px',
            'z-index': '9999',
            'box-shadow': '0 4px 6px rgba(0,0,0,0.1)',
            'animation': 'fadeIn 0.3s'
        })
        .html('<i class="fas fa-' + (type === 'success' ? 'check-circle' : 'exclamation-circle') + ' mr-2"></i>' + message);
    
    $('body').append(toast);
    
    // Auto remove after 3 seconds
    setTimeout(function() {
        toast.fadeOut(300, function() {
            $(this).remove();
        });
    }, 3000);
}

// Add fadeIn animation
if (!$('#toast-styles').length) {
    $('head').append('<style id="toast-styles">@keyframes fadeIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }</style>');
}
</script>
@endsection

