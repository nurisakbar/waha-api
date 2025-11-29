@extends('landing.layout')

@section('title', 'WAHA Gateway - WhatsApp API untuk Developer')

@section('content')
<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-4">
                    WhatsApp API Gateway<br>
                    <span class="text-warning">untuk Developer</span>
                </h1>
                <p class="lead mb-4">
                    Integrasikan WhatsApp messaging ke dalam aplikasi Anda dengan API yang powerful dan ramah developer. 
                    Kirim pesan, kelola session, dan bangun pengalaman yang luar biasa.
                </p>
                <div class="d-flex gap-3 flex-wrap mb-4">
                    @auth
                        <a href="{{ route('home') }}" class="btn btn-light btn-lg">
                            <i class="fas fa-tachometer-alt me-2"></i>Ke Dashboard
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="btn btn-light btn-lg">
                            <i class="fas fa-rocket me-2"></i>Mulai Gratis
                        </a>
                        <a href="#demo" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-play-circle me-2"></i>Coba Demo
                        </a>
                    @endauth
                </div>
                <div>
                    <p class="mb-2"><small><i class="fas fa-check-circle me-2"></i>Tidak perlu kartu kredit</small></p>
                    <p class="mb-0"><small><i class="fas fa-check-circle me-2"></i>Paket gratis tersedia</small></p>
                </div>
            </div>
            <div class="col-lg-6 text-center mt-5 mt-lg-0">
                <div class="code-block text-start">
                    <div class="mb-3">
                        <span class="comment"># Kirim pesan WhatsApp via API</span>
                    </div>
                    <div>
                        <span class="keyword">curl</span> -X POST <span class="string">https://api.wahagateway.com/v1/sessions/{id}/messages/text</span><br>
                        &nbsp;&nbsp;-H <span class="string">"X-Api-Key: waha_your_key"</span><br>
                        &nbsp;&nbsp;-H <span class="string">"Content-Type: application/json"</span><br>
                        &nbsp;&nbsp;-d <span class="string">'{</span><br>
                        &nbsp;&nbsp;&nbsp;&nbsp;<span class="string">"to": "6281234567890",</span><br>
                        &nbsp;&nbsp;&nbsp;&nbsp;<span class="string">"message": "Halo dari API!"</span><br>
                        &nbsp;&nbsp;<span class="string">}'</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section id="features" class="section bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-4 fw-bold mb-3">Fitur Unggulan</h2>
            <p class="lead text-muted">Semua yang Anda butuhkan untuk mengintegrasikan WhatsApp ke aplikasi Anda</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card feature-card h-100 p-4">
                    <div class="feature-icon">
                        <i class="fas fa-code"></i>
                    </div>
                    <h4 class="mb-3 fw-bold">API Ramah Developer</h4>
                    <p class="text-muted">
                        RESTful API yang dirancang untuk developer. Dokumentasi lengkap, contoh kode, dan SDK dalam berbagai bahasa pemrograman.
                    </p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card feature-card h-100 p-4">
                    <div class="feature-icon">
                        <i class="fas fa-layer-group"></i>
                    </div>
                    <h4 class="mb-3 fw-bold">Multi Session</h4>
                    <p class="text-muted">
                        Kelola beberapa session WhatsApp secara bersamaan. Sempurna untuk bisnis dengan banyak akun atau departemen.
                    </p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card feature-card h-100 p-4">
                    <div class="feature-icon">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <h4 class="mb-3 fw-bold">Webhook Real-Time</h4>
                    <p class="text-muted">
                        Terima notifikasi instan via webhook saat pesan masuk. Bangun aplikasi reaktif dengan mudah.
                    </p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card feature-card h-100 p-4">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h4 class="mb-3 fw-bold">Aman & Terpercaya</h4>
                    <p class="text-muted">
                        Autentikasi API key, rate limiting, dan SLA uptime 99.9%. Data Anda aman dan API selalu tersedia.
                    </p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card feature-card h-100 p-4">
                    <div class="feature-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h4 class="mb-3 fw-bold">Analitik & Monitoring</h4>
                    <p class="text-muted">
                        Lacak penggunaan API, tingkat pengiriman pesan, dan metrik performa. Buat keputusan berbasis data.
                    </p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card feature-card h-100 p-4">
                    <div class="feature-icon">
                        <i class="fas fa-rocket"></i>
                    </div>
                    <h4 class="mb-3 fw-bold">Integrasi Mudah</h4>
                    <p class="text-muted">
                        Mulai dalam hitungan menit. Autentikasi sederhana, dokumentasi jelas, dan contoh kode untuk integrasi cepat.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- How It Works Section -->
<section class="section">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-4 fw-bold mb-3">Cara Kerja</h2>
            <p class="lead text-muted">Mulai dalam 3 langkah sederhana</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4 text-center">
                <div class="step-number">1</div>
                <h4 class="fw-bold mb-3">Buat Akun</h4>
                <p class="text-muted">Daftar gratis dan dapatkan akses instan ke dashboard Anda.</p>
            </div>
            <div class="col-md-4 text-center">
                <div class="step-number">2</div>
                <h4 class="fw-bold mb-3">Dapatkan API Key</h4>
                <p class="text-muted">Generate API key Anda dan hubungkan session WhatsApp.</p>
            </div>
            <div class="col-md-4 text-center">
                <div class="step-number">3</div>
                <h4 class="fw-bold mb-3">Mulai Membangun</h4>
                <p class="text-muted">Integrasikan API kami ke aplikasi Anda dan mulai kirim pesan.</p>
            </div>
        </div>
    </div>
