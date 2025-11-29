@extends('layouts.base')

@section('title', 'Create Session')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Create New WhatsApp Session') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('sessions.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="session_name" class="form-label">{{ __('Session Name') }}</label>
                            <input type="text" class="form-control @error('session_name') is-invalid @enderror" 
                                   id="session_name" name="session_name" value="{{ old('session_name') }}" 
                                   required autofocus placeholder="{{ __('e.g., My Business WhatsApp') }}">
                            
                            @error('session_name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            
                            <small class="form-text text-muted">
                                {{ __('Give your session a descriptive name to easily identify it later.') }}
                            </small>
                        </div>

                        <div class="mb-3">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                <strong>{{ __('Note:') }}</strong>
                                {{ __('After creating the session, you will need to scan a QR code with your WhatsApp to pair the session.') }}
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('sessions.index') }}" class="btn btn-secondary">
                                {{ __('Cancel') }}
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-plus"></i> {{ __('Create Session') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

