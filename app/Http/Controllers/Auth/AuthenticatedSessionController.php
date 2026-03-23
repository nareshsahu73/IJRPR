<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    public function create()
    {
        return view('auth.login');
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Clear any existing session before login
        $request->session()->flush();
        
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            // Regenerate session to prevent fixation attacks
            $request->session()->regenerate();
            
            // Check if user is admin - block admin from user login
            if (auth()->user()->is_admin) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                return back()->withErrors([
                    'email' => 'Admin users must login through /myweb page.',
                ])->onlyInput('email');
            }
            
            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
        $request->session()->forget('filament');
        $request->session()->flush();

        cache()->flush();

        return redirect()->route('login');
    }
}
