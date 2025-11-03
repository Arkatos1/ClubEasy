<?php

namespace Modules\Membership\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;

class MembershipController extends Controller
{
    public function index(): View
    {
        return view('membership::index', [
            'user' => \Illuminate\Support\Facades\Auth::user(),
        ]);
    }

    public function plans(): View
    {
        return view('membership::plans');
    }

    public function status(): View
    {
        return view('membership::status', [
            'user' => \Illuminate\Support\Facades\Auth::user(),
        ]);
    }

    public function subscribe(Request $request): RedirectResponse
    {
        // TODO: Integrate with jobmetric/laravel-membership
        return redirect()->route('membership.status')->with('success', 'Membership subscription updated!');
    }
}
