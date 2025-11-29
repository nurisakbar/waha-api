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

                    <!-- Current Quota Section -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-wallet"></i> Current Quota
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="card border-left-primary">
                                        <div class="card-body">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Balance (IDR)
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                Rp {{ number_format($quota->balance, 0, ',', '.') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card border-left-info">
                                        <div class="card-body">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                                Text Quota
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                {{ number_format($quota->text_quota, 0, ',', '.') }} pesan
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card border-left-success">
                                        <div class="card-body">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                Multimedia Quota
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                {{ number_format($quota->multimedia_quota, 0, ',', '.') }} pesan
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3">
                                <a href="{{ route('quota.index') }}" class="btn btn-primary">
                                    <i class="fas fa-shopping-cart"></i> Purchase Quota
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Message Pricing Section -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-dollar-sign"></i> Message Pricing
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Message Type</th>
                                            <th>Price (IDR)</th>
                                            <th>Description</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><strong>Text with Watermark</strong></td>
                                            <td class="text-success">
                                                <strong>Rp {{ number_format($pricing->text_with_watermark_price, 0, ',', '.') }}</strong>
                                                @if($pricing->text_with_watermark_price == 0)
                                                    <span class="badge badge-success">FREE</span>
                                                @endif
                                            </td>
                                            <td>
                                                Pesan text dengan watermark "{{ $pricing->watermark_text }}" di akhir pesan
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Text without Watermark</strong></td>
                                            <td>
                                                <strong>Rp {{ number_format($pricing->text_without_watermark_price, 0, ',', '.') }}</strong>
                                            </td>
                                            <td>
                                                Pesan text premium tanpa watermark
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Multimedia</strong></td>
                                            <td>
                                                <strong>Rp {{ number_format($pricing->multimedia_price, 0, ',', '.') }}</strong>
                                            </td>
                                            <td>
                                                Pesan multimedia (image, document, video, audio, dll)
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="alert alert-info mt-3">
                                <i class="fas fa-info-circle"></i> 
                                <strong>Note:</strong> Harga di atas adalah per pesan. Balance akan dikurangi sesuai harga setiap kali mengirim pesan premium.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

