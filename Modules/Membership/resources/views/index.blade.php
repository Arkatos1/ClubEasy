@extends('layouts.master')

@section('title', __('Membership Management'))

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h1 class="text-3xl font-bold text-gray-900 mb-6">{{ __('Membership Management') }}</h1>

                @if (session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        {{ session('error') }}
                    </div>
                @endif

                @if (session('info'))
                    <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded mb-4">
                        {{ session('info') }}
                    </div>
                @endif

                <!-- Membership Status -->
                <div class="bg-gray-50 p-6 rounded-lg mb-6">
                    <h2 class="text-xl font-semibold mb-4">{{ __('Your Membership Status') }}</h2>

                    @if(auth()->user()->hasActiveMembership())
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium">
                                    {{ __('Active Member') }}
                                </span>
                                <p class="text-gray-600 mt-2">{{ __('Your membership is active. Thank you for being part of our club!') }}</p>
                                @php $activeMembership = auth()->user()->activeMembership(); @endphp
                                @if($activeMembership && $activeMembership->expires_at)
                                    <p class="text-sm text-gray-500 mt-1">
                                        {{ __('Expires:') }} <strong>{{ $activeMembership->expires_at->format('d.m.Y') }}</strong>
                                    </p>
                                @endif
                            </div>
                        </div>

                    @elseif(auth()->user()->hasPendingMembership())
                        <!-- PENDING PAYMENT STATUS -->
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-sm font-medium">
                                    {{ __('Waiting for Payment Confirmation') }}
                                </span>
                                <p class="text-gray-600 mt-2">
                                    {{ __('Your payment has been submitted and is waiting for administrator approval.') }}
                                </p>
                                @php $pendingMembership = auth()->user()->pendingMembership(); @endphp
                                @if($pendingMembership)
                                    <p class="text-sm text-gray-500 mt-1">
                                        {{ __('Reference number:') }} <strong>{{ $pendingMembership->payment_reference }}</strong>
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        {{ __('Submitted:') }} <strong>{{ $pendingMembership->payment_submitted_at->format('d.m.Y H:i') }}</strong>
                                    </p>
                                @endif
                            </div>
                            <form action="{{ route('membership.cancel-payment') }}" method="POST">
                                @csrf
                                <button type="submit"
                                        class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                                        onclick="return confirm('{{ __('Are you sure you want to cancel your payment?') }}')">
                                    {{ __('Cancel Payment') }}
                                </button>
                            </form>
                        </div>

                    @else
                        <!-- NOT A MEMBER STATUS -->
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-sm font-medium">
                                    {{ __('Not a Member') }}
                                </span>
                                <p class="text-gray-600 mt-2">{{ __('Join our club to access exclusive benefits and features.') }}</p>
                            </div>
                            <button type="button"
                                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                                    onclick="openPaymentModal()">
                                {{ __('Become a Member') }}
                            </button>
                        </div>
                    @endif
                </div>

                <!-- Membership Benefits -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white border border-gray-200 rounded-lg p-6 text-center">
                        <div class="text-3xl mb-4">🏆</div>
                        <h3 class="text-lg font-semibold mb-2">{{ __('Tournament Access') }}</h3>
                        <p class="text-gray-600">{{ __('Participate in exclusive club tournaments') }}</p>
                    </div>
                    <div class="bg-white border border-gray-200 rounded-lg p-6 text-center">
                        <div class="text-3xl mb-4">📅</div>
                        <h3 class="text-lg font-semibold mb-2">{{ __('Event Priority') }}</h3>
                        <p class="text-gray-600">{{ __('Early access to events and training sessions') }}</p>
                    </div>
                    <div class="bg-white border border-gray-200 rounded-lg p-6 text-center">
                        <div class="text-3xl mb-4">🎯</div>
                        <h3 class="text-lg font-semibold mb-2">{{ __('Premium Features') }}</h3>
                        <p class="text-gray-600">{{ __('Access to advanced training resources') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment Modal - ONLY SHOW IF NOT PENDING AND NOT MEMBER -->
@if(!auth()->user()->hasActiveMembership() && !auth()->user()->hasPendingMembership())
<div id="paymentModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 text-center">{{ __('Become a Member') }}</h3>

            <!-- QR Code Payment Section -->
            <div class="mt-4">
                <p class="text-sm text-gray-500 mb-4 text-center">{{ __('Scan the QR code to complete your membership payment') }}</p>

                <div class="bg-white p-4 rounded-lg border border-gray-200 mb-4">
                    <div id="qrCodeContainer" class="flex justify-center">
                        @if(isset($qrCode) && $qrCode)
                            <div class="text-center">
                                {!! $qrCode !!}
                            </div>
                        @else
                            <div class="text-center p-4">
                                <div class="bg-gray-200 w-48 h-48 flex items-center justify-center mx-auto mb-2 rounded">
                                    <span class="text-gray-500 text-sm">{{ __('QR Code Error') }}</span>
                                </div>
                                <p class="text-sm text-gray-600">{{ __('QR code failed to generate') }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="text-left bg-gray-50 p-4 rounded-lg mb-4">
                    <h4 class="font-semibold text-sm mb-2">{{ __('Payment Details') }}:</h4>
                    <p class="text-xs text-gray-600">{{ __('Amount') }}: <span class="font-mono">{{ $amount ?? '500.00' }} {{ $currency ?? 'CZK' }}</span></p>
                    <p class="text-xs text-gray-600">{{ __('Beneficiary') }}: <span class="font-semibold">{{ $beneficiary ?? 'Sports Club s.r.o.' }}</span></p>
                    <p class="text-xs text-gray-600">{{ __('Account') }}: <span class="font-mono select-all">{{ $account ?? 'CZ58 5500 0000 0012 6509 8001' }}</span></p>
                    <p class="text-xs text-gray-600">{{ __('Bank') }}: <span class="font-mono">{{ $bank ?? 'Airbank' }}</span></p>
                    <p class="text-xs text-gray-600 font-mono">{{ __('Variable Symbol') }}: {{ $paymentReference ?? 'N/A' }}</p>
                    <p class="text-xs text-gray-600 font-mono">{{ __('Membership valid until:') }} {{ $expiresAt->format('d.m.Y') }}</p>
                </div>

                <!-- Direct Confirmation Form -->
                <form action="{{ route('membership.confirm-payment') }}" method="POST" id="paymentConfirmationForm">
                    @csrf

                    <!-- Action Buttons -->
                    <div class="flex space-x-3 mt-6">
                        <button type="submit"
                                class="flex-1 bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center">
                            <span class="mr-2">✅</span>
                            {{ __('Confirm Payment') }}
                        </button>
                        <button type="button"
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-3 px-4 rounded-lg transition duration-200"
                                onclick="closePaymentModal()">
                            {{ __('Cancel') }}
                        </button>
                    </div>
                </form>

                <div class="mt-4 text-center">
                    <p class="text-xs text-gray-500">
                        {{ __('After confirmation, administrators will verify your payment within 1-2 business days') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<script>
function openPaymentModal() {
    document.getElementById('paymentModal').classList.remove('hidden');
}

function closePaymentModal() {
    document.getElementById('paymentModal').classList.add('hidden');
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('paymentModal');
    if (event.target === modal) {
        closePaymentModal();
    }
}

// Form submission handling
document.getElementById('paymentConfirmationForm')?.addEventListener('submit', function(e) {
    const checkbox = this.querySelector('input[name="payment_sent"]');
    if (!checkbox.checked) {
        e.preventDefault();
        alert('{{ __("Please confirm that you have sent the payment") }}');
        return false;
    }
});
</script>
@endsection
