@extends('layouts.base')

@section('title', 'Message Pricing Settings')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-dollar-sign"></i> Message Pricing Settings
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

                    <form method="POST" action="{{ route('admin.pricing.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="text_with_watermark_price" class="form-label">
                                        <i class="fas fa-tag"></i> Text Message with Watermark Price (IDR)
                                        <small class="text-muted">(Gratis = 0)</small>
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                        <input type="number" 
                                               class="form-control @error('text_with_watermark_price') is-invalid @enderror" 
                                               id="text_with_watermark_price" 
                                               name="text_with_watermark_price" 
                                               value="{{ old('text_with_watermark_price', $pricing->text_with_watermark_price) }}" 
                                               step="0.01" 
                                               min="0" 
                                               required>
                                    </div>
                                    @error('text_with_watermark_price')
                                        <span class="invalid-feedback d-block" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Harga untuk pesan text dengan watermark. Biasanya gratis (0).
                                    </small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="text_without_watermark_price" class="form-label">
                                        <i class="fas fa-tag"></i> Text Message without Watermark Price (IDR)
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                        <input type="number" 
                                               class="form-control @error('text_without_watermark_price') is-invalid @enderror" 
                                               id="text_without_watermark_price" 
                                               name="text_without_watermark_price" 
                                               value="{{ old('text_without_watermark_price', $pricing->text_without_watermark_price) }}" 
                                               step="0.01" 
                                               min="0" 
                                               required>
                                    </div>
                                    @error('text_without_watermark_price')
                                        <span class="invalid-feedback d-block" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Harga untuk pesan text tanpa watermark (premium).
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="multimedia_price" class="form-label">
                                        <i class="fas fa-tag"></i> Multimedia Message Price (IDR)
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                        <input type="number" 
                                               class="form-control @error('multimedia_price') is-invalid @enderror" 
                                               id="multimedia_price" 
                                               name="multimedia_price" 
                                               value="{{ old('multimedia_price', $pricing->multimedia_price) }}" 
                                               step="0.01" 
                                               min="0" 
                                               required>
                                    </div>
                                    @error('multimedia_price')
                                        <span class="invalid-feedback d-block" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Harga untuk pesan multimedia (image, document, video, dll).
                                    </small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="watermark_text" class="form-label">
                                        <i class="fas fa-water"></i> Watermark Text
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('watermark_text') is-invalid @enderror" 
                                           id="watermark_text" 
                                           name="watermark_text" 
                                           value="{{ old('watermark_text', $pricing->watermark_text) }}" 
                                           maxlength="255" 
                                           required>
                                    @error('watermark_text')
                                        <span class="invalid-feedback d-block" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Text watermark yang akan ditambahkan ke pesan gratis.
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="form-check">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="is_active" 
                                       name="is_active" 
                                       value="1" 
                                       {{ old('is_active', $pricing->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    <strong>Active</strong>
                                </label>
                            </div>
                            <small class="form-text text-muted">
                                Centang untuk mengaktifkan pricing ini. Jika tidak dicentang, pricing tidak akan digunakan.
                            </small>
                        </div>

                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Pricing Settings
                            </button>
                            <a href="{{ route('home') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Dashboard
                            </a>
                        </div>
                    </form>

                    <hr class="my-4">

                    <div class="alert alert-info">
                        <h6 class="alert-heading"><i class="fas fa-info-circle"></i> Pricing Information</h6>
                        <ul class="mb-0">
                            <li><strong>Text with Watermark:</strong> Pesan text akan ditambahkan watermark di akhir pesan. Biasanya gratis.</li>
                            <li><strong>Text without Watermark:</strong> Pesan text premium tanpa watermark. User akan dikenakan biaya.</li>
                            <li><strong>Multimedia:</strong> Semua pesan non-text (image, document, video, dll) akan dikenakan biaya ini.</li>
                            <li><strong>Watermark Text:</strong> Text yang akan ditambahkan ke pesan gratis, contoh: "Sent via WAHA SaaS"</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

