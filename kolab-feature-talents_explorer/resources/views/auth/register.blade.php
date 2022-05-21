@extends('layouts.app', ['isAuth' => true])

@section('content')
<div class="login" data-module="login">
    <div class="main-container">
        <div class="logo-wrapper">
            <img src="{{ url('/images/ui/kolab-logo-black.png') }}" class="img-fluid logo is-theme-light" alt="kolab" width="110" height="36">
            <img src="{{ url('/images/ui/kolab-logo.png') }}" class="img-fluid logo is-theme-dark" alt="kolab" width="110" height="36">
        </div>

        <div class="login-wrapper">
            <div class="container-fluid h-100">
                <div class="row h-100 align-items-center">
                    <div class="col-md-7 login__form-col">
                        <a href="{{ route('login') }}" class="action-link mb-20 is-left"><span class="icon icon-long-arrow"></span> {{ __('Retour vers la connexion') }}</a>

                        <p class="login__title">{{ __('Créer votre compte') }}</p>
                        
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                <p class="text c-main-grey">{{ session('status') }}</p>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('register') }}" class="login__form">
                            @csrf

                            <div class="form-box">
                                <input id="name" type="text" class="form-field @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" placeholder="{{ __('Votre nom') }}" required autocomplete="name" autofocus>

                                @error('name')
                                    <span class="invalid-feedback error-message" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-box">
                                <input id="email" type="email" class="form-field @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" placeholder="{{ __('Votre adresse mail') }}" required autocomplete="email">

                                @error('email')
                                    <span class="invalid-feedback error-message" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-box">
                                <input id="password" type="password" class="form-field @error('password') is-invalid @enderror" name="password" placeholder="{{ __('Mot de passe') }}" required autocomplete="new-password">

                                @error('password')
                                    <span class="invalid-feedback error-message" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-box">
                                <input id="password-confirm" type="password" class="form-field" name="password_confirmation" placeholder="{{ __('Confirmation du mot de passe') }}" required autocomplete="new-password">
                            </div>
                            
                            <div class="text-right">
                                <button type="submit" class="button is-gradient">{{ __('Créer votre compte') }}</button>
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
                <div class="card-header">{{ __('Register') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf


                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">

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
                                    {{ __('Register') }}
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
