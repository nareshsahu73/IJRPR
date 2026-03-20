<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is not logged in - redirect to custom admin login
        if (!auth()->check()) {
            return redirect()->route('admin.login')->with('error', 'Please login to continue.');
        }

        // Check if user is not admin or staff - block normal users from admin panel
        if (!auth()->user()->is_admin && !auth()->user()->is_staff) {
            // Logout the user
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            // If it's an AJAX request, return JSON
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Access denied. Only admin users can login here.'
                ], 403);
            }

            // Redirect to user login with error
            return redirect('/login')
                ->withErrors(['email' => 'Only admin users can login here. Please use regular login page.']);
        }

        return $next($request);
    }
}
