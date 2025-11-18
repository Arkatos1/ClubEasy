@extends('layouts.master')

@section('content')
<div class="px-4 py-6">
    <!-- News & Blog Section -->
    <div class="max-w-7xl mx-auto">
        <!-- Featured Post (Canvas UI-style) -->
        @if(isset($featuredPost) && $featuredPost)
        <div class="bg-white rounded-xl shadow-2xl overflow-hidden mb-12 transform hover:scale-[1.01] transition-transform duration-300">
            @if($featuredPost->featured_image)
            <img src="{{ $featuredPost->featured_image }}" alt="{{ $featuredPost->title }}"
                 class="w-full h-96 object-cover">
            @endif
            <div class="p-8">
                @if($featuredPost->topic->isNotEmpty())
                <span class="inline-block bg-gradient-to-r from-blue-500 to-blue-600 text-white px-3 py-1 rounded-full text-sm font-medium mb-4">
                    {{ $featuredPost->topic->first()->name }}
                </span>
                @endif

                <h1 class="text-4xl font-bold text-gray-900 mb-4 leading-tight">
                    <a href="{{ route('blog.show', $featuredPost->slug) }}" class="hover:text-blue-600 transition-colors">
                        {{ $featuredPost->title }}
                    </a>
                </h1>

                <div class="text-gray-600 mb-6 text-lg leading-relaxed">
                    {!! Str::limit(strip_tags($featuredPost->body), 200) !!}
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4 text-sm text-gray-500">
                        <span>📅 {{ $featuredPost->published_at->format('F j, Y') }}</span>
                        <span>⏱️ {{ round(str_word_count(strip_tags($featuredPost->body)) / 200) }} min read</span>
                    </div>
                    <a href="{{ route('blog.show', $featuredPost->slug) }}"
                       class="bg-gradient-to-r from-blue-600 to-green-500 text-white px-6 py-3 rounded-lg font-semibold hover:shadow-lg transition-all">
                        {{ __('Read Full Story') }}
                    </a>
                </div>
            </div>
        </div>
        @else
        <div class="bg-white rounded-lg shadow-md p-12 text-center mb-12">
            <div class="text-6xl mb-4">📝</div>
            <h3 class="text-2xl font-bold text-gray-700 mb-4">{{ __('No Featured Posts Yet') }}</h3>
            <p class="text-gray-600 mb-6">{{ __('Check back soon for the latest updates from our sports club!') }}</p>
            <a href="{{ route('canvas-ui') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700">
                {{ __('Write First Post') }}
            </a>
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-3">
                <!-- Section Header -->
                <div class="flex justify-between items-center mb-8">
                    <h2 class="text-3xl font-bold text-gray-900">{{ __('Latest News & Updates') }}</h2>
                    <div class="flex space-x-2">
                        @isset($topics)
                            @foreach($topics->take(3) as $topic)
                            <a href="{{ route('blog.topic', $topic->slug) }}"
                               class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1 rounded-full text-sm transition-colors">
                                {{ $topic->name }}
                            </a>
                            @endforeach
                        @endisset
                    </div>
                </div>

                <!-- Recent Posts Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8 mb-12">
                    @isset($recentPosts)
                        @foreach($recentPosts as $post)
                        <article class="bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300 overflow-hidden">
                            @if($post->featured_image)
                            <img src="{{ $post->featured_image }}" alt="{{ $post->title }}"
                                 class="w-full h-48 object-cover">
                            @else
                            <div class="w-full h-48 bg-gradient-to-br from-blue-400 to-green-400 flex items-center justify-center">
                                <span class="text-white text-lg font-semibold">{{ __('Sports Club') }}</span>
                            </div>
                            @endif

                            <div class="p-6">
                                @if($post->topic->isNotEmpty())
                                <span class="inline-block bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs font-medium mb-3">
                                    {{ $post->topic->first()->name }}
                                </span>
                                @endif

                                <h3 class="text-xl font-semibold text-gray-800 mb-3 leading-tight">
                                    <a href="{{ route('blog.show', $post->slug) }}" class="hover:text-blue-600 transition-colors">
                                        {{ $post->title }}
                                    </a>
                                </h3>

                                <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                                    {!! Str::limit(strip_tags($post->body), 120) !!}
                                </p>

                                <div class="flex items-center justify-between text-xs text-gray-500">
                                    <div class="flex items-center space-x-3">
                                        <span>{{ $post->published_at->format('M j') }}</span>
                                    </div>
                                    <a href="{{ route('blog.show', $post->slug) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                        {{ __('Read') }} →
                                    </a>
                                </div>
                            </div>
                        </article>
                        @endforeach
                    @endisset
                </div>

                <!-- Empty State -->
                @if((!isset($recentPosts) || $recentPosts->isEmpty()) && (!isset($featuredPost) || !$featuredPost))
                <div class="bg-white rounded-lg shadow-md p-12 text-center">
                    <div class="text-6xl mb-4">📝</div>
                    <h3 class="text-2xl font-bold text-gray-700 mb-4">{{ __('No News Yet') }}</h3>
                    <p class="text-gray-600 mb-6">{{ __('Check back soon for the latest updates from our sports club!') }}</p>
                    <a href="{{ route('canvas-ui') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700">
                        {{ __('Write First Post') }}
                    </a>
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1 space-y-8">
                <!-- Popular Posts -->
                @if(isset($popularPosts) && $popularPosts->count() > 0)
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 border-b pb-2">🔥 {{ __('Popular Posts') }}</h3>
                    <div class="space-y-4">
                        @foreach($popularPosts as $post)
                        <a href="{{ route('blog.show', $post->slug) }}" class="block group">
                            <h4 class="text-sm font-semibold text-gray-800 group-hover:text-blue-600 transition-colors mb-1">
                                {{ $post->title }}
                            </h4>
                            <div class="flex items-center text-xs text-gray-500">
                                <span>{{ $post->published_at->format('M j') }}</span>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Topics -->
                @if(isset($latestTopics) && $latestTopics->count() > 0)
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 border-b pb-2">📚 {{ __('Topics') }}</h3>
                    <div class="space-y-2">
                        @foreach($latestTopics as $topic)
                        <a href="{{ route('blog.topic', $topic->slug) }}"
                           class="flex items-center justify-between text-sm text-gray-700 hover:text-blue-600 transition-colors">
                            <span>{{ $topic->name }}</span>
                            <span class="bg-gray-100 text-gray-500 px-2 py-1 rounded-full text-xs">
                                {{ $topic->posts_count ?? 0 }}
                            </span>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Newsletter Signup -->
                <div class="bg-gradient-to-br from-blue-500 to-green-400 rounded-lg shadow-md p-6 text-white">
                    <h3 class="text-lg font-bold mb-2">📬 {{ __('Stay Updated') }}</h3>
                    <p class="text-blue-100 text-sm mb-4">{{ __('Get the latest news from our sports club delivered to your inbox.') }}</p>
                    <form class="space-y-3">
                        <input type="email" placeholder="{{ __('Your email address') }}"
                               class="w-full px-3 py-2 rounded text-gray-700 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
                        <button type="submit"
                                class="w-full bg-white text-blue-600 py-2 rounded font-semibold text-sm hover:bg-gray-100 transition-colors">
                            {{ __('Subscribe') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
