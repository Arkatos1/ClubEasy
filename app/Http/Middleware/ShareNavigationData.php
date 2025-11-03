<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ShareNavigationData
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            $isAdmin = $user->hasRole('administrator');
            $isTrainer = $user->hasRole('trainer');
            $isUser = $user->hasRole('user');

            // Share with all views
            view()->share('is_admin', $isAdmin);
            view()->share('is_trainer', $isTrainer);
            view()->share('is_user', $isUser);
            view()->share('user_roles', $user->roles);
        }

        return $next($request);
    }
}
