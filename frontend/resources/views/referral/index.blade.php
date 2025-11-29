@extends('layouts.base')

@section('title', 'Referral')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h1 class="h3 mb-1">Program Referral</h1>
            <p class="text-muted mb-0">Ajak teman Anda dan dapatkan bonus quota!</p>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <!-- Left Column - Referral Information -->
        <div class="col-lg-8">
            <!-- Referral Code Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-gift"></i> Kode Referral Anda
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="form-group mb-3">
                                <label class="small text-gray-500 mb-1">Kode Referral</label>
                                <div class="input-group">
                                    <input type="text" 
                                           class="form-control form-control-lg font-weight-bold" 
                                           id="referralCode" 
                                           value="{{ $user->referral_code }}" 
                                           readonly
                                           style="font-size: 24px; letter-spacing: 2px;">
                                    <div class="input-group-append">
                                        <button class="btn btn-primary" type="button" onclick="copyReferralCode()">
                                            <i class="fas fa-copy"></i> Copy
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group mb-3">
                                <label class="small text-gray-500 mb-1">Link Referral</label>
                                <div class="input-group">
                                    <input type="text" 
                                           class="form-control" 
                                           id="referralLink" 
                                           value="{{ $referralLink }}" 
                                           readonly>
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-primary" type="button" onclick="copyReferralLink()">
                                            <i class="fas fa-copy"></i> Copy Link
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="p-3 bg-light rounded">
                                <div class="h2 mb-0 text-primary font-weight-bold">{{ $referralCount }}</div>
                                <div class="small text-gray-500">Total Referral</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bonus Information -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle"></i> Informasi Bonus
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="card border-left-primary h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Bonus per Referral
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                {{ number_format($settings->text_quota_bonus ?? 0, 0, ',', '.') }} Text Quota
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                {{ number_format($settings->multimedia_quota_bonus ?? 0, 0, ',', '.') }} Multimedia Quota
                                            </div>
                                        </div>
                                        <div class="ml-3">
                                            <i class="fas fa-gift fa-2x text-primary"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="card border-left-success h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                Total Bonus Diterima
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                {{ number_format($totalTextQuotaBonus, 0, ',', '.') }} Text Quota
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                {{ number_format($totalMultimediaQuotaBonus, 0, ',', '.') }} Multimedia Quota
                                            </div>
                                        </div>
                                        <div class="ml-3">
                                            <i class="fas fa-chart-line fa-2x text-success"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Referred Users List -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-users"></i> User yang Mendaftar dengan Kode Referral Anda
                    </h6>
                    <span class="badge badge-primary">{{ $referrals->total() }} User</span>
                </div>
                <div class="card-body">
                    @if($referrals->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama</th>
                                        <th>Email</th>
                                        <th>Tanggal Daftar</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($referrals as $index => $referral)
                                        <tr>
                                            <td>{{ $referrals->firstItem() + $index }}</td>
                                            <td>{{ $referral->name }}</td>
                                            <td>{{ $referral->email }}</td>
                                            <td>{{ $referral->created_at->format('d M Y H:i') }}</td>
                                            <td>
                                                <span class="badge badge-success">Aktif</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="d-flex justify-content-center">
                            {{ $referrals->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-users fa-3x text-gray-300 mb-3"></i>
                            <p class="text-muted">Belum ada user yang mendaftar menggunakan kode referral Anda.</p>
                            <p class="text-muted">Bagikan kode referral Anda untuk mendapatkan bonus quota!</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column - How It Works -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-question-circle"></i> Cara Kerja
                    </h6>
                </div>
                <div class="card-body">
                    <ol class="pl-3">
                        <li class="mb-3">
                            <strong>Bagikan Kode Referral</strong>
                            <p class="small text-muted mb-0">Salin kode atau link referral Anda dan bagikan ke teman.</p>
                        </li>
                        <li class="mb-3">
                            <strong>Teman Mendaftar</strong>
                            <p class="small text-muted mb-0">Teman Anda mendaftar menggunakan kode atau link referral Anda.</p>
                        </li>
                        <li class="mb-3">
                            <strong>Dapatkan Bonus</strong>
                            <p class="small text-muted mb-0">Anda otomatis mendapatkan bonus quota sesuai setting admin.</p>
                        </li>
                        <li>
                            <strong>Lihat History</strong>
                            <p class="small text-muted mb-0">Lihat daftar user yang mendaftar menggunakan kode referral Anda.</p>
                        </li>
                    </ol>
                </div>
            </div>

            <div class="card shadow mb-4 border-left-info">
                <div class="card-body">
                    <h6 class="font-weight-bold text-info mb-3">
                        <i class="fas fa-lightbulb"></i> Tips
                    </h6>
                    <ul class="mb-0 pl-3">
                        <li class="mb-2">Bagikan link referral untuk kemudahan teman mendaftar</li>
                        <li class="mb-2">Semakin banyak referral, semakin banyak bonus yang Anda dapatkan</li>
                        <li class="mb-0">Bonus quota langsung ditambahkan ke akun Anda</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function copyReferralCode() {
    const codeInput = document.getElementById('referralCode');
    codeInput.select();
    codeInput.setSelectionRange(0, 99999); // For mobile devices
    
    try {
        document.execCommand('copy');
        
        // Show success message
        const btn = event.target.closest('button');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check"></i> Copied!';
        btn.classList.remove('btn-primary');
        btn.classList.add('btn-success');
        
        setTimeout(() => {
            btn.innerHTML = originalText;
            btn.classList.remove('btn-success');
            btn.classList.add('btn-primary');
        }, 2000);
    } catch (err) {
        alert('Gagal menyalin kode. Silakan salin manual.');
    }
}

function copyReferralLink() {
    const linkInput = document.getElementById('referralLink');
    linkInput.select();
    linkInput.setSelectionRange(0, 99999); // For mobile devices
    
    try {
        document.execCommand('copy');
        
        // Show success message
        const btn = event.target.closest('button');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check"></i> Copied!';
        btn.classList.remove('btn-outline-primary');
        btn.classList.add('btn-success');
        
        setTimeout(() => {
            btn.innerHTML = originalText;
            btn.classList.remove('btn-success');
            btn.classList.add('btn-outline-primary');
        }, 2000);
    } catch (err) {
        alert('Gagal menyalin link. Silakan salin manual.');
    }
}
</script>
@endpush
@endsection

