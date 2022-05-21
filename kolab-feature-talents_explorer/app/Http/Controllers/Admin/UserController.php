<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Image;

class UserController extends Controller
{
    public function profile(){
        return view('profile', array('user'=> Auth::user() ));
    }
    public function avatarUpdate(Request $request){
        //For handling the user method i create a function

        if ($request->hasFile('avatar')){
            $avatar = $request-> file('avatar');
            $filename = time() . '.' . $avatar->getClientOriginalExtension();
            Image::make($avatar)->resize(300,300)->save(public_path('/upload/avatars/'. $filename));

            $user = Auth::user();
            $user->avatar = $filename;
            $user->save();
        }

        return view('profile',array('user' => Auth::user()));

    }
}
