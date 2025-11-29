@extends('layouts.base')

@section('title', __('Templates'))

@section('content')
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('Message Templates') }}</h1>
        <a href="{{ route('templates.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> {{ __('Create Template') }}
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Content Row -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('Your Templates') }}</h6>
                </div>
                <div class="card-body">
                    @if($templates->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>{{ __('Name') }}</th>
                                        <th>{{ __('Type') }}</th>
                                        <th>{{ __('Content Preview') }}</th>
                                        <th>{{ __('Variables') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Created') }}</th>
                                        <th>{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($templates as $template)
                                    <tr>
                                        <td><strong>{{ $template->name }}</strong></td>
                                        <td>
                                            @if($template->template_type === 'otp')
                                                <span class="badge badge-info">OTP</span>
                                            @else
                                                <span class="badge badge-secondary">Pesan</span>
                                            @endif
                                        </td>
                                        <td>
                                            <small>{{ Str::limit($template->content, 80) }}</small>
                                        </td>
                                        <td>
                                            @if($template->variables && count($template->variables) > 0)
                                                @foreach($template->variables as $variable)
                                                    <span class="badge badge-secondary">{{ $variable }}</span>
                                                @endforeach
                                            @else
                                                <span class="text-muted">{{ __('No variables') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($template->is_active)
                                                <span class="badge badge-success">{{ __('Active') }}</span>
                                            @else
                                                <span class="badge badge-secondary">{{ __('Inactive') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <small>{{ $template->created_at->format('d M Y') }}</small>
                                        </td>
                                        <td>
                                            <a href="{{ route('templates.show', $template) }}" class="btn btn-sm btn-info" title="{{ __('View') }}">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('templates.edit', $template) }}" class="btn btn-sm btn-warning" title="{{ __('Edit') }}">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('templates.destroy', $template) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Are you sure you want to delete this template?') }}');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="{{ __('Delete') }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-3">
                            {{ $templates->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-file-alt fa-3x text-gray-300 mb-3"></i>
                            <p class="text-gray-500">Tidak ada template ditemukan. Buat template pertama Anda untuk memulai.</p>
                            <a href="{{ route('templates.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> {{ __('Create Template') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Info Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Tentang Template</h6>
                </div>
                <div class="card-body">
                    <p class="mb-2">Template pesan membantu Anda mengirim pesan lebih cepat dengan menggunakan konten yang sudah ditulis sebelumnya.</p>
                    <p class="mb-2"><strong>Menggunakan Variabel:</strong></p>
                    <ul>
                        <li>Gunakan <code>@{{variable_name}}</code> dalam template Anda untuk membuat placeholder</li>
                        <li>Contoh: <code>Halo @{{name}}, pesanan Anda @{{order_id}} sudah siap!</code></li>
                        <li>Saat mengirim via API, berikan nilai untuk setiap variabel</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

