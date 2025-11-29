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
use App\Models\Membership;

class MembershipController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        // Get or create payment reference from active/pending membership
        $activeMembership = $user->activeMembership();
        $pendingMembership = $user->pendingMembership();

        $paymentReference = $activeMembership?->payment_reference ??
                           $pendingMembership?->payment_reference ??
                           $this->generateCzechReference($user->id);

        $amount = '500.00';

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
            'bank' => 'AirBank',
            'beneficiary' => $beneficiary,
            'qrData' => $qrPlatbaData,
            'isValidFormat' => $isValidFormat,
            'hasActiveMembership' => $user->hasActiveMembership(),
            'hasPendingMembership' => $user->hasPendingMembership(),
            'expiresAt' => now()->endOfYear()
        ]);
    }

    /**
     * Generate Czech QR Platba format
     */
    private function generateQRPlatbaData(string $amount, string $currency = 'CZK', string $reference = '', string $message = '', string $beneficiary = ''): string
    {
        $components = [];
        $components[] = "SPD";
        $components[] = "1.0";
        $components[] = "ACC:CZ5855000000001265098001";
        $components[] = "AM:" . number_format(floatval($amount), 2, '.', '');
        $components[] = "CC:" . $currency;

        if (!empty($reference)) {
            $reference = substr(preg_replace('/[^0-9]/', '', $reference), 0, 10);
            if (!empty($reference)) {
                $components[] = "X-VS:" . $reference;
            }
        }

        if (!empty($beneficiary)) {
            $beneficiary = preg_replace('/[^*A-Z0-9 \-\/]/', '', strtoupper($beneficiary));
            $components[] = "RN:" . substr($beneficiary, 0, 35);
        }

        if (!empty($message)) {
            $message = preg_replace('/[^*A-Z0-9 \-\/]/', '', strtoupper($message));
            $components[] = "MSG:" . substr($message, 0, 60);
        }

        $dataString = implode('*', $components);
        $crc = $this->calculateCRC32($dataString);
        $components[] = "CRC32:" . $crc;

        return implode('*', $components);
    }

    /**
     * Calculate CRC32 checksum
     */
    private function calculateCRC32(string $data): string
    {
        $crc = crc32($data);

        if ($crc & 0x80000000) {
            $crc ^= 0xffffffff;
            $crc += 1;
        }

        return strtoupper(str_pad(dechex($crc), 8, '0', STR_PAD_LEFT));
    }

    /**
     * Validate QR Platba format
     */
    private function validateQRPlatbaFormat(string $qrData): bool
    {
        $checks = [
            str_starts_with($qrData, 'SPD*1.0*'),
            str_contains($qrData, 'ACC:'),
            str_contains($qrData, 'AM:'),
            str_contains($qrData, 'CC:CZK'),
            str_contains($qrData, 'CRC32:'),
            strlen($qrData) <= 512
        ];

        return !in_array(false, $checks);
    }

    /**
     * Generate Czech payment reference
     */
    private function generateCzechReference(int $userId): string
    {
        $base = str_pad($userId, 6, '0', STR_PAD_LEFT);
        $random = mt_rand(1000, 9999);
        $reference = $base . $random;

        if (strlen($reference) > 10) {
            $reference = substr($reference, 0, 10);
        }

        return $reference;
    }

    /**
     * Process payment confirmation (direct from modal)
     */
    public function processConfirmation(Request $request): RedirectResponse
    {
        $user = Auth::user();

        try {
            // Check if user already has active membership
            if ($user->hasActiveMembership()) {
                return redirect()->route('membership.index')
                    ->with('info', __('Již jste členem klubu! Děkujeme, že jste součástí našeho týmu.'));
            }

            // Check if user has pending membership
            if ($user->hasPendingMembership()) {
                return redirect()->route('membership.index')
                    ->with('info', __('Vaše platba již čeká na schválení. Počkejte prosím na potvrzení administrátora.'));
            }

            // Generate payment reference
            $paymentReference = $this->generateCzechReference($user->id);

            // Create new pending membership
            $membership = Membership::create([
                'user_id' => $user->id,
                'type' => 'premium',
                'status' => 'pending',
                'payment_reference' => $paymentReference,
                'amount' => 500.00,
                'currency' => 'CZK',
                'payment_submitted_at' => now(),
                'transaction_id' => $request->transaction_id,
                'expires_at' => now()->addYear(),
            ]);

            // Notify admins
            $admins = User::whereHas('roles', function($query) {
                $query->where('slug', 'administrator');
            })->get();

            foreach ($admins as $admin) {
                $admin->notify(new PaymentReceivedNotification($user, '500.00', $paymentReference));
            }

            // Notify user
            $user->notify(new PaymentConfirmationPendingNotification($user, $paymentReference));

            return redirect()->route('membership.index')
                ->with('success', __('Děkujeme! Vaše platba byla nahlášena a čeká na schválení. O aktivaci členství vás budeme informovat do 1-2 pracovních dnů.'));

        } catch (\Exception $e) {
            return redirect()->route('membership.index')
                ->with('error', __('Nastala chyba při odesílání potvrzení. Zkuste to prosím znovu.'));
        }
    }

    /**
     * Cancel pending payment
     */
    public function cancelPayment(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $pendingMembership = $user->pendingMembership();

        if ($pendingMembership) {
            $pendingMembership->cancel('Zrušeno uživatelem');

            Log::info("Payment cancelled by user", [
                'user_id' => $user->id,
                'reference' => $pendingMembership->payment_reference
            ]);

            return redirect()->route('membership.index')
                ->with('success', __('Platba byla zrušena. Můžete kdykoliv začít znovu.'));
        }

        return redirect()->route('membership.index')
            ->with('error', __('Nemáte žádnou platbu čekající na schválení.'));
    }

    /**
     * ADMIN: Check for missing payments
     */
    public function adminPaymentCheck(): View
    {
        $confirmedUsers = User::whereHas('memberships', function($query) {
            $query->where('status', 'active');
        })->with('roles')->get();

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
        $isMember = $user->hasActiveMembership();

        return view('membership::status', compact('user', 'isMember'));
    }

    public function subscribe(Request $request): RedirectResponse
    {
        return redirect()->route('membership.status')->with('success', 'Membership subscription updated!');
    }

    public function join(Request $request): RedirectResponse
    {
        $user = Auth::user();

        // Check if already has active membership
        if ($user->hasActiveMembership()) {
            return redirect()->route('membership.index')
                ->with('error', __('Již jste členem.'));
        }

        // Check if already has pending membership
        if ($user->hasPendingMembership()) {
            return redirect()->route('membership.index')
                ->with('info', __('Již máte platbu čekající na schválení.'));
        }

        // For direct flow, we don't redirect to confirmation page anymore
        // Instead, the modal will open with the QR code and confirmation form
        return redirect()->route('membership.index')
            ->with('info', __('Klikněte na tlačítko "Stát se členem" pro dokončení platby.'));
    }

    public function leave(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $activeMembership = $user->activeMembership();

        if ($activeMembership) {
            $activeMembership->cancel('Opustil členství');

            return redirect()->route('membership.index')
                ->with('success', __('Je nám líto, že odcházíte! Vaše členství bylo zrušeno.'));
        }

        return redirect()->route('membership.index')
            ->with('error', __('Momentálně nejste členem.'));
    }

    /**
     * REMOVED: confirmPayment() method since we don't need separate page anymore
     */
}
