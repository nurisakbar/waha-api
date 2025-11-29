@extends('layouts.base')

@section('title', 'Pair Device')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-qrcode mr-2"></i>Pair Device WhatsApp: {{ $session->session_name }}
                    </h6>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if ($session->status === 'connected')
                        <div class="text-center py-5">
                            <div class="mb-4">
                                <i class="fas fa-check-circle text-success" style="font-size: 80px;"></i>
                            </div>
                            <h4 class="text-success mb-3">Device Terhubung!</h4>
                            <p class="text-muted mb-4">Device WhatsApp Anda sudah terhubung dan siap digunakan.</p>
                            <a href="{{ route('sessions.show', $session) }}" class="btn btn-primary btn-lg">
                                <i class="fas fa-arrow-right mr-2"></i>Lihat Device
                            </a>
                        </div>
                    @else
                        <div class="row">
                            <!-- QR Code Section -->
                            <div class="col-lg-6 text-center mb-4">
                                <h5 class="mb-3">Pindai QR Code dengan WhatsApp</h5>
                                <p class="text-muted mb-4">
                                    Buka WhatsApp di ponsel Anda, masuk ke <strong>Pengaturan > Perangkat Tertaut</strong>, lalu pindai QR code ini.
                                </p>

                                @if ($session->qr_code)
                                    <div class="mb-4">
                                        <img src="data:image/png;base64,{{ $session->qr_code }}" 
                                             alt="QR Code" 
                                             class="img-fluid" 
                                             style="max-width: 400px; border: 3px solid #4e73df; padding: 15px; background: white; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                                    </div>

                                    @if ($session->qr_code_expires_at)
                                        <div class="alert alert-warning d-inline-block">
                                            <i class="fas fa-clock mr-2"></i>
                                            QR Code kedaluwarsa pada: {{ $session->qr_code_expires_at->format('H:i:s') }}
                                        </div>
                                    @endif

                                    <div class="mt-4">
                                        <button type="button" class="btn btn-primary btn-lg" onclick="checkStatus()">
                                            <i class="fas fa-sync mr-2"></i> Cek Status
                                        </button>
                                        <a href="{{ route('sessions.index') }}" class="btn btn-secondary btn-lg ml-2">
                                            <i class="fas fa-arrow-left mr-2"></i> Kembali
                                        </a>
                                    </div>

                                    <div id="status-message" class="mt-4"></div>
                                @else
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle mr-2"></i>
                                        QR code sedang dibuat. Harap tunggu...
                                    </div>
                                    <div class="mt-4">
                                        <button type="button" class="btn btn-primary btn-lg" onclick="location.reload()">
                                            <i class="fas fa-sync mr-2"></i> Refresh QR Code
                                        </button>
                                        <a href="{{ route('sessions.index') }}" class="btn btn-secondary btn-lg ml-2">
                                            <i class="fas fa-arrow-left mr-2"></i> Kembali
                                        </a>
                                    </div>
                                    <script>
                                        // Auto-refresh after 3 seconds if QR code not available
                                        setTimeout(function() {
                                            location.reload();
                                        }, 3000);
                                    </script>
                                @endif
                            </div>

                            <!-- Instructions Section -->
                            <div class="col-lg-6">
                                <div class="card border-left-primary mb-4">
                                    <div class="card-body">
                                        <h6 class="mb-3">
                                            <i class="fas fa-info-circle text-primary mr-2"></i>Cara Menggunakan
                                        </h6>
                                        <ol class="pl-3">
                                            <li class="mb-2">Buka aplikasi WhatsApp di ponsel Anda</li>
                                            <li class="mb-2">Ketuk <strong>Menu</strong> (3 titik) di pojok kanan atas</li>
                                            <li class="mb-2">Pilih <strong>Perangkat Tertaut</strong> atau <strong>Linked Devices</strong></li>
                                            <li class="mb-2">Ketuk <strong>Hubungkan Perangkat</strong></li>
                                            <li class="mb-2">Pindai QR code yang ditampilkan di layar</li>
                                            <li class="mb-0">Tunggu hingga status berubah menjadi "Terhubung"</li>
                                        </ol>
                                    </div>
                                </div>

                                <div class="card border-left-warning">
                                    <div class="card-body">
                                        <h6 class="mb-3">
                                            <i class="fas fa-lightbulb text-warning mr-2"></i>Tips
                                        </h6>
                                        <ul class="list-unstyled mb-0">
                                            <li class="mb-2">
                                                <i class="fas fa-check-circle text-success mr-2"></i>
                                                Pastikan ponsel Anda terhubung ke internet
                                            </li>
                                            <li class="mb-2">
                                                <i class="fas fa-check-circle text-success mr-2"></i>
                                                QR code akan otomatis diperbarui setiap beberapa detik
                                            </li>
                                            <li class="mb-0">
                                                <i class="fas fa-check-circle text-success mr-2"></i>
                                                Jika QR code kedaluwarsa, klik tombol "Cek Status" untuk memperbarui
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <script>
                            function checkStatus(btnElement = null) {
                                const btn = btnElement || document.querySelector('button[onclick="checkStatus()"]');
                                const originalText = btn ? btn.innerHTML : '';
                                
                                if (btn) {
                                    btn.disabled = true;
                                    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Mengecek...';
                                }

                                fetch('{{ route("sessions.check-status", $session) }}')
                                    .then(response => response.json())
                                    .then(data => {
                                        if (btn) {
                                            btn.disabled = false;
                                            btn.innerHTML = originalText;
                                        }

                                        const statusMsg = document.getElementById('status-message');
                                        if (data.is_connected) {
                                            statusMsg.innerHTML = '<div class="alert alert-success"><i class="fas fa-check-circle mr-2"></i> Device terhubung! Mengalihkan...</div>';
                                            setTimeout(() => {
                                                window.location.href = '{{ route("sessions.show", $session) }}';
                                            }, 2000);
                                        } else {
                                            statusMsg.innerHTML = '<div class="alert alert-info"><i class="fas fa-info-circle mr-2"></i> Masih menunggu pairing...</div>';
                                        }
                                    })
                                    .catch(error => {
                                        if (btn) {
                                            btn.disabled = false;
                                            btn.innerHTML = originalText;
                                        }
                                        const statusMsg = document.getElementById('status-message');
                                        if (statusMsg) {
                                            statusMsg.innerHTML = '<div class="alert alert-danger"><i class="fas fa-exclamation-circle mr-2"></i> Error saat mengecek status. Silakan coba lagi.</div>';
                                        }
                                    });
                            }

                            // Auto-check status every 5 seconds (silent check, no button update)
                            setInterval(() => checkStatus(), 5000);
                        </script>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
