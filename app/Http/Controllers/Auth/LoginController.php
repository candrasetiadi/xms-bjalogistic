<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function showLogin()
    {
        if (session('auth_user')) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('username', $request->username)->first();

        if (!$user) {
            return back()->withErrors(['username' => 'Username tidak ditemukan.'])->withInput();
        }

        $valid = false;

        if (password_verify($request->password, $user->password)) {
            $valid = true;
        } elseif ($request->password === $user->password) {
            // Plain-text legacy password — upgrade to bcrypt
            $user->password = bcrypt($request->password);
            $user->save();
            $valid = true;
        }

        if (!$valid) {
            return back()->withErrors(['password' => 'Password salah.'])->withInput();
        }

        session([
            'auth_user' => [
                'id'    => $user->id,
                'name'  => $user->name,
                'role'  => $user->role,
                'color' => $user->color,
            ],
        ]);

        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        $request->session()->forget('auth_user');

        return redirect()->route('login');
    }
}
