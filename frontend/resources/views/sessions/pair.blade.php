@extends('layouts.base')

@section('title', 'Pair Session')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('Pair WhatsApp Session') }}: {{ $session->session_name }}</h5>
                </div>

                <div class="card-body text-center">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if ($session->status === 'connected')
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i>
                            <strong>{{ __('Session Connected!') }}</strong>
                            <p class="mb-0 mt-2">{{ __('Your WhatsApp session is now connected and ready to use.') }}</p>
                            <a href="{{ route('sessions.show', $session) }}" class="btn btn-primary mt-3">
                                {{ __('Go to Session') }}
                            </a>
                        </div>
                    @else
                        <div class="mb-4">
                            <h5>{{ __('Scan QR Code with WhatsApp') }}</h5>
                            <p class="text-muted">
                                {{ __('Open WhatsApp on your phone, go to Settings > Linked Devices, and scan this QR code.') }}
                            </p>
                        </div>

                        @if ($session->qr_code)
                            <div class="mb-4">
                                <img src="data:image/png;base64,{{ $session->qr_code }}" 
                                     alt="QR Code" 
                                     class="img-fluid" 
                                     style="max-width: 300px; border: 2px solid #ddd; padding: 10px; background: white;">
                            </div>

                            @if ($session->qr_code_expires_at)
                                <div class="alert alert-warning">
                                    <i class="fas fa-clock"></i>
                                    {{ __('QR Code expires at:') }} {{ $session->qr_code_expires_at->format('H:i:s') }}
                                </div>
                            @endif

                            <div class="mb-3">
                                <button type="button" class="btn btn-primary" onclick="checkStatus()">
                                    <i class="fas fa-sync"></i> {{ __('Check Status') }}
                                </button>
                                <a href="{{ route('sessions.index') }}" class="btn btn-secondary">
                                    {{ __('Back to Sessions') }}
                                </a>
                            </div>

                            <div id="status-message" class="mt-3"></div>
                        @else
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                {{ __('QR code is being generated. Please wait...') }}
                            </div>
                            <div class="mb-3">
                                <button type="button" class="btn btn-primary" onclick="location.reload()">
                                    <i class="fas fa-sync"></i> {{ __('Refresh QR Code') }}
                                </button>
                                <a href="{{ route('sessions.index') }}" class="btn btn-secondary">
                                    {{ __('Back to Sessions') }}
                                </a>
                            </div>
                            <script>
                                // Auto-refresh after 3 seconds if QR code not available
                                setTimeout(function() {
                                    location.reload();
                                }, 3000);
                            </script>
                        @endif

                        <script>
                            function checkStatus(btnElement = null) {
                                const btn = btnElement || document.querySelector('button[onclick="checkStatus()"]');
                                const originalText = btn ? btn.innerHTML : '';
                                
                                if (btn) {
                                    btn.disabled = true;
                                    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> {{ __('Checking...') }}';
                                }

                                fetch('{{ route("sessions.check-status", $session) }}')
                                    .then(response => response.json())
                                    .then(data => {
                                        if (btn) {
                                            btn.disabled = false;
                                            btn.innerHTML = originalText;
                                        }

                                        const statusMsg = document.getElementById('status-message');
                                        if (data.is_connected) {
                                            statusMsg.innerHTML = '<div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ __("Session connected! Redirecting...") }}</div>';
                                            setTimeout(() => {
                                                window.location.href = '{{ route("sessions.show", $session) }}';
                                            }, 2000);
                                        } else {
                                            statusMsg.innerHTML = '<div class="alert alert-info"><i class="fas fa-info-circle"></i> {{ __("Still waiting for pairing...") }}</div>';
                                        }
                                    })
                                    .catch(error => {
                                        if (btn) {
                                            btn.disabled = false;
                                            btn.innerHTML = originalText;
                                        }
                                        const statusMsg = document.getElementById('status-message');
                                        if (statusMsg) {
                                            statusMsg.innerHTML = '<div class="alert alert-danger">{{ __("Error checking status. Please try again.") }}</div>';
                                        }
                                    });
                            }

                            // Auto-check status every 5 seconds (silent check, no button update)
                            setInterval(() => checkStatus(), 5000);
                        </script>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

