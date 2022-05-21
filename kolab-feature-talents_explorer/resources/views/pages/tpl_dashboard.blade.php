@extends('layouts.app')
@section('bodyClass'){!! '' !!}@endsection

@section('content')

<div class="main-container-wrapper" style="background: #150C2D;">

	<Dashboard
		:user="{{ Auth::user() }}"
	/>

</div>

@endsection