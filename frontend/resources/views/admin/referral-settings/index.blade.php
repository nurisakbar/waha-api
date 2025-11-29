@extends('layouts.base')

@section('title', 'Referral Settings')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h1 class="h3 mb-1">Referral Settings</h1>
            <p class="text-muted mb-0">Atur bonus quota untuk program referral</p>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-cog"></i> Pengaturan Bonus Referral
                    </h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.referral-settings.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="text_quota_bonus" class="font-weight-bold">
                                Bonus Text Quota per Referral
                            </label>
                            <div class="input-group">
                                <input type="number" 
                                       class="form-control @error('text_quota_bonus') is-invalid @enderror" 
                                       id="text_quota_bonus" 
                                       name="text_quota_bonus" 
                                       value="{{ old('text_quota_bonus', $settings->text_quota_bonus ?? 0) }}" 
                                       min="0" 
                                       required>
                                <div class="input-group-append">
                                    <span class="input-group-text">pesan</span>
                                </div>
                            </div>
                            <small class="form-text text-muted">
                                Jumlah text quota yang akan diberikan kepada referrer untuk setiap user yang mendaftar menggunakan kode referral mereka.
                            </small>
                            @error('text_quota_bonus')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="multimedia_quota_bonus" class="font-weight-bold">
                                Bonus Multimedia Quota per Referral
                            </label>
                            <div class="input-group">
                                <input type="number" 
                                       class="form-control @error('multimedia_quota_bonus') is-invalid @enderror" 
                                       id="multimedia_quota_bonus" 
                                       name="multimedia_quota_bonus" 
                                       value="{{ old('multimedia_quota_bonus', $settings->multimedia_quota_bonus ?? 0) }}" 
                                       min="0" 
                                       required>
                                <div class="input-group-append">
                                    <span class="input-group-text">pesan</span>
                                </div>
                            </div>
                            <small class="form-text text-muted">
                                Jumlah multimedia quota yang akan diberikan kepada referrer untuk setiap user yang mendaftar menggunakan kode referral mereka.
                            </small>
                            @error('multimedia_quota_bonus')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <div class="form-check">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="is_active" 
                                       name="is_active" 
                                       value="1" 
                                       {{ old('is_active', $settings->is_active ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    <strong>Aktifkan Program Referral</strong>
                                </label>
                            </div>
                            <small class="form-text text-muted">
                                Jika dinonaktifkan, bonus quota tidak akan diberikan meskipun ada user yang mendaftar menggunakan kode referral.
                            </small>
                        </div>

                        <hr>

                        <div class="form-group mb-0">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-2"></i>Simpan Pengaturan
                            </button>
                            <a href="{{ route('admin.dashboard.index') }}" class="btn btn-secondary">
                                Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Information Card -->
            <div class="card shadow mb-4 border-left-info">
                <div class="card-body">
                    <h6 class="font-weight-bold text-info mb-3">
                        <i class="fas fa-info-circle"></i> Informasi
                    </h6>
                    <ul class="mb-0 pl-3">
                        <li class="mb-2">Bonus quota akan otomatis ditambahkan ke akun referrer ketika ada user baru yang mendaftar menggunakan kode referral mereka.</li>
                        <li class="mb-2">Setiap user memiliki kode referral unik yang dapat mereka bagikan.</li>
                        <li class="mb-0">User dapat melihat daftar user yang mendaftar menggunakan kode referral mereka di halaman Referral.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

