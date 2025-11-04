<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        return view('dashboard', [
            'is_admin' => $user->hasRole('administrator'),
            'is_trainer' => $user->hasRole('trainer'),
            'is_user' => $user->hasRole('user'),
            'is_player' => $user->hasRole('player'),
        ]);
    }
}
