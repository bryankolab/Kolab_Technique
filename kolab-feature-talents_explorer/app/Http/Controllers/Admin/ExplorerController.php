<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\User;

class ExplorerController extends Controller
{
    //
    public function index()
    {
        return view('pages.explorer.tpl_explorer');
    }

    public function profile($id = null)
    {
        $user = \Auth::user();
        if ($id == null) {
            $userToSee = \Auth::user();
        } else {
            $userToSee = User::find($id);
        }

        return view('pages.explorer.tpl_explorer_profile', compact('user', 'userToSee'));
    }

    public function membership()
    {
        if (\Auth::user()->hasRole('talent')) {
            return view('pages.explorer.tpl_explorer_membership_freelance');
        } else {
            return view('pages.explorer.tpl_explorer_membership_client');
        }
    }

    public function messenger()
    {
        return view('pages.explorer.tpl_explorer_messenger');
    }
}
