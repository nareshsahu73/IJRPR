<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class StaffPasswordResetController extends Controller
{
    public function send(Request $request)
    {
        if (!auth()->user()?->is_admin) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $user = User::where('id', $request->user_id)
            ->where('is_staff', 1)
            ->first();

        if (!$user) {
            return response()->json(['message' => 'Staff user not found.'], 404);
        }

        // Generate one-time reset token
        $token = Str::random(64);
        DB::table('password_reset_tokens')->where('email', $user->email)->delete();
        DB::table('password_reset_tokens')->insert([
            'email'      => $user->email,
            'token'      => $token,
            'created_at' => now(),
        ]);

        $resetLink = route('admin.password.reset.form', [
            'token' => $token,
            'email' => $user->email,
        ]);

        // Get all admin emails from DB dynamically
        $adminEmails = User::where('is_admin', 1)->pluck('email')->toArray();

        // To: staff user, CC: all admins
        $primaryEmail = $user->email;
        $ccEmails     = array_diff($adminEmails, [$user->email]);

        try {
            Mail::send([], [], function ($message) use ($user, $resetLink, $primaryEmail, $ccEmails) {
                $html = "
                    <h2>Password Reset Request — IJRPR</h2>
                    <p>Hello <strong>{$user->name}</strong>,</p>
                    <p>An administrator has triggered a password reset for your account.</p>
                    <p>Click the button below to set a new password:</p>
                    <p>
                        <a href='{$resetLink}'
                           style='background:#1e40af;color:white;padding:12px 24px;
                                  text-decoration:none;border-radius:6px;
                                  display:inline-block;font-weight:bold;'>
                            Reset Password
                        </a>
                    </p>
                    <p>Or copy this link:<br><a href='{$resetLink}'>{$resetLink}</a></p>
                    <p>This link expires in <strong>60 minutes</strong>.</p>
                    <p>If you did not expect this, please contact your administrator immediately.</p>
                    <br><p>IJRPR Admin System</p>
                ";

                $message->to($primaryEmail, $user->name)
                    ->subject('Password Reset — IJRPR Account')
                    ->html($html);

                foreach ($ccEmails as $cc) {
                    $message->cc($cc);
                }
            });

            return response()->json([
                'message' => '✅ Reset link sent to ' . $user->email . '. Admins notified via CC.',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => '❌ Failed to send: ' . $e->getMessage(),
            ], 500);
        }
    }
}
