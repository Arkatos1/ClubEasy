<?php

namespace Modules\Membership\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use jeremykenedy\LaravelRoles\Models\Role;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class MembershipController extends Controller
{
public function index(): View
{
    $user = Auth::user();
    $qrCode = QrCode::size(150)->generate('TEST123');
    $paymentReference = 'TEST-' . time();

    return view('membership::index', [
        'user' => $user,
        'qrCode' => $qrCode,
        'paymentReference' => $paymentReference
    ]);
}

    public function plans(): View
    {
        return view('membership::plans');
    }

    public function status(): View
    {
        $user = Auth::user();
        $isMember = $user->hasRole('member');

        return view('membership::status', compact('user', 'isMember'));
    }

    public function subscribe(Request $request): RedirectResponse
    {
        // TODO: Integrate with jobmetric/laravel-membership
        return redirect()->route('membership.status')->with('success', 'Membership subscription updated!');
    }

    public function join(Request $request): RedirectResponse
    {
        $user = Auth::user();

        // Get the member role by name to ensure we have the correct ID
        $memberRole = Role::where('name', 'member')->first();

        if (!$memberRole) {
            return redirect()->route('membership.index')
                ->with('error', 'Membership role not found. Please contact administrator.');
        }

        // Add membership role to user using the role ID
        if (!$user->hasRole('member')) {
            $user->attachRole($memberRole->id);

            return redirect()->route('membership.index')
                ->with('success', 'Welcome to the club! Your membership has been activated.');
        }

        return redirect()->route('membership.index')
            ->with('error', 'You are already a member.');
    }

    public function leave(Request $request): RedirectResponse
    {
        $user = Auth::user();

        // Get the member role by name
        $memberRole = Role::where('name', 'member')->first();

        if (!$memberRole) {
            return redirect()->route('membership.index')
                ->with('error', 'Membership role not found. Please contact administrator.');
        }

        // Remove membership role from user using role ID
        if ($user->hasRole('member')) {
            $user->detachRole($memberRole->id);

            return redirect()->route('membership.index')
                ->with('success', 'Sorry to see you go! Your membership has been cancelled.');
        }

        return redirect()->route('membership.index')
            ->with('error', 'You are not currently a member.');
    }
}
