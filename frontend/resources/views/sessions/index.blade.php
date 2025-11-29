@extends('layouts.base')

@section('title', 'WhatsApp Sessions')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('WhatsApp Sessions') }}</span>
                    <a href="{{ route('sessions.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> {{ __('Create New Session') }}
                    </a>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if ($sessions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>{{ __('Session Name') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Last Activity') }}</th>
                                        <th>{{ __('Created At') }}</th>
                                        <th>{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($sessions as $session)
                                        <tr>
                                            <td>{{ $session->session_name }}</td>
                                            <td>
                                                @if ($session->status === 'connected')
                                                    <span class="badge bg-success">{{ __('Connected') }}</span>
                                                @elseif ($session->status === 'pairing')
                                                    <span class="badge bg-warning">{{ __('Pairing') }}</span>
                                                @elseif ($session->status === 'disconnected')
                                                    <span class="badge bg-secondary">{{ __('Disconnected') }}</span>
                                                @else
                                                    <span class="badge bg-danger">{{ __('Failed') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $session->last_activity_at ? $session->last_activity_at->diffForHumans() : __('Never') }}
                                            </td>
                                            <td>{{ $session->created_at->format('Y-m-d H:i') }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('sessions.show', $session) }}" class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i> {{ __('View') }}
                                                    </a>
                                                    @if ($session->status === 'pairing')
                                                        <a href="{{ route('sessions.pair', $session) }}" class="btn btn-sm btn-warning">
                                                            <i class="fas fa-qrcode"></i> {{ __('Pair') }}
                                                        </a>
                                                    @endif
                                                    @if ($session->status === 'connected')
                                                        <form action="{{ route('sessions.stop', $session) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-warning" onclick="return confirm('Are you sure?')">
                                                                <i class="fas fa-stop"></i> {{ __('Stop') }}
                                                            </button>
                                                        </form>
                                                    @endif
                                                    <form action="{{ route('sessions.destroy', $session) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this session?')">
                                                            <i class="fas fa-trash"></i> {{ __('Delete') }}
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center">
                            {{ $sessions->links() }}
                        </div>
                    @else
                        <div class="alert alert-info">
                            <p class="mb-0">{{ __('No sessions found.') }}</p>
                            <a href="{{ route('sessions.create') }}" class="btn btn-primary mt-2">
                                {{ __('Create Your First Session') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

