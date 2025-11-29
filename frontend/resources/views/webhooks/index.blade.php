@extends('layouts.base')

@section('title', 'Webhooks')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('Webhooks') }}</span>
                    <a href="{{ route('webhooks.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> {{ __('Create Webhook') }}
                    </a>
                </div>
                <div class="card-body">
                    @if ($webhooks->count() > 0)
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('URL') }}</th>
                                    <th>{{ __('Events') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($webhooks as $webhook)
                                    <tr>
                                        <td>{{ $webhook->name }}</td>
                                        <td><code>{{ Str::limit($webhook->url, 50) }}</code></td>
                                        <td>{{ implode(', ', $webhook->events) }}</td>
                                        <td>
                                            @if($webhook->is_active)
                                                <span class="badge bg-success">{{ __('Active') }}</span>
                                            @else
                                                <span class="badge bg-secondary">{{ __('Inactive') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('webhooks.show', $webhook) }}" class="btn btn-sm btn-info">{{ __('View') }}</a>
                                            <form action="{{ route('webhooks.destroy', $webhook) }}" method="POST" class="d-inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">{{ __('Delete') }}</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{ $webhooks->links() }}
                    @else
                        <p>{{ __('No webhooks found.') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

