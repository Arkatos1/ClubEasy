<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminAccess
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Allow access to login pages for everyone
        if ($request->is(config('admin.route.prefix') . '/auth/login') ||
            $request->is(config('admin.route.prefix') . '/auth/logout')) {
            return $next($request);
        }

        // Check if user is authenticated and is administrator for all other routes
        if (!auth()->check()) {
            // Redirect to Open Admin login page if not authenticated
            return redirect()->route('admin.login');
        }

        if (!auth()->user()->hasRole('administrator')) {
            abort(403, 'Unauthorized access. Administrator privileges required.');
        }

        return $next($request);
    }
}
