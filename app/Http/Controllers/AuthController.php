<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function getLogin()
    {
        return view('auth.login');
    }

    public function authenticate(Request $request)
    {
        $this->validate($request, [
            'email'     => 'required|email',
            'password'  => 'required|min:6',
        ]);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password, 'can_login' => 1, 'user_type' => 0, 'status' => 1])) {
            return redirect()->intended('admin');
        }
        return redirect('/login')->with('error', 'Invalid Email or Password');
    }

    public function getLogout()
    {
        if (Auth::check()) {
            Session::flush();
            Auth::logout();
        }
        return redirect('/login');
    }
}
