@extends('layouts.master')

@section('title', 'Sporty a Rozvrhy')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h1 class="text-3xl font-bold text-gray-900 mb-6">Sporty a Rozvrhy</h1>

                <!-- Sports Overview -->
                <div class="mb-8">
                    <h2 class="text-2xl font-semibold text-gray-800 mb-4">Sporty v našem klubu</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-lg p-6 shadow-md">
                            <div class="text-3xl mb-4">⚽</div>
                            <h3 class="text-xl font-bold mb-2">Fotbal</h3>
                            <p class="text-blue-100 text-sm">Muži, ženy, mládež</p>
                        </div>
                        <div class="bg-gradient-to-br from-green-500 to-green-600 text-white rounded-lg p-6 shadow-md">
                            <div class="text-3xl mb-4">🎾</div>
                            <h3 class="text-xl font-bold mb-2">Tenis</h3>
                            <p class="text-green-100 text-sm">Všechny úrovně</p>
                        </div>
                        <div class="bg-gradient-to-br from-purple-500 to-purple-600 text-white rounded-lg p-6 shadow-md">
                            <div class="text-3xl mb-4">🏀</div>
                            <h3 class="text-xl font-bold mb-2">Basketbal</h3>
                            <p class="text-purple-100 text-sm">Muži a ženy</p>
                        </div>
                    </div>
                </div>

                <!-- Training Schedule -->
                <div class="mb-8">
                    <h2 class="text-2xl font-semibold text-gray-800 mb-4">Tréninkové rozvrhy</h2>
                    <div class="bg-gray-50 rounded-lg p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="text-center">
                                <h3 class="font-semibold text-lg mb-3 text-gray-800">Fotbal</h3>
                                <ul class="space-y-2 text-sm text-gray-600">
                                    <li>Pondělí: 17:00-19:00</li>
                                    <li>Středa: 16:00-18:00</li>
                                    <li>Pátek: 18:00-20:00</li>
                                </ul>
                            </div>
                            <div class="text-center">
                                <h3 class="font-semibold text-lg mb-3 text-gray-800">Tenis</h3>
                                <ul class="space-y-2 text-sm text-gray-600">
                                    <li>Úterý: 16:00-22:00</li>
                                    <li>Čtvrtek: 14:00-20:00</li>
                                    <li>Sobota: 8:00-12:00</li>
                                </ul>
                            </div>
                            <div class="text-center">
                                <h3 class="font-semibold text-lg mb-3 text-gray-800">Basketbal</h3>
                                <ul class="space-y-2 text-sm text-gray-600">
                                    <li>Pondělí: 19:00-21:00</li>
                                    <li>Středa: 17:00-19:00</li>
                                    <li>Pátek: 18:00-20:00</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
