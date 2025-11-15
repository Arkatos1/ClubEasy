@extends('layouts.master')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h2 class="text-2xl font-bold mb-6">Membership Plans</h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Basic Plan -->
                    <div class="border border-gray-200 rounded-lg p-6 text-center">
                        <h3 class="text-xl font-semibold mb-4">Basic</h3>
                        <div class="text-3xl font-bold text-gray-900 mb-4">Free</div>
                        <ul class="text-left mb-6 space-y-2">
                            <li class="flex items-center">
                                <span class="text-green-500 mr-2">✓</span>
                                <span>Access to public events</span>
                            </li>
                            <li class="flex items-center">
                                <span class="text-green-500 mr-2">✓</span>
                                <span>Basic training resources</span>
                            </li>
                            <li class="flex items-center">
                                <span class="text-gray-400 mr-2">✗</span>
                                <span class="text-gray-400">Tournament participation</span>
                            </li>
                        </ul>
                        <button class="bg-gray-300 text-gray-700 font-bold py-2 px-4 rounded w-full cursor-not-allowed">
                            Current Plan
                        </button>
                    </div>

                    <!-- Premium Plan -->
                    <div class="border-2 border-blue-500 rounded-lg p-6 text-center transform scale-105">
                        <div class="bg-blue-500 text-white py-1 px-3 rounded-full text-sm font-medium inline-block mb-4">
                            MOST POPULAR
                        </div>
                        <h3 class="text-xl font-semibold mb-4">Premium</h3>
                        <div class="text-3xl font-bold text-gray-900 mb-4">€10<span class="text-lg text-gray-600">/month</span></div>
                        <ul class="text-left mb-6 space-y-2">
                            <li class="flex items-center">
                                <span class="text-green-500 mr-2">✓</span>
                                <span>All Basic features</span>
                            </li>
                            <li class="flex items-center">
                                <span class="text-green-500 mr-2">✓</span>
                                <span>Tournament participation</span>
                            </li>
                            <li class="flex items-center">
                                <span class="text-green-500 mr-2">✓</span>
                                <span>Priority event registration</span>
                            </li>
                            <li class="flex items-center">
                                <span class="text-green-500 mr-2">✓</span>
                                <span>Advanced training resources</span>
                            </li>
                        </ul>
                        <a href="{{ route('membership.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded w-full block">
                            Get Started
                        </a>
                    </div>

                    <!-- Family Plan -->
                    <div class="border border-gray-200 rounded-lg p-6 text-center">
                        <h3 class="text-xl font-semibold mb-4">Family</h3>
                        <div class="text-3xl font-bold text-gray-900 mb-4">€25<span class="text-lg text-gray-600">/month</span></div>
                        <ul class="text-left mb-6 space-y-2">
                            <li class="flex items-center">
                                <span class="text-green-500 mr-2">✓</span>
                                <span>All Premium features</span>
                            </li>
                            <li class="flex items-center">
                                <span class="text-green-500 mr-2">✓</span>
                                <span>Up to 4 family members</span>
                            </li>
                            <li class="flex items-center">
                                <span class="text-green-500 mr-2">✓</span>
                                <span>Family event discounts</span>
                            </li>
                            <li class="flex items-center">
                                <span class="text-green-500 mr-2">✓</span>
                                <span>Dedicated family coordinator</span>
                            </li>
                        </ul>
                        <a href="{{ route('membership.index') }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded w-full block">
                            Choose Family
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
