<footer class="bg-gray-800 text-white mt-auto">
    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div>
                <h3 class="text-lg font-semibold mb-4">{{ __('Sports Club') }}</h3>
                <p class="text-gray-300">{{ __('Your premier destination for sports management and tournament organization.') }}</p>
            </div>
            <div>
                <h3 class="text-lg font-semibold mb-4">{{ __('Quick Links') }}</h3>
                <ul class="space-y-2">
                    <li><a href="/" class="text-gray-300 hover:text-white">{{ __('Home') }}</a></li>
                    <li><a href="/players" class="text-gray-300 hover:text-white">{{ __('Players') }}</a></li>
                    <li><a href="/sports" class="text-gray-300 hover:text-white">{{ __('Matches') }}</a></li>
                </ul>
            </div>
            <div>
                <h3 class="text-lg font-semibold mb-4">{{ __('Contact') }}</h3>
                <p class="text-gray-300">{{ __('Email') }}: info@sportsclub.com</p>
                <p class="text-gray-300">{{ __('Phone') }}: (123) 456-7890</p>
            </div>
        </div>
        <div class="border-t border-gray-700 mt-8 pt-6 text-center text-gray-300">
            <p>&copy; 2024 {{ __('Sports Club') }}. {{ __('Licence') }}</p>
        </div>
    </div>
</footer>
