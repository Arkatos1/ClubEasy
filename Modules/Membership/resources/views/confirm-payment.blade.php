@extends('layouts.master')

@section('title', __('Potvrzení platby - Sports Club'))

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-3xl mx-auto px-4">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <!-- Header -->
            <div class="bg-green-600 px-6 py-4">
                <h1 class="text-2xl font-bold text-white">{{ __('Potvrďte svou platbu') }}</h1>
                <p class="text-green-100 mt-1">{{ __('Skoro hotovo! Stačí potvrdit, že jste odeslali platbu.') }}</p>
            </div>

            <!-- Progress Steps -->
            <div class="px-6 py-4 border-b">
                <div class="flex items-center justify-between max-w-md mx-auto">
                    <div class="text-center">
                        <div class="w-8 h-8 bg-green-600 text-white rounded-full flex items-center justify-center mx-auto">1</div>
                        <p class="text-xs mt-1 text-green-600 font-semibold">{{ __('Naskenujte a plaťte') }}</p>
                    </div>
                    <div class="flex-1 h-1 bg-green-600 mx-2"></div>
                    <div class="text-center">
                        <div class="w-8 h-8 bg-green-600 text-white rounded-full flex items-center justify-center mx-auto">2</div>
                        <p class="text-xs mt-1 text-green-600 font-semibold">{{ __('Potvrďte') }}</p>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <!-- Important Notice -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <div class="flex items-start">
                        <div class="text-blue-600 mt-1 mr-3">💡</div>
                        <div>
                            <p class="text-blue-800 font-semibold">{{ __('Jak to funguje:') }}</p>
                            <p class="text-blue-700 text-sm mt-1">
                                {{ __('Naskenovali jste QR kód a odeslali platbu. Nyní stačí kliknout na tlačítko níže a informovat naše administrátory. Vaše členství bude aktivováno po ověření platby.') }}
                            </p>
                        </div>
                    </div>
                </div>

                @if (session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                        {{ session('error') }}
                    </div>
                @endif

                @if (session('info'))
                    <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded mb-6">
                        {{ session('info') }}
                    </div>
                @endif

                <div class="grid md:grid-cols-2 gap-8">
                    <!-- Payment Details -->
                    <div>
                        <h3 class="font-semibold text-lg mb-4">{{ __('Detaily platby') }}</h3>
                        <div class="space-y-3 bg-gray-50 rounded-lg p-4">
                            <div class="flex justify-between">
                                <span class="text-gray-600">{{ __('Částka:') }}</span>
                                <span class="font-semibold">{{ $amount }} Kč</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">{{ __('Účet:') }}</span>
                                <span class="font-mono text-sm">{{ $account }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">{{ __('Variabilní symbol:') }}</span>
                                <span class="font-mono bg-yellow-100 px-2 py-1 rounded text-sm">{{ $reference }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">{{ __('Příjemce:') }}</span>
                                <span class="font-semibold">{{ $beneficiary }}</span>
                            </div>
                        </div>

                        <div class="mt-4 bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                            <p class="text-yellow-800 text-sm">
                                <strong>{{ __('Důležité:') }}</strong> {{ __('Uchovejte si potvrzení o platbě od vaší banky.') }}
                                {{ __('Váš variabilní symbol je') }} <code class="bg-yellow-100 px-1">{{ $reference }}</code>
                            </p>
                        </div>

                        <!-- What happens next -->
                        <div class="mt-6 bg-green-50 border border-green-200 rounded-lg p-4">
                            <h4 class="font-semibold text-green-800 mb-2">{{ __('Co se stane dál:') }}</h4>
                            <ul class="text-green-700 text-sm space-y-1">
                                <li>✅ {{ __('Obdržíte potvrzovací email') }}</li>
                                <li>✅ {{ __('Administrátoři budou informováni') }}</li>
                                <li>⏳ {{ __('Ověříme vaši platbu (1-2 pracovní dny)') }}</li>
                                <li>🎉 {{ __('Vaše členství bude aktivováno!') }}</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Confirmation Form -->
                    <div>
                        <h3 class="font-semibold text-lg mb-4">{{ __('Odeslat potvrzení platby') }}</h3>
                        <div class="bg-green-50 border border-green-200 rounded-lg p-5">
                            <p class="text-green-800 mb-4">
                                {{ __('Klikněte na tlačítko níže pro potvrzení, že jste odeslali platbu. Naši administrátoři budou informováni a aktivují vaše členství po ověření.') }}
                            </p>

                            @if(auth()->user()->hasPendingMembership())
                                <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4">
                                    {{ __('Vaše platba již čeká na schválení. Počkejte prosím na potvrzení administrátora.') }}
                                </div>
                                <a href="{{ route('membership.index') }}"
                                   class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 px-6 rounded-lg text-lg transition duration-200 flex items-center justify-center">
                                    <span class="mr-2">↩️</span>
                                    {{ __('Zpět na členství') }}
                                </a>
                            @elseif(auth()->user()->hasActiveMembership())
                                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                                    {{ __('Již jste členem! Děkujeme, že jste součástí našeho klubu.') }}
                                </div>
                                <a href="{{ route('membership.index') }}"
                                   class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 px-6 rounded-lg text-lg transition duration-200 flex items-center justify-center">
                                    <span class="mr-2">↩️</span>
                                    {{ __('Zpět na členství') }}
                                </a>
                            @else
                                <form action="{{ route('membership.process-confirmation') }}" method="POST">
                                    @csrf

                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            {{ __('ID transakce (volitelné)') }}
                                        </label>
                                        <input type="text" name="transaction_id"
                                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                               placeholder="{{ __('Z vaší bankovní aplikace nebo účtenky') }}"
                                               value="{{ old('transaction_id') }}">
                                        <p class="text-xs text-gray-500 mt-1">{{ __('Pomůže nám rychleji ověřit vaši platbu') }}</p>
                                    </div>

                                    <div class="mb-4">
                                        <label class="flex items-center">
                                            <input type="checkbox" name="payment_sent" required
                                                   class="rounded border-gray-300 text-green-600 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50">
                                            <span class="ml-2 text-sm text-gray-700">
                                                {{ __('Potvrzuji, že jsem odeslal(a) platbu ve výši') }} <strong>{{ $amount }} Kč</strong>
                                                {{ __('na výše uvedený účet s variabilním symbolem') }} <strong>{{ $reference }}</strong>
                                            </span>
                                        </label>
                                    </div>

                                    <button type="submit"
                                            class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-4 px-6 rounded-lg text-lg transition duration-200 flex items-center justify-center">
                                        <span class="mr-2">✅</span>
                                        {{ __('Ano, zaplatil(a) jsem - Odeslat ke schválení!') }}
                                    </button>
                                </form>
                            @endif

                            <p class="text-xs text-gray-500 mt-3 text-center">
                                {{ __('Kliknutím potvrzujete, že jste odeslali platbu na výše uvedený účet.') }}
                            </p>
                        </div>

                        <!-- Cancel Option -->
                        @if(!auth()->user()->hasActiveMembership() && !auth()->user()->hasPendingMembership())
                        <div class="mt-4 text-center">
                            <a href="{{ route('membership.index') }}"
                               class="text-gray-600 hover:text-gray-800 text-sm inline-flex items-center">
                                <span class="mr-1">←</span>
                                {{ __('Zrušit a vrátit se zpět') }}
                            </a>
                        </div>
                        @endif

                        <!-- Help Section -->
                        <div class="mt-6 text-center">
                            <p class="text-sm text-gray-600">
                                {{ __('Problém s platbou?') }} <br>
                                <a href="mailto:{{ config('mail.from.address') }}" class="text-blue-600 hover:text-blue-800">
                                    {{ __('Kontaktujte nás na') }} {{ config('mail.from.address') }}
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
