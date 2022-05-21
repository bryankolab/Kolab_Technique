@extends('layouts.app')

@section('bodyClass'){!! '' !!}@endsection

@section('content')

<div class="main-container-wrapper">

	<Projects
		:user="{{ Auth::user() }}"
	/>

</div>

@endsection
