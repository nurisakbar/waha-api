<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="@yield('meta_description', '')">
    <meta name="author" content="@yield('meta_author', '')">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'WAHA SaaS') }} - @yield('title', 'Auth')</title>

    <!-- Custom fonts for this template-->
    <link href="{{ asset('startbootstrap/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet"
        type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="{{ asset('startbootstrap/css/sb-admin-2.min.css') }}" rel="stylesheet">

    @stack('styles')
</head>

<body>
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="position: fixed; top: 20px; left: 50%; transform: translateX(-50%); z-index: 9999; min-width: 300px;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if (session('error') || (isset($errors) && $errors->any()))
        <div class="alert alert-danger alert-dismissible fade show" role="alert" style="position: fixed; top: 20px; left: 50%; transform: translateX(-50%); z-index: 9999; min-width: 300px;">
            <i class="fas fa-exclamation-triangle"></i>
            @if (session('error'))
                {{ session('error') }}
            @else
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @yield('content')

    <!-- Bootstrap core JavaScript-->
    <script src="{{ asset('startbootstrap/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('startbootstrap/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <!-- Core plugin JavaScript-->
    <script src="{{ asset('startbootstrap/vendor/jquery-easing/jquery.easing.min.js') }}"></script>

    <!-- Custom scripts for all pages-->
    <script src="{{ asset('startbootstrap/js/sb-admin-2.min.js') }}"></script>

    @stack('scripts')

</body>

</html>




