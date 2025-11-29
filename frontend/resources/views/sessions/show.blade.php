@extends('layouts.base')

@section('title', 'Session Details')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="row">
                <!-- Session Information Card -->
                <div class="col-md-8 mb-4">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-info-circle"></i> {{ __('Session Information') }}
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <tbody>
                                        <tr>
                                            <th width="35%" class="text-muted">{{ __('Session Name') }}</th>
                                            <td>
                                                <strong>{{ $session->session_name }}</strong>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="text-muted">{{ __('Session ID') }}</th>
                                            <td>
                                                <div class="input-group">
                                                    <input type="text" 
                                                           class="form-control font-monospace" 
                                                           id="sessionIdInput" 
                                                           value="{{ $session->session_id }}" 
                                                           readonly
                                                           style="background-color: #f8f9fa; font-size: 0.9rem;">
                                                    <button class="btn btn-outline-secondary" 
                                                            type="button" 
                                                            id="copySessionIdBtn"
                                                            data-bs-toggle="tooltip" 
                                                            data-bs-placement="top" 
                                                            title="Copy to clipboard">
                                                        <i class="fas fa-copy"></i>
                                                    </button>
                                                </div>
                                                <small class="text-muted mt-1 d-block">
                                                    <i class="fas fa-info-circle"></i> Click the copy button to copy Session ID
                                                </small>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="text-muted">{{ __('Status') }}</th>
                                            <td>
                                                @if ($session->status === 'connected')
                                                    <span class="badge bg-success fs-6">
                                                        <i class="fas fa-check-circle"></i> {{ __('Connected') }}
                                                    </span>
                                                @elseif ($session->status === 'pairing')
                                                    <span class="badge bg-warning fs-6">
                                                        <i class="fas fa-qrcode"></i> {{ __('Pairing') }}
                                                    </span>
                                                @elseif ($session->status === 'disconnected')
                                                    <span class="badge bg-secondary fs-6">
                                                        <i class="fas fa-times-circle"></i> {{ __('Disconnected') }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-danger fs-6">
                                                        <i class="fas fa-exclamation-circle"></i> {{ __('Failed') }}
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="text-muted">{{ __('Last Activity') }}</th>
                                            <td>
                                                @if ($session->last_activity_at)
                                                    <i class="fas fa-clock text-muted"></i>
                                                    {{ $session->last_activity_at->diffForHumans() }}
                                                    <small class="text-muted d-block">
                                                        ({{ $session->last_activity_at->format('Y-m-d H:i:s') }})
                                                    </small>
                                                @else
                                                    <span class="text-muted">{{ __('Never') }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="text-muted">{{ __('Connected At') }}</th>
                                            <td>
                                                @if ($session->connected_at)
                                                    <i class="fas fa-link text-success"></i>
                                                    {{ $session->connected_at->format('Y-m-d H:i:s') }}
                                                    <small class="text-muted d-block">
                                                        ({{ $session->connected_at->diffForHumans() }})
                                                    </small>
                                                @else
                                                    <span class="text-muted">{{ __('Not connected yet') }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="text-muted">{{ __('Created At') }}</th>
                                            <td>
                                                <i class="fas fa-calendar text-muted"></i>
                                                {{ $session->created_at->format('Y-m-d H:i:s') }}
                                                <small class="text-muted d-block">
                                                    ({{ $session->created_at->diffForHumans() }})
                                                </small>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions Card -->
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-cog"></i> {{ __('Actions') }}
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <!-- Back Button -->
                                <a href="{{ route('sessions.index') }}" class="btn btn-outline-secondary w-100">
                                    <i class="fas fa-arrow-left"></i> {{ __('Back to Sessions') }}
                                </a>

                                <!-- Send Message Button (Always visible if connected) -->
                                @if ($session->status === 'connected')
                                    <a href="{{ route('messages.create') }}?session_id={{ $session->id }}" class="btn btn-primary w-100">
                                        <i class="fas fa-paper-plane"></i> {{ __('Send Message') }}
                                    </a>
                                @endif

                                <!-- Pairing/Stop Session Button -->
                                @if ($session->status === 'pairing')
                                    <a href="{{ route('sessions.pair', $session) }}" class="btn btn-warning w-100">
                                        <i class="fas fa-qrcode"></i> {{ __('Continue Pairing') }}
                                    </a>
                                @elseif ($session->status === 'connected')
                                    <form action="{{ route('sessions.stop', $session) }}" method="POST" class="mb-0">
                                        @csrf
                                        <button type="submit" class="btn btn-warning w-100" onclick="return confirm('Are you sure you want to stop this session?')">
                                            <i class="fas fa-stop"></i> {{ __('Stop Session') }}
                                        </button>
                                    </form>
                                @endif

                                <!-- Divider -->
                                <hr class="my-2">

                                <!-- Delete Session Button -->
                                <form action="{{ route('sessions.destroy', $session) }}" method="POST" class="mb-0">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Are you sure you want to delete this session? This action cannot be undone.')">
                                        <i class="fas fa-trash"></i> {{ __('Delete Session') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Copy Session ID functionality
    const copyBtn = document.getElementById('copySessionIdBtn');
    const sessionIdInput = document.getElementById('sessionIdInput');

    if (copyBtn && sessionIdInput) {
        copyBtn.addEventListener('click', async function(e) {
            e.preventDefault();
            
            const originalHTML = copyBtn.innerHTML;
            const originalClasses = copyBtn.className;
            
            try {
                // Try modern clipboard API first
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    await navigator.clipboard.writeText(sessionIdInput.value);
                } else {
                    // Fallback to execCommand for older browsers
                    sessionIdInput.select();
                    sessionIdInput.setSelectionRange(0, 99999); // For mobile devices
                    document.execCommand('copy');
                }
                
                // Update button icon and show feedback
                copyBtn.innerHTML = '<i class="fas fa-check"></i>';
                copyBtn.className = 'btn btn-success';
                
                // Update tooltip
                const tooltip = bootstrap.Tooltip.getInstance(copyBtn);
                if (tooltip) {
                    tooltip.setContent({'.tooltip-inner': 'Copied!'});
                }

                // Reset after 2 seconds
                setTimeout(function() {
                    copyBtn.innerHTML = originalHTML;
                    copyBtn.className = originalClasses;
                    if (tooltip) {
                        tooltip.setContent({'.tooltip-inner': 'Copy to clipboard'});
                    }
                }, 2000);
            } catch (err) {
                console.error('Failed to copy:', err);
                // Show error feedback
                copyBtn.innerHTML = '<i class="fas fa-exclamation-triangle"></i>';
                copyBtn.className = 'btn btn-danger';
                
                setTimeout(function() {
                    copyBtn.innerHTML = originalHTML;
                    copyBtn.className = originalClasses;
                }, 2000);
                
                alert('Failed to copy Session ID. Please select and copy manually.');
            }
        });
    }
});
</script>
@endpush
@endsection

