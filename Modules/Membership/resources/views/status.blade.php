@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h2 class="text-2xl font-bold mb-6">Membership Status</h2>

                <div class="bg-green-50 border border-green-200 rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold text-green-800 mb-2">Active Membership</h3>
                    <p class="text-green-700">Your membership is currently active and in good standing.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="border rounded-lg p-6">
                        <h3 class="text-lg font-semibold mb-4">Membership Details</h3>
                        <dl class="space-y-2">
                            <div class="flex justify-between">
                                <dt class="text-gray-600">Plan:</dt>
                                <dd class="font-medium">Pro Membership</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-600">Status:</dt>
                                <dd class="font-medium text-green-600">Active</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-600">Renewal Date:</dt>
                                <dd class="font-medium">December 15, 2024</dd>
                            </div>
                        </dl>
                    </div>

                    <div class="border rounded-lg p-6">
                        <h3 class="text-lg font-semibold mb-4">Quick Actions</h3>
                        <div class="space-y-3">
                            <a href="{{ route('membership.plans') }}" class="block w-full bg-blue-600 text-white text-center px-4 py-2 rounded-md hover:bg-blue-700">Change Plan</a>
                            <a href="#" class="block w-full bg-gray-600 text-white text-center px-4 py-2 rounded-md hover:bg-gray-700">Update Payment</a>
                            <a href="{{ route('membership.index') }}" class="block w-full bg-green-600 text-white text-center px-4 py-2 rounded-md hover:bg-green-700">Back to Dashboard</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
