@extends('layouts.base')

@section('title', 'Groups Detail')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('Groups') }} - {{ $session->session_name }}</span>
                    <a href="{{ route('groups.index') }}" class="btn btn-sm btn-secondary">{{ __('Back') }}</a>
                </div>
                <div class="card-body">
                    @if(count($groups) > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>{{ __('Group Name') }}</th>
                                        <th>{{ __('Group ID') }}</th>
                                        <th>{{ __('Participants') }}</th>
                                        <th>{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($groups as $group)
                                        <tr>
                                            <td>{{ $group['name'] ?? $group['id'] ?? 'N/A' }}</td>
                                            <td><code>{{ $group['id'] ?? 'N/A' }}</code></td>
                                            <td>{{ isset($group['participants']) ? count($group['participants']) : 'N/A' }}</td>
                                            <td>
                                                <button class="btn btn-sm btn-primary" onclick="sendMessage('{{ $group['id'] ?? '' }}')">
                                                    {{ __('Send Message') }}
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <p class="mb-0">{{ __('No groups found.') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function sendMessage(chatId) {
    window.location.href = '{{ route("messages.create") }}?session_id={{ $session->id }}&to_number=' + chatId.replace('@g.us', '');
}
</script>
@endsection

