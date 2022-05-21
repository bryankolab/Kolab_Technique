@extends('layouts.app')
@section('bodyClass'){!! '' !!}@endsection

@section('content')
    <div class="main-container-wrapper" style="background: #150c2d; padding: 0;">
        <explorer-messenger :user="{{ Auth::user() }}"/>
    </div>
@endsection
