@extends('layouts.base')

@section('title', 'Quota Purchase Management')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-shopping-cart"></i> Quota Purchase Management
            </h1>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Menunggu Pembayaran
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['waiting_payment'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Menunggu Konfirmasi
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['pending_verification'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-upload fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Completed Purchases
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['completed'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Failed Purchases
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['failed'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Revenue
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp {{ number_format($stats['total_amount'], 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-filter"></i> Filters
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.quota-purchases.index') }}" class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="status">Payment Status</label>
                        <select name="status" id="status" class="form-control">
                            <option value="">All Status</option>
                            <option value="waiting_payment" {{ request('status') === 'waiting_payment' ? 'selected' : '' }}>Menunggu Pembayaran</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="pending_verification" {{ request('status') === 'pending_verification' ? 'selected' : '' }}>Pending Verification (Menunggu Konfirmasi)</option>
                            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed (Sudah Bayar)</option>
                            <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed (Gagal)</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="payment_method">Payment Method</label>
                        <select name="payment_method" id="payment_method" class="form-control">
                            <option value="">All Methods</option>
                            <option value="manual" {{ request('payment_method') === 'manual' ? 'selected' : '' }}>Manual</option>
                            <option value="xendit" {{ request('payment_method') === 'xendit' ? 'selected' : '' }}>Xendit</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="search">Search</label>
                        <input type="text" 
                               name="search" 
                               id="search" 
                               class="form-control" 
                               placeholder="Purchase number, user name, or email..."
                               value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-search"></i> Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Purchases Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list"></i> Purchase List
            </h6>
        </div>
        <div class="card-body">
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

            @if($purchases->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Purchase Number</th>
                                <th>User</th>
                                <th>Amount</th>
                                <th>Quota Details</th>
                                <th>Payment Method</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($purchases as $purchase)
                                <tr>
                                    <td>
                                        <strong>{{ $purchase->purchase_number }}</strong>
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ $purchase->user->name }}</strong><br>
                                            <small class="text-muted">{{ $purchase->user->email }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <strong>Rp {{ number_format($purchase->amount, 0, ',', '.') }}</strong>
                                    </td>
                                    <td>
                                        @if($purchase->balance_added > 0)
                                            <span class="badge badge-info">Balance: Rp {{ number_format($purchase->balance_added, 0, ',', '.') }}</span><br>
                                        @endif
                                        @if($purchase->text_quota_added > 0)
                                            <span class="badge badge-primary">Text: {{ number_format($purchase->text_quota_added, 0, ',', '.') }} pesan</span><br>
                                        @endif
                                        @if($purchase->multimedia_quota_added > 0)
                                            <span class="badge badge-success">Multimedia: {{ number_format($purchase->multimedia_quota_added, 0, ',', '.') }} pesan</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($purchase->payment_method === 'xendit')
                                            <span class="badge badge-info">
                                                <i class="fas fa-credit-card"></i> Xendit
                                            </span>
                                            @if($purchase->xendit_invoice_url)
                                                <br><a href="{{ $purchase->xendit_invoice_url }}" target="_blank" class="btn btn-sm btn-link p-0 mt-1">
                                                    View Invoice
                                                </a>
                                            @endif
                                        @else
                                            <span class="badge badge-secondary">
                                                <i class="fas fa-hand-holding-usd"></i> Manual
                                            </span>
                                            @if($purchase->payment_reference)
                                                <br><small class="text-muted">{{ $purchase->payment_reference }}</small>
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        @if($purchase->status === 'completed')
                                            <span class="badge badge-success">Completed</span>
                                        @elseif($purchase->status === 'pending_verification')
                                            <span class="badge badge-info">
                                                <i class="fas fa-clock"></i> Menunggu Konfirmasi
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
                                        {{ $purchase->created_at->format('d/m/Y H:i') }}
                                        @if($purchase->completed_at)
                                            <br><small class="text-muted">Completed: {{ $purchase->completed_at->format('d/m/Y H:i') }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.quota-purchases.show', $purchase) }}" 
                                               class="btn btn-sm btn-info" 
                                               title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if(in_array($purchase->status, ['waiting_payment', 'pending', 'pending_verification']))
                                                <form action="{{ route('admin.quota-purchases.approve', $purchase) }}" 
                                                      method="POST" 
                                                      class="d-inline"
                                                      onsubmit="return confirm('Are you sure you want to approve this purchase?');">
                                                    @csrf
                                                    <button type="submit" 
                                                            class="btn btn-sm btn-success" 
                                                            title="Approve">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                                <button type="button" 
                                                        class="btn btn-sm btn-danger" 
                                                        title="Reject"
                                                        onclick="showRejectModal({{ $purchase->id }})">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            @endif
                                        </div>
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
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> No purchases found.
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Purchase</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="rejectForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="rejection_reason">Rejection Reason (Optional)</label>
                        <textarea name="rejection_reason" 
                                  id="rejection_reason" 
                                  class="form-control" 
                                  rows="3" 
                                  placeholder="Enter reason for rejection..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject Purchase</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showRejectModal(purchaseId) {
    const form = document.getElementById('rejectForm');
    form.action = '{{ route("admin.quota-purchases.reject", ":id") }}'.replace(':id', purchaseId);
    $('#rejectModal').modal('show');
}
</script>
@endsection

