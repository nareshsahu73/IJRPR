<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class auth_service extends Controller
{
    public function showForm()
    {
        return view('auth.admin-forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $key = 'admin-forgot-password:' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->withErrors(['email' => 'Too many attempts. Try again in ' . ceil($seconds / 60) . ' minute(s).']);
        }

        RateLimiter::hit($key, 3600);

        // Also rate limit by email
        $emailKey = 'admin-forgot-password-email:' . sha1($request->email);
        if (RateLimiter::tooManyAttempts($emailKey, 5)) {
            $seconds = RateLimiter::availableIn($emailKey);
            return back()->withErrors(['email' => 'Too many reset requests for this email. Try again in ' . ceil($seconds / 60) . ' minute(s).']);
        }
        RateLimiter::hit($emailKey, 3600);

        $request->validate([
            'email' => 'required|email',
        ]);

        // Only allow admin users
        $user = User::where('email', $request->email)
            ->where('is_admin', 1)
            ->first();

        // Always show same message to prevent email enumeration
        if (!$user) {
            return back()->with('status', 'If this email belongs to an admin account, a reset link has been sent.');
        }

        $token = Str::random(64);

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();
        DB::table('password_reset_tokens')->insert([
            'email' => $request->email,
            'token'  => $token,
            'created_at' => now(),
        ]);

        try {
            Mail::send([], [], function ($message) use ($user, $token) {
                $resetLink = route('admin.password.reset.form', ['token' => $token, 'email' => $user->email]);

                $html = "
                    <h2>Admin Password Reset</h2>
                    <p>Hello {$user->name},</p>
                    <p>A password reset was requested for your admin account.</p>
                    <p><a href='{$resetLink}' style='background:#1e40af;color:white;padding:12px 24px;text-decoration:none;border-radius:6px;display:inline-block;'>Reset Password</a></p>
                    <p>Or copy this link: {$resetLink}</p>
                    <p>This link expires in 60 minutes.</p>
                    <p>If you did not request this, ignore this email.</p>
                    <br><p>IJRPR Team</p>
                ";

                $message->to($user->email)
                    ->subject('Admin Password Reset - IJRPR')
                    ->html($html);
            });
        } catch (\Exception $e) {
            return back()->withErrors(['email' => 'Failed to send email. Please try again.']);
        }

        return back()->with('status', 'If this email belongs to an admin account, a reset link has been sent.');
    }

    public function showResetForm(Request $request, $token)
    {
        return view('auth.admin-reset-password', [
            'token' => $token,
            'email' => $request->query('email', ''),
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => [
                'required', 'confirmed', 'min:15',
                'regex:/[a-z]/', 'regex:/[A-Z]/',
                'regex:/[0-9]/', 'regex:/[@$!%*#?&]/',
            ],
        ], [
            'password.min'   => 'Admin password must be at least 15 characters.',
            'password.regex' => 'Must contain uppercase, lowercase, number and special character.',
        ]);

        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$record) {
            return back()->withErrors(['email' => 'Invalid reset token.']);
        }

        if (now()->diffInMinutes($record->created_at) > 60) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return back()->withErrors(['email' => 'Token expired. Please request a new one.']);
        }

        $user = User::where('email', $request->email)->where('is_admin', 1)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Unauthorized.']);
        }

        $user->password = bcrypt($request->password);
        $user->save();

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('admin.login')->with('status', 'Password reset successfully. Please login.');
    }
}
