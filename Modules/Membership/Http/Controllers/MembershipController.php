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
use App\Notifications\PaymentReceivedNotification;
use App\Notifications\PaymentConfirmationPendingNotification;
use App\Models\User;

class MembershipController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        // Generate or reuse payment reference
        $paymentReference = $user->payment_reference ?? $this->generateCzechReference($user->id);

        // Store reference if new
        if (!$user->payment_reference) {
            $user->update(['payment_reference' => $paymentReference]);
        }

        $amount = '500.00'; // Fixed amount to 500.00

        // Get from .env
        $iban = env('CZECH_IBAN', 'CZ5855000000001265098001');
        $beneficiary = env('CZECH_BENEFICIARY', 'Sports Club s.r.o.');

        // Generate Czech QR Platba
        $qrPlatbaData = $this->generateQRPlatbaData(
            amount: $amount,
            currency: 'CZK',
            reference: $paymentReference,
            message: 'Sports Club Membership',
            beneficiary: $beneficiary
        );

        $isValidFormat = $this->validateQRPlatbaFormat($qrPlatbaData);

        $qrCode = QrCode::size(220)
            ->backgroundColor(255, 255, 255)
            ->color(0, 0, 0)
            ->margin(2)
            ->generate($qrPlatbaData);

        return view('membership::index', [
            'user' => $user,
            'qrCode' => $qrCode,
            'paymentReference' => $paymentReference,
            'amount' => $amount,
            'currency' => 'CZK',
            'account' => $iban,
            'bank' => 'Your Bank',
            'beneficiary' => $beneficiary,
            'qrData' => $qrPlatbaData,
            'isValidFormat' => $isValidFormat,
            'paymentVerified' => !is_null($user->payment_verified_at),
            'paymentStatus' => $user->payment_status
        ]);
    }

    /**
     * Generate Czech QR Platba format - CORRECTED IMPLEMENTATION
     * Official spec: https://qr-platba.cz/pro-vyvojare/
     */
    private function generateQRPlatbaData(string $amount, string $currency = 'CZK', string $reference = '', string $message = '', string $beneficiary = ''): string
    {
        $components = [];
        $components[] = "SPD";
        $components[] = "1.0";

        // Account - MUST be IBAN format for Czech banks
        // Using test IBAN from official specification: CZ5855000000001265098001
        $components[] = "ACC:CZ5855000000001265098001";

        // Amount - format correctly with 2 decimal places
        $components[] = "AM:" . number_format(floatval($amount), 2, '.', '');

        // Currency
        $components[] = "CC:" . $currency;

        // Use X-VS for Czech variable symbol (instead of RF)
        if (!empty($reference)) {
            // Ensure reference is max 10 digits for Czech banks
            $reference = substr(preg_replace('/[^0-9]/', '', $reference), 0, 10);
            if (!empty($reference)) {
                $components[] = "X-VS:" . $reference;
            }
        }

        // Beneficiary name (optional but recommended)
        if (!empty($beneficiary)) {
            // Remove special characters and limit length
            $beneficiary = preg_replace('/[^*A-Z0-9 \-\/]/', '', strtoupper($beneficiary));
            $components[] = "RN:" . substr($beneficiary, 0, 35);
        }

        // Message (optional)
        if (!empty($message)) {
            // Remove special characters and limit length
            $message = preg_replace('/[^*A-Z0-9 \-\/]/', '', strtoupper($message));
            $components[] = "MSG:" . substr($message, 0, 60);
        }

        // Generate CRC32 checksum - CORRECTED: calculate without final asterisk
        $dataString = implode('*', $components);
        $crc = $this->calculateCRC32($dataString);
        $components[] = "CRC32:" . $crc;

        return implode('*', $components);
    }

    /**
     * Calculate CRC32 checksum according to QR Platba specification
     */
    private function calculateCRC32(string $data): string
    {
        $crc = crc32($data);

        // Handle negative CRC values on 32-bit systems
        if ($crc & 0x80000000) {
            $crc ^= 0xffffffff;
            $crc += 1;
        }

        return strtoupper(str_pad(dechex($crc), 8, '0', STR_PAD_LEFT));
    }

    /**
     * Validate QR Platba format (basic validation)
     */
    private function validateQRPlatbaFormat(string $qrData): bool
    {
        // Basic validation checks
        $checks = [
            str_starts_with($qrData, 'SPD*1.0*'),
            str_contains($qrData, 'ACC:'),
            str_contains($qrData, 'AM:'),
            str_contains($qrData, 'CC:CZK'),
            str_contains($qrData, 'CRC32:'),
            strlen($qrData) <= 512 // Reasonable length limit
        ];

        return !in_array(false, $checks);
    }

    /**
     * Generate proper Czech payment reference (max 10 digits)
     */
    private function generateCzechReference(int $userId): string
    {
        // Format: user ID + random digits (max 10 digits total)
        $base = str_pad($userId, 6, '0', STR_PAD_LEFT);
        $random = mt_rand(1000, 9999);

        $reference = $base . $random;

        // Ensure max 10 digits
        if (strlen($reference) > 10) {
            $reference = substr($reference, 0, 10);
        }

        return $reference;
    }

    /**
     * Show payment confirmation page
     */
    public function confirmPayment(): View
    {
        $user = Auth::user();

        // Generate payment reference if missing
        if (!$user->payment_reference) {
            $paymentReference = $this->generateCzechReference($user->id);
            $user->update(['payment_reference' => $paymentReference]);
        }

        return view('membership::confirm-payment', [
            'user' => $user,
            'reference' => $user->payment_reference,
            'amount' => '500.00',
            'account' => env('CZECH_IBAN', 'CZ5855000000001265098001'),
            'beneficiary' => env('CZECH_BENEFICIARY', 'Sports Club s.r.o.')
        ]);
    }

    /**
     * Process payment confirmation - NEW VERSION
     */
