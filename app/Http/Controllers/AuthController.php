<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login()
    {
        return view('login.index');
    }

    public function login_post(Request $request)
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password], true)) {

            if (Auth::user()->is_role == 2) {
                // echo "Super Admin";
                return redirect()->intended('dashboardsuperadmin');
            } else if (Auth::user()->is_role == 1) {
                // echo "Admin";
                return redirect()->intended('dashboardadmin');
            } else if (Auth::user()->is_role == 0) {
                // echo "User";
                return redirect()->intended('dashboardusers');
            } else if (Auth::user()->is_role == 3) {
                // echo "HRD";
                return redirect()->intended('dashboardhr');;
            } else {
                return redirect('login')->with('error', 'No Available Email.. Please Check');
            }
        } else {
            return redirect()->back()->with('error', 'Please enter the correct credentials');
        }
    }

    public function register()
    {
        return view('register.index');
    }

    public function registerpost(Request $request)
    {

        // dd($request->all());
        $user = request()->validate([
            'name' => 'required',
            'email' => 'required|unique:users',
            'password' => 'required|min:6',
            // 'confirm_password' => 'required_with:password|same:password|min:6',
            // 'is_role' => 'required'
        ]);

        $user = new User();
        $user->name = trim($request->name);
        $user->email = trim($request->email);
        $user->password = Hash::make($request->password);
        // $user->is_role = trim($request->is_role);
        $user->remember_token = Str::random(50);
        $user->save();

        return redirect('/')->with('success', 'Register successfully');
    }

    public function logout()
    {
        Auth::logout();
        return redirect(url('/'));
    }

    public function loginuser()
    {
        return view('user');
    }
    public function loginsuperadmin()
    {
        return view('superadmin');
    }
    public function loginadmin()
    {
        return view('admin');
    }
}
