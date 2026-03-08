<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is not logged in
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login to continue.');
        }

        // Check if user is not admin
        if (!auth()->user()->is_admin) {
            // If it's an AJAX request, return JSON
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Access denied. Admin privileges required.'
                ], 403);
            }

            // For normal requests, redirect to user dashboard with message
            return redirect()->route('dashboard')
                ->with('error', 'Access denied. This area is restricted to administrators only.');
        }

        return $next($request);
    }
}
