@php setlocale(LC_TIME, 'fr_FR') @endphp

<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <base href="{{ url()->full() }}">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

		<!-- Metas -->
    <title>{{ config('app.name', 'Laravel') }}</title>
    <meta name="description" content="@yield('meta_description')" />

    <link rel="apple-touch-icon" sizes="180x180" href="{{ url('/images/favicon/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ url('/images/favicon/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ url('/images/favicon/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ url('/images/favicon/site.webmanifest') }}">
    <link rel="mask-icon" href="{{ url('/images/favicon/safari-pinned-tab.svg') }}" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#ffffff">

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>

<body class="body theme-dark @yield('bodyClass')" data-mode="{{ config('app.env') }}" data-anim="wrapper">
    <div class="main-wrapper" data-anim="container" id="app" style="background: #150C2D;">
        <main class="js-scroll-container">
        	@if(empty($isAuth))
          @include('partials.header')
        	@endif

        	@yield('content')
        	<Modal></Modal>
        	<Confirm></Confirm>
        </main>
    </div>


    <script>
        @if(!empty(Auth::user()))
            window.userId = {!! Auth::user()->id !!};
        @endif
    </script>
    <script src="{{ mix('js/app.js') }}"></script>
    {{-- <script src="https://momentjs.com/downloads/moment-with-locales.min.js"></script>
    <script>moment.locale('fr');</script> --}}
		<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/i18n/fr.js"></script>
		<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
		<script src="/js/vendor/jquery.ui.widget.js"></script>
		<script src="/js/jquery.iframe-transport.js"></script>
		<script src="/js/jquery.fileupload.js"></script>

		<script>
			@if(!empty(Auth::user()))
				window.axios.defaults.headers.post.user = {!! Auth::user()->id !!};
				Vue.prototype.Global.user_id = window.axios.defaults.headers.post.user;
			@endif
		</script>

    @yield('after_scripts')
		@stack('after_scripts')
</body>
</html>
