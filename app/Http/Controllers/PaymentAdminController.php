<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
use jeremykenedy\LaravelRoles\Models\Role;
use App\Notifications\PaymentVerifiedNotification;
use App\Notifications\PaymentRejectedNotification;

class PaymentAdminController extends Controller
{
    /**
     * Payment dashboard overview
     */
    public function index(): View
    {
        $pendingCount = User::where('payment_status', 'pending')->count();
        $verifiedCount = User::where('payment_status', 'verified')->count();
        $verifiedThisMonth = User::where('payment_status', 'verified')
            ->whereMonth('payment_verified_at', now()->month)
            ->whereYear('payment_verified_at', now()->year)
            ->count();

        $totalRevenue = User::where('payment_status', 'verified')->count() * 500;

        return view('administration.payments.index', [
            'pendingCount' => $pendingCount,
            'verifiedCount' => $verifiedCount,
            'verifiedThisMonth' => $verifiedThisMonth,
            'totalRevenue' => $totalRevenue,
        ]);
    }

    /**
     * List pending payments
     */
    public function pending(): View
    {
        $pendingUsers = User::where('payment_status', 'pending')
            ->with('roles')
            ->orderBy('payment_submitted_at', 'asc')
            ->get();

        return view('administration.payments.pending', [
            'pendingUsers' => $pendingUsers
        ]);
    }

    /**
     * Verify a payment and grant member role
     */
    public function verifyPayment(Request $request, User $user): RedirectResponse
    {
        // Check if user is actually pending
        if ($user->payment_status !== 'pending') {
            return redirect()->route('administration.payments.pending')
                ->with('error', __('User does not have a pending payment.'));
        }

        try {
            // Only assign member role if user doesn't already have admin or trainer role
            if (!$user->hasRole('administrator') && !$user->hasRole('trainer')) {
                $memberRole = Role::where('name', 'member')->first();
                if ($memberRole) {
                    $user->attachRole($memberRole);
                }
            }

            // Update payment status
            $user->update([
                'payment_status' => 'verified',
                'payment_verified_at' => now()
            ]);

            // Send confirmation email to user
            $user->notify(new PaymentVerifiedNotification());

            Log::info("Payment verified by admin", [
                'admin_id' => auth()->id(),
                'user_id' => $user->id,
                'reference' => $user->payment_reference
            ]);

            return redirect()->route('administration.payments.pending')
                ->with('success', __('Payment verified and member role assigned successfully.'));

        } catch (\Exception $e) {
            Log::error("Payment verification failed", [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->route('administration.payments.pending')
                ->with('error', __('Payment verification failed: ') . $e->getMessage());
        }
    }

    /**
     * Reject a payment
     */
    public function rejectPayment(Request $request, User $user): RedirectResponse
    {
        if ($user->payment_status !== 'pending') {
            return redirect()->route('administration.payments.pending')
                ->with('error', __('User does not have a pending payment.'));
        }

        try {
            $previousReference = $user->payment_reference;

            // Reset payment status but keep the reference for potential reuse
            $user->update([
                'payment_status' => 'cancelled',
                'payment_submitted_at' => null,
                'payment_verified_at' => null
            ]);

            // Send rejection email to user
            $user->notify(new PaymentRejectedNotification($request->reason));

            Log::info("Payment rejected by admin", [
                'admin_id' => auth()->id(),
                'user_id' => $user->id,
                'reference' => $previousReference,
                'reason' => $request->reason
            ]);

            return redirect()->route('administration.payments.pending')
                ->with('success', __('Payment rejected and user notified.'));

        } catch (\Exception $e) {
            Log::error("Payment rejection failed", [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->route('administration.payments.pending')
                ->with('error', __('Payment rejection failed: ') . $e->getMessage());
        }
    }
}
