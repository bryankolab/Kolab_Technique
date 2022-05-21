@extends('layouts.app')

@section('bodyClass'){!! '' !!}@endsection

@section('content')

<div class="main-container-wrapper">

  <Planning
  	:user="{{ Auth::user() }}"
  />

</div>

@endsection
