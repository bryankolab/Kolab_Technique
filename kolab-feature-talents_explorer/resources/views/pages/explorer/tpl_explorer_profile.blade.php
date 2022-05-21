@extends('layouts.app')
@section('bodyClass'){!! '' !!}@endsection

@section('content')

    <div class="main-container-wrapper" style="background: #150c2d; padding-top: 50px">
        <explorer-profile
            :user="{{ $user }}"
            :user-to-see="{{ $userToSee }}"
        />

    </div>

@endsection
