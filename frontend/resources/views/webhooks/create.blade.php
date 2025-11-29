@extends('layouts.base')

@section('title', 'Create Webhook')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Create Webhook') }}</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('webhooks.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">{{ __('Webhook Name') }}</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="url" class="form-label">{{ __('Webhook URL') }}</label>
                            <input type="url" class="form-control" id="url" name="url" required placeholder="https://example.com/webhook">
                        </div>
                        <div class="mb-3">
                            <label for="session_id" class="form-label">{{ __('Device') }} <small class="text-muted">({{ __('Optional') }})</small></label>
                            <select class="form-select" id="session_id" name="session_id">
                                <option value="">{{ __('All Devices') }}</option>
                                @foreach($sessions as $session)
                                    <option value="{{ $session->id }}">{{ $session->session_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('Events') }}</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="events[]" value="message" id="event_message">
                                <label class="form-check-label" for="event_message">{{ __('Message Events') }}</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="events[]" value="status" id="event_status">
                                <label class="form-check-label" for="event_status">{{ __('Status Events') }}</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="events[]" value="session" id="event_session">
                                <label class="form-check-label" for="event_session">{{ __('Device Events') }}</label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="secret" class="form-label">{{ __('Secret') }} <small class="text-muted">({{ __('Optional') }})</small></label>
                            <input type="text" class="form-control" id="secret" name="secret" placeholder="{{ __('Webhook secret for verification') }}">
                        </div>
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('webhooks.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                            <button type="submit" class="btn btn-primary">{{ __('Create Webhook') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

