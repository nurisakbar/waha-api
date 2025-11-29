<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'WAHA Gateway - WhatsApp API untuk Developer')</title>
    <meta name="description" content="Platform WhatsApp Gateway API yang powerful untuk developer. Kirim pesan, kelola session, dan integrasikan WhatsApp ke aplikasi Anda dengan mudah.">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        
        .navbar {
            background: rgba(255, 255, 255, 0.98);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .hero-section {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 120px 0 100px;
            margin-top: 76px;
        }
        
        .feature-card {
            transition: all 0.3s ease;
            border: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            height: 100%;
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(16, 185, 129, 0.2);
        }
        
        .feature-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 28px;
            margin-bottom: 20px;
        }
        
        .pricing-card {
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
            height: 100%;
        }
        
        .pricing-card:hover {
            border-color: #10b981;
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(16, 185, 129, 0.15);
        }
        
        .pricing-card.featured {
            border-color: #10b981;
            border-width: 3px;
            position: relative;
        }
        
        .pricing-card.featured::before {
            content: 'POPULER';
            position: absolute;
            top: -15px;
            left: 50%;
            transform: translateX(-50%);
            background: #10b981;
            color: white;
            padding: 5px 20px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .code-block {
            background: #1e1e1e;
            color: #d4d4d4;
            padding: 25px;
            border-radius: 10px;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            overflow-x: auto;
            border: 1px solid #333;
        }
        
        .code-block .keyword { color: #569cd6; }
        .code-block .string { color: #ce9178; }
        .code-block .number { color: #b5cea8; }
        .code-block .comment { color: #6a9955; }
        
        .section {
            padding: 80px 0;
        }
        
        .step-number {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 32px;
            font-weight: bold;
            margin: 0 auto 20px;
        }
        
        footer {
            background: #1a1a1a;
            color: white;
            padding: 60px 0 30px;
        }
        
        footer a {
            color: #adb5bd;
            text-decoration: none;
            transition: color 0.3s;
        }
        
        footer a:hover {
            color: white;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border: none;
            padding: 12px 30px;
            font-weight: 600;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(16, 185, 129, 0.4);
        }
        
        @media (max-width: 768px) {
            .hero-section {
                padding: 80px 0 60px;
                margin-top: 56px;
            }
            
            .section {
                padding: 60px 0;
            }
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold fs-4" href="/">
                <i class="fab fa-whatsapp text-success me-2"></i>
                WAHA Gateway
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link" href="/">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Fitur</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#pricing">Harga</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#demo">Demo</a>
                    </li>
                    @auth
                        <li class="nav-item ms-2">
                            <a class="btn btn-primary text-white" href="{{ route('home') }}">Dashboard</a>
                        </li>
                    @else
                        <li class="nav-item ms-2">
                            <a class="btn btn-primary text-white" href="{{ route('register') }}">Daftar Akun</a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    @yield('content')

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4">
                    <h5 class="mb-3">
                        <i class="fab fa-whatsapp text-success me-2"></i>
                        WAHA Gateway
                    </h5>
                    <p class="text-muted">Platform WhatsApp Gateway API yang powerful untuk developer. Bangun aplikasi luar biasa dengan integrasi WhatsApp.</p>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="mb-3">Produk</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#features">Fitur</a></li>
                        <li class="mb-2"><a href="#pricing">Harga</a></li>
                        <li class="mb-2"><a href="#docs">Dokumentasi</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="mb-3">Perusahaan</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#">Tentang</a></li>
                        <li class="mb-2"><a href="#">Blog</a></li>
                        <li class="mb-2"><a href="#">Kontak</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="mb-3">Legal</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#">Privasi</a></li>
                        <li class="mb-2"><a href="#">Syarat</a></li>
                    </ul>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="mb-3">Terhubung</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#"><i class="fab fa-twitter me-2"></i>Twitter</a></li>
                        <li class="mb-2"><a href="#"><i class="fab fa-github me-2"></i>GitHub</a></li>
                    </ul>
                </div>
            </div>
            <hr class="bg-secondary">
            <div class="row">
                <div class="col-12 text-center">
                    <p class="text-muted mb-0">&copy; {{ date('Y') }} WAHA Gateway. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Smooth Scroll -->
    <script>
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    const offsetTop = target.offsetTop - 80;
                    window.scrollTo({
                        top: offsetTop,
                        behavior: 'smooth'
                    });
                }
            });
        });
    </script>
    
    @stack('scripts')
</body>
</html>
