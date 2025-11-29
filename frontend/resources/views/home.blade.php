@extends('layouts.base')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h1 class="h3 mb-1">Dashboard</h1>
            <p class="text-muted mb-0">Pantau sesi WhatsApp dan aktivitas terbaru Anda.</p>
        </div>
    </div>

    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <p class="text-uppercase text-muted mb-1 small">Total Sessions</p>
                    <h3 class="fw-bold mb-0">{{ $metrics['totalSessions'] ?? 0 }}</h3>
                    <small class="text-muted">Semua sesi yang telah dibuat</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <p class="text-uppercase text-muted mb-1 small">Active</p>
                    <h3 class="fw-bold mb-0">{{ $metrics['activeSessions'] ?? 0 }}</h3>
                    <small class="text-muted">Sedang terhubung</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <p class="text-uppercase text-muted mb-1 small">Sent Today</p>
                    <h3 class="fw-bold mb-0">{{ $metrics['messagesSentToday'] ?? 0 }}</h3>
                    <small class="text-muted">Pesan keluar</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <p class="text-uppercase text-muted mb-1 small">Received Today</p>
                    <h3 class="fw-bold mb-0">{{ $metrics['messagesReceivedToday'] ?? 0 }}</h3>
                    <small class="text-muted">Pesan masuk</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Grafik Pesan Bulan Ini -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <div>
                <h2 class="h5 mb-0">Grafik Pesan Bulan Ini</h2>
                <small class="text-muted">Jumlah pesan yang dikirim dari awal bulan hingga hari ini</small>
            </div>
            <span class="badge bg-success">
                Total: {{ number_format($messageStats['total'] ?? 0, 0, ',', '.') }} pesan
            </span>
        </div>
        <div class="card-body">
            <canvas id="messageChart" height="80"></canvas>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <div>
                <h2 class="h5 mb-0">Aktivitas Terbaru</h2>
                <small class="text-muted">5 aktivitas terakhir dari akun Anda</small>
            </div>
            <span class="badge bg-light text-dark">Realtime</span>
        </div>
        <div class="card-body">
            @if (empty($recentActivity))
                <div class="text-center py-4">
                    <p class="mb-1 fw-semibold">Belum ada aktivitas</p>
                    <p class="text-muted mb-0">Pesan masuk/keluar akan muncul di sini setelah integrasi WAHA aktif.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                        <tr>
                            <th scope="col">Waktu</th>
                            <th scope="col">Jenis</th>
                            <th scope="col">Arah</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($recentActivity as $activity)
                            <tr>
                                <td class="text-nowrap">{{ $activity['timestamp'] }}</td>
                                <td>
                                    <span class="badge text-bg-primary">{{ $activity['type'] }}</span>
                                </td>
                                <td>
                                    <span class="badge {{ $activity['direction'] === 'outgoing' ? 'text-bg-success' : 'text-bg-info' }}">
                                        {{ $activity['label'] }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<!-- Chart.js (using CDN for latest version) -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('messageChart');
    if (!ctx) return;

    const messageStats = @json($messageStats ?? ['labels' => [], 'data' => []]);
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: messageStats.labels || [],
            datasets: [{
                label: 'Pesan Dikirim',
                data: messageStats.data || [],
                borderColor: 'rgb(16, 185, 129)',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointHoverRadius: 6,
                pointBackgroundColor: 'rgb(16, 185, 129)',
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
                    labels: {
                        usePointStyle: true,
                        padding: 15,
                        font: {
                            size: 12,
                            weight: '500'
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    titleFont: {
                        size: 14,
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 13
                    },
                    callbacks: {
                        label: function(context) {
                            return 'Pesan: ' + context.parsed.y.toLocaleString('id-ID');
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        callback: function(value) {
                            return value.toLocaleString('id-ID');
                        },
                        font: {
                            size: 11
                        }
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    ticks: {
                        font: {
                            size: 11
                        },
                        maxRotation: 45,
                        minRotation: 45
                    },
                    grid: {
                        display: false
                    }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
            }
        }
    });
});
</script>
@endpush
@endsection
