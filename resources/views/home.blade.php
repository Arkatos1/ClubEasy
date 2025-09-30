@extends('layouts.app')

@section('content')
<div class="px-4 py-6">
    <!-- News Section -->
    <div class="max-w-4xl mx-auto">
        <h2 class="text-3xl font-bold text-gray-900 mb-8">Latest News & Updates</h2>

        <!-- News Post 1 -->
        <article class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h3 class="text-2xl font-semibold text-gray-800 mb-4">Upcoming Tournament Announcement</h3>
            <p class="text-gray-600 mb-4">
                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
                Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
            </p>
            <p class="text-gray-600 mb-4">
                Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.
                Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
            </p>
            <div class="text-sm text-gray-500">
                Published on: January 15, 2024
            </div>
        </article>

        <!-- News Post 2 -->
        <article class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h3 class="text-2xl font-semibold text-gray-800 mb-4">New Team Registrations Open</h3>
            <p class="text-gray-600 mb-4">
                Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium,
                totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo.
            </p>
            <p class="text-gray-600 mb-4">
                Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores
                eos qui ratione voluptatem sequi nesciunt.
            </p>
            <div class="text-sm text-gray-500">
                Published on: January 10, 2024
            </div>
        </article>

        <!-- News Post 3 -->
        <article class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-2xl font-semibold text-gray-800 mb-4">Season Schedule Released</h3>
            <p class="text-gray-600 mb-4">
                At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti
                quos dolores et quas molestias excepturi sint occaecati cupiditate non provident.
            </p>
            <p class="text-gray-600 mb-4">
                Similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga.
                Et harum quidem rerum facilis est et expedita distinctio.
            </p>
            <div class="text-sm text-gray-500">
                Published on: January 5, 2024
            </div>
        </article>
    </div>
</div>
@endsection
