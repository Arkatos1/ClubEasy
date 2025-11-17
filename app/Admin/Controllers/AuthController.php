<?php

namespace App\Admin\Controllers;

use OpenAdmin\Admin\Controllers\AuthController as BaseAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends BaseAuthController
{
    /**
     * Handle login - use main app authentication
     */
    public function postLogin(Request $request)
    {
        $credentials = $request->only([$this->username(), 'password']);

        // Use main web guard authentication
        if (Auth::guard('web')->attempt($credentials)) {
            $user = Auth::guard('web')->user();

            // Only allow administrators to access Open Admin
            if ($user->hasRole('administrator')) {
                return redirect()->intended(config('admin.route.prefix'));
            } else {
                Auth::guard('web')->logout();
                return back()->withInput()->withErrors([
                    $this->username() => 'Only administrators can access this area.',
                ]);
            }
        }

        return back()->withInput()->withErrors([
            $this->username() => $this->getFailedLoginMessage(),
        ]);
    }

    /**
     * Logout from both systems
     */
    public function getLogout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        return redirect(config('admin.route.prefix'));
    }

    /**
     * Use email instead of username
     */
    protected function username()
    {
        return 'email';
    }
}
