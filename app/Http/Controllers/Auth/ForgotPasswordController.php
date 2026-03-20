<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        // Rate limit: 5 attempts per IP per hour
        $key = 'forgot-password:' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            $minutes = ceil($seconds / 60);
            return back()->withErrors([
                'email' => "Too many password reset attempts. Please try again in {$minutes} minute(s).",
            ]);
        }

        RateLimiter::hit($key, 3600); // 1 hour decay

        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.exists' => 'We could not find a user with that email address.',
        ]);

        $user = User::where('email', $request->email)->first();

        // Generate reset token
        $token = Str::random(64);

        // Delete old tokens for this email
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        // Store new token
        DB::table('password_reset_tokens')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => now(),
        ]);

        // Send email
        try {
            Mail::send([], [], function ($message) use ($user, $token) {
                $resetLink = route('password.reset', ['token' => $token]);

                $htmlContent = "
                    <h2>Password Reset Request</h2>
                    <p>Hello {$user->name},</p>
                    <p>You are receiving this email because we received a password reset request for your account.</p>
                    <p>Click the button below to reset your password:</p>
                    <p><a href='{$resetLink}' style='background: #3b82f6; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; display: inline-block;'>Reset Password</a></p>
                    <p>Or copy and paste this link into your browser:</p>
                    <p>{$resetLink}</p>
                    <p>This password reset link will expire in 60 minutes.</p>
                    <p>If you did not request a password reset, no further action is required.</p>
                    <br>
                    <p>Regards,<br>IJRPR Team</p>
                ";

                $message->to($user->email)
                    ->subject('Reset Password - IJRPR')
                    ->html($htmlContent);
            });
        } catch (\Exception $e) {
            return back()->withErrors(['email' => 'Failed to send reset email. Please try again.']);
        }

        return back()->with('status', 'We have emailed your password reset link!');
    }

    public function showResetForm($token)
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => [
                'required',
                'confirmed',
                'min:12',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*#?&]/',
            ],
        ], [
            'password.min' => 'Password must be at least 12 characters.',
            'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character (!@#$%^&*).',
        ]);

        // Check if token exists and is valid
        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$resetRecord) {
            return back()->withErrors(['email' => 'Invalid reset token.']);
        }

        // Check if token is expired (60 minutes)
        if (now()->diffInMinutes($resetRecord->created_at) > 60) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return back()->withErrors(['email' => 'Reset token has expired. Please request a new one.']);
        }

        // Update password
        $user = User::where('email', $request->email)->first();
        $user->password = bcrypt($request->password);
        $user->save();

        // Delete token
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('status', 'Your password has been reset successfully!');
    }
}
