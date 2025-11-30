@extends('layouts.base')

@section('title', 'User Details')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0 text-gray-800">
                    <i class="fas fa-user"></i> User Details
                </h1>
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Users List
                </a>
            </div>
        </div>
    </div>

    <!-- User Information -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">User Information</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-3 font-weight-bold">Name:</div>
                        <div class="col-md-9">
                            <div class="d-flex align-items-center">
                                @if($user->avatar)
                                    <img src="{{ $user->avatar }}" alt="{{ $user->name }}" 
                                         class="rounded-circle mr-2" width="48" height="48">
                                @else
                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mr-2" 
                                         style="width: 48px; height: 48px; font-size: 20px;">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                @endif
                                <span>{{ $user->name }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-3 font-weight-bold">Email:</div>
                        <div class="col-md-9">{{ $user->email }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-3 font-weight-bold">Phone:</div>
                        <div class="col-md-9">{{ $user->phone ?? '-' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-3 font-weight-bold">Role:</div>
                        <div class="col-md-9">
                            <span class="badge badge-secondary">{{ ucfirst($user->role ?? 'user') }}</span>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-3 font-weight-bold">Registered:</div>
                        <div class="col-md-9">{{ $user->created_at->format('d M Y H:i') }}</div>
                    </div>
                    @if($user->last_login_at)
                    <div class="row mb-3">
                        <div class="col-md-3 font-weight-bold">Last Login:</div>
                        <div class="col-md-9">{{ $user->last_login_at->format('d M Y H:i') }}</div>
                    </div>
                    @endif
                    @if($user->referral_code)
                    <div class="row mb-3">
                        <div class="col-md-3 font-weight-bold">Referral Code:</div>
                        <div class="col-md-9">
                            <code>{{ $user->referral_code }}</code>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Quota Information -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quota Information</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="font-weight-bold text-muted mb-1">Balance:</div>
                            <div class="h5 text-primary">Rp {{ number_format($quota->balance, 0, ',', '.') }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="font-weight-bold text-muted mb-1">Text Quota:</div>
                            <div class="h5 text-info">{{ number_format($quota->text_quota, 0, ',', '.') }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="font-weight-bold text-muted mb-1">Multimedia Quota:</div>
                            <div class="h5 text-success">{{ number_format($quota->multimedia_quota, 0, ',', '.') }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="font-weight-bold text-muted mb-1">Free Text Quota:</div>
                            <div class="h5 text-warning">{{ number_format($quota->free_text_quota, 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Subscription Information -->
            @if($user->activeSubscription || $user->subscriptionPlan)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Subscription Information</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-3 font-weight-bold">Plan:</div>
                        <div class="col-md-9">
                            <span class="badge badge-success">{{ $user->subscriptionPlan->name ?? 'N/A' }}</span>
                        </div>
                    </div>
                    @if($user->activeSubscription)
                    <div class="row mb-3">
                        <div class="col-md-3 font-weight-bold">Status:</div>
                        <div class="col-md-9">
                            <span class="badge badge-{{ $user->activeSubscription->status === 'active' ? 'success' : 'warning' }}">
                                {{ ucfirst($user->activeSubscription->status) }}
                            </span>
                        </div>
                    </div>
                    @if($user->activeSubscription->current_period_end)
                    <div class="row mb-3">
                        <div class="col-md-3 font-weight-bold">Expires At:</div>
                        <div class="col-md-9">{{ $user->activeSubscription->current_period_end->format('d M Y H:i') }}</div>
                    </div>
                    @endif
                    @elseif($user->subscription_status)
                    <div class="row mb-3">
                        <div class="col-md-3 font-weight-bold">Status:</div>
                        <div class="col-md-9">
                            <span class="badge badge-{{ $user->subscription_status === 'active' ? 'success' : 'warning' }}">
                                {{ ucfirst($user->subscription_status) }}
                            </span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Statistics -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Statistics</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="text-xs font-weight-bold text-muted text-uppercase mb-1">Total Devices</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_sessions'] }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-xs font-weight-bold text-muted text-uppercase mb-1">Connected Devices</div>
                        <div class="h5 mb-0 font-weight-bold text-success">{{ $stats['connected_sessions'] }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-xs font-weight-bold text-muted text-uppercase mb-1">Total Messages</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['total_messages'], 0, ',', '.') }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-xs font-weight-bold text-muted text-uppercase mb-1">Messages Sent</div>
                        <div class="h5 mb-0 font-weight-bold text-primary">{{ number_format($stats['messages_sent'], 0, ',', '.') }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-xs font-weight-bold text-muted text-uppercase mb-1">Messages Received</div>
                        <div class="h5 mb-0 font-weight-bold text-info">{{ number_format($stats['messages_received'], 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Devices List -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Devices</h6>
        </div>
        <div class="card-body">
            @if($user->whatsappSessions->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Device Name</th>
                                <th>Status</th>
                                <th>Created At</th>
                                <th>Last Activity</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($user->whatsappSessions as $session)
                                <tr>
                                    <td>{{ $session->session_name }}</td>
                                    <td>
                                        <span class="badge badge-{{ $session->status === 'connected' ? 'success' : ($session->status === 'pairing' ? 'warning' : 'secondary') }}">
                                            {{ ucfirst($session->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $session->created_at->format('d M Y H:i') }}</td>
                                    <td>{{ $session->last_activity_at ? $session->last_activity_at->format('d M Y H:i') : '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted text-center py-4">No devices found</p>
            @endif
        </div>
    </div>
</div>
@endsection

