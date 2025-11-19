<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Membership;
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
        $pendingCount = Membership::where('status', 'pending')->count();
        $verifiedCount = Membership::where('status', 'active')->count();
        $verifiedThisMonth = Membership::where('status', 'active')
            ->whereMonth('payment_verified_at', now()->month)
            ->whereYear('payment_verified_at', now()->year)
            ->count();

        $totalRevenue = Membership::where('status', 'active')->sum('amount');

        // Get recent payment activity with pagination (10 per page)
        $recentPayments = Membership::with('user')
            ->whereIn('status', ['active', 'cancelled'])
            ->whereNotNull('payment_submitted_at')
            ->orderBy('updated_at', 'desc')
            ->paginate(10);

        return view('administration.payments.index', [
            'pendingCount' => $pendingCount,
            'verifiedCount' => $verifiedCount,
            'verifiedThisMonth' => $verifiedThisMonth,
            'totalRevenue' => $totalRevenue,
            'recentPayments' => $recentPayments
        ]);
    }

    /**
     * List pending payments
     */
    public function pending(): View
    {
        $pendingMemberships = Membership::where('status', 'pending')
            ->with(['user', 'user.roles'])
            ->orderBy('payment_submitted_at', 'asc')
            ->get();

        return view('administration.payments.pending', [
            'pendingMemberships' => $pendingMemberships
        ]);
    }

    /**
     * Verify a payment and grant membership
     *
     * @param Request $request
     * @param Membership $membership
     * @return RedirectResponse
     */
    public function verifyPayment(Request $request, Membership $membership): RedirectResponse
    {
        // Check if membership is actually pending
        if ($membership->status !== 'pending') {
            return redirect()->route('administration.payments.pending')
                ->with('error', __('This membership is not pending approval.'));
        }

        try {
            $user = $membership->user;

            // Only assign member role if user doesn't already have admin or trainer role
            if (!$user->hasRole('administrator') && !$user->hasRole('trainer')) {
                $memberRole = Role::where('name', 'member')->first();
                if ($memberRole) {
                    $user->attachRole($memberRole);
                }
            }

            // Activate the membership
            $membership->activate();

            // Send confirmation email to user
            $user->notify(new PaymentVerifiedNotification());

            Log::info("Payment verified by admin", [
                'admin_id' => auth()->id(),
                'user_id' => $user->id,
                'membership_id' => $membership->id,
                'reference' => $membership->payment_reference
            ]);

            return redirect()->route('administration.payments.pending')
                ->with('success', __('Payment verified and membership activated successfully.'));

        } catch (\Exception $e) {
            Log::error("Payment verification failed", [
                'membership_id' => $membership->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->route('administration.payments.pending')
                ->with('error', __('Payment verification failed: ') . $e->getMessage());
        }
    }

    /**
     * Reject a payment
     *
     * @param Request $request
     * @param Membership $membership
     * @return RedirectResponse
     */
    public function rejectPayment(Request $request, Membership $membership): RedirectResponse
    {
        if ($membership->status !== 'pending') {
            return redirect()->route('administration.payments.pending')
                ->with('error', __('This membership is not pending approval.'));
        }

        try {
            $previousReference = $membership->payment_reference;
            $user = $membership->user;

            // Cancel the membership
            $membership->cancel($request->reason);

            // Send rejection email to user
            $user->notify(new PaymentRejectedNotification($request->reason));

            Log::info("Payment rejected by admin", [
                'admin_id' => auth()->id(),
                'user_id' => $user->id,
                'membership_id' => $membership->id,
                'reference' => $previousReference,
                'reason' => $request->reason
            ]);

            return redirect()->route('administration.payments.pending')
                ->with('success', __('Payment rejected and user notified.'));

        } catch (\Exception $e) {
            Log::error("Payment rejection failed", [
                'membership_id' => $membership->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->route('administration.payments.pending')
                ->with('error', __('Payment rejection failed: ') . $e->getMessage());
        }
    }
}
