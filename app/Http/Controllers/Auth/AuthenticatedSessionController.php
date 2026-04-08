<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Sleep;

class AuthenticatedSessionController extends Controller
{
    public function create()
    {
        return view('auth.login');
    }

    public function store(Request $request)
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Rate limiting — 5 attempts per IP per 15 minutes
        $ipKey = 'login:ip:' . $request->ip();
        if (RateLimiter::tooManyAttempts($ipKey, 5)) {
            $seconds = RateLimiter::availableIn($ipKey);
            return back()->withErrors([
                'email' => 'Too many login attempts. Please try again in ' . ceil($seconds / 60) . ' minute(s).',
            ])->onlyInput('email');
        }

        // reCAPTCHA v2 verification (skip on local environment)
        if (app()->environment('production')) {
            $recaptcha = $request->input('g-recaptcha-response');
            if (!$recaptcha) {
                return back()->withErrors(['g-recaptcha-response' => 'Please complete the reCAPTCHA.'])->onlyInput('email');
            }
            $recaptchaResponse = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret'   => env('RECAPTCHA_V2_SECRET_KEY'),
                'response' => $recaptcha,
                'remoteip' => $request->ip(),
            ]);
            if (!($recaptchaResponse->json()['success'] ?? false)) {
                return back()->withErrors(['g-recaptcha-response' => 'reCAPTCHA verification failed. Please try again.'])->onlyInput('email');
            }
        }

        // Clear any existing session before login
        $request->session()->flush();

        if (Auth::attempt([
            'email'    => $request->email,
            'password' => $request->password,
        ], $request->boolean('remember'))) {

            RateLimiter::clear($ipKey);
            $request->session()->regenerate();

            // Block admin from user login
            if (auth()->user()->is_admin) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()->withErrors([
                    'email' => 'If an account is associated with this email,you will receive a link.',
                ])->onlyInput('email');
            }

            return redirect()->intended('dashboard');
        }

        // Throttle failed attempts — slow down brute force
        RateLimiter::hit($ipKey, 900);
        sleep(1);

        // Generic error — never reveal which field was wrong
        return back()->withErrors([
            'email' => 'If an account is associated with this email,you will receive a link.',
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
