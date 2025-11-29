@extends('layouts.base')

@section('title', 'Contacts Detail')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('Contacts') }} - {{ $session->session_name }}</span>
                    <a href="{{ route('contacts.index') }}" class="btn btn-sm btn-secondary">{{ __('Back') }}</a>
                </div>
                <div class="card-body">
                    @if(count($contacts) > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>{{ __('Name') }}</th>
                                        <th>{{ __('Phone') }}</th>
                                        <th>{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($contacts as $contact)
                                        <tr>
                                            <td>{{ $contact['name'] ?? $contact['id'] ?? 'N/A' }}</td>
                                            <td>{{ $contact['id'] ?? 'N/A' }}</td>
                                            <td>
                                                <button class="btn btn-sm btn-primary" onclick="sendMessage('{{ $contact['id'] ?? '' }}')">
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
                            <p class="mb-0">{{ __('No contacts found.') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function sendMessage(chatId) {
    window.location.href = '{{ route("messages.create") }}?session_id={{ $session->id }}&to_number=' + chatId.replace('@c.us', '');
}
</script>
@endsection

