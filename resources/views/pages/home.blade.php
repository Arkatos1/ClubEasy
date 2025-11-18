@extends('layouts.master')

@section('title', __('Home'))

@section('content')
<div class="px-4 py-6">
    <!-- News & Blog Section -->
    <div class="max-w-7xl mx-auto">
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

                <!-- All Posts Grid - Full Width Cards -->
                <div class="space-y-8 mb-12">
                    @isset($posts)
                        @foreach($posts as $post)
                        @php
                            // Convert HTML to plain text with smarter spacing
                            $text = $post->body;

                            // Replace closing paragraph tags with single newline (not double)
                            $text = str_replace('</p>', "\n", $text);

                            // Replace <br> tags with single newline
                            $text = str_replace(['<br>', '<br/>', '<br />'], "\n", $text);

                            // Remove all HTML tags
                            $text = strip_tags($text);

                            // Decode HTML entities
                            $text = html_entity_decode($text);

                            // Clean up whitespace - replace multiple newlines with just one
                            $text = preg_replace('/\n\s*\n/', "\n", $text); // Multiple blank lines → single line
                            $text = preg_replace('/[ ]+/', ' ', $text);     // Multiple spaces → single space
                            $text = trim($text);                            // Remove leading/trailing whitespace

                            // Create preview
                            $previewText = Str::limit($text, 250);
                            $readingTime = max(1, round(str_word_count($text) / 200));
                        @endphp
                        <article class="bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300 overflow-hidden">
                            <div class="flex flex-col lg:flex-row">
                                @if($post->featured_image)
                                <div class="lg:w-1/3">
                                    <img src="{{ $post->featured_image }}" alt="{{ $post->title }}"
                                         class="w-full h-64 lg:h-full object-cover">
                                </div>
                                @else
                                <div class="lg:w-1/3 bg-gradient-to-br from-blue-400 to-green-400 flex items-center justify-center min-h-64">
                                    <span class="text-white text-lg font-semibold">{{ __('Sports Club') }}</span>
                                </div>
                                @endif

                                <div class="lg:w-2/3 p-6 flex flex-col justify-between">
                                    <div>
                                        @if($post->topic->isNotEmpty())
                                        <span class="inline-block bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium mb-3">
                                            {{ $post->topic->first()->name }}
                                        </span>
                                        @endif

                                        <h3 class="text-2xl font-semibold text-gray-800 mb-4 leading-tight">
                                            <a href="{{ route('blog.show', $post->slug) }}" class="hover:text-blue-600 transition-colors">
                                                {{ $post->title }}
                                            </a>
                                        </h3>

                                        <p class="text-gray-600 text-base mb-4 line-clamp-4" style="white-space: pre-line;">
                                            {{ $previewText }}
                                        </p>
                                    </div>

                                    <div class="flex items-center justify-between text-sm text-gray-500 mt-4">
                                        <div class="flex items-center space-x-4">
                                            <span>{{ $post->published_at->translatedFormat('F j, Y') }}</span>
                                            <span>⏱️ {{ $readingTime }} {{ __('min read') }}</span>
                                        </div>
                                        <a href="{{ route('blog.show', $post->slug) }}" class="text-blue-600 hover:text-blue-800 font-semibold text-lg">
                                            {{ __('Read Full Story') }} →
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </article>
                        @endforeach
                    @endisset
                </div>

                <!-- Pagination -->
                @isset($posts)
                    @if($posts->hasPages())
                    <div class="flex justify-center mt-8">
                        <div class="flex space-x-2">
                            <!-- Previous Page Link -->
                            @if($posts->onFirstPage())
                                <span class="px-4 py-2 bg-gray-100 text-gray-400 rounded-lg cursor-not-allowed">← {{ __('Previous') }}</span>
                            @else
                                <a href="{{ $posts->previousPageUrl() }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">← {{ __('Previous') }}</a>
                            @endif

                            <!-- Page Numbers -->
                            @foreach(range(1, $posts->lastPage()) as $page)
                                @if($page == $posts->currentPage())
                                    <span class="px-4 py-2 bg-blue-600 text-white rounded-lg">{{ $page }}</span>
                                @else
                                    <a href="{{ $posts->url($page) }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">{{ $page }}</a>
                                @endif
                            @endforeach

                            <!-- Next Page Link -->
                            @if($posts->hasMorePages())
                                <a href="{{ $posts->nextPageUrl() }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">{{ __('Next') }} →</a>
                            @else
                                <span class="px-4 py-2 bg-gray-100 text-gray-400 rounded-lg cursor-not-allowed">{{ __('Next') }} →</span>
                            @endif
                        </div>
                    </div>
                    @endif
                @endisset

                <!-- Empty State -->
                @if((!isset($posts) || $posts->isEmpty()))
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

            <!-- Sidebar - Top Right -->
            <div class="lg:col-span-1 space-y-8">
                <!-- Popular Posts -->
                @if(isset($popularPosts) && $popularPosts->count() > 0)
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 border-b pb-2">{{ __('Popular Posts') }}</h3>
                    <div class="space-y-4">
                        @foreach($popularPosts as $post)
                        <a href="{{ route('blog.show', $post->slug) }}" class="block group">
                            <h4 class="text-sm font-semibold text-gray-800 group-hover:text-blue-600 transition-colors mb-1 leading-tight">
                                {{ $post->title }}
                            </h4>
                            <div class="flex items-center text-xs text-gray-500">
                                <span>{{ $post->published_at->translatedFormat('M j, Y') }}</span>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Topics -->
                @if(isset($latestTopics) && $latestTopics->count() > 0)
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 border-b pb-2">{{ __('Topics') }}</h3>
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
            </div>
        </div>
    </div>
</div>

<style>
.line-clamp-4 {
    display: -webkit-box;
    -webkit-line-clamp: 4;
    -webkit-box-orient: vertical;
    overflow: hidden;
    white-space: pre-line;
}
</style>
@endsection
