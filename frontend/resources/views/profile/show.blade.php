@extends('layouts.base')

@section('title', 'Profile')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Left Column - Profile Settings -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('Profile Settings') }}</h6>
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

                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Name -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">{{ __('Name') }} <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           id="name" 
                                           name="name" 
                                           value="{{ old('name', $user->name) }}" 
                                           required>
                                    @error('name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Email -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">{{ __('Email') }} <span class="text-danger">*</span></label>
                                    <input type="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           id="email" 
                                           name="email" 
                                           value="{{ old('email', $user->email) }}" 
                                           required>
                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Phone -->
                        <div class="form-group">
                            <label for="phone">{{ __('Phone') }}</label>
                            <input type="text" 
                                   class="form-control @error('phone') is-invalid @enderror" 
                                   id="phone" 
                                   name="phone" 
                                   value="{{ old('phone', $user->phone) }}"
                                   placeholder="{{ __('e.g., +6281234567890') }}">
                            @error('phone')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <hr class="my-4">

                        <h6 class="mb-3">{{ __('Change Password') }}</h6>
                        <p class="text-muted small">{{ __('Leave blank if you don\'t want to change your password.') }}</p>

                        <div class="row">
                            <!-- Current Password -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="current_password">{{ __('Current Password') }}</label>
                                    <input type="password" 
                                           class="form-control @error('current_password') is-invalid @enderror" 
                                           id="current_password" 
                                           name="current_password">
                                    @error('current_password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <!-- New Password -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="password">{{ __('New Password') }}</label>
                                    <input type="password" 
                                           class="form-control @error('password') is-invalid @enderror" 
                                           id="password" 
                                           name="password" 
                                           minlength="8">
                                    <small class="form-text text-muted">{{ __('Min. 8 characters') }}</small>
                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Confirm Password -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="password_confirmation">{{ __('Confirm Password') }}</label>
                                    <input type="password" 
                                           class="form-control" 
                                           id="password_confirmation" 
                                           name="password_confirmation" 
                                           minlength="8">
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-0">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-2"></i>{{ __('Update Profile') }}
                            </button>
                            <a href="{{ route('home') }}" class="btn btn-secondary">
                                {{ __('Cancel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Right Column - Account Information -->
        <div class="col-lg-4">
            <!-- Account Information -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('Account Information') }}</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <img class="img-profile rounded-circle mb-3" 
                             src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=4e73df&color=fff&size=128"
                             alt="Profile Picture"
                             style="width: 128px; height: 128px;">
                        <h5 class="mb-1">{{ $user->name }}</h5>
                        <p class="text-muted mb-0">{{ $user->email }}</p>
                    </div>

                    <hr>

                    <div class="mb-3">
                        <div class="small text-gray-500 mb-1">{{ __('Member Since') }}</div>
                        <div class="font-weight-bold">{{ $user->created_at->format('F d, Y') }}</div>
                    </div>

                    @if($user->last_login_at)
                    <div class="mb-3">
                        <div class="small text-gray-500 mb-1">{{ __('Last Login') }}</div>
                        <div class="font-weight-bold">{{ $user->last_login_at->diffForHumans() }}</div>
                    </div>
                    @endif

                    @if($user->subscriptionPlan)
                    <div class="mb-3">
                        <div class="small text-gray-500 mb-1">{{ __('Current Plan') }}</div>
                        <div>
                            <span class="badge badge-primary badge-lg">{{ $user->subscriptionPlan->name }}</span>
                        </div>
                    </div>
                    @endif

                    @if($user->phone)
                    <div class="mb-3">
                        <div class="small text-gray-500 mb-1">{{ __('Phone') }}</div>
                        <div class="font-weight-bold">{{ $user->phone }}</div>
                    </div>
                    @endif

                    @if($user->referral_code)
                    <hr>
                    <div class="mb-3">
                        <div class="small text-gray-500 mb-1">{{ __('Referral Code') }}</div>
                        <div class="font-weight-bold">
                            <code style="font-size: 16px; letter-spacing: 2px;">{{ $user->referral_code }}</code>
                        </div>
                        <small class="text-muted">Bagikan kode ini untuk mendapatkan bonus quota</small>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
