@extends('layouts.base')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-tachometer-alt"></i> Admin Dashboard
            </h1>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row">
        <!-- Total Users -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Users
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalUsers, 0, ',', '.') }}</div>
                            <div class="text-xs text-muted mt-1">
                                <i class="fas fa-user-plus"></i> +{{ $newUsersThisMonth }} this month
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Devices -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Active Devices
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($activeSessions, 0, ',', '.') }}</div>
                            <div class="text-xs text-muted mt-1">
                                <i class="fas fa-link"></i> {{ number_format($totalSessions, 0, ',', '.') }} total
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-plug fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Revenue -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Revenue
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                            </div>
                            <div class="text-xs text-muted mt-1">
                                <i class="fas fa-calendar"></i> Rp {{ number_format($revenueThisMonth, 0, ',', '.') }} this month
                                @if($revenueGrowth != 0)
                                    <span class="{{ $revenueGrowth > 0 ? 'text-success' : 'text-danger' }}">
                                        <i class="fas fa-arrow-{{ $revenueGrowth > 0 ? 'up' : 'down' }}"></i> {{ number_format(abs($revenueGrowth), 1) }}%
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Purchases -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Pending Purchases
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($pendingPurchases, 0, ',', '.') }}</div>
                            <div class="text-xs text-muted mt-1">
                                <i class="fas fa-check-circle"></i> {{ number_format($completedPurchases, 0, ',', '.') }} completed
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- User Growth Chart -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-line"></i> User Growth (Last 12 Months)
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="userGrowthChart" height="80"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Purchases -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-shopping-cart"></i> Recent Purchases
                    </h6>
                    <a href="{{ route('admin.quota-purchases.index') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-list"></i> View All
                    </a>
                </div>
                <div class="card-body">
                    @if($recentPurchases->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-sm">
                                <thead>
                                    <tr>
                                        <th>Purchase Number</th>
                                        <th>User</th>
                                        <th>Amount</th>
                                        <th>Payment Method</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentPurchases as $purchase)
                                        <tr>
                                            <td><strong>{{ $purchase->purchase_number }}</strong></td>
                                            <td>
                                                <div>
                                                    <strong>{{ $purchase->user->name }}</strong><br>
                                                    <small class="text-muted">{{ $purchase->user->email }}</small>
                                                </div>
                                            </td>
                                            <td><strong>Rp {{ number_format($purchase->amount, 0, ',', '.') }}</strong></td>
                                            <td>
                                                @if($purchase->payment_method === 'xendit')
                                                    <span class="badge badge-info">Xendit</span>
                                                @else
                                                    <span class="badge badge-secondary">Manual</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($purchase->status === 'completed')
                                                    <span class="badge badge-success">Completed</span>
                                                @elseif($purchase->status === 'pending')
                                                    <span class="badge badge-warning">Pending</span>
                                                @elseif($purchase->status === 'failed')
                                                    <span class="badge badge-danger">Failed</span>
                                                @else
                                                    <span class="badge badge-secondary">{{ ucfirst($purchase->status) }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $purchase->created_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> No purchases yet.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
// User Growth Chart
const userGrowthCtx = document.getElementById('userGrowthChart');
if (userGrowthCtx) {
    new Chart(userGrowthCtx, {
        type: 'line',
        data: {
            labels: @json($userGrowthLabels),
            datasets: [{
                label: 'Total Users',
                data: @json($userGrowthData),
                borderColor: 'rgb(78, 115, 223)',
                backgroundColor: 'rgba(78, 115, 223, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointHoverRadius: 6,
                pointBackgroundColor: 'rgb(78, 115, 223)',
                pointBorderColor: '#fff',
                pointBorderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        precision: 0
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            },
            interaction: {
                mode: 'nearest',
                axis: 'x',
                intersect: false
            }
        }
    });
}
</script>
@endsection

