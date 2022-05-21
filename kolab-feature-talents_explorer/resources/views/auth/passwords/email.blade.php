@extends('layouts.app', ['isAuth' => true])

@section('content')
<div class="login" data-module="login">
    <div class="main-container">
        <div class="logo-wrapper">
            <img src="{{ url('/images/ui/kolab-logo-black.svg') }}" class="img-fluid logo is-theme-light" alt="kolab" width="110" height="36">
            <img src="{{ url('/images/ui/kolab-logo.svg') }}" class="img-fluid logo is-theme-dark" alt="kolab" width="110" height="36">
        </div>

        <div class="login-wrapper">
            <div class="container-fluid h-100">
                <div class="row h-100 align-items-center">
                    <div class="col-md-7 login__form-col">
                        <a href="{{ route('login') }}" class="action-link mb-20 is-left"><span class="icon icon-long-arrow"></span> {{ __('Retour vers la connexion') }}</a>

                        <p class="login__title">{{ __('Changer votre mot de passe') }}</p>
                        
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                <p class="text c-main-grey">{{ session('status') }}</p>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('password.email') }}" class="login__form">
                            @csrf

                            <div class="form-box">
                                <input id="email" type="email" class="form-field @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" placeholder="{{ __('Votre adresse mail') }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback error-message" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror

                                <span class="form-icon icon-email"></span>
                            </div>
                            
                            <div class="text-right">
                                <button type="submit" class="button is-gradient">{{ __('Réinitialiser le mot de passe') }}</button>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-5 login__image" style="background-image: url({{ url('/images/kolab-login.jpg') }});"></div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
