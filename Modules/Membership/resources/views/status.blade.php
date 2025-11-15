@extends('layouts.master')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h2 class="text-2xl font-bold mb-6">Membership Status</h2>

                @if($isMember)
                    <div class="bg-green-50 border border-green-200 rounded-lg p-6 mb-6">
                        <h3 class="text-lg font-semibold text-green-800 mb-2">Active Membership</h3>
                        <p class="text-green-700">Your membership is currently active and in good standing.</p>
                    </div>
                @else
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mb-6">
                        <h3 class="text-lg font-semibold text-yellow-800 mb-2">No Active Membership</h3>
                        <p class="text-yellow-700">You don't have an active membership. Join now to unlock all features!</p>
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="border rounded-lg p-6">
                        <h3 class="text-lg font-semibold mb-4">Membership Details</h3>
                        <dl class="space-y-2">
                            <div class="flex justify-between">
                                <dt class="text-gray-600">Status:</dt>
                                <dd class="font-medium {{ $isMember ? 'text-green-600' : 'text-gray-600' }}">
                                    {{ $isMember ? 'Active' : 'Inactive' }}
                                </dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-600">Plan:</dt>
                                <dd class="font-medium">{{ $isMember ? 'Premium Membership' : 'Basic Access' }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-600">Member Since:</dt>
                                <dd class="font-medium">{{ auth()->user()->created_at->format('F j, Y') }}</dd>
                            </div>
                        </dl>
                    </div>

                    <div class="border rounded-lg p-6">
                        <h3 class="text-lg font-semibold mb-4">Quick Actions</h3>
                        <div class="space-y-3">
                            @if($isMember)
                                <form action="{{ route('membership.leave') }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="block w-full bg-red-600 text-white text-center px-4 py-2 rounded-md hover:bg-red-700"
                                            onclick="return confirm('Are you sure you want to leave the membership?')">
                                        Leave Membership
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('membership.index') }}" class="block w-full bg-blue-600 text-white text-center px-4 py-2 rounded-md hover:bg-blue-700">
                                    Join Membership
                                </a>
                            @endif
                            <a href="{{ route('membership.plans') }}" class="block w-full bg-gray-600 text-white text-center px-4 py-2 rounded-md hover:bg-gray-700">
                                View Plans
                            </a>
                            <a href="{{ route('membership.index') }}" class="block w-full bg-green-600 text-white text-center px-4 py-2 rounded-md hover:bg-green-700">
                                Back to Dashboard
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Membership Features Comparison -->
                <div class="mt-8">
                    <h2 class="text-xl font-semibold mb-4">Feature Comparison</h2>
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse border border-gray-200">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="border border-gray-200 px-4 py-2 text-left">Feature</th>
                                    <th class="border border-gray-200 px-4 py-2 text-center">Basic</th>
                                    <th class="border border-gray-200 px-4 py-2 text-center">Premium</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="border border-gray-200 px-4 py-2">Tournament Participation</td>
                                    <td class="border border-gray-200 px-4 py-2 text-center">❌</td>
                                    <td class="border border-gray-200 px-4 py-2 text-center">✅</td>
                                </tr>
                                <tr>
                                    <td class="border border-gray-200 px-4 py-2">Event Priority</td>
                                    <td class="border border-gray-200 px-4 py-2 text-center">❌</td>
                                    <td class="border border-gray-200 px-4 py-2 text-center">✅</td>
                                </tr>
                                <tr>
                                    <td class="border border-gray-200 px-4 py-2">Training Resources</td>
                                    <td class="border border-gray-200 px-4 py-2 text-center">Basic</td>
                                    <td class="border border-gray-200 px-4 py-2 text-center">Advanced</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
