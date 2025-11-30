@extends('layouts.auth')

@section('title', 'Lengkapi Nomor HP')

@section('content')
<div class="login-container">
    <div class="login-card">
        <div class="login-header">
            <h2 class="login-title">Lengkapi Nomor HP</h2>
            <p class="login-subtitle">Silakan masukan nomor WhatsApp Anda untuk melanjutkan</p>
        </div>

        @if (session('info'))
            <div class="alert alert-info" role="alert">
                <i class="fas fa-info-circle"></i> {{ session('info') }}
            </div>
        @endif

        <form method="POST" action="{{ route('auth.google.phone.save') }}" class="login-form" id="phoneForm">
            @csrf

            <div class="form-group">
                <label for="phone" class="form-label">Nomor WhatsApp</label>
                <input id="phone" type="tel"
                       class="form-control @error('phone') is-invalid @enderror"
                       name="phone" value="{{ old('phone') }}" required autocomplete="tel" autofocus
                       placeholder="Contoh: +6281234567890">

                @error('phone')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
                <small class="form-text text-muted">Gunakan format internasional (contoh: +6281234567890)</small>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-login btn-block">
                    Simpan dan Lanjutkan
                </button>
            </div>
        </form>
    </div>
</div>

@push('styles')
<style>
body {
    background-color: #f0f2f5;
    font-family: 'Nunito', sans-serif;
}

.login-container {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.login-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 400px;
    padding: 30px;
}

.login-header {
    text-align: center;
    margin-bottom: 30px;
}

.login-title {
    font-size: 24px;
    font-weight: bold;
    color: #333;
    margin-bottom: 5px;
}

.login-subtitle {
    font-size: 14px;
    color: #666;
    margin: 0;
}

.login-form {
    margin-top: 0;
}

.form-group {
    margin-bottom: 15px;
}

.form-label {
    display: block;
    font-size: 14px;
    font-weight: 600;
    color: #333;
    margin-bottom: 5px;
}

.form-control {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 14px;
    transition: all 0.3s ease;
    box-sizing: border-box;
}

.form-control:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.form-control.is-invalid {
    border-color: #dc3545;
}

.invalid-feedback {
    display: block;
    color: #dc3545;
    font-size: 12px;
    margin-top: 5px;
}

.form-text {
    display: block;
    margin-top: 5px;
    font-size: 12px;
    color: #666;
}

.alert {
    padding: 12px 15px;
    margin-bottom: 20px;
    border-radius: 5px;
    font-size: 14px;
}

.alert-info {
    background-color: #d1ecf1;
    border-color: #bee5eb;
    color: #0c5460;
}

.btn {
    padding: 12px;
    border: none;
    border-radius: 5px;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    display: block;
    width: 100%;
    text-align: center;
    box-sizing: border-box;
}

.btn-login {
    background-color: #007bff;
    border-color: #007bff;
    color: white;
}

.btn-login:hover {
    background-color: #0056b3;
    border-color: #0056b3;
    color: white;
}

@media (max-width: 480px) {
    .login-card {
        padding: 20px;
    }
}
</style>
@endpush
@endsection

