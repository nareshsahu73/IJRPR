<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class TwoFactorController extends Controller
{
    private function adminPath(): string
    {
        return '/' . env('ADMIN_PANEL_PATH', 'myweb/blue_sky_42');
    }

    public function sendCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Rate limit: 5 attempts per IP per 15 minutes
        $key = 'admin-login:' . $request->ip();
        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = \Illuminate\Support\Facades\RateLimiter::availableIn($key);
            return back()->withErrors(['email' => 'Too many login attempts. Please try again in ' . ceil($seconds / 60) . ' minute(s).']);
        }
        \Illuminate\Support\Facades\RateLimiter::hit($key, 900);

        $user = User::where('email', $request->email)->first();

        if (!$user || !password_verify($request->password, $user->password)) {
            \Illuminate\Support\Facades\RateLimiter::hit($key, 900);
            sleep(1); // Throttle failed attempts
            return back()->withErrors(['email' => 'If an account is associated with this email,you will receive a link.']);
        }

        if (!$user->is_admin && !$user->is_staff) {
            return back()->withErrors(['email' => 'If an account is associated with this email,you will receive a link.']);
        }

        $code  = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $token = Str::random(64);

        $user->update([
            'two_factor_code'       => $code,
            'two_factor_expires_at' => now()->addMinutes(10),
            'two_factor_token'      => $token,
        ]);

        try {
            Mail::send([], [], function ($message) use ($user, $code, $token) {
                $loginLink = url($this->adminPath() . '/2fa/verify-token/' . $token);

                $htmlContent = "
                    <h2>Admin Login Verification</h2>
                    <p>Hello {$user->name},</p>
                    <p>Your admin login verification code is:</p>
                    <h1 style='font-size: 32px; color: #3b82f6; letter-spacing: 5px;'>{$code}</h1>
                    <p>This code will expire in 10 minutes.</p>
                    <br>
                    <p>Regards,<br>IJRPR Team</p>
                ";

                $message->to($user->email)
                    ->subject('Admin Login Verification Code')
                    ->html($htmlContent);
            });
        } catch (\Exception $e) {
            return back()->withErrors(['email' => 'Failed to send verification email']);
        }

        session(['2fa_email' => $user->email]);

        return redirect()->route('admin.2fa.verify');
    }

    public function showVerifyForm()
    {
        if (!session('2fa_email')) {
            return redirect($this->adminPath() . '/login');
        }

        return view('myweb.2fa-verify');
    }

    public function verifyCode(Request $request)
    {
        $request->validate([
            'code' => 'required|digits:6',
        ]);

        $key = 'admin-2fa:' . $request->ip();
        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = \Illuminate\Support\Facades\RateLimiter::availableIn($key);
            return back()->withErrors(['code' => 'Too many attempts. Please try again in ' . ceil($seconds / 60) . ' minute(s).']);
        }
        \Illuminate\Support\Facades\RateLimiter::hit($key, 900);

        $email = session('2fa_email');
        if (!$email) {
            return redirect($this->adminPath() . '/login')->withErrors(['code' => 'Session expired']);
        }

        $user = User::where('email', $email)
            ->where('two_factor_code', $request->code)
            ->where('two_factor_expires_at', '>', now())
            ->first();

        if (!$user) {
            return back()->withErrors(['code' => 'Invalid or expired code']);
        }

        $user->update([
            'two_factor_code'       => null,
            'two_factor_expires_at' => null,
            'two_factor_token'      => null,
        ]);

        auth()->login($user);
        session()->forget('2fa_email');
        session()->regenerate();

        \Illuminate\Support\Facades\RateLimiter::clear('admin-2fa:' . $request->ip());

        return redirect($this->adminPath());
    }

    public function verifyToken($token)
    {
        $user = User::where('two_factor_token', $token)
            ->where('two_factor_expires_at', '>', now())
            ->where(function ($q) {
                $q->where('is_admin', true)->orWhere('is_staff', true);
            })
            ->first();

        if (!$user) {
            return redirect($this->adminPath() . '/login')->withErrors(['email' => 'Invalid or expired login link']);
        }

        $user->update([
            'two_factor_code'       => null,
            'two_factor_expires_at' => null,
            'two_factor_token'      => null,
        ]);

        auth()->login($user);
        session()->regenerate();

        return redirect($this->adminPath());
    }
}
