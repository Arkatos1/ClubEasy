@extends('layouts.master')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-3xl mx-auto px-4">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <!-- Header -->
            <div class="bg-green-600 px-6 py-4">
                <h1 class="text-2xl font-bold text-white">Confirm Your Payment</h1>
                <p class="text-green-100 mt-1">Almost there! Just confirm you've sent the payment.</p>
            </div>

            <!-- Progress Steps -->
            <div class="px-6 py-4 border-b">
                <div class="flex items-center justify-between max-w-md mx-auto">
                    <div class="text-center">
                        <div class="w-8 h-8 bg-green-600 text-white rounded-full flex items-center justify-center mx-auto">1</div>
                        <p class="text-xs mt-1 text-green-600 font-semibold">Scan & Pay</p>
                    </div>
                    <div class="flex-1 h-1 bg-green-600 mx-2"></div>
                    <div class="text-center">
                        <div class="w-8 h-8 bg-green-600 text-white rounded-full flex items-center justify-center mx-auto">2</div>
                        <p class="text-xs mt-1 text-green-600 font-semibold">Confirm</p>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <!-- Important Notice -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <div class="flex items-start">
                        <div class="text-blue-600 mt-1 mr-3">💡</div>
                        <div>
                            <p class="text-blue-800 font-semibold">How this works:</p>
                            <p class="text-blue-700 text-sm mt-1">
                                You've scanned the QR code and sent the payment. Now just click the button below
                                to activate your membership immediately. We'll verify the payment on our end.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-8">
                    <!-- Payment Details -->
                    <div>
                        <h3 class="font-semibold text-lg mb-4">Payment Details</h3>
                        <div class="space-y-3 bg-gray-50 rounded-lg p-4">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Amount:</span>
                                <span class="font-semibold">{{ $amount }} CZK</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Account:</span>
                                <span class="font-mono text-sm">{{ $account }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Reference:</span>
                                <span class="font-mono bg-yellow-100 px-2 py-1 rounded text-sm">{{ $reference }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Beneficiary:</span>
                                <span class="font-semibold">{{ $beneficiary }}</span>
                            </div>
                        </div>

                        <div class="mt-4 bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                            <p class="text-yellow-800 text-sm">
                                <strong>Important:</strong> Keep your payment confirmation from your bank.
                                Your reference number is <code class="bg-yellow-100 px-1">{{ $reference }}</code>
                            </p>
                        </div>
                    </div>

                    <!-- Confirmation Form -->
                    <div>
                        <h3 class="font-semibold text-lg mb-4">Activate Membership</h3>
                        <div class="bg-green-50 border border-green-200 rounded-lg p-5">
                            <p class="text-green-800 mb-4">
                                Click the button below to confirm you've sent the payment and activate your membership immediately.
                            </p>

                            <form action="{{ route('membership.process-confirmation') }}" method="POST">
                                @csrf

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Transaction ID (optional)
                                    </label>
                                    <input type="text" name="transaction_id"
                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                           placeholder="From your bank app or receipt">
                                    <p class="text-xs text-gray-500 mt-1">Helps us verify your payment faster</p>
                                </div>

                                <button type="submit"
                                        class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-4 px-6 rounded-lg text-lg transition duration-200 flex items-center justify-center">
                                    <span class="mr-2">✅</span>
                                    Yes, I've Paid - Activate My Membership!
                                </button>
                            </form>

                            <p class="text-xs text-gray-500 mt-3 text-center">
                                By clicking, you confirm you've sent the payment to the account above.
                            </p>
                        </div>

                        <!-- Help Section -->
                        <div class="mt-4 text-center">
                            <p class="text-sm text-gray-600">
                                Problem with payment? <br>
                                <a href="mailto:club@example.com" class="text-blue-600 hover:text-blue-800">
                                    Contact us at club@example.com
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
