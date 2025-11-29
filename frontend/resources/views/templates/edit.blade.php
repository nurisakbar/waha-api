@extends('layouts.base')

@section('title', __('Edit Template'))

@section('content')
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('Edit Template') }}</h1>
        <a href="{{ route('templates.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> {{ __('Back to Templates') }}
        </a>
    </div>

    <!-- Content Row -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('Template Details') }}</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('templates.update', $template) }}">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="name">{{ __('Template Name') }} <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name', $template->name) }}" 
                                   required 
                                   placeholder="Contoh: Pesan Selamat Datang, Konfirmasi Pesanan">
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="template_type">{{ __('Jenis Template') }} <span class="text-danger">*</span></label>
                            <select 
                                class="form-control @error('template_type') is-invalid @enderror" 
                                id="template_type" 
                                name="template_type" 
                                required>
                                <option value="message" {{ old('template_type', $template->template_type ?? 'message') == 'message' ? 'selected' : '' }}>Pesan Biasa</option>
                                <option value="otp" {{ old('template_type', $template->template_type ?? 'message') == 'otp' ? 'selected' : '' }}>OTP</option>
                            </select>
                            <small class="form-text text-muted">
                                Pilih "OTP" untuk template kode OTP. Variabel <code>@{{kode_otp}}</code> akan otomatis tersedia.
                            </small>
                            @error('template_type')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="content">{{ __('Template Content') }} <span class="text-danger">*</span></label>
                            <textarea 
                                class="form-control @error('content') is-invalid @enderror" 
                                id="content" 
                                name="content" 
                                rows="8" 
                                required 
                                placeholder="Masukkan template pesan Anda di sini...">{{ old('content', $template->content) }}</textarea>
                            <small class="form-text text-muted">
                                Gunakan <code>@{{variable_name}}</code> untuk menyisipkan konten dinamis. Untuk template OTP, gunakan <code>@{{kode_otp}}</code>.
                            </small>
                            @error('content')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="variables">{{ __('Variables') }}</label>
                            <input type="text" 
                                   class="form-control @error('variables') is-invalid @enderror" 
                                   id="variables" 
                                   name="variables" 
                                   value="{{ old('variables', is_array($template->variables) ? implode(', ', $template->variables) : '') }}" 
                                   placeholder="Contoh: name, order_id, amount (dipisahkan koma)">
                            <small class="form-text text-muted">
                                Masukkan nama variabel yang dipisahkan dengan koma
                            </small>
                            @error('variables')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" 
                                       class="custom-control-input" 
                                       id="is_active" 
                                       name="is_active" 
                                       value="1" 
                                       {{ old('is_active', $template->is_active) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_active">
                                    {{ __('Active') }}
                                </label>
                            </div>
                            <small class="form-text text-muted">
                                Hanya template yang aktif yang dapat digunakan untuk mengirim pesan
                            </small>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> {{ __('Update Template') }}
                            </button>
                            <a href="{{ route('templates.index') }}" class="btn btn-secondary">
                                {{ __('Cancel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