/**
 * Process payment confirmation - DEBUG VERSION
 */
public function processConfirmation(Request $request): RedirectResponse
{
    \Log::info("=== PROCESS CONFIRMATION STARTED ===");
    $user = Auth::user();
    \Log::info("User ID: " . $user->id . ", Email: " . $user->email);

    // Check if already a member
    if ($user->hasRole('member')) {
        \Log::info("User already member - aborting");
        return redirect()->route('membership.index')
            ->with('error', 'Již jste členem klubu!');
    }

    // Check if payment already pending
    if ($user->payment_status === 'pending') {
        \Log::info("Payment already pending - aborting");
        return redirect()->route('membership.index')
            ->with('info', 'Vaše platba již čeká na schválení.');
    }

    \Log::info("Current payment_status: " . ($user->payment_status ?? 'NULL'));
    \Log::info("Current payment_reference: " . ($user->payment_reference ?? 'NULL'));

    // Generate payment reference if missing
    if (!$user->payment_reference) {
        $paymentReference = $this->generateCzechReference($user->id);
        \Log::info("Generated new reference: " . $paymentReference);

        // Update user with reference FIRST
        $updateResult = $user->update([
            'payment_reference' => $paymentReference
        ]);
        \Log::info("Reference update result: " . ($updateResult ? 'SUCCESS' : 'FAILED'));

        // Refresh user object to get the new reference
        $user->refresh();
        \Log::info("After refresh - payment_reference: " . ($user->payment_reference ?? 'NULL'));
    }

    // Set payment as pending
    \Log::info("Setting payment as pending...");
    $updateResult = $user->update([
        'payment_status' => 'pending',
        'payment_submitted_at' => now(),
        'payment_verified_at' => null
    ]);

    \Log::info("Pending status update result: " . ($updateResult ? 'SUCCESS' : 'FAILED'));

    // Refresh again to confirm changes
    $user->refresh();
    \Log::info("Final status - payment_status: " . ($user->payment_status ?? 'NULL'));
    \Log::info("Final reference - payment_reference: " . ($user->payment_reference ?? 'NULL'));

    // Get all admin users
    $admins = User::whereHas('roles', function($query) {
        $query->where('slug', 'administrator');
    })->get();

    \Log::info("Found " . $admins->count() . " admins to notify");

    // Send notification to all admins
    foreach ($admins as $admin) {
        try {
            $admin->notify(new PaymentReceivedNotification($user, '500.00'));
            \Log::info("Notification sent to admin: " . $admin->email);
        } catch (\Exception $e) {
            \Log::error("Failed to send notification to " . $admin->email . ": " . $e->getMessage());
        }
    }

    // Send confirmation to the user
    try {
        $user->notify(new PaymentConfirmationPendingNotification());
        \Log::info("Confirmation sent to user: " . $user->email);
    } catch (\Exception $e) {
        \Log::error("Failed to send user confirmation: " . $e->getMessage());
    }

    \Log::info("=== PROCESS CONFIRMATION COMPLETED ===");

    return redirect()->route('membership.index')
        ->with('success', 'Děkujeme! Vaše platba byla nahlášena a čeká na schválení. O aktivaci členství vás budeme informovat.');
}

    /**
     * Cancel pending payment
     */
    public function cancelPayment(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if ($user->payment_status === 'pending') {
            $user->update([
                'payment_status' => 'cancelled',
                'payment_submitted_at' => null
            ]);

            Log::info("Payment cancelled by user", [
                'user_id' => $user->id,
                'reference' => $user->payment_reference
            ]);

            return redirect()->route('membership.index')
                ->with('success', 'Platba byla zrušena. Můžete kdykoliv začít znovu.');
        }

        return redirect()->route('membership.index')
            ->with('error', 'Nemáte žádnou platbu čekající na schválení.');
    }

    /**
     * ADMIN: Check for missing payments (manual process)
     */
    public function adminPaymentCheck(): View
    {
        // This would be protected by admin middleware in real app
        $confirmedUsers = \App\Models\User::whereNotNull('payment_verified_at')
            ->where('payment_status', 'verified')
            ->with('roles')
            ->get();

        return view('membership::admin-payments', [
            'confirmedUsers' => $confirmedUsers
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
        return redirect()->route('membership.status')->with('success', 'Membership subscription updated!');
    }

    public function join(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $memberRole = Role::where('name', 'member')->first();

        if (!$memberRole) {
            return redirect()->route('membership.index')
                ->with('error', 'Membership role not found. Please contact administrator.');
        }

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

        $memberRole = Role::where('name', 'member')->first();

        if (!$memberRole) {
            return redirect()->route('membership.index')
                ->with('error', 'Membership role not found. Please contact administrator.');
        }

        if ($user->hasRole('member')) {
            $user->detachRole($memberRole->id);

            return redirect()->route('membership.index')
                ->with('success', 'Sorry to see you go! Your membership has been cancelled.');
        }

        return redirect()->route('membership.index')
            ->with('error', 'You are not currently a member.');
    }
}
