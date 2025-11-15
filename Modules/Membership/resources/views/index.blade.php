@extends('layouts.master')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h1 class="text-3xl font-bold text-gray-900 mb-6">Membership Management</h1>

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

                <!-- Membership Status -->
                <div class="bg-gray-50 p-6 rounded-lg mb-6">
                    <h2 class="text-xl font-semibold mb-4">Your Membership Status</h2>

                    @if(auth()->user()->hasRole('member'))
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium">
                                    Active Member
                                </span>
                                <p class="text-gray-600 mt-2">Your membership is active. Thank you for being part of our club!</p>
                            </div>
                            <form action="{{ route('membership.leave') }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                                        onclick="return confirm('Are you sure you want to leave the membership?')">
                                    Leave Membership
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-sm font-medium">
                                    Not a Member
                                </span>
                                <p class="text-gray-600 mt-2">Join our club to access exclusive benefits and features.</p>
                            </div>
                            <button type="button"
                                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                                    onclick="openPaymentModal()">
                                Become a Member
                            </button>
                        </div>
                    @endif
                </div>

                <!-- Membership Benefits -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white border border-gray-200 rounded-lg p-6 text-center">
                        <div class="text-3xl mb-4">🏆</div>
                        <h3 class="text-lg font-semibold mb-2">Tournament Access</h3>
                        <p class="text-gray-600">Participate in exclusive club tournaments</p>
                    </div>
                    <div class="bg-white border border-gray-200 rounded-lg p-6 text-center">
                        <div class="text-3xl mb-4">📅</div>
                        <h3 class="text-lg font-semibold mb-2">Event Priority</h3>
                        <p class="text-gray-600">Early access to events and training sessions</p>
                    </div>
                    <div class="bg-white border border-gray-200 rounded-lg p-6 text-center">
                        <div class="text-3xl mb-4">🎯</div>
                        <h3 class="text-lg font-semibold mb-2">Premium Features</h3>
                        <p class="text-gray-600">Access to advanced training resources</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div id="paymentModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <h3 class="text-lg font-medium text-gray-900">Become a Member</h3>

            <!-- QR Code Payment Section -->
        <div class="mt-4">
            <p class="text-sm text-gray-500 mb-4">Scan the QR code to complete your membership payment</p>

            <div class="bg-white p-4 rounded-lg border border-gray-200 mb-4">
                <!-- QR Code will be generated here -->
                <div id="qrCodeContainer" class="flex justify-center">
                    @if(isset($qrCode) && $qrCode)
                        <div class="text-center">
                            {!! $qrCode !!}
                            <p class="text-xs text-gray-500 mt-2">Scan to simulate payment</p>
                        </div>
                    @else
                        <div class="text-center p-4">
                            <div class="bg-gray-200 w-48 h-48 flex items-center justify-center mx-auto mb-2 rounded">
                                <span class="text-gray-500 text-sm">QR Code Error</span>
                            </div>
                            <p class="text-sm text-gray-600">QR code failed to generate</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="text-left bg-gray-50 p-4 rounded-lg mb-4">
                <h4 class="font-semibold text-sm mb-2">Payment Details:</h4>
                <p class="text-xs text-gray-600">Amount: 0.00 EUR (Testing)</p>
                <p class="text-xs text-gray-600">Beneficiary: Sports Club</p>
                <p class="text-xs text-gray-600">Reference: {{ $paymentReference ?? 'N/A' }}</p>
            </div>
        </div>

            <div class="flex justify-between mt-4">
                <button type="button"
                        class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded"
                        onclick="closePaymentModal()">
                    Cancel
                </button>

                <!-- For testing - simulate successful payment -->
                <form action="{{ route('membership.join') }}" method="POST">
                    @csrf
                    <button type="submit"
                            class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                        Simulate Payment (Test)
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

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
</script>
@endsection
