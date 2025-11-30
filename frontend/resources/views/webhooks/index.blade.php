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
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Webhook Explanation -->
                    <div class="alert alert-info mb-4">
                        <h5 class="alert-heading">
                            <i class="fas fa-info-circle"></i> {{ __('Apa itu Webhook?') }}
                        </h5>
                        <p class="mb-2">
                            {{ __('Webhook adalah mekanisme untuk menerima notifikasi real-time dari sistem ketika terjadi event tertentu. Dengan webhook, aplikasi Anda dapat secara otomatis menerima informasi tentang:') }}
                        </p>
                        <ul class="mb-2">
                            <li><strong>{{ __('Message Events') }}</strong> – {{ __('Notifikasi ketika ada pesan masuk atau status pesan berubah (delivered, read, dll)') }}</li>
                            <li><strong>{{ __('Status Events') }}</strong> – {{ __('Notifikasi tentang perubahan status pesan yang dikirim') }}</li>
                            <li><strong>{{ __('Device Events') }}</strong> – {{ __('Notifikasi tentang perubahan status device/session (connected, disconnected, dll)') }}</li>
                        </ul>
                        <p class="mb-0">
                            <strong>{{ __('Cara Kerja:') }}</strong> {{ __('Ketika event terjadi, sistem akan mengirim HTTP POST request ke URL webhook yang Anda konfigurasi. Aplikasi Anda dapat memproses data tersebut untuk melakukan tindakan otomatis seperti menyimpan pesan, mengirim notifikasi, atau memicu workflow lainnya.') }}
                        </p>
                    </div>
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

