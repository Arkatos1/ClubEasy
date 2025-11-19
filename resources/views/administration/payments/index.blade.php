@extends('layouts.master')

@section('title', 'Správa plateb')

@section('content')
<div class="min-h-screen pb-16">
    <div class="px-4 py-8">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Správa plateb</h1>
                <p class="text-gray-600">Schvalování a správa plateb za členství v klubu</p>
            </div>

            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-orange-500">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center">
                                <span class="text-orange-600">⏳</span>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Čekající platby</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $pendingCount }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                <span class="text-green-600">✅</span>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Ověřené platby</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $verifiedCount }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                <span class="text-blue-600">📅</span>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Tento měsíc</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $verifiedThisMonth }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                <span class="text-purple-600">💰</span>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Příjmy</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $totalRevenue }} Kč</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow mb-8">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Rychlé akce</h3>
                </div>
                <div class="p-6">
                    <div class="flex flex-wrap gap-4">
                        <a href="{{ route('administration.payments.pending') }}"
                           class="inline-flex items-center px-4 py-2 bg-orange-600 border border-transparent rounded-md font-semibold text-white hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition">
                            <span class="mr-2">⏳</span>
                            Zobrazit čekající platby
                        </a>

                        <a href="{{ route('users.index') }}"
                           class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition">
                            <span class="mr-2">👥</span>
                            Správa uživatelů
                        </a>

                        <a href="{{ url('/administration') }}"
                           class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                            <span class="mr-2">←</span>
                            Zpět na administraci
                        </a>
                    </div>
                </div>
            </div>

            <!-- Recent Payments Log -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-lg font-medium text-gray-900">Historie plateb</h3>
                    <p class="text-sm text-gray-500">
                        Zobrazeno {{ $recentPayments->firstItem() }} - {{ $recentPayments->lastItem() }} z {{ $recentPayments->total() }}
                    </p>
                </div>
                <div class="p-6">
                    @if($recentPayments->count() > 0)
                        <div class="space-y-4 mb-6">
                            @foreach($recentPayments as $payment)
                            <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                                <div class="flex items-center space-x-4">
                                    <div class="flex-shrink-0">
                                        @if($payment->status === 'active')
                                            <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                                <span class="text-green-600 text-lg">✅</span>
                                            </div>
                                        @else
                                            <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                                                <span class="text-red-600 text-lg">❌</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">
                                            {{ $payment->user->first_name }} {{ $payment->user->last_name }}
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            {{ $payment->user->email }}
                                        </p>
                                        <p class="text-xs text-gray-400 mt-1">
                                            VS: {{ $payment->payment_reference }} •
                                            {{ $payment->amount }} Kč
                                        </p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium
                                        @if($payment->status === 'active') text-green-600
                                        @else text-red-600 @endif">
                                        @if($payment->status === 'active')
                                            Schváleno
                                        @else
                                            Zamítnuto
                                        @endif
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        @if($payment->payment_verified_at)
                                            {{ $payment->payment_verified_at->format('d.m.Y H:i') }}
                                        @else
                                            {{ $payment->updated_at->format('d.m.Y H:i') }}
                                        @endif
                                    </p>
                                    @if($payment->cancellation_reason)
                                        <p class="text-xs text-gray-400 mt-1">
                                            Důvod: {{ Str::limit($payment->cancellation_reason, 30) }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        @if($recentPayments->hasPages())
                        <div class="flex items-center justify-between border-t border-gray-200 pt-4">
                            <div class="flex justify-between flex-1 sm:hidden">
                                @if($recentPayments->onFirstPage())
                                    <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-300 bg-white border border-gray-300 cursor-default rounded-md">
                                        ← Předchozí
                                    </span>
                                @else
                                    <a href="{{ $recentPayments->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                                        ← Předchozí
                                    </a>
                                @endif

                                @if($recentPayments->hasMorePages())
                                    <a href="{{ $recentPayments->nextPageUrl() }}" class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                                        Další →
                                    </a>
                                @else
                                    <span class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-gray-300 bg-white border border-gray-300 cursor-default rounded-md">
                                        Další →
                                    </span>
                                @endif
                            </div>

                            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-sm text-gray-700">
                                        Zobrazeno
                                        <span class="font-medium">{{ $recentPayments->firstItem() }}</span>
                                        -
                                        <span class="font-medium">{{ $recentPayments->lastItem() }}</span>
                                        z
                                        <span class="font-medium">{{ $recentPayments->total() }}</span>
                                        výsledků
                                    </p>
                                </div>
                                <div>
                                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                        {{-- Previous Page Link --}}
                                        @if($recentPayments->onFirstPage())
                                            <span class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-300 cursor-default">
                                                ←
                                            </span>
                                        @else
                                            <a href="{{ $recentPayments->previousPageUrl() }}" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                                ←
                                            </a>
                                        @endif

                                        {{-- Pagination Elements --}}
                                        @foreach($recentPayments->getUrlRange(1, $recentPayments->lastPage()) as $page => $url)
                                            @if($page == $recentPayments->currentPage())
                                                <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-blue-50 text-sm font-medium text-blue-600">
                                                    {{ $page }}
                                                </span>
                                            @else
                                                <a href="{{ $url }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                                    {{ $page }}
                                                </a>
                                            @endif
                                        @endforeach

                                        {{-- Next Page Link --}}
                                        @if($recentPayments->hasMorePages())
                                            <a href="{{ $recentPayments->nextPageUrl() }}" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                                →
                                            </a>
                                        @else
                                            <span class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-300 cursor-default">
                                                →
                                            </span>
                                        @endif
                                    </nav>
                                </div>
                            </div>
                        </div>
                        @endif
                    @else
                        <div class="text-center py-8">
                            <div class="text-4xl mb-4">📝</div>
                            <p class="text-gray-500">Zatím nebyly zpracovány žádné platby.</p>
                            <p class="text-sm text-gray-400 mt-1">Zobrazí se zde po schválení nebo zamítnutí plateb.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
