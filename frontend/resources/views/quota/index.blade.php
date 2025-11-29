@extends('layouts.base')

@section('title', 'Quota Management')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-wallet"></i> Quota Management
            </h1>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        <!-- Current Quota Cards -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-wallet"></i> Current Quota
                    </h6>
                    <a href="{{ route('quota.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Purchase Quota
                    </a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="card border-left-primary shadow-sm h-100">
                                <div class="card-body">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Balance (IDR)
                                    </div>
                                    <div class="h4 mb-0 font-weight-bold text-gray-800">
                                        Rp {{ number_format($quota->balance, 0, ',', '.') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card border-left-info shadow-sm h-100">
                                <div class="card-body">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                        Text Quota
                                    </div>
                                    <div class="h4 mb-0 font-weight-bold text-gray-800">
                                        {{ number_format($quota->text_quota, 0, ',', '.') }} pesan
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card border-left-success shadow-sm h-100">
                                <div class="card-body">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        Multimedia Quota
                                    </div>
                                    <div class="h4 mb-0 font-weight-bold text-gray-800">
                                        {{ number_format($quota->multimedia_quota, 0, ',', '.') }} pesan
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Purchase History -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-history"></i> Purchase History
                    </h6>
                </div>
                <div class="card-body">
                    @if($purchases->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Purchase Number</th>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($purchases as $purchase)
                                        <tr>
                                            <td><strong>{{ $purchase->purchase_number }}</strong></td>
                                            <td>{{ $purchase->created_at->format('d/m/Y H:i') }}</td>
                                            <td><strong>Rp {{ number_format($purchase->amount, 0, ',', '.') }}</strong></td>
                                            <td>
                                                @if($purchase->status === 'completed')
                                                    <span class="badge badge-success">Completed</span>
                                                @elseif($purchase->status === 'pending_verification')
                                                    <span class="badge badge-info">
                                                        <i class="fas fa-clock"></i> Menunggu Konfirmasi Admin
                                                    </span>
                                                @elseif($purchase->status === 'waiting_payment')
                                                    <span class="badge badge-warning">
                                                        <i class="fas fa-money-bill-wave"></i> Menunggu Pembayaran
                                                    </span>
                                                @elseif($purchase->status === 'pending')
                                                    <span class="badge badge-warning">Pending</span>
                                                @elseif($purchase->status === 'failed')
                                                    <span class="badge badge-danger">Failed</span>
                                                @else
                                                    <span class="badge badge-secondary">{{ ucfirst($purchase->status) }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($purchase->status === 'waiting_payment' && $purchase->payment_method === 'manual')
                                                    <a href="{{ route('quota.confirm-payment', $purchase) }}" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-upload"></i> Konfirmasi Pembayaran
                                                    </a>
                                                @elseif($purchase->status === 'pending' && $purchase->payment_method === 'manual')
                                                    <a href="{{ route('quota.confirm-payment', $purchase) }}" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-upload"></i> Konfirmasi Pembayaran
                                                    </a>
                                                @elseif($purchase->status === 'pending_verification')
                                                    <span class="text-info"><i class="fas fa-check-circle"></i> Menunggu verifikasi admin</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{ $purchases->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No purchase history yet.</p>
                            <a href="{{ route('quota.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Purchase Quota
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar Info -->
        <div class="col-lg-4">
            <!-- Pricing Information -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-tag"></i> Pricing Information
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span><i class="fas fa-comment text-primary"></i> Text Quota (Premium)</span>
                            <strong>Rp {{ number_format($pricing->text_without_watermark_price, 0, ',', '.') }}/pesan</strong>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-image text-info"></i> Multimedia Quota</span>
                            <strong>Rp {{ number_format($pricing->multimedia_price, 0, ',', '.') }}/pesan</strong>
                        </div>
                    </div>
                    <hr>
                    <div class="alert alert-info mb-0">
                        <small>
                            <i class="fas fa-info-circle"></i> 
                            Minimum purchase: <strong>Rp 1.000</strong>
                        </small>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-bolt"></i> Quick Actions
                    </h6>
                </div>
                <div class="card-body">
                    <a href="{{ route('quota.create') }}" class="btn btn-primary btn-block mb-2">
                        <i class="fas fa-shopping-cart"></i> Purchase Quota
                    </a>
                    <a href="{{ route('billing.index') }}" class="btn btn-outline-secondary btn-block">
                        <i class="fas fa-credit-card"></i> View Billing
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
