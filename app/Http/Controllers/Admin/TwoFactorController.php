<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class TwoFactorController extends Controller
{
    public function sendCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !password_verify($request->password, $user->password)) {
            return back()->withErrors(['email' => 'Invalid credentials']);
        }

        if (!$user->is_admin) {
            return back()->withErrors(['email' => 'Only admin users can login here']);
        }

        // Generate 6-digit code
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Generate unique token for direct login link
        $token = Str::random(64);

        // Save to database (expires in 10 minutes)
        $user->update([
            'two_factor_code' => $code,
            'two_factor_expires_at' => now()->addMinutes(10),
            'two_factor_token' => $token,
        ]);

        // Send email
        try {
            Mail::send([], [], function ($message) use ($user, $code, $token) {
                $loginLink = url("/admin/2fa/verify-token/{$token}");
                
                $htmlContent = "
                    <h2>Admin Login Verification</h2>
                    <p>Hello {$user->name},</p>
                    <p>Your admin login verification code is:</p>
                    <h1 style='font-size: 32px; color: #3b82f6; letter-spacing: 5px;'>{$code}</h1>
                    <p>This code will expire in 10 minutes.</p>
                    <p>Alternatively, you can click the link below to login directly:</p>
                    <p><a href='{$loginLink}' style='background: #3b82f6; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; display: inline-block;'>Login to Admin Panel</a></p>
                    <p>If you didn't request this, please ignore this email.</p>
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

        // Store email in session for verification page
        session(['2fa_email' => $user->email]);

        return redirect()->route('admin.2fa.verify');
    }

    public function showVerifyForm()
    {
        if (!session('2fa_email')) {
            return redirect('/admin/login');
        }

        return view('admin.2fa-verify');
    }

    public function verifyCode(Request $request)
    {
        $request->validate([
            'code' => 'required|digits:6',
        ]);

        $email = session('2fa_email');
        if (!$email) {
            return redirect('/admin/login')->withErrors(['code' => 'Session expired']);
        }

        $user = User::where('email', $email)
            ->where('two_factor_code', $request->code)
            ->where('two_factor_expires_at', '>', now())
            ->first();

        if (!$user) {
            return back()->withErrors(['code' => 'Invalid or expired code']);
        }

        // Clear 2FA data
        $user->update([
            'two_factor_code' => null,
            'two_factor_expires_at' => null,
            'two_factor_token' => null,
        ]);

        // Login user
        auth()->login($user);
        session()->forget('2fa_email');
        session()->regenerate();

        return redirect('/admin');
    }

    public function verifyToken($token)
    {
        $user = User::where('two_factor_token', $token)
            ->where('two_factor_expires_at', '>', now())
            ->where('is_admin', true)
            ->first();

        if (!$user) {
            return redirect('/admin/login')->withErrors(['email' => 'Invalid or expired login link']);
        }

        // Clear 2FA data
        $user->update([
            'two_factor_code' => null,
            'two_factor_expires_at' => null,
            'two_factor_token' => null,
        ]);

        // Login user
        auth()->login($user);
        session()->regenerate();

        return redirect('/admin');
    }
}
