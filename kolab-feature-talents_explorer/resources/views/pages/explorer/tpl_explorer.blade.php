@extends('layouts.app')
@section('bodyClass'){!! '' !!}@endsection

@section('content')

    <div class="main-container-wrapper" style="background: #150c2d; padding: 0;">
        @if(Auth::user()->is_explorer)
            <explorer
                :user="{{ Auth::user() }}"
                :user_role="{{ Auth::user()->role }}"
            />
        @else
            <explorer-home :user="{{ Auth::user() }}"/>
        @endif
    </div>
@endsection
