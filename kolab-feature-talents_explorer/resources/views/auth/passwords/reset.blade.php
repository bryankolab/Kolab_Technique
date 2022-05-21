@extends('layouts.app', ['isAuth' => true])

@section('content')
<div class="login" data-module="login">
    <div class="main-container">
        <div class="logo-wrapper">
            <img src="{{ url('/images/ui/kolab-logo-black.png') }}" class="img-fluid logo is-light" alt="kolab" width="173" height="50">
            <img src="{{ url('/images/ui/kolab-logo.png') }}" class="img-fluid logo is-dark" alt="kolab" width="173" height="50">
        </div>

        <div class="login-wrapper">
            <div class="container-fluid h-100">
                <div class="row h-100 align-items-center">
                    <div class="col-md-7 login__form-col">
                        <p class="login__title">Changer votre mot de passe</p>
                        <form method="POST" action="{{ route('password.update') }}" class="login__form">
                            @csrf

                            <input type="hidden" name="token" value="{{ $token }}">

                            <div class="form-box">
                                <input id="email" type="email" class="form-field @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" placeholder="{{ __('Votre adresse mail') }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback error-message" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror

                                <span class="form-icon email-icon"></span>
                            </div>

                            <div class="form-box">
                                <input id="password" type="password" class="form-field js-password-field @error('password') is-invalid @enderror" name="password" placeholder="{{ __('Nouveau mot de passe') }}" required autocomplete="new-password">

                                @error('password')
                                    <span class="invalid-feedback error-message" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror

                                <span class="form-icon see-icon js-see-pwd"><span class="sr-only">Voir</span></span>
                            </div>

                            <div class="form-box">
                                <input id="password-confirm" type="password" class="form-field js-password-field" name="password_confirmation" placeholder="{{ __('Confirmation mot de passe') }}" required autocomplete="new-password">

                                <span class="form-icon see-icon js-see-pwd"><span class="sr-only">Voir</span></span>
                            </div>
                            
                            <div class="text-right">
                                <button type="submit" class="main-button">{{ __('Réinitialiser le mot de passe') }}</button>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-5 login__image" style="background-image: url({{ url('/images/kolab-login.jpg') }});"></div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- <div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Reset Password') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf

                        <input type="hidden" name="token" value="{{ $token }}">

                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-right">{{ __('Confirm Password') }}</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Reset Password') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div> --}}
@endsection
