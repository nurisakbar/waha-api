@extends('layouts.base')

@section('title', 'Billing & Subscription')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Billing & Subscription') }}</div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($currentSubscription)
                        <div class="alert alert-info mb-4">
                            <h5>{{ __('Current Plan') }}: {{ $currentSubscription->plan->name }}</h5>
                            <p class="mb-0">
                                {{ __('Period') }}: {{ $currentSubscription->current_period_start->format('Y-m-d') }} - {{ $currentSubscription->current_period_end->format('Y-m-d') }}
                            </p>
                        </div>
                    @endif

                    <h5 class="mb-4">{{ __('Available Plans') }}</h5>
                    <div class="row">
                        @foreach($plans as $plan)
                            <div class="col-md-3 mb-4">
                                <div class="card h-100 {{ $plan->is_featured ? 'border-primary' : '' }}">
                                    @if($plan->is_featured)
                                        <div class="card-header bg-primary text-white text-center">
                                            <strong>{{ __('Featured') }}</strong>
                                        </div>
                                    @endif
                                    <div class="card-body text-center">
                                        <h4>{{ $plan->name }}</h4>
                                        <h3 class="text-primary">${{ number_format($plan->price, 2) }}</h3>
                                        <p class="text-muted">{{ $plan->description }}</p>
                                        <ul class="list-unstyled text-start">
                                            <li><i class="fas fa-check text-success"></i> {{ $plan->sessions_limit }} {{ __('Sessions') }}</li>
                                            <li><i class="fas fa-check text-success"></i> 
                                                @if($plan->messages_per_month)
                                                    {{ number_format($plan->messages_per_month) }} {{ __('Messages/month') }}
                                                @else
                                                    {{ __('Unlimited Messages') }}
                                                @endif
                                            </li>
                                            <li><i class="fas fa-check text-success"></i> {{ $plan->api_rate_limit }} {{ __('API calls/min') }}</li>
                                        </ul>
                                        <form action="{{ route('billing.subscribe') }}" method="POST" class="mt-auto">
                                            @csrf
                                            <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                                            <button type="submit" class="btn btn-primary w-100">
                                                {{ __('Subscribe') }}
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

