@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h2 class="text-2xl font-bold mb-6">Membership Plans</h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="border rounded-lg p-6 text-center">
                        <h3 class="text-xl font-bold mb-4">Basic</h3>
                        <p class="text-3xl font-bold mb-4">$10/month</p>
                        <ul class="text-left mb-6">
                            <li>✓ Access to facilities</li>
                            <li>✓ Basic training</li>
                            <li>✗ Tournament entry</li>
                        </ul>
                        <button class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700">Select Plan</button>
                    </div>

                    <div class="border rounded-lg p-6 text-center border-blue-500 bg-blue-50">
                        <h3 class="text-xl font-bold mb-4">Pro</h3>
                        <p class="text-3xl font-bold mb-4">$25/month</p>
                        <ul class="text-left mb-6">
                            <li>✓ Access to facilities</li>
                            <li>✓ Advanced training</li>
                            <li>✓ Tournament entry</li>
                        </ul>
                        <button class="bg-green-600 text-white px-6 py-2 rounded-md hover:bg-green-700">Select Plan</button>
                    </div>

                    <div class="border rounded-lg p-6 text-center">
                        <h3 class="text-xl font-bold mb-4">Elite</h3>
                        <p class="text-3xl font-bold mb-4">$50/month</p>
                        <ul class="text-left mb-6">
                            <li>✓ All Pro features</li>
                            <li>✓ Personal trainer</li>
                            <li>✓ Priority booking</li>
                        </ul>
                        <button class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700">Select Plan</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
