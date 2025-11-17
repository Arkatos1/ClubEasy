@extends('layouts.master')

@section('title', 'Administrace')

@section('content')
<div class="min-h-screen pb-16">
    <div class="px-4 py-8">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-12">
                <h1 class="text-4xl font-bold text-gray-900 mb-4">Administrace</h1>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Centrální panel pro správu všech aspektů aplikace sportovního klubu
                </p>
            </div>

            <!-- Admin Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-16">
                <!-- Blog Management Card -->
                <a href="{{ url('/canvas') }}" class="block group">
                    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-8 text-white hover:shadow-xl transition-all duration-300 h-full flex flex-col justify-between min-h-[280px]">
                        <div class="flex-1">
                            <div class="text-5xl mb-6 text-center">📝</div>
                            <h3 class="text-2xl font-bold mb-4 text-center">Správa blogu</h3>
                            <p class="text-blue-100 text-center leading-relaxed">
                                Správa blogových příspěvků, témat a nastavení obsahu
                            </p>
                        </div>
                        <div class="mt-6 flex items-center justify-center text-blue-200 group-hover:text-white transition-colors">
                            <span class="font-semibold text-lg">Přejít do správy blogu</span>
                            <svg class="w-5 h-5 ml-3 group-hover:translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>
                    </div>
                </a>

                <!-- App Management Card -->
                <a href="/admin" class="block group">
                    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-8 hover:shadow-xl transition-all duration-300 h-full flex flex-col justify-between min-h-[280px]">
                        <div class="flex-1">
                            <div class="text-5xl mb-6 text-center">⚙️</div>
                            <h3 class="text-2xl font-bold mb-4 text-center">Správa aplikace</h3>
                            <p class="text-center leading-relaxed">
                                Správa aplikace, databáze a systémových nastavení
                            </p>
                        </div>
                        <div class="mt-6 flex items-center justify-center text-green-200 group-hover:text-white transition-colors">
                            <span class="font-semibold text-lg">Přejít do správy aplikace</span>
                            <svg class="w-5 h-5 ml-3 group-hover:translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>
                    </div>
                </a>

                <!-- User Management Card -->
                <a href="{{ route('users.index') }}" class="block group">
                    <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-8 hover:shadow-xl transition-all duration-300 h-full flex flex-col justify-between min-h-[280px]">
                        <div class="flex-1">
                            <div class="text-5xl mb-6 text-center">👥</div>
                            <h3 class="text-2xl font-bold mb-4 text-center">Správa uživatelů</h3>
                            <p class="text-purple-100 text-center leading-relaxed">
                                Správa uživatelů, rolí a oprávnění v celém systému
                            </p>
                        </div>
                        <div class="mt-6 flex items-center justify-center group-hover:text-white transition-colors">
                            <span class="font-semibold text-lg">Přejít do správy uživatelů</span>
                            <svg class="w-5 h-5 ml-3 group-hover:translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