</section>

<!-- Pricing Section -->
<section id="pricing" class="section bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-4 fw-bold mb-3">Harga Sederhana & Transparan</h2>
            <p class="lead text-muted">Pilih paket yang sesuai dengan kebutuhan Anda</p>
        </div>
        <div class="row g-4 justify-content-center">
            @php
                $plans = \App\Models\Plan::where('is_active', true)->orderBy('sort_order')->get();
            @endphp
            @foreach($plans as $plan)
            <div class="col-lg-3 col-md-6">
                <div class="card pricing-card h-100 {{ $plan->is_featured ? 'featured' : '' }}">
                    <div class="card-body p-4">
                        <h3 class="mb-2 fw-bold">{{ $plan->name }}</h3>
                        <div class="mb-3">
                            @if($plan->price == 0)
                                <span class="display-4 fw-bold">Gratis</span>
                            @else
                                <span class="display-4 fw-bold">Rp{{ number_format($plan->price * 15000, 0, ',', '.') }}</span>
                                <span class="text-muted">/bulan</span>
                            @endif
                        </div>
                        <p class="text-muted mb-4">{{ $plan->description }}</p>
                        <ul class="list-unstyled mb-4">
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                {{ $plan->sessions_limit }} Session{{ $plan->sessions_limit > 1 ? '' : '' }}
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                {{ $plan->messages_per_month ? number_format($plan->messages_per_month) . ' pesan/bulan' : 'Pesan unlimited' }}
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                {{ $plan->api_rate_limit }}/menit API calls
                            </li>
                            @if($plan->features && is_array($plan->features))
                                @foreach($plan->features as $feature)
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    {{ $feature }}
                                </li>
                                @endforeach
                            @endif
                        </ul>
                        @auth
                            <a href="{{ route('billing.index') }}" class="btn btn-primary w-100">
                                @if($plan->price == 0)
                                    Mulai Sekarang
                                @else
                                    Berlangganan
                                @endif
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="btn btn-primary w-100">
                                Mulai Sekarang
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Demo Section -->
<section id="demo" class="section">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-4 fw-bold mb-3">Demo & Dokumentasi</h2>
            <p class="lead text-muted">Lihat bagaimana API kami bekerja dan pelajari cara menggunakannya</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100 p-4 text-center">
                    <i class="fas fa-play-circle text-success mb-3" style="font-size: 3rem;"></i>
                    <h4 class="fw-bold mb-3">Demo Interaktif</h4>
                    <p class="text-muted">Coba fitur-fitur API kami secara langsung dengan demo interaktif.</p>
                    @auth
                        <a href="{{ route('home') }}" class="btn btn-outline-success mt-3">Coba Demo</a>
                    @else
                        <a href="{{ route('register') }}" class="btn btn-outline-success mt-3">Daftar untuk Demo</a>
                    @endauth
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 p-4 text-center">
                    <i class="fas fa-book text-success mb-3" style="font-size: 3rem;"></i>
                    <h4 class="fw-bold mb-3">Referensi API</h4>
                    <p class="text-muted">Dokumentasi API lengkap dengan semua endpoint, parameter, dan format response.</p>
                    @auth
                        <a href="{{ route('api-docs.index') }}" class="btn btn-outline-success mt-3">Lihat Dokumentasi</a>
                    @else
                        <a href="{{ route('register') }}" class="btn btn-outline-success mt-3">Mulai Sekarang</a>
                    @endauth
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 p-4 text-center">
                    <i class="fas fa-code text-success mb-3" style="font-size: 3rem;"></i>
                    <h4 class="fw-bold mb-3">Contoh Kode</h4>
                    <p class="text-muted">Contoh kode siap pakai dalam PHP, JavaScript, Python, dan lainnya.</p>
                    @auth
                        <a href="{{ route('api-docs.index') }}" class="btn btn-outline-success mt-3">Lihat Contoh</a>
                    @else
                        <a href="{{ route('register') }}" class="btn btn-outline-success mt-3">Mulai Sekarang</a>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="section bg-success text-white">
    <div class="container text-center">
        <h2 class="display-5 fw-bold mb-4">Siap untuk Memulai?</h2>
        <p class="lead mb-4">Bergabunglah dengan ribuan developer yang membangun aplikasi luar biasa dengan WhatsApp</p>
        @auth
            <a href="{{ route('home') }}" class="btn btn-light btn-lg">
                <i class="fas fa-tachometer-alt me-2"></i>Ke Dashboard
            </a>
        @else
            <a href="{{ route('register') }}" class="btn btn-light btn-lg me-3">
                <i class="fas fa-rocket me-2"></i>Mulai Trial Gratis
            </a>
            <a href="#demo" class="btn btn-outline-light btn-lg">
                <i class="fas fa-play-circle me-2"></i>Coba Demo
            </a>
        @endauth
    </div>
</section>
@endsection
