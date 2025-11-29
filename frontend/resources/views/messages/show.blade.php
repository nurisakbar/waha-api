@extends('layouts.base')

@section('title', 'Message Details')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('Message Details') }}</span>
                    <a href="{{ route('messages.index') }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-arrow-left"></i> {{ __('Back') }}
                    </a>
                </div>

                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">{{ __('Message Type') }}</th>
                            <td>
                                <span class="badge bg-info">{{ ucfirst($message->message_type) }}</span>
                                @if ($message->isIncoming())
                                    <span class="badge bg-primary">{{ __('Incoming') }}</span>
                                @else
                                    <span class="badge bg-success">{{ __('Outgoing') }}</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>{{ __('From') }}</th>
                            <td>{{ $message->from_number }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('To') }}</th>
                            <td>{{ $message->to_number }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Status') }}</th>
                            <td>
                                @if ($message->status === 'sent')
                                    <span class="badge bg-success">{{ __('Sent') }}</span>
                                @elseif ($message->status === 'delivered')
                                    <span class="badge bg-info">{{ __('Delivered') }}</span>
                                @elseif ($message->status === 'read')
                                    <span class="badge bg-primary">{{ __('Read') }}</span>
                                @elseif ($message->status === 'failed')
                                    <span class="badge bg-danger">{{ __('Failed') }}</span>
                                @else
                                    <span class="badge bg-warning">{{ __('Pending') }}</span>
                                @endif
                            </td>
                        </tr>
                        @if ($message->message_type === 'text')
                            <tr>
                                <th>{{ __('Content') }}</th>
                                <td>{{ $message->content }}</td>
                            </tr>
                        @elseif ($message->message_type === 'image')
                            <tr>
                                <th>{{ __('Image') }}</th>
                                <td>
                                    @if ($message->media_url)
                                        <img src="{{ asset('storage/' . str_replace('/storage/', '', $message->media_url)) }}" 
                                             alt="Image" class="img-fluid" style="max-width: 300px;">
                                    @endif
                                    @if ($message->caption)
                                        <p class="mt-2"><strong>{{ __('Caption') }}:</strong> {{ $message->caption }}</p>
                                    @endif
                                </td>
                            </tr>
                        @else
                            <tr>
                                <th>{{ __('Document') }}</th>
                                <td>
                                    @if ($message->media_url)
                                        <a href="{{ asset('storage/' . str_replace('/storage/', '', $message->media_url)) }}" 
                                           target="_blank" class="btn btn-sm btn-primary">
                                            <i class="fas fa-download"></i> {{ __('Download') }}
                                        </a>
                                    @endif
                                    @if ($message->caption)
                                        <p class="mt-2"><strong>{{ __('Caption') }}:</strong> {{ $message->caption }}</p>
                                    @endif
                                </td>
                            </tr>
                        @endif
                        @if ($message->error_message)
                            <tr>
                                <th>{{ __('Error') }}</th>
                                <td><span class="text-danger">{{ $message->error_message }}</span></td>
                            </tr>
                        @endif
                        <tr>
                            <th>{{ __('Device') }}</th>
                            <td>{{ $message->session->session_name }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Sent At') }}</th>
                            <td>{{ $message->sent_at ? $message->sent_at->format('Y-m-d H:i:s') : __('Not sent yet') }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Delivered At') }}</th>
                            <td>{{ $message->delivered_at ? $message->delivered_at->format('Y-m-d H:i:s') : __('Not delivered yet') }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Read At') }}</th>
                            <td>{{ $message->read_at ? $message->read_at->format('Y-m-d H:i:s') : __('Not read yet') }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Created At') }}</th>
                            <td>{{ $message->created_at->format('Y-m-d H:i:s') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

