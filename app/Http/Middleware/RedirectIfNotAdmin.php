<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfNotAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is not logged in - redirect to custom admin login
        if (!auth()->check()) {
            return redirect()->route('admin.login');
        }

        // Check if user is not admin or staff - block normal users from admin panel
        if (!auth()->user()->is_admin && !auth()->user()->is_staff) {
            // Logout the user
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            // Redirect to user login with error
            return redirect('/login')
                ->withErrors(['email' => 'Only admin users can login here. Please use regular login page.']);
        }

        return $next($request);
    }
}
