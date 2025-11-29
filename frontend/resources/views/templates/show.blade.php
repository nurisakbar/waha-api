@extends('layouts.base')

@section('title', $template->name)

@section('content')
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ $template->name }}</h1>
        <div>
            <a href="{{ route('templates.edit', $template) }}" class="btn btn-sm btn-warning shadow-sm">
                <i class="fas fa-edit fa-sm text-white-50"></i> {{ __('Edit') }}
            </a>
            <a href="{{ route('templates.index') }}" class="btn btn-sm btn-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> {{ __('Back to Templates') }}
            </a>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Template Details -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('Template Content') }}</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="font-weight-bold">{{ __('Content:') }}</label>
                        <div class="p-3 bg-light rounded">
                            <pre class="mb-0" style="white-space: pre-wrap; word-wrap: break-word;">{{ $template->content }}</pre>
                        </div>
                    </div>

                    @if($template->variables && count($template->variables) > 0)
                        <div class="mb-3">
                            <label class="font-weight-bold">{{ __('Variables:') }}</label>
                            <div>
                                @foreach($template->variables as $variable)
                                    <span class="badge badge-primary mr-1 mb-1">@{{ $variable }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label class="font-weight-bold">{{ __('Status:') }}</label>
                        @if($template->is_active)
                            <span class="badge badge-success">{{ __('Active') }}</span>
                        @else
                            <span class="badge badge-secondary">{{ __('Inactive') }}</span>
                        @endif
                    </div>

                    <div class="mb-0">
                        <label class="font-weight-bold">{{ __('Created:') }}</label>
                        <span class="text-muted">{{ $template->created_at->format('d F Y H:i') }}</span>
                    </div>

                    @if($template->updated_at && $template->updated_at != $template->created_at)
                        <div class="mb-0">
                            <label class="font-weight-bold">{{ __('Last Updated:') }}</label>
                            <span class="text-muted">{{ $template->updated_at->format('d F Y H:i') }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Actions & Preview -->
        <div class="col-lg-4">
            <!-- Actions Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('Actions') }}</h6>
                </div>
                <div class="card-body">
                    <a href="{{ route('templates.edit', $template) }}" class="btn btn-block btn-warning mb-2">
                        <i class="fas fa-edit"></i> {{ __('Edit Template') }}
                    </a>
                    <form action="{{ route('templates.destroy', $template) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to delete this template?') }}');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-block btn-danger">
                            <i class="fas fa-trash"></i> {{ __('Delete Template') }}
                        </button>
                    </form>
                </div>
            </div>

            <!-- API Usage Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('API Usage') }}</h6>
                </div>
                <div class="card-body">
                    <p class="text-sm">Untuk menggunakan template ini via API:</p>
                    <div class="bg-dark text-white p-3 rounded" style="font-size: 0.85rem;">
                        <code style="color: #00ff00;">
POST /api/v1/messages<br><br>
{<br>
&nbsp;&nbsp;"device_id": "YOUR_DEVICE_ID",<br>
&nbsp;&nbsp;"template_id": "{{ $template->id }}",<br>
&nbsp;&nbsp;"to": "6281234567890"
@if($template->variables && count($template->variables) > 0)
,<br>
&nbsp;&nbsp;"variables": {<br>
@foreach($template->variables as $index => $variable)
@php
    $valueIndex = $index + 1;
    $valueStr = 'value' . $valueIndex;
@endphp
&nbsp;&nbsp;&nbsp;&nbsp;"{{ $variable }}": "{{ $valueStr }}"{{ $loop->last ? '' : ',' }}<br>
@endforeach
&nbsp;&nbsp;}
@endif
<br>
}
                        </code>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

