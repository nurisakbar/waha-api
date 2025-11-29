@extends('layouts.base')

@section('title', 'Groups')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Groups') }}</div>
                <div class="card-body">
                    @if($sessions->count() > 0)
                        <div class="row">
                            @foreach($sessions as $session)
                                <div class="col-md-4 mb-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5>{{ $session->session_name }}</h5>
                                            <p class="text-muted">{{ __('View groups for this device') }}</p>
                                            <a href="{{ route('groups.show', $session) }}" class="btn btn-primary">
                                                {{ __('View Groups') }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-info">
                            <p class="mb-0">{{ __('No connected devices found. Please connect a device first.') }}</p>
                            <a href="{{ route('sessions.index') }}" class="btn btn-primary mt-2">{{ __('Go to Devices') }}</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

