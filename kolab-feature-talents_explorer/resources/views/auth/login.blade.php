@extends('layouts.app', ['isAuth' => true])

@section('content')

<div class="login" data-module="login" id="vue__login">
    <div class="main-container">
        <div class="logo-wrapper">
            <img src="{{ url('/images/ui/kolab-logo-black.png') }}" class="img-fluid logo is-theme-light" alt="kolab" width="110" height="36">
            <img src="{{ url('/images/ui/kolab-logo.png') }}" class="img-fluid logo is-theme-dark" alt="kolab" width="110" height="36">
        </div>

        <div class="login-wrapper">
            <div class="container-fluid h-100">
                <div class="row h-100 align-items-center">
                    <div class="col-md-7 login__form-col">
                        <p class="login__title">Hello !</p>
                        <form method="POST" action="{{ route('login') }}" class="login__form js-login-form">
                            @csrf

                            <div class="form-box">
                                <input id="email" type="email" class="form-field @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" placeholder="{{ __('Adresse mail') }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback error-message" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror

                                <span class="form-icon icon-email"></span>
                            </div>
                            <div class="form-box">
                                <input id="password" type="password" class="form-field js-password-field @error('password') is-invalid @enderror" name="password" placeholder="{{ __('Mot de passe') }}" required autocomplete="current-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror

                                {{-- <span class="form-icon see-icon js-see-pwd"><span class="sr-only">Voir</span></span> --}}
                                <span class="form-icon icon-see see-icon js-see-pwd"><span class="sr-only">Voir</span></span>
                            </div>
                            @if (Route::has('password.request'))
                                <div class="text-right">
                                    <a class="forgot-pwd-link" href="{{ route('password.request') }}">
                                        {{ __('Mot de passe oublié ?') }}
                                    </a>
                                </div>
                            @endif
                            <div class="text-right">
                                <button type="submit" class="button is-gradient js-login-button" disabled>{{ __('Connexion') }}</button>
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
