@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <img src="/upload/avatars/{{$user->avatar}}" alt="logo" style="width: 140px; height:140px; float:left;border-radius:50%;">
            <h2>{{ $user->name}} Profile</h2>
            <form enctype="multipart/form-data" action="/profile" method="POST">
            <label for=""> Update Profile Image</label>
            <input type="file" name="avatar">
            <input type="hidden" name="_token" value="{{csrf_token()}}">
            <input type="submit">
        </form>
            </div>
        </div>
    </div>
</div>
@endsection