@extends('layouts.base')

@section('title', 'Purchase Details')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-shopping-cart"></i> Purchase Details
            </h1>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle"></i> Purchase Information
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

                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Purchase Number</th>
                            <td><strong>{{ $quotaPurchase->purchase_number }}</strong></td>
                        </tr>
                        <tr>
                            <th>User</th>
                            <td>
                                <strong>{{ $quotaPurchase->user->name }}</strong><br>
                                <small class="text-muted">{{ $quotaPurchase->user->email }}</small>
                            </td>
                        </tr>
                        <tr>
                            <th>Amount</th>
                            <td><strong class="text-primary">Rp {{ number_format($quotaPurchase->amount, 0, ',', '.') }}</strong></td>
                        </tr>
                        <tr>
                            <th>Payment Method</th>
                            <td>
                                @if($quotaPurchase->payment_method === 'xendit')
                                    <span class="badge badge-info">
                                        <i class="fas fa-credit-card"></i> Xendit
                                    </span>
                                    @if($quotaPurchase->xendit_invoice_id)
                                        <br><small class="text-muted">Invoice ID: {{ $quotaPurchase->xendit_invoice_id }}</small>
                                    @endif
                                    @if($quotaPurchase->xendit_invoice_url)
                                        <br><a href="{{ $quotaPurchase->xendit_invoice_url }}" target="_blank" class="btn btn-sm btn-link p-0 mt-1">
                                            <i class="fas fa-external-link-alt"></i> View Invoice
                                        </a>
                                    @endif
                                @else
                                    <span class="badge badge-secondary">
                                        <i class="fas fa-hand-holding-usd"></i> Manual
                                    </span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Payment Reference</th>
                            <td>{{ $quotaPurchase->payment_reference ?? '-' }}</td>
                        </tr>
                        @if($quotaPurchase->payment_proof)
                        <tr>
                            <th>Payment Proof</th>
                            <td>
                                <a href="{{ asset('storage/' . $quotaPurchase->payment_proof) }}" target="_blank" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i> View Payment Proof
                                </a>
                                <br><small class="text-muted">Click to view full image</small>
                                <br><img src="{{ asset('storage/' . $quotaPurchase->payment_proof) }}" alt="Payment Proof" class="img-thumbnail mt-2" style="max-height: 200px;">
                            </td>
                        </tr>
                        @endif
                        <tr>
                            <th>Status</th>
                            <td>
                                @if($quotaPurchase->status === 'completed')
                                    <span class="badge badge-success">Completed</span>
                                @elseif($quotaPurchase->status === 'pending_verification')
                                    <span class="badge badge-info">
                                        <i class="fas fa-clock"></i> Menunggu Konfirmasi
                                    </span>
                                @elseif($quotaPurchase->status === 'waiting_payment')
                                    <span class="badge badge-warning">
                                        <i class="fas fa-money-bill-wave"></i> Menunggu Pembayaran
                                    </span>
                                @elseif($quotaPurchase->status === 'pending')
                                    <span class="badge badge-warning">Pending</span>
                                @elseif($quotaPurchase->status === 'failed')
                                    <span class="badge badge-danger">Failed</span>
                                @else
                                    <span class="badge badge-secondary">{{ ucfirst($quotaPurchase->status) }}</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Created At</th>
                            <td>{{ $quotaPurchase->created_at->format('d/m/Y H:i:s') }}</td>
                        </tr>
                        @if($quotaPurchase->completed_at)
                        <tr>
                            <th>Completed At</th>
                            <td>{{ $quotaPurchase->completed_at->format('d/m/Y H:i:s') }}</td>
                        </tr>
                        @endif
                        @if($quotaPurchase->notes)
                        <tr>
                            <th>Notes</th>
                            <td>{{ $quotaPurchase->notes }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-gift"></i> Quota Details
                    </h6>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Balance Added</th>
                            <td>
                                @if($quotaPurchase->balance_added > 0)
                                    <strong class="text-success">Rp {{ number_format($quotaPurchase->balance_added, 0, ',', '.') }}</strong>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Text Quota Added</th>
                            <td>
                                @if($quotaPurchase->text_quota_added > 0)
                                    <strong class="text-primary">{{ number_format($quotaPurchase->text_quota_added, 0, ',', '.') }} pesan</strong>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Multimedia Quota Added</th>
                            <td>
                                @if($quotaPurchase->multimedia_quota_added > 0)
                                    <strong class="text-info">{{ number_format($quotaPurchase->multimedia_quota_added, 0, ',', '.') }} pesan</strong>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-cog"></i> Actions
                    </h6>
                </div>
                <div class="card-body">
                    @if(in_array($quotaPurchase->status, ['waiting_payment', 'pending', 'pending_verification']))
                        <form action="{{ route('admin.quota-purchases.approve', $quotaPurchase) }}" 
                              method="POST" 
                              class="mb-3"
                              onsubmit="return confirm('Are you sure you want to approve this purchase? This will add quota to the user account.');">
                            @csrf
                            <button type="submit" class="btn btn-success btn-block">
                                <i class="fas fa-check"></i> Approve Purchase
                            </button>
                        </form>

                        <button type="button" 
                                class="btn btn-danger btn-block" 
                                onclick="showRejectModal()">
                            <i class="fas fa-times"></i> Reject Purchase
                        </button>
                    @elseif($quotaPurchase->status === 'completed')
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i> This purchase has been completed.
                        </div>
                    @elseif($quotaPurchase->status === 'failed')
                        <div class="alert alert-danger">
                            <i class="fas fa-times-circle"></i> This purchase has been failed.
                        </div>
                    @endif

                    <hr>

                    <a href="{{ route('admin.quota-purchases.index') }}" class="btn btn-secondary btn-block">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                </div>
            </div>
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
            <form action="{{ route('admin.quota-purchases.reject', $quotaPurchase) }}" method="POST">
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
function showRejectModal() {
    $('#rejectModal').modal('show');
}
</script>
@endsection

