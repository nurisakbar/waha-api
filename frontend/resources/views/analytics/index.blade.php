@extends('layouts.base')

@section('title', 'Analytics')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Analytics') }}</div>
                <div class="card-body">
                    <form method="GET" action="{{ route('analytics.index') }}" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">{{ __('Date From') }}</label>
                                <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">{{ __('Date To') }}</label>
                                <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">&nbsp;</label>
                                <button type="submit" class="btn btn-primary w-100">{{ __('Filter') }}</button>
                            </div>
                        </div>
                    </form>

                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h5>{{ __('Messages Sent') }}</h5>
                                    <h2>{{ number_format($stats['total_messages_sent']) }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h5>{{ __('Messages Received') }}</h5>
                                    <h2>{{ number_format($stats['total_messages_received']) }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h5>{{ __('Active Sessions') }}</h5>
                                    <h2>{{ number_format($stats['active_sessions']) }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <h5>{{ __('Total Sessions') }}</h5>
                                    <h2>{{ number_format($stats['total_sessions']) }}</h2>
                                </div>
                            </div>
                        </div>
                    </div>

                    <h5 class="mb-3">{{ __('Messages by Type') }}</h5>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>{{ __('Message Type') }}</th>
                                    <th>{{ __('Total') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($messagesByType as $type)
                                    <tr>
                                        <td>{{ ucfirst($type->message_type) }}</td>
                                        <td>{{ number_format($type->total) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

