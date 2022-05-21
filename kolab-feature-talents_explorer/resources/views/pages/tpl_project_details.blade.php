@extends('layouts.app')

@section('bodyClass'){!! '' !!}@endsection

@section('content')

<div class="main-container-wrapper">

	<project-detail
		:user="{{ Auth::user() }}"
		:_project="{{ $project }}"
	/>

</div>

@endsection