@extends('layouts.base')

@section('title', 'Webhook Details')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('Webhook Details') }}</span>
                    <a href="{{ route('webhooks.index') }}" class="btn btn-sm btn-secondary">{{ __('Back') }}</a>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr><th width="30%">{{ __('Name') }}</th><td>{{ $webhook->name }}</td></tr>
                        <tr><th>{{ __('URL') }}</th><td><code>{{ $webhook->url }}</code></td></tr>
                        <tr><th>{{ __('Events') }}</th><td>{{ implode(', ', $webhook->events) }}</td></tr>
                        <tr><th>{{ __('Status') }}</th><td>
                            @if($webhook->is_active)
                                <span class="badge bg-success">{{ __('Active') }}</span>
                            @else
                                <span class="badge bg-secondary">{{ __('Inactive') }}</span>
                            @endif
                        </td></tr>
                        <tr><th>{{ __('Last Triggered') }}</th><td>{{ $webhook->last_triggered_at ? $webhook->last_triggered_at->diffForHumans() : __('Never') }}</td></tr>
                        <tr><th>{{ __('Failure Count') }}</th><td>{{ $webhook->failure_count }}</td></tr>
                    </table>
                    <form action="{{ route('webhooks.destroy', $webhook) }}" method="POST" class="mt-3">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">{{ __('Delete Webhook') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

